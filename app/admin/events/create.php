<?php
require_once '../../../includes/bootstrap.php';
require_once '../../../includes/dbconnect.php';
$pdo = db_connect();

$message = '';
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $category_id = $_POST['category_id'];
  $date = $_POST['date'];
  $time = $_POST['time'];
  $time_end = $_POST['time_end'];
  $location_id = $_POST['location_id'];
  $capacity = $_POST['capacity'];
  $tag_id = $_POST['tag_id'];
  $image_url = null;

  if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == UPLOAD_ERR_OK) {
    $image_url = $_FILES['image_url']['name'];
    if (move_uploaded_file($_FILES['image_url']['tmp_name'], "../../../uploads/" . $image_url)) {
    } else {
      $image_url = null; // Deja el valor como null si falla
    }
  } else {
    $message = "No se subió un archivo o hubo un error.";
  }

  // Preparar la consulta SQL
  $stmt = $pdo->prepare("INSERT INTO events (title, description, category_id, date, time, time_end, location_id, capacity, image_url, tag_id) 
        VALUES (:title, :description, :category_id, :date, :time, :time_end, :location_id, :capacity, :image_url, :tag_id)");
  $stmt->bindParam(':title', $title);
  $stmt->bindParam(':description', $description);
  $stmt->bindParam(':category_id', $category_id);
  $stmt->bindParam(':date', $date);
  $stmt->bindParam(':time', $time);
  $stmt->bindParam(':time_end', $time_end);
  $stmt->bindParam(':location_id', $location_id);
  $stmt->bindParam(':capacity', $capacity);
  $stmt->bindParam(':image_url', $image_url);
  $stmt->bindParam(':tag_id', $tag_id);

  try {
    if ($stmt->execute()) {
      $message = "Evento creado con éxito.";
    }
  } catch (PDOException $e) {
    $message = "No se ha podido crear el evento, inténtelo de nuevo.";
    $error = true;
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear Evento</title>
  <link href="../../../css/common.css" rel="stylesheet" />
  <link href="../../../css/layout.css" rel="stylesheet" />
  <link href="../../../css/create-edit.css" rel="stylesheet" />

</head>

<body>
  <?php loadComponent('top-wrapper') ?>

  <main class="content-wrapper">
    <header>
      <h1>Crear Evento</h1>
    </header>
    <?php if (!empty($message)) : ?>
      <div class="<?= $error ? 'error-message' : 'success-message' ?>">
        <?php if (strpos($message, 'Error') !== false) { ?>
          <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
          </svg>
        <?php } else { ?>
          <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
        <?php } ?>
        <?= $message ?>
      </div>
    <?php endif; ?>

    <section>
      <form action="create.php" method="POST" enctype="multipart/form-data">
        <div class="form card card-body">
          <div class="form-group full-width">
            <label class="label" for="title">Título:</label>
            <input class="input" type="text" id="title" name="title">
          </div>

          <div class="form-group full-width">
            <label class="label" for="description">Descripción:</label>
            <textarea class="input" id="description" name="description"></textarea>
          </div>

          <div class="field-container span-2">
            <label class="label" for="date">Fecha:</label>
            <input class="input" type="date" id="date" name="date">
          </div>

          <div class="field-container span-2">
            <label class="label" for="time">Hora de Inicio:</label>
            <input class="input" type="time" id="time" name="time">
          </div>

          <div class="field-container span-2">
            <label class="label" for="time_end">Hora de Finalización:</label>
            <input class="input" type="time" id="time_end" name="time_end">
          </div>

          <div class="form-group span-3">
            <label class="label" for="capacity">Capacidad:</label>
            <input class="input" type="number" id="capacity" name="capacity">
          </div>

          <div class="form-group span-3">
            <label class="label" for="location_id">Ubicación:</label>
            <select class="input" id="location_id" name="location_id">
              <option value="">Selecciona una ubicación</option>
              <?php
              $stmt = $pdo->query("SELECT * FROM locations");
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group span-3">
            <label class="label" for="category_id">Categorías:</label>
            <select class="input" id="category_id" name="category_id">
              <option value="">Selecciona una categoría</option>
              <?php
              $stmt = $pdo->query("SELECT * FROM category");
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group span-3">
            <label class="label" for="tag_id">Temáticas:</label>
            <select class="input" id="tag_id" name="tag_id">
              <option value="">Selecciona una temática</option>
              <?php
              $stmt = $pdo->query("SELECT * FROM tags");
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
              }
              ?>
            </select>
          </div>


          <div class="form-group full-width upload-section">
            <label class="label" for="image_url">Sube la Imagen del Evento:</label>
            <input type="file" id="image_url" name="image_url" accept="image/*">
          </div>
        </div>

        <div class="button-group">
          <button class="btn btn-primary" type="submit">Crear Evento</button>
          <button type="button" class="btn btn-secondary" onclick="window.location.href = 'list'">Cancelar</button>
        </div>
      </form>
    </section>
  </main>

  <?php loadComponent('bottom-wrapper') ?>
</body>

</html>

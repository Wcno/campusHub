<?php
  require_once '../../../includes/bootstrap.php';
  require_once '../../../includes/dbconnect.php';
  $pdo = db_connect();

  // Obtener el ID del evento de la URL
  if (!isset($_GET['id'])) {
      die('ID de evento no especificado.');
  }

  $eventId = $_GET['id'];

  // Obtener los datos del evento
  $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
  $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
  $stmt->execute();
  $event = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$event) {
      die('Evento no encontrado.');
  }

  // Verificar si se ha enviado el formulario de actualización
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
      $image_url = $event['image_url']; // Mantener la imagen anterior si no se cambia

      // Manejar la actualización de la imagen si se sube una nueva
      if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == UPLOAD_ERR_OK) {
          $image_url = $_FILES['image_url']['name'];
          if (move_uploaded_file($_FILES['image_url']['tmp_name'], "../../../uploads/" . $image_url)) {
              // Imagen subida con éxito
          } else {
              $image_url = $event['image_url']; // Mantener la imagen anterior si falla la subida
          }
      }

      // Actualizar los datos del evento en la base de datos
      $stmt = $pdo->prepare("UPDATE events 
                             SET title = :title, description = :description, category_id = :category_id, date = :date, 
                                 time = :time, time_end = :time_end, location_id = :location_id, capacity = :capacity, 
                                 image_url = :image_url, tag_id = :tag_id
                             WHERE id = :id");
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
      $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);

      try {
          if ($stmt->execute()) {
              $message = "Evento actualizado con éxito.";
          }
      } catch (PDOException $e) {
          $message = "Error al actualizar el evento.";
      }
  }
  ?>
  <!doctype html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Evento</title>
    <link href="../../../css/common.css" rel="stylesheet" />
    <link href="../../../css/layout.css" rel="stylesheet" />
    <link href="../../../css/edit.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  </head>

  <body>
    <?php loadComponent('top-wrapper') ?>

    <main class="container">
    <header>
        <h2>Editar Evento</h2>
    </header>
    <?php if (isset($message)) : ?>
            <div class="<?= strpos($message, 'Error') !== false ? 'error-message' : 'success-message' ?>">
                <span class="material-icons">
                    <?= strpos($message, 'Error') !== false ? 'error' : 'done' ?>
                </span>
                <?= $message ?>
            </div>
    <?php endif; ?>
    <section>
        <form action="edit.php?id=<?= htmlspecialchars($eventId) ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group full-width">
                <label for="title">Título:</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($event['title']) ?>">
            </div>
            <div class="form-group full-width">
                <label for="description">Descripción:</label>
                <textarea id="description" name="description"><?= htmlspecialchars($event['description']) ?></textarea>
            </div>
            <div class="full-width date-time-row">
                <div class="field-container">
                    <label for="date">Fecha:</label>
                    <input type="date" id="date" name="date" value="<?= htmlspecialchars($event['date']) ?>">
                </div>
                <div class="field-container">
                    <label for="time">Hora de Inicio:</label>
                    <input type="time" id="time" name="time" value="<?= htmlspecialchars($event['time']) ?>">
                </div>
                <div class="field-container">
                    <label for="time_end">Hora de Finalización:</label>
                    <input type="time" id="time_end" name="time_end" value="<?= htmlspecialchars($event['time_end']) ?>">
                </div>
                <div class="field-container">
                    <label for="capacity">Capacidad:</label>
                    <input type="number" id="capacity" name="capacity" value="<?= htmlspecialchars($event['capacity']) ?>">
                </div>
            </div>

            <div class="full-width select-group">
                <div class="field-container">
                    <label for="category_id">Categorías:</label>
                    <div class="select-wrapper">
                        <select id="category_id" name="category_id">
                            <option value="">Selecciona una categoría</option>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM category");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $row['id'] == $event['category_id'] ? 'selected' : '';
                                echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="field-container">
                    <label for="tag_id">Temáticas:</label>
                    <div class="select-wrapper">
                        <select id="tag_id" name="tag_id">
                            <option value="">Selecciona una temática</option>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM tags");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $row['id'] == $event['tag_id'] ? 'selected' : '';
                                echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="field-container">
                    <label for="location_id">Ubicación:</label>
                    <div class="select-wrapper">
                        <select id="location_id" name="location_id">
                            <option value="">Selecciona una ubicación</option>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM locations");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $row['id'] == $event['location_id'] ? 'selected' : '';
                                echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group full-width upload-section">
                <label for="image_url">Sube la Imagen del Evento:</label>
                <input type="file" id="image_url" name="image_url" accept="image/*">
                <small>Imagen actual: <?= htmlspecialchars($event['image_url']) ?></small>
            </div>

            <div class="button-group">
                <button type="button" class="cancel-button" onclick="window.history.back();">Cancelar</button>
                <button type="submit">Guardar Cambios</button>
            </div>
        </form>
    </section>
    </main>

    <?php loadComponent('bottom-wrapper') ?>
    <script src="/js/dashboardLayout.js"></script>
  </body>
  </html>

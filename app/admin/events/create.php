  <?php
  require_once '../../../includes/bootstrap.php';
  require_once '../../../includes/dbconnect.php';
  $pdo = db_connect();

  ?>
  <!doctype html>
  <html lang="en">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Crear Evento</title>
    <link href="../../../css/common.css" rel="stylesheet" />
    <link href="../../../css/layout.css" rel="stylesheet" />
    <link href="../../../css/create.css" rel="stylesheet" />
  </head>

  <body>
    <?php loadComponent('top-wrapper') ?>

    <main class="container">
    <header>
        <h2>Crear Evento</h2>
    </header>
    <section>
        <!-- Formulario de creación de eventos -->
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Título:</label>
                <input type="text" id="title" name="title">
            </div>

            <div class="form-group">
                <label for="category_id">Categoría:</label>
                <select id="category_id" name="category_id">
                    <option value="">Selecciona una categoría</option>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM category");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date">Fecha:</label>
                <input type="date" id="date" name="date">
            </div>

            <div class="form-group">
                <label for="time">Hora de Inicio:</label>
                <input type="time" id="time" name="time">
            </div>

            <div class="form-group">
                <label for="time_end">Hora de Fin:</label>
                <input type="time" id="time_end" name="time_end">
            </div>

            <div class="form-group">
                <label for="location_id">Ubicación:</label>
                <select id="location_id" name="location_id">
                    <option value="">Selecciona una ubicación</option>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM locations");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="capacity">Capacidad:</label>
                <input type="number" id="capacity" name="capacity">
            </div>

            <div class="form-group full-width">
                <label for="description">Descripción:</label>
                <textarea id="description" name="description"></textarea>
            </div>

            <div class="form-group full-width upload-section">
                <label for="image_url">Sube la Imagen del Evento:</label>
                <input type="file" id="image_url" name="image_url" accept="image/*">
            </div>

            <div class="form-group full-width">
                <label for="tag_id">Etiquetas:</label>
                <select id="tag_id" name="tag_id">
                    <option value="">Selecciona una etiqueta</option>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM tags");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="button-group">
                <button type="button" class="cancel-button">Cancelar</button>
                <button type="submit">Crear Evento</button>
            </div>
        </form>
    </section>
</main>

    <?php loadComponent('bottom-wrapper') ?>
    <script src="/js/dashboardLayout.js"></script>

  </body>

  </html>

  <?php
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
          echo "Archivo subido correctamente: $image_url";
      } else {
          echo "Error al mover el archivo.";
          $image_url = null; // Deja el valor como null si falla
      }
  } else {
      echo "No se subió un archivo o hubo un error.";
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

  // Ejecutar la consulta y manejar errores
  try {
      if ($stmt->execute()) {
          echo "Evento creado con éxito.";
      }
  } catch (PDOException $e) {
      echo "Error en la creación del evento: "  ;
  }
}

  ?>

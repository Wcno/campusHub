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
            echo "Archivo subido correctamente: $image_url";
        } else {
            echo "Error al mover el archivo.";
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
            header("Location: list.php?success=Evento actualizado con éxito.");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error al actualizar el evento: " . $e->getMessage();
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <?php loadComponent('top-wrapper') ?>

    <main class="container my-5">
        <div class="card p-4 shadow-sm">
            <header class="mb-4">
                <h1 class="text-center">Editar Evento</h1>
            </header>
            <section>
                <!-- Formulario de edición de eventos -->
                <form action="edit.php?id=<?= htmlspecialchars($eventId) ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título del Evento</label>
                        <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea id="description" name="description" class="form-control" rows="3" required><?= htmlspecialchars($event['description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Categoría</label>
                        <select id="category_id" name="category_id" class="form-select" required>
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

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="date" class="form-label">Fecha</label>
                            <input type="date" id="date" name="date" class="form-control" value="<?= htmlspecialchars($event['date']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="time" class="form-label">Hora de Inicio</label>
                            <input type="time" id="time" name="time" class="form-control" value="<?= htmlspecialchars($event['time']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="time_end" class="form-label">Hora de Fin</label>
                            <input type="time" id="time_end" name="time_end" class="form-control" value="<?= htmlspecialchars($event['time_end']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="location" class="form-label">Ubicación</label>
                        <select id="location_id" name="location_id" class="form-select" required>
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

                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacidad</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" value="<?= htmlspecialchars($event['capacity']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagen del Evento</label>
                        <input type="file" id="image_url" name="image_url" class="form-control" accept="image/*">
                        <small>Imagen actual: <?= htmlspecialchars($event['image_url']) ?></small>
                    </div>

                    <div class="mb-3">
                        <label for="tag_id" class="form-label">Etiquetas</label>
                        <select id="tag_id" name="tag_id" class="form-select">
                            <option value="">Selecciona una etiqueta</option>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM tags");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $selected = $row['id'] == $event['tag_id'] ? 'selected' : '';
                                echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="window.history.back();">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <?php loadComponent('bottom-wrapper') ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>

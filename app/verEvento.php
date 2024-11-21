<?php

//importaciones de archivos
require_once '../includes/dbconnect.php';
require_once '../includes/bootstrap.php';

// Obtener el ID del evento desde la URL
$event_id = $_GET['id'];

try {
    $pdo = db_connect();

    // Consulta para obtener los detalles del evento
    $stmt = $pdo->prepare("
        SELECT events.*, locations.name AS location, tags.name AS topic, category.name AS category
        FROM events
        LEFT JOIN locations ON events.location_id = locations.id
        LEFT JOIN tags ON events.tag_id = tags.id
        LEFT JOIN category ON events.category_id = category.id
        WHERE events.id = :id AND events.post = 1
    ");
    $stmt->execute([':id' => $event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die('Error: No se encontró el evento o no está publicado.');
    }
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}

// Manejo de la inscripción o cancelación de inscripción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    try {
        if (!isset($_SESSION['user_id'])) {
            die('Error: Debes iniciar sesión para realizar esta acción.');
        }

        $user_id = $_SESSION['user_id'];

        if ($action === 'subscribe') {
            // Inscribirse al evento
            $stmt = $pdo->prepare("INSERT IGNORE INTO inscriptions (user_id, event_id, inscription_date) VALUES (:user_id, :event_id, NOW())");
            $stmt->execute([':user_id' => $user_id, ':event_id' => $event_id]);
        } elseif ($action === 'unsubscribe') {
            // Cancelar inscripción
            $stmt = $pdo->prepare("DELETE FROM inscriptions WHERE user_id = :user_id AND event_id = :event_id");
            $stmt->execute([':user_id' => $user_id, ':event_id' => $event_id]);
        }

        // Recargar el estado de inscripción
        $stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE user_id = :user_id AND event_id = :event_id");
        $stmt->execute([':user_id' => $user_id, ':event_id' => $event_id]);
        $isEnrolled = $stmt->rowCount() > 0; // Devuelve true si hay una inscripción
    } catch (PDOException $e) {
        die('Error al actualizar inscripción: ' . $e->getMessage());
    }
} else {
    // Verificar si el usuario está inscrito al cargar la página
    $isEnrolled = false;

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE user_id = :user_id AND event_id = :event_id");
        $stmt->execute([':user_id' => $user_id, ':event_id' => $event_id]);
        $isEnrolled = $stmt->rowCount() > 0;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detalle del Evento</title>
    <link href="../css/common.css" rel="stylesheet" />
    <link href="../css/layout.css" rel="stylesheet" />
    <link href="../css/verEvento.css" rel="stylesheet">

</head>

<body>
    <?php loadComponent('top-wrapper') ?>

    <main class="content-wrapper">
        <header>
            <h1>Detalle del Evento</h1>
        </header>

        <section class="content">
            <a href="home.php">
                <button class="btn-sec">Volver a eventos</button>
            </a>
            <!-- contenedor del evento -->
            <section class="event">
                <h1 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h1>
                <img src="../uploads/test-img-catalog.jpg" alt="Imagen del evento">
                <div class="event-detail">
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                    <p><strong>Hora de inicio:</strong> <?php echo date('H:i', strtotime($event['time'])); ?></p>
                    <p><strong>Hora de finalización:</strong> <?php echo date('H:i', strtotime($event['time_end'])); ?></p>


                    <p><strong>Capacidad:</strong> <?php echo htmlspecialchars($event['capacity']); ?></p>
                    <p><strong>location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                    <p><strong>Tema:</strong> <?php echo htmlspecialchars($event['topic']); ?></p>
                    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($event['category'] ?? 'General'); ?></p>
                </div>

                <!-- Botón de inscripción/cancelación -->
                <form method="POST" action="verEvento.php?id=<?php echo htmlspecialchars($event_id); ?>">
                    <?php if ($isEnrolled): ?>
                        <button type="submit" name="action" value="unsubscribe" class="btn-unsubscribe">Cancelar inscripción</button>
                    <?php else: ?>
                        <button type="submit" name="action" value="subscribe" class="btn-prim">Inscribirse</button>
                    <?php endif; ?>
                </form>
            </section>
        </section>
    </main>

    <?php loadComponent('bottom-wrapper') ?>
</body>

</html>
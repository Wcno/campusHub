<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/dbconnect.php';

$event_id = $_GET['id'];

try {
  $pdo = db_connect();

  // Consulta para obtener los detalles del evento
  $stmt = $pdo->prepare("
        SELECT events.*, locations.name AS location, tags.name AS tag, category.name AS category
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
    if (!isset($_SESSION['user'])) {
      die('Error: Debes iniciar sesión para realizar esta acción.');
    }

    $user_id = $_SESSION['user']['id'];

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

  if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];

    $stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE user_id = :user_id AND event_id = :event_id");
    $stmt->execute([':user_id' => $user_id, ':event_id' => $event_id]);
    $isEnrolled = $stmt->rowCount() > 0;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detalle del Evento</title>
  <link href="/css/common.css" rel="stylesheet" />
  <link href="/css/layout.css" rel="stylesheet" />
  <link href="/css/view.css" rel="stylesheet">

</head>

<body>
  <?php loadComponent('top-wrapper') ?>

  <main class="content-wrapper">
    <header>
      <h1>Ver Evento</h1>

      <div class="actions">
        <a href="./list.php" class="btn btn-secondary">
          Volver
        </a>

        <form method="POST">
          <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']) ?>">
          <?php if ($isEnrolled) { ?>
            <button type="submit" name="action" value="unsubscribe" class="btn btn-primary" type="submit">
              Desincribirse
            </button>
          <?php } else { ?>
            <button type="submit" name="action" value="subscribe" class="btn btn-primary" type="submit">
              Inscribirse
            </button>
          <?php } ?>
        </form>
      </div>
    </header>

    <section class="content">
      <div class="event-card card">
        <div class="image-view">
          <!-- <img src="<?php $event['image_url'] ?? '' ?>" alt="event image" /> -->
          <img src="../../uploads/test-img-catalog.jpg" alt="event image" />
        </div>
        <div class="card-body event-info">
          <h2><?php echo htmlspecialchars($event['title']) ?></h2>

          <div class="tags">
            <?php if (!empty($event['tag'])) { ?>
              <span class="tag"><?php echo htmlspecialchars($event['tag']) ?></span>
            <?php } ?>
            <?php if (!empty($event['category'])) { ?>
              <span class="tag"><?php echo htmlspecialchars($event['category']) ?></span>
            <?php } ?>
          </div>

          <p><?php echo htmlspecialchars($event['description']) ?></p>
        </div>

      </div>

      <div class="inscription-card card card-body">
        <div class="event-date">
          <div class="form-group">
            <label class="muted-label">Inicia</label>
            <p>
              <?php echo htmlspecialchars(date('d M, h:ia', strtotime($event['date'] . ' ' . $event['time']))) ?>
            </p>
          </div>

          <div class="form-group">
            <label class="muted-label">Termina</label>
            <p>
              <?php echo htmlspecialchars(date('d M, h:ia', strtotime($event['date'] . ' ' . $event['time_end']))) ?>
            </p>
          </div>
        </div>

        <div class="form-group">
          <label class="muted-label">Ubicación</label>
          <div class="event-location">
            <svg style="color: var(--text-muted);" width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
            </svg>
            <span><?php echo htmlspecialchars($event['location']) ?></span>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php loadComponent('bottom-wrapper') ?>
</body>

</html>

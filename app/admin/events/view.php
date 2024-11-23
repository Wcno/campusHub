<?php
require_once '../../../includes/bootstrap.php';
require_once '../../../includes/dbconnect.php';

$event_id = $_GET['id'];

try {
  $pdo = db_connect();


  // Consulta para obtener los detalles del evento
  $query = "
        SELECT events.*, locations.name AS location, tags.name AS tag, category.name AS category
        FROM events
        LEFT JOIN locations ON events.location_id = locations.id
        LEFT JOIN tags ON events.tag_id = tags.id
        LEFT JOIN category ON events.category_id = category.id
        WHERE events.id = :id
    ";
  $stmt = $pdo->prepare($query);
  $stmt->execute([':id' => $event_id]);
  $event = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die('Error: ' . $e->getMessage());
}

$isPublished = (bool) $event['post'];

// Manejo de la inscripción o cancelación de inscripción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  $action = $_POST['action'];

  try {
    $eventId = $event['id'];

    if ($action === 'publish') {
      // Publicar evento
      $stmt = $pdo->prepare("UPDATE events SET post = TRUE, post_date = NOW() WHERE id = :id");
      $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
      $stmt->execute();
    } elseif ($action === 'unpublish') {
      // Despublicar evento
      $stmt = $pdo->prepare("UPDATE events SET post = FALSE, post_date = NULL WHERE id = :id");
      $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
      $stmt->execute();
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $event_id]);

    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    $isPublished = (bool) $event['post'];
  } catch (PDOException $e) {
    die('Error al actualizar el evento: ' . $e->getMessage());
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
        <a href="./list">
          <button class="btn btn-secondary" type="button">
            Volver
          </button>
        </a>

        <a href="./edit?id=<?php echo htmlspecialchars($event['id']) ?>"">
          <button class=" btn btn-primary" type="button">
          Editar
          </button>
        </a>

        <form method="POST">
          <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']) ?>">
          <?php if ($isPublished) { ?>
            <button type="submit" name="action" value="unpublish" class="btn btn-primary" type="submit">
              Despublicar
            </button>
          <?php } else { ?>
            <button type="submit" name="action" value="publish" class="btn btn-primary" type="submit">
              Publicar
            </button>
          <?php } ?>
        </form>
      </div>
    </header>

    <section class="content">
      <div class="event-card card">
        <div class="image-view">
          <?php
          $imagePath = '/uploads/' . $event['image_url'];
          $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

          $source = ($user['img_profile'] && file_exists($fullImagePath)) ? $imagePath : '/uploads/test-img-catalog.jpg';
          ?>
          <img src="<?php echo baseUrl($source) ?? '' ?>" alt="event image" />
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
          <?php if ($isPublished) { ?>
            <div class="form-group">
              <label class="muted-label">Publicado en</label>
              <p>
                <?php echo htmlspecialchars(date('d M, y', strtotime($event['post_date']))) ?>
              </p>
            </div>
          <?php } ?>

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

    <section class="inscriptions">
      <div>
        <h2>Inscripciones</h2>
      </div>
      <table>
        <thead>
          <tr>
            <th></th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Inscrito en</th>
          </tr>
          <tr>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $stmt = $pdo->prepare("
            SELECT users.*, inscriptions.inscription_date
            FROM users 
            LEFT JOIN inscriptions ON users.id = inscriptions.user_id
            WHERE inscriptions.event_id = :event_id
          ");
          $stmt->bindParam(':event_id', $event_id);
          $stmt->execute();

          $users = $stmt->fetchAll();
          foreach ($users as $user) {
          ?>
            <tr>
              <td>
                <div class="profile-picture">
                  <?php
                  $imagePath = '/uploads/' . $event['image_url'];
                  $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

                  $source = ($user['img_profile'] && file_exists($fullImagePath)) ? $imagePath : '/uploads/test-img-catalog.jpg';
                  ?>
                  <img src="<?php echo baseUrl($source) ?? '' ?>" alt="event image" />
                </div>
              </td>
              <td><?php echo htmlspecialchars($user['name']) ?></td>
              <td><?php echo htmlspecialchars($user['email']) ?></td>
              <td><?php echo htmlspecialchars($user['phone_number']) ?></td>
              <td><?php echo htmlspecialchars($user['inscription_date']) ?></td>
            </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
    </section>
  </main>

  <?php loadComponent('bottom-wrapper') ?>
</body>

</html>

<?php

// Importaciones de archivos
require_once '../../includes/dbconnect.php';
require_once '../../includes/bootstrap.php';

$pdo = db_connect();

// Proceso de inscripción/cancelación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $event_id = $_POST['event_id'];
  $action = $_POST['action'] ?? '';

  try {
    if ($action === 'subscribe') {
      // Inscribirse
      $stmt = $pdo->prepare("INSERT IGNORE INTO inscriptions (user_id, event_id, inscription_date) VALUES (:user_id, :event_id, NOW())");
      $stmt->execute([':user_id' => $user_id, ':event_id' => $event_id]);
    } elseif ($action === 'unsubscribe') {
      // Cancelar inscripción
      $stmt = $pdo->prepare("DELETE FROM inscriptions WHERE user_id = :user_id AND event_id = :event_id");
      $stmt->execute([':user_id' => $user_id, ':event_id' => $event_id]);
    }
  } catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
  }
}

// Capturar filtros y búsqueda
$search = $_GET['search'] ?? ''; // Campo de búsqueda
$location = $_GET['location'] ?? '';   // Filtro de lugar
$category = $_GET['category'] ?? ''; // Filtro de categorías principales
$topic = $_GET['topic'] ?? []; // Filtro de tema

try {
  // Consulta base para los eventos
  $query = "SELECT events.id, events.title, events.date, events.time, events.time_end, 
                     locations.name AS location_name, tags.name AS topic, category.name AS category_name
              FROM events
              LEFT JOIN locations ON events.location_id = locations.id
              LEFT JOIN tags ON events.tag_id = tags.id
              LEFT JOIN category ON events.category_id = category.id
              WHERE events.post = 1"; // Solo eventos publicados

  // Parámetros para la consulta
  $params = [];

  // Filtrar por búsqueda
  if (!empty($search)) {
    $query .= " AND LOWER(events.title) LIKE LOWER(:search)";
    $params[':search'] = '%' . $search . '%';
  }

  // Filtrar por lugar (excepto si selecciona "general")
  if (!empty($location) && $location !== 'general') {
    $query .= " AND locations.id = :location";
    $params[':location'] = $location;
  }

  // Filtrar por tema
  if (!empty($topic) && is_array($topic)) {
    $topicParams = [];
    foreach ($topic as $index => $subcat) {
      $topicParams[] = ":topic" . $index;
      $params[":topic" . $index] = $subcat;
    }
    $query .= " AND tags.id IN (" . implode(',', $topicParams) . ")";
  }

  // Filtrar por categorías
  if (!empty($category) && $category !== 'general') {
    $query .= " AND category.id = :category";
    $params[':category'] = $category;
  }

  // Añadir ordenamiento
  $query .= " ORDER BY events.date ASC, events.time ASC";

  // Preparar y ejecutar la consulta
  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  $events = $stmt->fetchAll();
} catch (PDOException $e) {
  echo '<p>Error al obtener los eventos: ' . $e->getMessage() . '</p>';
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Próximos Eventos</title>
  <link href="../../css/common.css" rel="stylesheet" />
  <link href="../../css/layout.css" rel="stylesheet" />
  <link href="../../css/home.css" rel="stylesheet" />
</head>

<body>
  <?php loadComponent('top-wrapper') ?>

  <main class="content-wrapper">
    <header>
      <h1>Próximos Eventos</h1>
    </header>
    <section class="content">
<!-- abarca los filtros y búsqueda personalizada-->
      <section class="actions">
        <form method="GET" action="list.php">

          <div class="search-event">
            <div class="search-box">
              <input type="text" placeholder="Buscar Evento" name="search" value="<?php echo htmlspecialchars($search); ?>">
              <svg xmlns="http://www.w3.org/2000/svg" class="search-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
              </svg>
            </div>
            <button type="submit" class="btn-prim">Buscar</button>
          </div>

          <section class="filter">
            <h3>Filtros</h3>
            <div class="filter-items">
              <h4>Lugar</h4>
              <?php
              $stmt = $pdo->query("SELECT id, name FROM locations");
              echo '<div class="filter-option"><input type="radio" name="location" value="general" id="general" ' . ($location === 'general' || empty($location) ? 'checked' : '') . '>';
              echo '<label for="general">General</label></div>';
              while ($row = $stmt->fetch()) {
                echo '<div class="filter-option"><input type="radio" name="location" value="' . $row['id'] . '" id="location' . $row['id'] . '" ' . ($location == $row['id'] ? 'checked' : '') . '>';
                echo '<label for="location' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</label></div>';
              }
              ?>
            </div>
            <div class="filter-items">
              <h4>Categorías</h4>
              <?php
              $stmt = $pdo->query("SELECT id, name FROM category");
              echo '<div class="filter-option"><input type="radio" name="category" value="general" id="cat_general" ' . ($category === 'general' || empty($category) ? 'checked' : '') . '>';
              echo '<label for="cat_general">General</label></div>';
              while ($row = $stmt->fetch()) {
                echo '<div class="filter-option"><input type="radio" name="category" value="' . $row['id'] . '" id="cat' . $row['id'] . '" ' . ($category == $row['id'] ? 'checked' : '') . '>';
                echo '<label for="cat' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</label></div>';
              }
              ?>
            </div>
            <div class="filter-items">
              <h4>Temas</h4>
              <?php
              $stmt = $pdo->query("SELECT id, name FROM tags");
              while ($row = $stmt->fetch()) {
                echo '<div class="filter-option"><input type="checkbox" name="topic[]" value="' . $row['id'] . '" id="subcat' . $row['id'] . '" ' . (in_array($row['id'], $topic) ? 'checked' : '') . '>';
                echo '<label for="subcat' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</label></div>';
              }
              ?>
            </div>
            <button type="submit" class="btn-prim">Filtrar</button>
          </section>

        </form>
      </section>
      <!-- Muestra todos los eventos que han sido publicados-->
      <div class="posts">
        <?php
        if (!empty($events)) {
          foreach ($events as $event) {
            // Verificar si el usuario está inscrito
            $stmt = $pdo->prepare("SELECT 1 FROM inscriptions WHERE user_id = :user_id AND event_id = :event_id");
            $stmt->execute([':user_id' => $_SESSION['user_id'], ':event_id' => $event['id']]);
            $isEnrolled = $stmt->rowCount() > 0;

            echo '<div class="event">';
            echo '<a href="view.php?id=' . htmlspecialchars($event['id']) . '">';
            echo '<img src="../../uploads/test-img-catalog.jpg">';
            echo '<div class="info-event">';
            echo '<h3>' . htmlspecialchars($event['title']) . '</h3>';
            echo '<hr>';
            echo '<p>' . htmlspecialchars($event['date']) . '</p>';
            echo '<p>' . htmlspecialchars($event['location_name']) . '</p>';
            echo '<p>' . htmlspecialchars($event['topic']) . '</p>';
            echo '<p class="category">' . htmlspecialchars($event['category_name'] ?? 'General') . '</p>';

            echo '</div>';
            echo '</a>';
            echo '<form method="POST">';
            echo '<input type="hidden" name="event_id" value="' . $event['id'] . '">';
            if ($isEnrolled) {
              echo '<button type="submit" name="action" value="unsubscribe" class="btn-red">Cancelar inscripción</button>';
            } else {
              echo '<button type="submit" name="action" value="subscribe" class="btn-azul btn-sec">Inscribirse</button>';
            }
            echo '</form>';
            echo '</div>';
          }
        } else {
          echo '<div class="not-found">';
          echo '<h3><span>¡Aviso!</span> No se encontraron eventos disponibles</h3>';
          echo '</div>';
        }
        ?>
      </div>
    </section>
  </main>

  <?php loadComponent('bottom-wrapper') ?>
</body>

</html>
<?php

// Importaciones de archivos
require_once '../../includes/dbconnect.php';
require_once '../../includes/bootstrap.php';

$pdo = db_connect();

// Capturar filtros y búsqueda
$search = $_GET['search'] ?? ''; // Campo de búsqueda
$location = $_GET['location'] ?? '';   // Filtro de lugar
$category = $_GET['category'] ?? ''; // Filtro de categorías principales
$tag = $_GET['tag'] ?? []; // Filtro de tema

try {
  // Consulta base para los eventos
  $query = "SELECT events.*, locations.name AS location, tags.name AS tag, category.name AS category FROM events
    LEFT JOIN locations ON events.location_id = locations.id
    LEFT JOIN tags ON events.tag_id = tags.id
    LEFT JOIN category ON events.category_id = category.id
    WHERE events.post = 1"; // Solo eventos publicados

  // Parámetros para la consulta
  $params = [];

  // Filtrar por búsqueda
  if (!empty($search)) {
    $query .= " AND (LOWER(events.title) LIKE LOWER(:search_title) OR LOWER(events.description) LIKE LOWER(:search_description))";
    $params[':search_title'] = '%' . $search . '%';
    $params[':search_description'] = '%' . $search . '%';
  }

  // Filtrar por lugar (excepto si selecciona "general")
  if (!empty($location) && $location !== 'general') {
    $query .= " AND locations.id = :location";
    $params[':location'] = $location;
  }

  // Filtrar por tema
  if (!empty($tag) && is_array($tag)) {
    $tagParams = [];
    foreach ($tag as $index => $subcat) {
      $tagParams[] = ":tag" . $index;
      $params[":tag" . $index] = $subcat;
    }
    $query .= " AND tags.id IN (" . implode(',', $tagParams) . ")";
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

$locations = $pdo
  ->query("SELECT * FROM locations")
  ->fetchAll();

$categories = $pdo
  ->query("SELECT * FROM category")
  ->fetchAll();

$tags = $pdo
  ->query("SELECT * FROM tags")
  ->fetchAll();

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
      <h1>Explora nuestros eventos</h1>
    </header>
    <section class="content">
      <form id="filters-form" method="GET" action="">
        <div class="filters card card-body">
          <div class="search form-group">
            <input class="input" type="text" name="search" placeholder="Busca tu evento..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" />
          </div>

          <div class="form-group">
            <select class="input" name="location">
              <option value="">Ubicación</option>
              <?php foreach ($locations as $location) { ?>
                <option
                  value="<?php echo $location['id'] ?>"
                  <?php echo isset($_GET['location']) && $_GET['location'] == $location['id'] ? 'selected' : ''; ?>>
                  <?php echo $location['name'] ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <select class="input" name="category">
              <option value="">Categoria</option>
              <?php foreach ($categories as $category) { ?>
                <option
                  value="<?php echo $category['id'] ?>"
                  <?php echo isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'selected' : ''; ?>>
                  <?php echo $category['name'] ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <select class="input" name="tag">
              <option value="">Tipo</option>
              <?php foreach ($tags as $tag) { ?>
                <option
                  value="<?php echo $tag['id'] ?>"
                  <?php echo isset($_GET['tag']) && $_GET['tag'] == $tag['id'] ? 'selected' : ''; ?>>
                  <?php echo $tag['name'] ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="btn-group">
            <button class="btn btn-primary btn-icon" type="submit">
              <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
              </svg>
            </button>

            <?php if (!empty($_GET['search']) || !empty($_GET['location']) || !empty($_GET['tag'])) { ?>
              <button class="btn btn-danger btn-icon" type="button" onclick="resetFilters()">
                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
              </button>
            <?php } ?>
          </div>
        </div>
      </form>

      <!-- Muestra todos los eventos que han sido publicados-->
      <div class="event-list">
        <?php
        if (!empty($events)) {
          foreach ($events as $event) {
            // Verificar si el usuario está inscrito
            $stmt = $pdo->prepare("SELECT 1 FROM inscriptions WHERE user_id = :user_id AND event_id = :event_id");
            $stmt->execute([':user_id' => $_SESSION['user']['id'], ':event_id' => $event['id']]);
            $isEnrolled = $stmt->rowCount() > 0;
        ?>
            <a href="/app/events/view?id=<?php echo htmlspecialchars($event['id']) ?>">
              <div class="card card-body event-list-item">
                <div class="img-container">
                  <img src="/uploads/logo-test.png" />
                </div>
                <div class="event-info">
                  <h4 class="event-title"><?php echo htmlspecialchars($event['title']) ?></h4>
                  <p class="event-description"><?php echo htmlspecialchars(truncateText($event['description'], 50)) ?></p>
                  <div class="tags">
                    <?php if (!empty($event['tag'])) { ?>
                      <span class="tag"><?php echo htmlspecialchars($event['tag']) ?></span>
                    <?php } ?>
                    <?php if (!empty($event['category'])) { ?>
                      <span class="tag"><?php echo htmlspecialchars($event['category']) ?></span>
                    <?php } ?>
                  </div>
                  <div class="event-details">
                    <p class="event-location"> <?php echo htmlspecialchars($event['location']) ?> </p>
                    <p class="event-date"> <?php echo htmlspecialchars((new DateTime($event['date']))->format('F j, Y')) ?> </p>
                  </div>
                </div>
              </div>
            </a>
          <?php
          }
        } else {
          ?>
          <div class="not-found">
            <h3><span>¡Aviso!</span> No se encontraron eventos disponibles</h3>
          </div>
        <?php } ?>
      </div>
    </section>
  </main>

  <?php loadComponent('bottom-wrapper') ?>
  <script>
    function resetFilters() {
      const form = document.getElementById('filters-form');
      form.reset();

      const url = new URL(window.location.href);
      url.search = '';
      window.location.href = url.href;
    }
  </script>
</body>

</html>

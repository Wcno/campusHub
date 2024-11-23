<?php

// Importaciones de archivos
require_once '../includes/dbconnect.php';
require_once '../includes/bootstrap.php';

$pdo = db_connect();

try {
  // Eventos proximos
  $query = "
    SELECT events.*,
      locations.name AS location,
      tags.name AS tag,
      category.name AS category
    FROM events
    LEFT JOIN locations ON events.location_id = locations.id
    LEFT JOIN tags ON events.tag_id = tags.id
    LEFT JOIN category ON events.category_id = category.id
    WHERE events.post = 1
    ORDER BY events.date ASC, events.time ASC
    LIMIT 3";

  $nextEvents = $pdo->query($query)->fetchAll();

  // Eventos proximos mas famosos
  $query = "
    SELECT events.*, 
      locations.name AS location, 
      tags.name AS tag, 
      category.name AS category, 
      COUNT(inscriptions.id) AS inscription_count 
    FROM events
    LEFT JOIN locations ON events.location_id = locations.id
    LEFT JOIN tags ON events.tag_id = tags.id
    LEFT JOIN category ON events.category_id = category.id
    LEFT JOIN inscriptions ON events.id = inscriptions.event_id
    WHERE events.post = 1
    GROUP BY events.id
    ORDER BY inscription_count DESC, events.date ASC, events.time ASC
    LIMIT 3";

  $popularEvents = $pdo->query($query)->fetchAll();
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
  <link href="../css/common.css" rel="stylesheet" />
  <link href="../css/layout.css" rel="stylesheet" />
  <link href="../css/home.css" rel="stylesheet" />
</head>

<body>
  <?php loadComponent('top-wrapper') ?>

  <main class="content-wrapper">
    <header>
      <h1>Explora nuestras opciones</h1>
    </header>
    <section class="content">
      <div class="event-wrapper">
        <h2 class="section-title">Próximos Eventos</h2>
        <div class="event-list">
          <?php foreach ($nextEvents as $event) { ?>
            <a href="../app/events/view?id=<?php echo htmlspecialchars($event['id']) ?>">
              <div class="card card-body event-list-item">
                <div class="img-container">
                  <?php
                  $imagePath = '/uploads/' . $event['image_url'];
                  $urlPath = parse_url(baseUrl($imagePath))['path'];
                  $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . $urlPath;


                  $source = ($user['img_profile'] && file_exists($fullImagePath)) ? $imagePath : '/uploads/test-img-catalog.jpg';
                  ?>
                  <img src="<?php echo baseUrl($source) ?? '' ?>" alt="event image" />
                </div>
                <div class="event-info">
                  <h4 class="event-title"><?php echo htmlspecialchars($event['title']) ?></h4>
                  <div class="event-details">
                    <p class="event-location"> <?php echo htmlspecialchars($event['location']) ?> </p>
                    <p class="event-date"> <?php echo htmlspecialchars((new DateTime($event['date']))->format('F j, Y')) ?> </p>
                  </div>
                </div>
              </div>
            </a>
          <?php
          }
          ?>
        </div>
        <div class="see-more">
          <a href="../app/events/list">Ver más...</a>
        </div>
      </div>

      <div class="event-wrapper">
        <h2 class="section-title">Revisa nuestros eventos más populares</h2>
        <div class="event-list">
          <?php foreach ($nextEvents as $event) { ?>
            <a href="../app/events/view?id=<?php echo htmlspecialchars($event['id']) ?>">
              <div class="card card-body event-list-item">
                <div class="img-container">
                  <?php
                  $imagePath = '/uploads/' . $event['image_url'];
                  $urlPath = parse_url(baseUrl($imagePath))['path'];
                  $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . $urlPath;


                  $source = ($user['img_profile'] && file_exists($fullImagePath)) ? $imagePath : '/uploads/test-img-catalog.jpg';
                  ?>
                  <img src="<?php echo baseUrl($source) ?? '' ?>" alt="event image" />
                </div>
                <div class="event-info">
                  <h4 class="event-title"><?php echo htmlspecialchars($event['title']) ?></h4>
                  <div class="event-details">
                    <p class="event-location"> <?php echo htmlspecialchars($event['location']) ?> </p>
                    <p class="event-date"> <?php echo htmlspecialchars((new DateTime($event['date']))->format('F j, Y')) ?> </p>
                  </div>
                </div>
              </div>
            </a>
          <?php
          }
          ?>
        </div>
        <div class="see-more">
          <a href="../app/events/list">Ver más...</a>
        </div>
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

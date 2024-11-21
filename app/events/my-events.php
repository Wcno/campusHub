<?php
require_once '../../includes/bootstrap.php';
require_once '../../includes/dbconnect.php';

$search = $_GET['search'] ?? null;
$location = $_GET['location'] ?? null;
$tag = $_GET['tag'] ?? null;

$database = db_connect();
$query = "
  SELECT events.*, tags.name AS tag, locations.name AS location FROM events
  INNER JOIN inscriptions ON events.id = inscriptions.event_id
  LEFT JOIN tags ON events.tag_id = tags.id
  LEFT JOIN locations ON events.location_id = locations.id
  WHERE inscriptions.user_id = :user_id
";

$params = [':user_id' => 1];

if (!empty($search)) {
  $query .= " AND (LOWER(events.title) LIKE :search_title OR LOWER(events.description) LIKE :search_description)";
  $params[':search_title'] = '%' . strtolower($search) . '%';
  $params[':search_description'] = '%' . strtolower($search) . '%';
}

if (!empty($location)) {
  $query .= " AND locations.id = :location";
  $params[':location'] = (int) $location;
}

if (!empty($tag)) {
  $query .= " AND tags.id = :tag";
  $params[':tag'] = (int) $tag;
}

$query .= " ORDER BY events.date";
$statement = $database
  ->prepare($query);

$statement->execute($params);
$events = $statement->fetchAll();

$locations = $database
  ->query("SELECT * FROM locations")
  ->fetchAll();

$tags = $database
  ->query("SELECT * FROM tags")
  ->fetchAll();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mis Eventos</title>
  <link href="/css/common.css" rel="stylesheet" />
  <link href="/css/layout.css" rel="stylesheet" />
  <link href="/css/my-events.css" rel="stylesheet" />
</head>

<body>
  <?php loadComponent('top-wrapper') ?>

  <main class="content-wrapper">
    <header>
      <h1>Mis Eventos</h1>
    </header>
    <section>
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
            <select class="input" name="tag">
              <option value="">Categoria</option>
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
      <div class="event-list">
        <?php foreach ($events as $event) { ?>
          <a href="/app/events/view">
            <div class="card card-body event-list-item">
              <div class="img-container">
                <img src="/uploads/logo-test.png" />
              </div>
              <div class="event-info">
                <h4 class="event-title"><?php echo $event['title'] ?></h4>
                <p class="event-description"><?php echo truncateText($event['description'], 50) ?></p>
                <span class="tag"><?php echo $event['tag'] ?></span>
                <div class="event-details">
                  <p class="event-location"> <?php echo $event['location'] ?> </p>
                  <p class="event-date"> <?php echo (new DateTime($event['date']))->format('F j, Y'); ?> </p>
                </div>
              </div>
            </div>
          </a>
        <?php } ?>
      </div>
    </section>
  </main>

  <?php loadComponent('bottom-wrapper') ?>
  <script>
    function resetFilters() {
      console.log("HEY");
      const form = document.getElementById('filters-form');
      form.reset();

      const url = new URL(window.location.href);
      url.search = '';
      window.location.href = url.href;
    }
  </script>
</body>

</html>

<?php

// Importaciones de archivos
require_once '../../includes/dbconnect.php';
require_once '../../includes/bootstrap.php';

try {
  $db = db_connect();

  $eventosOrganizados = $db->query("SELECT COUNT(*) AS total_events FROM events WHERE post = 1")->fetch(PDO::FETCH_ASSOC)['total_events'];
  $totalParticipantes = $db->query("SELECT COUNT(*) AS total_participant FROM inscriptions")->fetch(PDO::FETCH_ASSOC)['total_participant'];
  $totalLugares = $db->query("SELECT COUNT(DISTINCT location_id) AS total_lugares_anfitriones FROM events WHERE post = 1")->fetch(PDO::FETCH_ASSOC)['total_lugares_anfitriones'];

  $tagsData = $db->query("
    SELECT 
        t.name AS tag_name, 
        COUNT(DISTINCT i.user_id) AS total_participant, 
        COUNT(DISTINCT e.id) AS total_events,
        COUNT(DISTINCT e.location_id) AS total_locations
    FROM inscriptions i
    JOIN events e ON i.event_id = e.id
    JOIN tags t ON e.tag_id = t.id
    WHERE e.post = 1
    GROUP BY t.id
    ORDER BY total_participant DESC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

  $usersData = $db->query("
    SELECT 
        u.name AS user_name, 
        u.email AS user_email, 
        COUNT(i.event_id) AS total_events
    FROM inscriptions i
    JOIN events e ON i.event_id = e.id
    JOIN users u ON i.user_id = u.id
    WHERE e.post = 1 AND u.role != 'admin'
    GROUP BY u.id
    ORDER BY total_events DESC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

  $locationsData = $db->query("
    SELECT 
        l.name AS locations_name,
        COUNT(DISTINCT i.user_id) AS total_participant_present,
        COUNT(DISTINCT e.id) AS total_events,
        MAX(e.date) AS ultimo_evento
    FROM locations l
    JOIN events e ON l.id = e.location_id
    JOIN inscriptions i ON e.id = i.event_id
    WHERE e.post = 1
    GROUP BY l.id
    ORDER BY total_participant_present DESC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);


  //porcentaje de eventos publicados
  $percentagePublishedEvents = $db->query("
    SELECT 
        (SUM(CASE WHEN post = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS porcentaje_publicados
    FROM events
")->fetch(PDO::FETCH_ASSOC)['porcentaje_publicados'];

  //porcentaje de lugares con capacidad máxima
  $percentajeMaxCapacity = $db->query("
    SELECT 
        (COUNT(DISTINCT l.id) / (SELECT COUNT(*) FROM locations)) * 100 AS porcentaje_capacidad_maxima
    FROM events e
    JOIN locations l ON e.location_id = l.id
    WHERE e.capacity = (
        SELECT capacity 
        FROM locations 
        WHERE id = e.location_id
    )
")->fetch(PDO::FETCH_ASSOC)['porcentaje_capacidad_maxima'];

  $proximosEventos = $db->query("
    SELECT 
        id,
        title, 
        date 
    FROM events
    WHERE post = 1 AND date >= CURDATE()
    ORDER BY date ASC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}


?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel</title>
  <link href="../../css/common.css" rel="stylesheet" />
  <link href="../../css/layout.css" rel="stylesheet" />
  <link href="../../css/dashboard.css" rel="stylesheet">
</head>

<body>
  <?php loadComponent('top-wrapper') ?>

  <main class="content-wrapper">
    <header>
      <h1>Panel de control</h1>
    </header>

    <section class="content">

      <div class="main-data">
        <div class="banner-container">
          <div class="banner planned-events">
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-check">
              <path d="M8 2v4" />
              <path d="M16 2v4" />
              <rect width="18" height="18" x="3" y="4" rx="2" />
              <path d="M3 10h18" />
              <path d="m9 16 2 2 4-4" />
            </svg>
            <div class="info">
              <p class="quantity"><?= $eventosOrganizados; ?></p>
              <p class="about-data">Eventos Publicados</p>
            </div>
          </div>
          <a href="events/list" class="more-info more-info-plan-ev">
            <p>Ver más</p>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right">
              <path d="m9 18 6-6-6-6" />
            </svg>

          </a>
        </div>

        <div class="banner-container">
          <div class="banner total-participants">
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            <div class="info">
              <p class="quantity"><?= $totalParticipantes; ?></p>
              <p class="about-data">Participantes Totales</p>
            </div>
          </div>
          <a href="events/list" class="more-info more-info-tot-part">
            <p>Ver más</p>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right">
              <path d="m9 18 6-6-6-6" />
            </svg>
          </a>
        </div>

        <div class="banner-container">
          <div class="banner total-locations">
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin">
              <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
              <circle cx="12" cy="10" r="3" />
            </svg>
            <div class="info">
              <p class="quantity"><?= $totalLugares; ?></p>
              <p class="about-data">Lugares Anfitriones</p>
            </div>
          </div>
          <a href="events/list" class="more-info more-info-tot-loc">
            <p>Ver más</p>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right">
              <path d="m9 18 6-6-6-6" />
            </svg>

          </a>
        </div>

      </div>

      <div class="info-events">
        <div class="info-event-item-container">
          <div class="info-event-item">
            <h3>Eventos con mayor concurrencia</h3>
            <table>
              <thead>
                <tr>
                  <th>Tema</th>
                  <th>Participantes</th>
                  <th>Eventos Publicados</th>
                  <th>Lugares Anfitriones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($tagsData as $tag) : ?>
                  <tr>
                    <td><?= $tag['tag_name']; ?></td>
                    <td><?= $tag['total_participant']; ?></td>
                    <td><?= $tag['total_events']; ?></td>
                    <td><?= $tag['total_locations']; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

          </div>
          <div class="info-event-item">
            <h3>Usuarios con más participaciones</h3>
            <table>
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th>Eventos inscritos</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($usersData as $user) : ?>
                  <tr>
                    <td><?= $user['user_name']; ?></td>
                    <td><?= $user['user_email']; ?></td>
                    <td><?= $user['total_events']; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

          </div>

          <div class="info-event-item">
            <h3>Lugares más concurrentes</h3>
            <table>
              <thead>
                <tr>
                  <th>Lugar</th>
                  <th>Personas Inscritas</th>
                  <th>Eventos Públicados</th>
                  <th>Último Evento Publicado</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($locationsData as $location) : ?>
                  <tr>
                    <td><?= $location['locations_name']; ?></td>
                    <td><?= $location['total_participant_present']; ?></td>
                    <td><?= $location['total_events']; ?></td>
                    <td><?= date("m/d/Y", strtotime($location['ultimo_evento'])) ?></td>

                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

          </div>
        </div>

        <div class="info-event-item-container upcoming-event-container">

          <div class="indicators">
            <h3>Indicadores clave</h3>
            <div class="percentege-container">
              <div class="info-event-item percentage">
                <p class="percentage-number"><?= floor($percentagePublishedEvents) ?>%</p>
                <p>Eventos Publicados</p>
              </div>

              <div class="info-event-item percentage">
                <p class="percentage-number"><?= floor($percentajeMaxCapacity) ?>%</p>
                <p>Lugares con capacidad máxima</p>
              </div>
            </div>
          </div>


          <div class="info-event-item">
            <h3>Próximos Eventos</h3>
            <div class="upcoming-events">

              <?php foreach ($proximosEventos as $evento) { ?>
                <div class="upcoming-event">
                  <p class="upcoming-event-item">
                    <span class="upcoming-event-title"><?php echo htmlspecialchars($evento['title']) ?></span>
                    <span class="date-upcoming-event"><?php echo date("m/d/Y", strtotime($evento['date'])) ?></span>
                    <a class="upcoming-event-link" href="/app/admin/events/view?id=<?php echo htmlspecialchars($evento['id']) ?>">
                      <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                      </svg>
                    </a>
                  </p>
                </div>
              <?php } ?>

            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php loadComponent('bottom-wrapper') ?>
</body>

</html>

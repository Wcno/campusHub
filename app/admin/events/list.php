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
  <title>Lista de Eventos</title>
  <link href="../../../css/common.css" rel="stylesheet" />
  <link href="../../../css/layout.css" rel="stylesheet" />
  <link href="../../../css/event-list.css" rel="stylesheet" />
</head>

<body>

  <?php loadComponent('top-wrapper') ?>
  <main class="content-wrapper container">
    <header>
      <h1>Lista de Eventos</h1>
    </header>
    <section class="content">
      <table>
        <thead>
          <tr>
            <th>Título</th>
            <th>Fecha</th>
            <th>Ubicación</th>
            <th>Publicado</th>
            <th>Acciones</th>
          </tr>
          <tr>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $stmt = $pdo->query("
                        SELECT events.*, locations.name AS location_name, 
                               (SELECT COUNT(*) FROM inscriptions WHERE inscriptions.event_id = events.id) AS num_inscriptions
                        FROM events 
                        LEFT JOIN locations ON events.location_id = locations.id
                    ");

          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>";
            echo "<strong>" . htmlspecialchars($row['title']) . "</strong><br>";
            echo "<small>" . htmlspecialchars($row['num_inscriptions']) . " inscrito(s)</small>";
            echo "</td>";
            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['location_name']) . "</td>";
            echo "<td>";
            if ($row['post']) {
              echo '<svg width="20" height="20" style="color: var(--primary)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"> <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /> </svg>';
            } else {
              echo '<svg width="20" height="20" style="color: var(--danger)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"> <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /> </svg>';
            }
            echo "</td>";
            echo "<td>
              <div class='actions'>
                <a class='view-action' href='view?id={$row['id']}' title='Ver'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-eye'><path d='M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0'/><circle cx='12' cy='12' r='3'/></svg>
                </a>
                <a class='edit-action' href='edit?id={$row['id']}' title='Editar'>
                  <svg width='18' height='18' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='size-6'> <path stroke-linecap='round' stroke-linejoin='round' d='m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10' /> </svg>
                </a>
                <a class='delete-action' href='delete?id={$row['id']}' title='Eliminar' onclick=\"return confirm('¿Estás seguro de que deseas eliminar este evento?')\">
                    <svg width='18' height='18' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='size-6'> <path stroke-linecap='round' stroke-linejoin='round' d='m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' /> </svg>
                </a>
              </div>
            </td>";
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </section>
  </main>

  <?php loadComponent('bottom-wrapper') ?>
</body>

</html>

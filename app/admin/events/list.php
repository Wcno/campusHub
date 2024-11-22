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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php loadComponent('top-wrapper') ?>

    <main class="container">
        <header>
            <h1>Lista de Eventos</h1>
        </header>
        <section>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Ubicación</th>
                        <th>Publicado</th>
                        <th>Acciones</th>
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
                            echo "<i class='fas fa-check' style='color: var(--light-primary); font-size: 1.1rem;'></i>";
                        } else {
                            echo "<i class='fas fa-times' style='color: #dc3545; font-size: 1.1rem;'></i>";
                        }
                        echo "</td>";
                        echo "<td>
                                <a href='edit.php?id={$row['id']}' class='btn btn-primary btn-sm' title='Editar' style='font-size: 1.1rem; color: var(--light-primary); text-decoration: none;'>
                                    <i class='fas fa-edit' style='color: var(--light-primary); font-size: 1.1rem;'></i>
                                </a>
                                <a href='delete.php?id={$row['id']}' class='btn btn-danger btn-sm' title='Eliminar' style='font-size: 1.1rem; color: #dc3545; text-decoration: none;' onclick=\"return confirm('¿Estás seguro de que deseas eliminar este evento?')\">
                                    <i class='fas fa-trash-alt' style='color: #dc3545; font-size: 1.1rem;'></i>
                                </a>
                                <a href='publish.php?id={$row['id']}' class='btn btn-success btn-sm' title='" . ($row['post'] ? 'Publicado' : 'Publicar') . "' style='font-size: 1.1rem; color: " . ($row['post'] ? "#dc3545" : "var(--light-primary)") . ";'>
                                    <i class='" . ($row['post'] ? "fas fa-circle-xmark" : "fas fa-bullhorn") . "' style='color: " . ($row['post'] ? "#dc3545" : "var(--light-primary)") . "; font-size: 1.1rem;'></i>
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php loadComponent('bottom-wrapper') ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

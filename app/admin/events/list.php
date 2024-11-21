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
    
</head>

<body>
    <?php loadComponent('top-wrapper') ?>

    <main class="container my-5">
        <div class="card p-4 shadow-sm">
            <header class="mb-4">
                <h1 class="text-center">Lista de Eventos</h1>
            </header>
            <section>
                <table class="table table-striped table-bordered">
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
                        // Obtener los eventos de la base de datos
                        $stmt = $pdo->query("SELECT * FROM events");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['location_id']) . "</td>";
                            echo "<td>" . ($row['post'] ? 'Sí' : 'No') . "</td>";
                            echo "<td>
                                    <a href='edit.php?id={$row['id']}' class='btn btn-primary btn-sm'>Editar</a>
                                    <a href='delete.php?id={$row['id']}' class='btn btn-danger btn-sm'>Eliminar</a>
                                    <a href='publish.php?id={$row['id']}' class='btn btn-success btn-sm'>" . 
                                    ($row['post'] ? 'Publicado' : 'Publicar') . 
                                    "</a>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <?php loadComponent('bottom-wrapper') ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>

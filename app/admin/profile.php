<?php


// Importaciones de archivos
require_once '../../includes/dbconnect.php';
require_once '../../includes/bootstrap.php';

$pdo = db_connect();

// Obtener datos del usuario autenticado
$id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT name, phone_number, email, birth_date, img_profile FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
  $_SESSION['user'] = array_merge($_SESSION['user'], $row);
} else {
  echo "Usuario no encontrado.";
  exit;
}

// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'editProfile') {
    header('Location: editProfile');
    exit;
  } elseif ($action === 'changePassword') {
    header('Location: changePassword');
    exit;
  } else {
    echo "Acción no reconocida.";
  }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="../../css/common.css" rel="stylesheet" />
  <link href="../../css/layout.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../css/perfil.css">
  <title>Configuración de Perfil</title>
</head>

<body>
  <?php loadComponent('top-wrapper'); ?>

  <main class="content-wrapper">
    <div class="main-container">
      <div class="panel">
        <div class="main-content">

          <!-- Título Principal -->
          <header class="profile-header">
            <h1>Configuración de Perfil</h1>
            <p>Gestiona tu información personal y actualiza tu configuración</p>
          </header>

          <!-- Contenedor principal de la información -->
          <div class="profile-layout card card-body">
            <!-- Sección de Foto de Perfil -->
            <div class="profile-picture-section">
              <h2>Foto de Perfil</h2>
              <?php
              $imagePath = '/uploads/' . ($user['img_profile'] ?? '');
              $urlPath = parse_url(baseUrl($imagePath))['path'];
              $fullImagePath = $_SERVER['DOCUMENT_ROOT'] . $urlPath;

              $source = !empty($user['img_profile']) && file_exists($fullImagePath)
                ? baseUrl($imagePath)
                :  'https://cdn-icons-png.flaticon.com/512/6676/6676023.png';
              ?>
              <img class="profile-photo" src="<?php echo htmlspecialchars($source) ?>" alt="Imagen de usuario">
            </div>

            <!-- Formulario de Datos -->
            <div class="form-container">
              <h2>Detalles del Usuario</h2>

              <form class="form" action="" method="POST">
                <div class="form-group">
                  <label class="label" for="fname">Nombre Completo</label>
                  <input class="input" type="text" name="fname" id="fname" value="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>" readonly>
                </div>

                <div class="form-group">
                  <label class="label" for="email">Correo Electrónico</label>
                  <input class="input" type="email" name="email" id="email" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>" readonly>
                </div>

                <div class="form-group">
                  <label class="label" for="phone">Número de Teléfono</label>
                  <input class="input" type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($_SESSION['user']['phone_number']); ?>" readonly>
                </div>

                <div class="form-group">
                  <label class="label" for="birth_date">Fecha de Nacimiento</label>
                  <input class="input" type="date" name="birth_date" id="birth_date" value="<?php echo htmlspecialchars($_SESSION['user']['birth_date']); ?>" readonly>
                </div>

                <div class="button-group">
                  <button class="save" type="submit" name="action" value="editProfile">Editar Perfil</button>
                  <button class="save" type="submit" name="action" value="changePassword">Cambiar Contraseña</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <?php loadComponent('bottom-wrapper'); ?>
</body>

</html>

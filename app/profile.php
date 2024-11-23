<?php


// Importaciones de archivos
require_once '../includes/dbconnect.php';
require_once '../includes/bootstrap.php';

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
        header('Location: editProfile.php');
        exit;
    } elseif ($action === 'changePassword') {
        header('Location: changePassword.php');
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
  <link href="../css/common.css" rel="stylesheet" />
  <link href="../css/layout.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/perfil.css">
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
          <div class="profile-layout">
            <!-- Sección de Foto de Perfil -->
            <div class="profile-picture-section">
              <h2>Foto de Perfil</h2>
              <img class="profile-photo" src="<?php echo htmlspecialchars($_SESSION['user']['img_profile'] ?? 'https://cdn-icons-png.flaticon.com/512/6676/6676023.png'); ?>" alt="Imagen de usuario">
            </div>

            <!-- Formulario de Datos -->
            <div class="form-container">
              <h2>Detalles del Usuario</h2>
              
              <form class="form" action="" method="POST">
  <div class="form-group">
    <label class="form-label" for="fname">Nombre Completo</label>
    <input class="form-input" type="text" name="fname" id="fname" value="dds">
  </div>

  <div class="form-group">
    <label class="form-label" for="email">Correo Electrónico</label>
    <input class="form-input" type="email" name="email" id="email" value="prueba@prueba.com">
  </div>

  <div class="form-group">
    <label class="form-label" for="phone">Número de Teléfono</label>
    <input class="form-input" type="tel" name="phone" id="phone" value="4355324">
  </div>

  <div class="form-group">
    <label class="form-label" for="birth_date">Fecha de Nacimiento</label>
    <input class="form-input" type="date" name="birth_date" id="birth_date" value="">
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


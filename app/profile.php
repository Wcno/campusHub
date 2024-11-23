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
  <title>Configurar Perfil</title>
</head>

<body>
  <?php loadComponent('top-wrapper'); ?>

  <main class="content-wrapper">
    <div class="main-container">
      <div class="panel">
        <div class="main-content">
          <div class="profile-info">
            <img class="profile-photo" src="<?php echo $_SESSION["user"]["img_profile"] ?>" alt="imagen de usuario">
          </div>
          <div class="form-container">
            <form class="form" action="" method="POST">
              <label class="form-label" for="fname">Nombre Completo</label>
              <input class="form-input" type="text" name="fname" id="fname" value="<?php echo htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES); ?>" readonly><br>

              <label class="form-label" for="phone">Número de Teléfono</label>
              <input class="form-input" type="tel" name="phone" id="phone" pattern="[0-9]{8,10}" value="<?php echo htmlspecialchars($_SESSION['user']['phone_number'], ENT_QUOTES); ?>" readonly><br>

              <label class="form-label" for="email">Correo Electrónico</label><br>
              <input class="form-input" type="email" name="email" id="email" value="<?php echo htmlspecialchars($_SESSION['user']['email'], ENT_QUOTES); ?>" readonly><br>

              <label class="form-label" for="fecha-nacimiento">Fecha de Nacimiento</label><br>
              <input class="form-input" type="date" name="fecha-nacimiento" id="fecha-nacimiento" value="<?php echo htmlspecialchars($_SESSION['user']['birth_date'], ENT_QUOTES); ?>" readonly><br>

              <div class="but">
                <button type="submit" name="action" value="editProfile" class="save">Editar perfil</button>
                <button type="submit" name="action" value="changePassword" class="save">Cambiar contraseña</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
  <?php loadComponent('bottom-wrapper'); ?>
</body>

</html>

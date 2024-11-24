<?php

require_once '../../includes/dbconnect.php';
require_once '../../includes/bootstrap.php';

// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $oldPassword = $_POST['op'];
  $newPassword = $_POST['np'];
  $confirmPassword = $_POST['c_np'];

  // Verifica si el usuario está autenticado
  if (!isset($_SESSION['user'])) {
    echo "Usuario no autenticado.";
    exit;
  }

  $userId = $_SESSION['user']['id']; // ID del usuario autenticado

  // Conexión a la base de datos
  $pdo = db_connect();

  // Obtén la contraseña actual del usuario desde la base de datos
  $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
  $stmt->execute([':id' => $userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    $message = "Error: Usuario no encontrado.";
  } elseif (!password_verify($oldPassword, $user['password'])) {
    // Verifica si la contraseña antigua es correcta
    $message = "Error: La contraseña actual no es correcta.";
  } elseif ($newPassword !== $confirmPassword) {
    // Verifica si las nuevas contraseñas coinciden
    $message = "Error: La nueva contraseña y la confirmación no coinciden.";
  } else {
    // Hashea la nueva contraseña
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Actualiza la contraseña en la base de datos
    $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
    $updated = $updateStmt->execute([
      ':password' => $hashedPassword,
      ':id' => $userId
    ]);

    if ($updated) {
      $message = "Contraseña actualizada correctamente.";
      $success = true;
    } else {
      $message = "Error: No se puedo actualizar la contraseña.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cambiar Contraseña</title>
  <link href="../../css/common.css" rel="stylesheet">
  <link href="../../css/layout.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/perfil.css">
</head>

<body>
  <?php loadComponent('top-wrapper'); ?>

  <main class="content-wrapper">
    <!-- Formulario de cambio de contraseña -->
    <div class="main-container">
      <div class="panel">
        <div class="main-content">
          <header class="profile-header">
            <h1>Cambiar Contraseña</h1>
            <p>Introduce tu contraseña actual y define una nueva</p>
          </header>

          <?php if (isset($message)) : ?>
            <div class="<?= strpos($message, 'Error') !== false ? 'error-message' : 'success-message' ?>">
              <?php if (strpos($message, 'Error') !== false) { ?>
                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
              <?php } else { ?>
                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
              <?php } ?>
              <?= $message ?>
            </div>
          <?php endif; ?>

          <div class="form-container card card-body">
            <form class="form" method="POST">
              <div class="form-group">
                <label class="label" for="op">Contraseña Actual</label>
                <input class="input" type="password" name="op" id="op" placeholder="Contraseña Actual" required>
              </div>
              <div class="form-group">
                <label class="label" for="np">Nueva Contraseña</label>
                <input class="input" type="password" name="np" id="np" placeholder="Nueva Contraseña" required>
              </div>
              <div class="form-group">
                <label class="label" for="c_np">Confirmar Nueva Contraseña</label>
                <input class="input" type="password" name="c_np" id="c_np" placeholder="Repetir Contraseña" required>
              </div>

              <!-- Botones de acción -->
              <div class="button-group">
                <button class="btn btn-secondary" name="cancel" class="exit" onclick="window.location.href = 'profile'">Cancelar</button>
                <button class="btn btn-primary" type="submit" name="save">Guardar Cambios</button>
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

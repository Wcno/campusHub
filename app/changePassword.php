<?php

require_once '../includes/dbconnect.php';
require_once '../includes/bootstrap.php';

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
    $message = "Usuario no encontrado.";
  } elseif (!password_verify($oldPassword, $user['password'])) {
    // Verifica si la contraseña antigua es correcta
    $message = "La contraseña actual no es correcta.";
  } elseif ($newPassword !== $confirmPassword) {
    // Verifica si las nuevas contraseñas coinciden
    $message = "La nueva contraseña y la confirmación no coinciden.";
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
      $message = "Error al actualizar la contraseña.";
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
  <link href="../css/common.css" rel="stylesheet">
  <link href="../css/layout.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/perfil.css">
</head>

<body>
  <?php loadComponent('top-wrapper'); ?>

  <main class="content-wrapper">
    <?php if (isset($message)): ?>
      <!-- Mensaje de éxito o error -->
      <div class="message <?php echo isset($success) && $success ? 'success' : 'error'; ?>">
        <p><?php echo htmlspecialchars($message); ?></p>
        <a href="<?php echo isset($success) && $success ? 'profile.php' : 'changePassword.php'; ?>">
          <?php echo isset($success) && $success ? 'Aceptar' : 'Intentar de nuevo'; ?>
        </a>
      </div>
    <?php else: ?>
      <!-- Formulario de cambio de contraseña -->
      <div class="main-container">
        <div class="panel">
          <div class="main-content">
            <header class="profile-header">
              <h1>Cambiar Contraseña</h1>
              <p>Introduce tu contraseña actual y define una nueva</p>
            </header>

            <div class="form-container">
              <form class="form" action="changePassword.php" method="POST">
                <div class="form-group">
                  <label class="form-label" for="op">Contraseña Actual</label>
                  <input class="form-input" type="password" name="op" id="op" placeholder="Contraseña Actual" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="np">Nueva Contraseña</label>
                  <input class="form-input" type="password" name="np" id="np" placeholder="Nueva Contraseña" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="c_np">Confirmar Nueva Contraseña</label>
                  <input class="form-input" type="password" name="c_np" id="c_np" placeholder="Repetir Contraseña" required>
                </div>

                <!-- Botones de acción -->
                <div class="button-group">
                  <button class="cancel" type="submit" name="cancel">Cancelar</button>
                  <button class="save" type="submit" name="save">Guardar Cambios</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </main>
</body>

</html>

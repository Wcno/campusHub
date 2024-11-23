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
<html>

<head>
  <title>Cambiar Contraseña</title>
  <link rel="stylesheet" type="text/css" href="../css/changePass.css">
  <link href="../css/common.css" rel="stylesheet" />
  <link href="../css/layout.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/perfil.css">
</head>

<body>
  <?php loadComponent('top-wrapper'); ?>
  <main class="content-wrapper">
    <?php if (isset($message)): ?>
      <div class="message <?php echo isset($success) && $success ? 'success' : 'error'; ?>">
        <p><?php echo htmlspecialchars($message); ?></p>
        <?php if (isset($success) && $success): ?>
          <a href="profile.php">Aceptar</a>
        <?php else: ?>
          <a href="changePassword.php">Intentar de nuevo</a>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <form action="changePassword.php" method="post">
        <div class="form-group">
        <h2>Cambiar Contraseña</h2>
        <div class="form-group">
        <label class="form-label">Contraseña Actual</label>
        <input class="form-input" type="password" name="op" placeholder="Contraseña Actual" required>
       
        </div>
        <div class="form-group">
        <label class="form-label">Nueva Contraseña</label>
        <input class="form-input" type="password" name="np" placeholder="Contraseña Actual" required>
       
        </div>
        <div class="form-group">
        <label class="form-label">Confirmar Nueva Contraseña</label>
        <input class="form-input" type="password" name="c_np" placeholder="Contraseña Actual" required>
       

        
        </div>
        <div class="button-group">
              <button class="cancel" type="submit" name="cancel" class="exit">Cancelar</button>
                <button class="save" type="submit" name="save" class="save">Guardar Cambios</button>
               
              </div>
      </form>
    <?php endif; ?>
  </main>

</body>

</html>

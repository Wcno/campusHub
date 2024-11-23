<?php
require_once '../includes/dbconnect.php';
require_once '../php/helpers.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $pdo = db_connect();
  $errorMessage = '';

  // Obtener datos del formulario
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Buscar al usuario por correo electrónico
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND role = 'user'");
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch();

  // Verificar la contraseña
  if ($user && password_verify($password, $user['password'])) {
    session_start(); // Inicia sesión
    $_SESSION['user'] = $user;

    // Redirigir a la página principal de usuario
    header("Location: " . baseUrl("/app/home"));
    exit();
  } else {
    $errorMessage = "Correo o contraseña incorrectos.";
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - CampusHub</title>
  <link rel="stylesheet" href="../css/common.css">
  <link rel="stylesheet" href="../css/login.css">
</head>

<body>

  <div class="container">
    <!-- Sección de Información -->
    <div class="event-info">
      <h1>Bienvenido a CampusHub</h1>
      <p>Inicia sesión para acceder a nuestra plataforma.</p>

      <div class="host-info">
        <img src="<?php echo baseUrl('/uploads/logo-test.png') ?>" alt="CampusHub Logo" class="logo">
        <p>Gestionado por:</p>
        <h3>CampusHub</h3>
        <p>Optimización en gestión de eventos</p>
      </div>
    </div>

    <!-- Sección del Formulario -->
    <div class="login-form">
      <h2>Iniciar sesión</h2>

      <!-- Mostrar mensaje de error -->
      <?php if (isset($errorMessage) && $errorMessage): ?>
        <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
      <?php endif; ?>

      <form action="login" method="post">
        <div class="form-group">
          <label class="label">Correo</label>
          <input class="input" type="email" name="email" placeholder="Dirección de correo electrónico" required>
        </div>
        <div class="form-group">
          <label class="label">Contraseña</label>
          <input class="input" type="password" name="password" placeholder="Contraseña" required>
        </div>
        <input class="btn btn-primary" type="submit" value="Iniciar sesión">
      </form>

      <!-- Enlace al registro -->
      <p class="register-link">¿No tienes una cuenta? <a href="register">Regístrate</a></p>
    </div>
  </div>

</body>

</html>

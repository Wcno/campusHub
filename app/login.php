<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - CampusHub</title>
  <link rel="stylesheet" href="../css/login.css">
</head>

<body>

  <div class="container">
    <!-- Sección de Información -->
    <div class="event-info">
      <h1>Bienvenido a CampusHub</h1>
      <p>Inicia sesión para acceder a nuestra plataforma.</p>

      <div class="host-info">
        <img src="../uploads/logo-test.png" alt="CampusHub Logo" class="logo">
        <p>Gestionado por:</p>
        <h3>CampusHub</h3>
        <p>Optimización en gestión de eventos</p>
      </div>
    </div>

    <!-- Sección del Formulario -->
    <div class="login-form">
      <h2>Iniciar sesión</h2>

      <?php
      require_once '../includes/dbconnect.php';

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $pdo = db_connect();
        $errorMessage = '';

        // Obtener datos del formulario
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Buscar al usuario por correo electrónico
        $stmt = $pdo->prepare("SELECT id, name, password, email, role FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Verificar la contraseña
        if ($user && password_verify($password, $user['password'])) {
          session_start(); // Inicia sesión
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['name'] = $user['name'];
          $_SESSION['email'] = $user['email'];

          // Redirigir a la página principal de usuario
          header("Location: ../php/user_index.php");
          exit();
        } else {
          $errorMessage = "Correo o contraseña incorrectos.";
        }
      }
      ?>

      <!-- Mostrar mensaje de error -->
      <?php if (isset($errorMessage) && $errorMessage): ?>
        <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
      <?php endif; ?>

      <form action="login.php" method="post">
        <input type="email" name="email" placeholder="Dirección de correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="submit" value="Iniciar sesión">
      </form>

      <!-- Enlace al registro -->
      <p class="register-link">¿No tienes una cuenta? <a href="register.php">Regístrate</a></p>
    </div>
  </div>

</body>

</html>

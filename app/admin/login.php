<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Administradores</title>
  <link rel="stylesheet" href="../../css/login.css"> <!-- Ajustar ruta según tu estructura -->
</head>

<body>

  <div class="container">
    <!-- Sección de Información -->
    <div class="event-info">
      <h1>Bienvenido a CampusHub</h1>
      <p>Inicia sesión como administrador para gestionar la plataforma.</p>

      <div class="host-info">
        <img src="../../uploads/logo-test.png" alt="CampusHub Logo" class="logo"> <!-- Ajustar ruta según tu estructura -->
        <p>Gestionado por:</p>
        <h3>CampusHub</h3>
        <p>Optimización en gestión de eventos</p>
      </div>
    </div>

    <!-- Sección del Formulario -->
    <div class="login-form">
      <h2>Iniciar sesión como Administrador</h2>

      <?php
      require_once '../../includes/dbconnect.php';

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $pdo = db_connect();
        $errorMessage = '';

        // Obtener datos del formulario
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Buscar al usuario por correo electrónico y rol de administrador
        $stmt = $pdo->prepare("SELECT id, name, password, email, role FROM users WHERE email = :email AND role = 'admin'");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Verificar la contraseña y el rol
        if ($user && password_verify($password, $user['password'])) {
          session_start(); // Inicia sesión
          $_SESSION['admin_id'] = $user['id'];
          $_SESSION['name'] = $user['name'];
          $_SESSION['email'] = $user['email'];

          // Redirigir al panel de administrador
          header("Location: dashboard.php");
          exit();
        } else {
          $errorMessage = "Credenciales incorrectas o no tienes permisos de administrador.";
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

      <!-- Enlace al registro de administradores -->
      <p class="register-link">¿No tienes una cuenta de administrador? <a href="register.php">Regístrate</a></p>
    </div>
  </div>

</body>

</html>

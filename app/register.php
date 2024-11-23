<?php
require_once '../includes/dbconnect.php';
require_once '../php/helpers.php';

$errors = []; // Arreglo para almacenar errores

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $pdo = db_connect();

  // Recibir y validar los datos del formulario
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $phone_number = trim($_POST['phone_number']);
  $birth_date = $_POST['birth_date'];

  // Validación de la contraseña
  if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*[a-z]).{8,}$/', $password)) {
    $errors['password'] = "La contraseña debe tener al menos 8 caracteres, incluir una mayúscula y un carácter especial.";
  }

  // Validar que ambas contraseñas coincidan
  if ($password !== $confirm_password) {
    $errors['confirm_password'] = "Las contraseñas no coinciden.";
  }

  // Verificar si el email ya existe
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
  $stmt->execute(['email' => $email]);
  if ($stmt->rowCount() > 0) {
    $errors['email'] = "El correo electrónico ya está en uso.";
  }

  // Si no hay errores, insertar datos en la base de datos
  if (empty($errors)) {
    try {
      $password_hashed = password_hash($password, PASSWORD_BCRYPT); // Encriptar contraseña

      // Insertar el usuario en la base de datos
      $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone_number, birth_date) 
                                   VALUES (:name, :email, :password, :phone_number, :birth_date)");
      $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password' => $password_hashed,
        'phone_number' => $phone_number,
        'birth_date' => $birth_date
      ]);

      // Recuperar el usuario recién registrado
      $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
      $stmt->execute(['email' => $email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      // Validar si se recuperaron los datos del usuario
      if ($user && isset($user['id'], $user['name'], $user['email'])) {
        // Iniciar sesión
        session_start();
        $_SESSION['user'] = $user;

        // Redirigir al home
        header("Location: " . baseUrl("/app/events/home"));
        exit();
      } else {
        $errors['general'] = "Error al iniciar sesión automáticamente.";
      }
    } catch (PDOException $e) {
      $errors['general'] = "Error en la base de datos: " . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario - CampusHub</title>
  <link rel="stylesheet" href="../css/common.css" />
  <link rel="stylesheet" href="../css/register.css">
</head>

<body>

  <div class="container">
    <!-- Sección de Información del Evento o Aplicación -->
    <div class="event-info">
      <h1>Bienvenido a CampusHub</h1>
      <p>Regístrate para acceder a nuestra plataforma.</p>
      <div class="host-info">
        <img src="<?php echo baseUrl('/uploads/logo-test.png') ?>" alt="CampusHub Logo" class="logo">
        <p>Gestionado por:</p>
        <h3>CampusHub</h3>
        <p>Optimización en gestión de eventos</p>
      </div>
    </div>

    <!-- Sección de Registro -->
    <div class="register-form">
      <h2>Regístrate</h2>
      <p>Ingrese su información para crear una cuenta.</p>

      <?php if (isset($errors['general'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['general']) ?></p>
      <?php endif; ?>

      <form action="register" method="post">
        <div class="form-group">
          <label class="label">Nombre</label>
          <input class="input" type="text" name="name" placeholder="Nombre Completo" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
          <?php if (isset($errors['name'])): ?>
            <p class="error-message"><?= htmlspecialchars($errors['name']) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="label">Correo</label>
          <input class="input" type="email" name="email" placeholder="Correo Electrónico" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
          <?php if (isset($errors['email'])): ?>
            <p class="error-message"><?= htmlspecialchars($errors['email']) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="label">Teléfono</label>
          <input class="input" type="tel" name="phone_number" placeholder="Número de Teléfono" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label class="label">Fecha de Nacimiento</label>
          <input class="input" type="date" name="birth_date" id="birth_date" value="<?= htmlspecialchars($_POST['birth_date'] ?? '') ?>" required>
        </div>

        <div class="form-group password-container">
          <label class="label">Contraseña</label>
          <input class="input" type="password" name="password" id="password" placeholder="Contraseña" required>
          <!-- <div class="password-info"> -->
          <!--   La contraseña debe tener: -->
          <!--   <ul> -->
          <!--     <li>Al menos 8 caracteres.</li> -->
          <!--     <li>Una letra mayúscula.</li> -->
          <!--     <li>Un carácter especial (!@#$%^&*).</li> -->
          <!--   </ul> -->
          <!-- </div> -->
          <?php if (isset($errors['password'])): ?>
            <p class="error-message"><?= htmlspecialchars($errors['password']) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="label">Confirmar Contraseña</label>
          <input class="input" type="password" name="confirm_password" id="confirm_password" placeholder="Confirmar Contraseña" required>
          <?php if (isset($errors['confirm_password'])): ?>
            <p class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></p>
          <?php endif; ?>
        </div>

        <input class="btn btn-primary submit" type="submit" value="Registrarse">
      </form>
      <br>
      <p class="login-link">¿Ya tienes una cuenta? <a href="login">Inicia sesión</a></p>
    </div>
  </div>

</body>

</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - PanaEvents</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>

<div class="container">
 
    <div class="login-form">
        <img src="../uploads/logo.png" alt="PanaEvents Logo" class="logo"> 
        <h2>Iniciar sesión</h2>

        <?php
        session_start();
        require_once '../includes/dbconnect.php';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $pdo = db_connect();
            $errorMessage = '';

            $email = $_POST['email'];
            $password = $_POST['password'];


            $stmt = $pdo->prepare("SELECT id, username, password, email, role FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
        
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role']; 
        
                // Redirigir según el rol
                if ($user['role'] === 'admin') {
                    header("Location: admin_index.php");
                } else {
                    header("Location: ../php/user_index.php");
                }
                exit();
            } else {
                $errorMessage = "Correo o contraseña incorrectos.";
            }
        }
        ?>

        <?php if (isset($errorMessage) && $errorMessage): ?>
            <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <input type="email" name="email" placeholder="Dirección de correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="submit" value="Iniciar sesión">
        </form>

        <!-- Enlace al formulario de registro -->
        <p class="register-link">¿No tienes una cuenta? <a href="register.php">Regístrate</a></p>
    </div>

    <!-- Sección de Imagen -->
    <div class="image-section">
        <img src="../images/login_background.jpg" alt="Imagen de fondo"> <!-- Asegúrate de tener una imagen de fondo -->
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - PanaEvents</title>
    <link rel="stylesheet" href="../styles/register.css">
</head>
<body>

<div class="container">
    <!-- Sección de Información del Evento o Aplicación -->
    <div class="event-info">
        <h1>Bienvenido a CampusHub</h1>
        <p>Regístrate para acceder a nuestra plataforma .</p>
        
        <div class="host-info">
            <img src="../images/logo.png" alt="Logo de CampusHub"> <!-- Asegúrate de tener una imagen de logo -->
            <p>Gestionado por:</p>
            <h3>PanaEvents</h3>
            <p>Optimización en gestión de eventos</p>
        </div>
    </div>

    <!-- Sección de Registro -->
    <div class="register-form">
        <h2>Regístrate</h2>
        <p>Ingrese su información para crear una cuenta.</p>

        <?php
        require_once '../includes/dbconnect.php';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $pdo = db_connect();

            
        
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encriptar la contraseña
        
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
            $stmt->execute(['username' => $username, 'email' => $email]);
        
            if ($stmt->rowCount() > 0) {
                echo "<p class='message'>El nombre de usuario o correo electrónico ya están en uso.</p>";
            } else {
              
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                if ($stmt->execute(['username' => $username, 'email' => $email, 'password' => $password])) {
                    echo "<p class='message' style='color: #5cb85c;'>Usuario creado con éxito. Ahora puedes iniciar sesión.</p>";
                } else {
                    echo "<p class='message'>Error al crear usuario.</p>";
                }
            }
        }
        ?>

       

        <form action="register.php" method="post">
            <input type="text" name="username" placeholder="Nombre de Usuario" required>
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="submit" value="Registrarse">
        </form>
        <br>
        <p class="login-link">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</div>

</body>
</html>

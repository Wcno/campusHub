<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - CampusHub</title>
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>

<div class="container">
    <!-- Sección de Información del Evento o Aplicación -->
    <div class="event-info">
        <h1>Bienvenido a CampusHub</h1>
        <p>Regístrate para acceder a nuestra plataforma.</p>
        
        <div class="host-info">
            <img src="../uploads/logo-test.png" alt="Logo de CampusHub"> <!-- Asegúrate de tener una imagen de logo -->
            <p>Gestionado por:</p>
            <h3>CampusHub</h3>
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
            
            // Recibir y validar los datos del formulario
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encriptar la contraseña
            $phone_number = $_POST['phone_number'];
            $birth_date = $_POST['birth_date'];

            // Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
        
            if ($stmt->rowCount() > 0) {
                echo "<p class='message'>El correo electrónico ya está en uso.</p>";
            } else {
                // Insertar los datos en la tabla
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone_number, birth_date) VALUES (:name, :email, :password, :phone_number, :birth_date)");
                if ($stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'phone_number' => $phone_number,
                    'birth_date' => $birth_date
                ])) {
                    echo "<p class='message' style='color: #5cb85c;'>Usuario creado con éxito. Ahora puedes iniciar sesión.</p>";
                } else {
                    echo "<p class='message'>Error al crear usuario.</p>";
                }
            }
        }
        ?>

        <form action="register.php" method="post">
            <input type="text" name="name" placeholder="Nombre Completo" required>
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="tel" name="phone_number" placeholder="Número de Teléfono" required>
            <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="birth_date" id="birth_date" required>
            <input type="submit" value="Registrarse">
        </form>
        <br>
        <p class="login-link">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</div>

</body>
</html>

<?php
// Importaciones de archivos
require_once '../../includes/dbconnect.php';
require_once '../../includes/bootstrap.php';

$errors = []; // Arreglo para almacenar errores
$successMessage = ''; // Mensaje de éxito

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo = db_connect();

    // Recibir y validar los datos del formulario
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; // Campo adicional
    $phone_number = trim($_POST['phone_number']);
    $birth_date = $_POST['birth_date'];
    $role = 'admin';

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

    // Si no hay errores, insertar datos en la base de datos con rol 'admin'
    if (empty($errors)) {
        $password_hashed = password_hash($password, PASSWORD_BCRYPT); // Encriptar contraseña

        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password, phone_number, birth_date, role) 
            VALUES (:name, :email, :password, :phone_number, :birth_date, :role)"
        );

        if ($stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $password_hashed,
            'phone_number' => $phone_number,
            'birth_date' => $birth_date,
            'role' => $role
        ])) {
            $successMessage = "¡Administrador creado con éxito! Ahora puedes iniciar sesión.";
        } else {
            $errors['general'] = "Error al crear la cuenta de administrador.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Administrador - CampusHub</title>
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <link rel="stylesheet" href="../../css/register_admin.css">
</head>
<body>
<?php loadComponent('top-wrapper') ?>

<main class="content-wrapper">
    <header>
        <h1>Registrar administrador</h1>
        <h3>Ingrese su información para crear una cuenta de administrador.</h3>
    </header>
    <div class="container">
    <!-- Sección de Registro -->
        <div class="register-form">
            <!-- Mostrar mensaje de éxito dentro del cuadro -->
            <?php if (!empty($successMessage)): ?>
                <p class="success-message"><?= htmlspecialchars($successMessage) ?></p>
            <?php endif; ?>

            <form action="register.php" method="post" id="register-form">
                <input type="text" name="name" placeholder="Nombre Completo" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <p class="error-message"><?= htmlspecialchars($errors['name']) ?></p>
                <?php endif; ?>

                <input type="email" name="email" placeholder="Correo Electrónico" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <p class="error-message"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>

                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Contraseña" required>
                    <div class="password-info">
                        La contraseña debe tener:
                        <ul>
                            <li>Al menos 8 caracteres.</li>
                            <li>Una letra mayúscula.</li>
                            <li>Un carácter especial (!@#$%^&*).</li>
                        </ul>
                    </div>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <p class="error-message"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>

                <!-- Campo para confirmar la contraseña -->
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmar Contraseña" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <p class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></p>
                <?php endif; ?>

                <input type="tel" name="phone_number" placeholder="Número de Teléfono" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>" required>
                <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                <input type="date" name="birth_date" id="birth_date" value="<?= htmlspecialchars($_POST['birth_date'] ?? '') ?>" required>
            </form>
        </div>
    </div>

    <!-- Botón de registro fuera del cuadro -->
    <div class="register-button-wrapper">
        <button type="submit" form="register-form">Registrar Administrador</button>
    </div>
</main>
<?php loadComponent('bottom-wrapper') ?>
</body>
</html>

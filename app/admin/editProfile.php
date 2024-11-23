<?php
// Importaciones de archivos
require_once '../../includes/dbconnect.php';
require_once '../../includes/bootstrap.php';

$pdo = db_connect();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user']['id'])) {
  header('Location: login.php');
  exit;
}

$userId = $_SESSION['user']['id'];
$uploadDir = '../uploads/';
$webPath = '/campusHub/uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['save'])) {
    // Capturar los datos del formulario
    $name = htmlspecialchars(trim($_POST['fname'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $birthDate = $_POST['fecha-nacimiento'] ?? '';
    $profileImagePath = $_SESSION['user']['img_profile'] ?? $webPath . 'default-profile.png';

    // Manejo de la imagen subida
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
      $fileName = uniqid('profile_', true) . '_' . basename($_FILES['profile_image']['name']);
      $uploadFilePath = $uploadDir . $fileName;

      // Verificar si el directorio existe, si no, crearlo
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      // Validar permisos del directorio
      if (!is_writable($uploadDir)) {
        echo "El directorio no tiene permisos de escritura: " . realpath($uploadDir);
        exit;
      }

      // Validar el tipo de archivo
      $fileType = mime_content_type($_FILES['profile_image']['tmp_name']);
      if (!in_array($fileType, ['image/jpeg', 'image/png', 'image/gif'])) {
        echo "El archivo debe ser una imagen válida (JPEG, PNG, GIF).";
        exit;
      }

      // Mover la imagen al directorio especificado
      if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFilePath)) {
        $profileImagePath = $webPath . $fileName; // Ruta accesible desde el navegador
      } else {
        echo "No se pudo mover el archivo de " . $_FILES['profile_image']['tmp_name'] . " a " . $uploadFilePath;
        exit;
      }
    }

    // Actualizar los datos en la base de datos
    $stmt = $pdo->prepare("
            UPDATE users 
            SET name = :name, phone_number = :phone, email = :email, 
                birth_date = :birth_date, img_profile = :img_profile 
            WHERE id = :id
        ");
    $stmt->execute([
      ':name' => $name,
      ':phone' => $phone,
      ':email' => $email,
      ':birth_date' => $birthDate,
      ':img_profile' => $profileImagePath,
      ':id' => $userId,
    ]);

    // Actualizar la sesión con los nuevos datos
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['phone_number'] = $phone;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['birth_date'] = $birthDate;
    $_SESSION['user']['img_profile'] = $profileImagePath;

    // Redirigir al perfil
    header('Location: profile.php');
    exit;
  } elseif (isset($_POST['cancel'])) {
    header('Location: profile.php');
    exit;
  }
}

// Obtener los datos del usuario
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../../css/common.css" rel="stylesheet">
  <link href="../../css/layout.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/perfil.css">
  <title>Editar Perfil</title>
</head>

<body>
  <?php loadComponent('top-wrapper'); ?>

  <main class="content-wrapper">
    <div class="main-container">
      <div class="panel">
        <div class="main-content">
          <div class="profile-info">
            <img class="profile-photo" src="<?php echo htmlspecialchars($_SESSION['user']['img_profile'] ?? 'https://cdn-icons-png.flaticon.com/512/6676/6676023.png'); ?>" alt="imagen de usuario">
          </div>
          <div class="form-container">
            <form class="form" method="POST" enctype="multipart/form-data">
              <label class="form-label" for="fname">Nombre Completo</label>
              <input class="form-input" type="text" name="fname" id="fname" value="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>"><br>

              <label class="form-label" for="phone">Número de Teléfono</label>
              <input class="form-input" type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($_SESSION['user']['phone_number']); ?>"><br>

              <label class="form-label" for="email">Correo Electrónico</label><br>
              <input class="form-input" type="email" name="email" id="email" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>"><br>

              <label class="form-label" for="fecha-nacimiento">Fecha de Nacimiento</label><br>
              <input class="form-input" type="date" name="fecha-nacimiento" id="fecha-nacimiento" value="<?php echo htmlspecialchars($_SESSION['user']['birth_date']); ?>"><br>

              <label for="profile_image" class="form-label">Cambiar Foto</label>
              <input type="file" class="form-input" name="profile_image" id="profile_image" accept="image/*">

              <div class="but">
                <button type="submit" name="save" class="save">Save Changes</button>
                <button type="submit" name="cancel" class="exit">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

</body>

</html>

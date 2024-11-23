<?php
session_start();

$user = $_SESSION['user'] ?? [];

// Destruir la sesion
session_unset();
session_destroy();

if ($user && $user['role'] == 'admin') {
  header("Location: ../app/admin/login");
} else {
  header("Location: ../app/login");
}
exit;

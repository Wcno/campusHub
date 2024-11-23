<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../php/helpers.php';

error_reporting(E_ALL);
session_start();

$user = $_SESSION['user'] ?? null;
if ($user && $user['role'] != 'admin' && currentRouteIncludes('/admin')) {
  // Logica por si un usuario sin privilegio se cree vivo
  header('Location: /app/admin/login');
  exit();
}

if (!$user && currentRouteIncludes('/admin')) {
  header('Location: /app/admin/login');
  exit();
}

if (!$user && !currentRouteIncludes('/login') && !currentRouteIncludes('/register')) {
  header('Location: /app/login');
  exit();
}

<?php
 // Define el archivo que contiene el contenido principal
error_reporting(E_ALL);
// Define el archivo de contenido para que `dashboard-layout.php` incluya este mismo archivo
$contenido = __FILE__; 

$titulo = 'Home'; 
require_once '../php/dashboardLayout.php';
?>

<h1>Hola mundo</h1>
<?php
 // Define el archivo que contiene el contenido principal
error_reporting(E_ALL);
// Define el archivo de contenido para que `dashboard-layout.php` incluya este mismo archivo
$contenido = __FILE__; 

$titulo = 'Home'; 
require_once '../php/dashboardLayout.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear evento</title>
    <link rel="stylesheet" href="../styles/create_event.css">
</head>
<body>
  
<div class="container">
        <h1>Crear Nuevo Evento</h1>
        <form action="create_event.php" method="POST" enctype="multipart/form-data">
            <label for="title">Título del Evento</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Descripción</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="date">Fecha</label>
            <input type="date" id="date" name="date" required>

            <label for="time">Hora</label>
            <input type="time" id="time" name="time" required>

            <label for="date">Hora de Finalización</label>
            <input type="time" id="time_end" name="time_end" required>

            <label for="lugar">Lugar</label>
            <input type="text" id="lugar" name="lugar" required>

            <label for="capacity">Capacidad</label>
            <input type="number" id="capacity" name="capacity" required min="1">

            <label for="image">Imagen del Evento</label>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit" class="btn">Crear Evento</button>
        </form>
    </div>
  
</body>
</html>
<?php
error_reporting(E_ALL);
session_start(); 
$usuarioRol = $_SESSION['role'];
$current_page = basename($_SERVER['PHP_SELF']);
$titulo = $titulo ?? 'Dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title> <!-- Título dinámico -->
    <link rel="stylesheet" type="text/css" href="../styles/dashboardLayout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body style="display: flex;">

    <nav class="sidebar" id="sidebar">

        <div>
            <i id="menu-toggle" class="fa-solid fa-bars"></i>
            <div class="contenido-principal-sidebar" id="contenido-principal-sidebar">
                <div class="nav-header">
                    <img class="logo" src="../uploads/logo-test.png" alt="logo de PANAEVENTS">
                    <h1>PANAEVENTS</h1>
                </div>

                <div class="nav-links">
                    <a href="dashboard-layout.php?page=home.php" class="sidebar-item <?php echo $current_page == 'home.php' ? 'active' : ''?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg><span>Inicio</span></a>
                    <?php if ($usuarioRol == 'admin'): ?>
                        <!-- Opciones para el administrador -->
                        <a href="../views/create_event.php" class="sidebar-item <?php echo $current_page == '../views/create_event.php' ? 'active' : ''; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-clock"><path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h5"/><path d="M17.5 17.5 16 16.3V14"/><circle cx="16" cy="16" r="6"/></svg><span>Gestión de eventos</span></a>
                        <a href="#" class="sidebar-item <?php echo $current_page == 'estadisticas.php' ? 'active' : ''; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chart-no-axes-combined"><path d="M12 16v5"/><path d="M16 14v7"/><path d="M20 10v11"/><path d="m22 3-8.646 8.646a.5.5 0 0 1-.708 0L9.354 8.354a.5.5 0 0 0-.707 0L2 15"/><path d="M4 18v3"/><path d="M8 14v7"/></svg><span>Estadísticas</span></a>
                    <?php endif; ?>
                    <a href="#" class="sidebar-item <?php echo $current_page == 'misEventos.php' ? 'active' : ''; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ticket-check"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="m9 12 2 2 4-4"/></svg><span>Mis Eventos</span></a>
                </div>
                <hr>
                <div>
                    <h3>Configuración</h3>
                    <div class="nav-links">
                        <a href="#" class="sidebar-item <?php echo $current_page == 'ajustes.php' ? 'active' : ''; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg><span>Ajustes</span></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="caja-perfil" id="caja-perfil">
            <div class="perfil-logout">
                <i class="fa-solid fa-circle-user"></i>
                <a href="../php/logout.php">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
            <div class="perfil-info">
            <h3 class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
            <p class="nombre-correo"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
 
        </div>
    </nav>

    <script src="../js/dashboardLayout.js">

    </script>


</body>

</html>
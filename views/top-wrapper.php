<?php
$userRole = $_SESSION['user']['role'] ?? null;
?>

<nav class="sidebar" id="sidebar">
  <div class="nav-header">
    <img
      class="logo"
      src="<?php echo baseUrl('/uploads/logo-test.png') ?>"
      alt="logo" />
    <h1>CampusHub</h1>
  </div>

  <div class="link-wrapper">
    <?php
    match ($userRole) {
      'admin' => loadComponent('admin-sidebar-links'),
      'user' => loadComponent('user-sidebar-links'),
      default => loadComponent('guest-sidebar-links'),
    };
    ?>
  </div>

  <?php if ($userRole) { ?>
    <div class="caja-perfil" id="caja-perfil">
      <div class="perfil-logout">
        <svg height="32" width="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
          <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd" />
        </svg>
        <a href="<?php echo baseUrl('/php/logout') ?>" class="logout-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" x2="9" y1="12" y2="12" />
          </svg>
        </a>
      </div>
      <div class="perfil-info">
        <h3 class="nombre-usuario">
          <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
        </h3>
        <p class="nombre-correo">
          <?php echo htmlspecialchars($_SESSION['user']['email']); ?>
        </p>
      </div>
    </div>
  <?php } ?>
</nav>

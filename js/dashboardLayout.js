document.getElementById("menu-toggle").addEventListener("click", function() {

    const sidebar = document.getElementById('sidebar');
    const cajaPerfil = document.getElementById('caja-perfil');
    const contenidoPrincipalSidebar = document.getElementById('contenido-principal-sidebar');

    if (sidebar.classList.contains('sidebar-hidden')) {
        sidebar.classList.remove('sidebar-hidden');
        sidebar.classList.add('sidebar');
        cajaPerfil.classList.remove('caja-perfil-hidden');
        cajaPerfil.classList.add('caja-perfil');
        contenidoPrincipalSidebar.classList.remove('contenido-principal-sidebar-hidden');
        contenidoPrincipalSidebar.classList.add('contenido-principal-sidebar');
    } else {
        sidebar.classList.remove('sidebar');
        sidebar.classList.add('sidebar-hidden');
        cajaPerfil.classList.remove('caja-perfil');
        cajaPerfil.classList.add('caja-perfil-hidden');
        contenidoPrincipalSidebar.classList.remove('contenido-principal-sidebar');
        contenidoPrincipalSidebar.classList.add('contenido-principal-sidebar-hidden');
    }


});
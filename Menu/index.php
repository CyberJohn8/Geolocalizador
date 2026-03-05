<?php
    // ✅ CONFIGURACIÓN ANTES de session_start()
    ini_set('session.gc_maxlifetime', 1800);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);

    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // ✅ Configuración para el ingreso como invitado
    if (isset($_GET['guest']) && $_GET['guest'] === 'true') {
        $_SESSION["username"] = "Invitado";
        $_SESSION["rol"] = "invitado";
        $_SESSION["user_id"] = 0; // ID para invitados
    }

    // ✅ Redirección MEJORADA - usar ruta relativa
    if (!isset($_SESSION["username"])) {
        // Limpiar buffers antes de redirección
        if (ob_get_level()) {
            ob_clean();
        }
        
        // Usar ruta relativa en lugar de absoluta
        header("Location: ../Iniciar_Sesion.php");
        exit();
    }

    // ✅ Configuración adicional de seguridad
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');

    // ✅ Tu código continúa aquí...
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    
    <link rel="stylesheet" href="Menu.css"> <!-- Enlace al archivo de estilos CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">

</head>
<body>


    <!-- Íconos visibles solo en escritorio -->
    <!-- CONTENEDOR DEL MENÚ -->
    <nav class="menu-nav">
        <button class="icon-btn btn-menu" onclick="toggleSidebarMenu()" title="Menú">
            <img src="/iconos/Menu.png" alt="Menú">
        </button>
        <button class="icon-btn solo-pc" onclick="navigateTo('/Menu/index.php')" title="Inicio">
            <img src="/iconos/Inicio.png" alt="Inicio">
        </button>
        <button class="icon-btn solo-pc" onclick="navigateTo('/Menu/Submenu.php')" title="Ubicación">
            <img src="/iconos/ubicaciones.png" alt="Ubicación">
        </button>
        <button class="icon-btn solo-pc" onclick="navigateTo('/Menu/Eventos/index.php')" title="Eventos">
            <img src="/iconos/eventos.png" alt="Eventos">
        </button>
        <!--<?php if ($_SESSION["rol"] !== "invitado") : ?>
            <button class="icon-btn solo-pc" onclick="navigateTo('/Menu/Donaciones/index.php')" title="Donaciones">
                <img src="/iconos/donation.png" alt="Donaciones">
            </button>
        <?php endif; ?>-->
        <button class="icon-btn solo-pc" onclick="navigateTo('/Menu/Material/index.php')" title="Material Literario">
            <img src="/iconos/material.png" alt="Material Literario">
        </button>
        <button class="icon-btn solo-pc" onclick="navigateTo('/Menu/LiteraturaBiblica/index.php')" title="Biblia">
            <img src="/iconos/Biblia.png" alt="Estudio Bíblico">
        </button>
        <button class="icon-btn btn-sesion" onclick="navigateTo('/Menu/logout.php')" title="Cerrar Sesión">
            <img src="/iconos/Sesion.png" alt="Cerrar Sesión">
        </button>
    </nav>

    <!-- Menú emergente (sidebar) para celular -->
    <div class="sidebar mobile-only" id="sidebarMenu">
        <h2>Menú</h2>
        <a href="/Menu/index.php">Inicio</a>
        <a href="/Menu/Submenu.php">Ubicación</a>
        <a href="/Menu/Eventos/index.php">Eventos</a>
        <!--<?php if ($_SESSION["rol"] !== "invitado") : ?>
            <a href="/Menu/Donaciones/index.php">Donaciones</a>
        <?php endif; ?>-->
        <a href="/Menu/Material/index.php">Material Literario</a>
        <a href="/Menu/LiteraturaBiblica/index.php">Estudio Bíblico</a>
        <?php if ($_SESSION["rol"] !== "invitado") : ?>
            <a href="/Menu/AdminUsuario/index.php">Gestionar Sesión</a>
        <?php endif; ?>

        <a href="/Menu/Somos.html">¿Quiénes Somos?</a>
        <a href="/Menu/Copiryt.php">Acerca de</a>

        <button class="close-btn" onclick="toggleSidebarMenu()">Cerrar</button>
    </div>

    <!-- Fondo oscuro -->
    <div class="overlay" id="overlay" onclick="toggleSidebarMenu()"></div>

    <script>
        function toggleSidebarMenu() {
            const sidebar = document.getElementById('sidebarMenu');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Función universal para navegación segura
        function navigateTo(url) {
            console.log('Navegando a:', url);
            window.location.href = url;
        }

        // Inicialización para mejor manejo de enlaces
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar enlaces para mejor compatibilidad con PWA
            const links = document.querySelectorAll('a[href^="/"]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    console.log('Navegando por enlace a:', this.href);
                });
            });
            
            // Verificar modo PWA
            if (window.matchMedia('(display-mode: standalone)').matches) {
                console.log('Modo PWA activado - Rutas relativas funcionando');
            }
        });
    </script>

    <!-- Contenido principal -->
    <div class="contenido">
        <header>
            <!-- Dentro del <head>
            <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png"> -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

            <img src="/iconos/Titulo.png" alt="Bienvenida" class="hero-img">

            
        </header>


        <div class="Versiculo">
            <!--<img src="IMG/mapa venezuela.jpg" alt="Bienvenida" class="hero-img">-->
            <p class="texto-bienvenida">
                Porque donde están dos o tres congregados en mi nombre, </p>
            <p class="texto-bienvenida">    allí estoy yo en medio de ellos.
            </p>
            <p class="texto-cita">Mateo 18:20</p>
        </div>

        <div class="opciones">
            <button onclick="location.href='Submenu.php'" class="btn-opcion">Ubicaciones</button>
            <button onclick="location.href='Eventos/index.php'" class="btn-opcion">Eventos</button>
            
            <!--<?php if ($_SESSION["rol"] !== "invitado") : ?>
                <button onclick="location.href='Donaciones/index.php'" class="btn-opcion">Hacer Donación</button>
            <?php endif; ?>-->

            <button onclick="location.href='Material/index.php'" class="btn-opcion">Material Literario</button>
            <button onclick="location.href='LiteraturaBiblica/index.php'" class="btn-opcion">Estudio Bíblico</button>

            <button onclick="location.href='Somos.html'" class="btn-opcion">¿Quiénes Somos?</button>
            <button onclick="location.href='logout.php'" class="btn-opcion logout">Cerrar Sesión</button>
        </div>

        <!--<footer>
            <p>&copy; 2025 John Malavé. Todos los derechos reservados.</p>
            <p>Prohibida su reproducción total o parcial sin autorización previa.</p>
            <p>Versión 1.0 | Última actualización: agosto de 2025</p>
        </footer>-->
    </div>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("overlay");
            sidebar.classList.toggle("active");
            overlay.classList.toggle("active");
        }
    </script>


    <!--https://directorioasambleasvzla.com/Menu/-->
    

</body>
</html>

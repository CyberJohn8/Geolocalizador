<?php
session_start();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Bíblica con IA</title>
    
    <link rel="stylesheet" href="IA_Biblica.css">
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

        // Función universal para navegación segura en PWA y HostGator
        function navigateTo(url) {
            console.log('Navegando a:', url);
            window.location.href = url;
        }

        // Manejo de enlaces en PWA
        document.addEventListener('DOMContentLoaded', function() {
            // Convertir todos los botones onclick a event listeners
            const buttons = document.querySelectorAll('button[onclick*="location.href"]');
            buttons.forEach(button => {
                const match = button.getAttribute('onclick').match(/location\.href\s*=\s*'([^']+)'/);
                if (match) {
                    const url = match[1];
                    button.removeAttribute('onclick');
                    button.addEventListener('click', function() {
                        navigateTo(url);
                    });
                }
            });
            
            // Verificar si estamos en modo standalone (PWA)
            if (window.matchMedia('(display-mode: standalone)').matches) {
                console.log('Ejecutando en modo PWA - Todas las rutas son relativas a la raíz');
            }
        });
    </script>

<main>
  <div>
    <header>
        <button onclick="history.back()"></button>
        <h1>Consulta Bíblica con Inteligencia Artificial</h1>
    </header>

    <div class="container">
        <p>Formula tus preguntas sobre la Biblia y deja que la IA bíblica te responda:</p>
        <iframe src="https://www.yeschat.ai/es/gpts-ZxX36KFX-BibleGPT"></iframe>
    </div>
  </div>
</main>


</body>
</html>

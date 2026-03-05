<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cookie de prueba
if (!isset($_COOKIE['test_cookie'])) {
    setcookie('test_cookie', '1', time() + 3600, "/", "", isset($_SERVER['HTTPS']), true);
}

$cookie_disabled = !isset($_COOKIE['test_cookie']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="Formulario.css" />
    <link href="https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation:wght@400;700&display=swap" rel="stylesheet" />


    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">



    <!--PWA:    -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0066cc">

    <script>
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("/service-worker.js")
            .then(() => console.log("Service Worker registrado"))
            .catch(err => console.error("SW error:", err));
        }
    </script>
    <!---->
    
</head>
<body>
<div class="wrapper">
    <div class="container">
        <h1>Bienvenido</h1>
        <p>Al Directorio de las Asambleas Congregadas al Nombre del Señor en Venezuela</p>
        <button onclick="checkSession()" class="btn-login">Iniciar Sesión</button>
        <button onclick="location.href='Registrarse.php'" class="btn-register" style="background-color: #4D6164;">Registrarse</button>
        <button onclick="location.href='Menu/index.php?guest=true'" class="btn-guest">Ingresar como Invitado</button>
        
        
        <button id="installBtn" style="display: none; padding: 5px 5px; cursor: pointer; border-radius: 5px; background-color: #E6CDB7; color: #192E2F;">
            Instalar aplicación 
            <img src="iconos/icon2-8.png" alt="APP Icono" style="width: 10px; height: 10px;">
        </button>

    </div>
</div>

<script>
    let deferredPrompt;

    // Notificación si cookies están desactivadas
    <?php if ($cookie_disabled): ?>
    if ("Notification" in window) {
        Notification.requestPermission().then(function(permission) {
            if (permission === "granted") {
                new Notification("⚠ Cookies desactivadas", {
                    body: "Activa las cookies para continuar usando esta aplicación.",
                    icon: "icon-192.png" // icono para la notificación
                });
            } else {
                alert("⚠ Cookies desactivadas. Por favor, actívalas en la configuración de tu navegador.");
            }
        });
    }
    <?php endif; ?>

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        const installBtn = document.getElementById('installBtn');
        installBtn.style.display = 'inline-block';

        installBtn.addEventListener('click', () => {
            installBtn.style.display = 'none';
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('PWA instalada');
                } else {
                    console.log('Instalación cancelada');
                }
                deferredPrompt = null;
            });
        }, { once: true });
    });

    function checkSession() {
        let userSession = localStorage.getItem("userLogged");
        if (userSession) {
            window.location.href = "Menu/index.php";
        } else {
            window.location.href = "Iniciar_Sesion.php";
        }
    }
</script>

<script>
    // Registro MEJORADO del Service Worker
    if ('serviceWorker' in navigator) {
    window.addEventListener('load', async function() {
        try {
        const registration = await navigator.serviceWorker.register('/service-worker.js');
        console.log('SW registrado correctamente:', registration);
        
        // Verificar actualizaciones
        registration.addEventListener('updatefound', () => {
            const newWorker = registration.installing;
            console.log('Nueva versión de SW encontrada:', newWorker);
        });
        
        // Manejar errores de SW
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            console.log('Nuevo SW tomó control');
            window.location.reload();
        });
        
        } catch (error) {
        console.error('Error registrando SW:', error);
        }
    });
    
    // Manejar mensajes del SW
    navigator.serviceWorker.addEventListener('message', event => {
        console.log('Mensaje del SW:', event.data);
    });
    }

    // Función para forzar actualización del SW
    function updateServiceWorker() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.ready.then(registration => {
        registration.update();
        });
    }
    }

    // Actualizar SW cada vez que se vuelve online
    window.addEventListener('online', updateServiceWorker);
</script>
</body>
</html>

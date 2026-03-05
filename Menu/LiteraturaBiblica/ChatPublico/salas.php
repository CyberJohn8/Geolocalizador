<?php
/* ============================================================
   AQUI SE MUESTRAN LAS SALAS
============================================================ */




session_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$rol = $_SESSION['rol'];

// Conexión a MySQL con soporte utf8mb4 // Incluir archivo de conexión (mysqli)
//require "https://directorioasambleasvzla.com/conexion.php"; // tu archivo de conexión
require __DIR__ . "/../../../conexion.php";
 // tu archivo de conexión

// Verifica conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// IMPORTANTE: establecer codificación utf8mb4 para soportar caracteres especiales y emojis
$conn->set_charset("utf8mb4");

if (!$conn->set_charset("utf8mb4")) {
    echo "Error al establecer utf8mb4: " . $conn->error;
    exit();
}

// Crear sala con mensaje inicial
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nueva_sala'])) {
    $nueva_sala = trim($_POST['nueva_sala']);
    $mensaje_inicial = trim($_POST['mensaje_inicial']);
    if (!empty($nueva_sala) && !empty($mensaje_inicial)) {
        $stmt = $conn->prepare("INSERT INTO mensajes (sala, nombre, mensaje) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nueva_sala, $username, $mensaje_inicial);
        $stmt->execute();
        $stmt->close();
        header("Location: salas.php");
        exit();
    }
}

// Eliminar sala si el usuario la creó o es admin
if (isset($_GET['eliminar'])) {
    $salaEliminar = $_GET['eliminar'];

    $stmt = $conn->prepare("SELECT nombre FROM mensajes WHERE sala = ? ORDER BY id ASC LIMIT 1");
    $stmt->bind_param("s", $salaEliminar);
    $stmt->execute();
    $result_creador = $stmt->get_result();
    $creadorSala = null;

    if ($row_creador = $result_creador->fetch_assoc()) {
        $creadorSala = $row_creador['nombre'];
    }
    $stmt->close();

    if ($creadorSala && (strcasecmp(trim($creadorSala), trim($username)) === 0 || $rol === 'admin')) {
        $stmt = $conn->prepare("DELETE FROM mensajes WHERE sala = ?");
        $stmt->bind_param("s", $salaEliminar);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: salas.php");
    exit();
}

// Obtener salas únicas con su creador y cantidad de mensajes
$sql = "
    SELECT m1.sala, m1.nombre AS creador, COUNT(m2.id) as total
    FROM mensajes m1
    INNER JOIN (
        SELECT sala, MIN(id) AS primer_id
        FROM mensajes
        GROUP BY sala
    ) m_idx ON m1.sala = m_idx.sala AND m1.id = m_idx.primer_id
    LEFT JOIN mensajes m2 ON m1.sala = m2.sala
    GROUP BY m1.sala
    ORDER BY m1.sala ASC
";
$result = $conn->query($sql);












// Solo si $_SESSION['user_id'] está definida pero $_SESSION['username'] no
if (!isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    $conn_users = new mysqli("localhost", "root", "", "directorio");
    $conn_users->set_charset("utf8mb4");
    $stmt = $conn_users->prepare("SELECT username, rol FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($username, $rol);
    if ($stmt->fetch()) {
        $_SESSION['username'] = $username;
        $_SESSION['rol'] = $rol;
    }
    $stmt->close();
    $conn_users->close();
}/**/
// SOLO PARA DEPURAR:
//echo "Usuario actual: " . htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
?>





<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salas del chat</title>
    <link rel="stylesheet" href="salas.css">
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

<div class="container">
    <header class="top-header">
        <button onclick="window.history.back()" class="back-btn" title="Volver"></button>
        <h1>Sistema de Chat Bíblico</h1>
    </header>

    <div class="salas-layout">
        <!-- Contenedor de salas -->
        <div class="salas-scroll">
            <h3>Salas disponibles</h3>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="sala-item">
                        <a class="sala-btn" href="sala_conversacion.php?sala=<?= urlencode($row['sala']) ?>">
                            <?= htmlspecialchars($row['sala']) ?>
                            <?= strcasecmp(trim($row['creador']), trim($username)) === 0 ? ' (tú)' : '' ?>
                        </a>

                        <?php if (strcasecmp(trim($row['creador']), trim($username)) === 0 || $rol === 'admin'): ?>
                            <a class="delete-btn" href="?eliminar=<?= urlencode($row['sala']) ?>"
                            onclick="return confirm('¿Eliminar la sala &quot;<?= htmlspecialchars($row['sala']) ?>&quot; y todos sus mensajes?');">
                                Eliminar
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-salas">Aún no hay salas disponibles.</p>
            <?php endif; ?>
        </div>


        <!-- Formulario para crear nueva sala -->
        <form class="crear-sala-form" method="post">
            <h3>Crear nueva sala</h3>
            <label>Nombre de la sala:</label>
            <input type="text" name="nueva_sala" placeholder="Nombre de la nueva sala" required>

            <label>Mensaje inicial (tema de conversación):</label>
            <textarea name="mensaje_inicial" placeholder="Escribe el tema del chat o una bienvenida" required></textarea>

            <button type="submit">Crear sala</button>
        </form>
    </div>
</div>

</body>
</html>
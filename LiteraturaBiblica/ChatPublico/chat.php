<?php
session_start();
require_once "conexion.php";

// Validar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$rol = $_SESSION['rol'];

// Obtener la sala actual desde GET o POST
$sala = isset($_GET['sala']) ? $_GET['sala'] : 'General';

// Insertar nuevo mensaje
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['mensaje'])) {
    $mensaje = $conn_chat->real_escape_string($_POST['mensaje']);
    $stmt = $conn_chat->prepare("INSERT INTO mensajes (sala, nombre, mensaje) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $sala, $username, $mensaje);
    $stmt->execute();
    $stmt->close();
}

// Obtener mensajes de la sala
$sql = "SELECT nombre, mensaje, fecha FROM mensajes WHERE sala = ? ORDER BY fecha ASC";
$stmt = $conn_chat->prepare($sql);
$stmt->bind_param("s", $sala);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat - Sala <?php echo htmlspecialchars($sala); ?></title>
    
    <link rel="stylesheet" href="chat.css">
</head>
<body>
    <!-- Menú lateral con íconos fijos -->
    <div class="icon-sidebar">
        <button class="icon-btn" onclick="toggleSidebarMenu()">
            <img src="iconos/Menu.png" alt="Menú" title="Menú">
        </button>
        <button class="icon-btn" onclick="location.href='http://localhost/Geolocalizador/Menu/index.php'">
            <img src="iconos/Inicio.png" alt="Inicio" title="Inicio">
        </button>
        <button class="icon-btn" onclick="location.href='http://localhost/Geolocalizador/Menu/Submenu.php'">
            <img src="iconos/ubicaciones.png" alt="Ubicación" title="Ubicación">
        </button>
        <button class="icon-btn" onclick="location.href='http://localhost/Geolocalizador/Menu/Eventos/index.php'">
            <img src="iconos/eventos.png" alt="Eventos" title="Eventos">
        </button>
        <?php if ($_SESSION["rol"] !== "invitado") : ?>
            <button class="icon-btn" onclick="location.href='http://localhost/Geolocalizador/Menu/Donaciones/index.php'">
                <img src="iconos/donation.png" alt="Donaciones" title="Donaciones">
            </button>
        <?php endif; ?>
        <button class="icon-btn" onclick="location.href='http://localhost/Geolocalizador/Menu/Material/index.php'">
            <img src="iconos/material.png" alt="Material" title="Material Literario">
        </button>
        <button class="icon-btn" onclick="location.href='http://localhost/Geolocalizador/Menu/LiteraturaBiblica/index.php'">
            <img src="iconos/Biblia.png" alt="Biblia" title="Estudio Bíblica">
        </button>
        <button class="icon-btn" onclick="location.href='http://localhost/Geolocalizador'">
            <img src="iconos/Sesion.png" alt="Salir" title="Salir">
        </button>

        <script>
            function toggleSidebarMenu() {
                const sidebar = document.getElementById('sidebarMenu');
                const overlay = document.getElementById('overlay');
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }
        </script>

    </div>

    <!-- Menú emergente lateral -->
    <div class="sidebar" id="sidebarMenu">
        <h2>Menú</h2>
        <a href="http://localhost/Geolocalizador">Inicio de Sesión</a>
        <a href="http://localhost/Geolocalizador/Menu/index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Inicio</a>
        <a href="http://localhost/Geolocalizador/Menu/Submenu.php">Ubicaciones</a>
        <a href="http://localhost/Geolocalizador/Menu/Eventos/index.php">Eventos</a>
        <?php if ($_SESSION["rol"] !== "invitado") : ?>
            <a href="http://localhost/Geolocalizador/Menu/Donaciones/index.php">Donaciones</a>
        <?php endif; ?>
        <a href="http://localhost/Geolocalizador/Menu/Material/index.php">Material Literario</a>
        <a href="http://localhost/Geolocalizador/Menu/LiteraturaBiblica/index.php">Estudio Bíblica</a>
        <a href="logout.php">Cerrar Sesión</a>
        <button class="close-btn" onclick="toggleSidebarMenu()">Cerrar</button>
    </div>

    <!-- Fondo oscuro -->
    <div class="overlay" id="overlay" onclick="toggleSidebarMenu()"></div>

    <div class="container">
        <div class="chat-box">
            <header class="top-header">
                <button onclick="location.href='http://localhost/Geolocalizador/Menu/LiteraturaBiblica/ChatPublico/salas.php'" title="Volver" class="back-btn"></button>
                <h1>Sistema de Chat Bíblico</h1>
            </header>

            <h2>Sala: <?php echo htmlspecialchars($sala); ?></h2>

            <!-- Contenedor del área de chat con scroll -->
            <div class="mensaje-container">
            <div id="chat-scroll">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $nombre = isset($row['nombre']) ? htmlspecialchars($row['nombre']) : 'Desconocido';
                            $mensaje = isset($row['mensaje']) ? str_replace(["\r\n", "\n", "\r"], ' ', htmlspecialchars($row['mensaje'])) : '';
                            $fecha = isset($row['fecha']) ? htmlspecialchars($row['fecha']) : '';
                            $esPropio = $nombre === $_SESSION['username'];
                            $clase = $esPropio ? 'mensaje propio' : 'mensaje';
                        ?>
                        <div class="<?= $clase ?>">
                            <span class="nombre"><?= $esPropio ? 'Tú' : $nombre ?>:</span>
                            <span><?= $mensaje ?></span><br>
                            <span class="fecha"><?= $fecha ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #888;">No hay mensajes en esta sala todavía.</p>
                <?php endif; ?>
            </div>


                <!-- Formulario de mensaje -->
                <form class="formulario-chat" method="POST" action="">
                    <textarea name="mensaje" rows="2" placeholder="Escribe tu mensaje..." required></textarea>
                    <button type="submit">Enviar</button>
                </form>
            </div>

            <script>
                // Esperar que el contenido cargue completamente
                window.addEventListener('load', function () {
                    const chatScroll = document.getElementById('chat-scroll');
                    if (chatScroll) {
                        chatScroll.scrollTop = chatScroll.scrollHeight;
                    }
                });
            </script>

        </div>
    </div>
</body>
</html>

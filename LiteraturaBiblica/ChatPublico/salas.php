<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "chat_biblico");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Crear sala con mensaje inicial
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nueva_sala'])) {
    $nueva_sala = trim($_POST['nueva_sala']);
    $mensaje_inicial = trim($_POST['mensaje_inicial']);
    if (!empty($nueva_sala) && !empty($mensaje_inicial)) {
        $stmt = $conn->prepare("INSERT INTO mensajes (sala, nombre, mensaje) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nueva_sala, $_SESSION['username'], $mensaje_inicial);
        $stmt->execute();
        $stmt->close();
        header("Location: salas.php");
        exit();
    }
}

// Eliminar sala si el usuario la creó o si es admin
if (isset($_GET['eliminar'])) {
    $salaEliminar = $_GET['eliminar'];

    $stmt = $conn->prepare("SELECT nombre FROM mensajes WHERE sala = ? ORDER BY id ASC LIMIT 1");
    $stmt->bind_param("s", $salaEliminar);
    $stmt->execute();
    $stmt->bind_result($creadorSala);
    $stmt->fetch();
    $stmt->close();

    if ($creadorSala === $_SESSION['username'] || $_SESSION['rol'] === 'admin') {
        $stmt = $conn->prepare("DELETE FROM mensajes WHERE sala = ?");
        $stmt->bind_param("s", $salaEliminar);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: salas.php");
    exit();
}

// Obtener salas únicas con creador
$sql = "
    SELECT m1.sala, m1.nombre AS creador, COUNT(*) as total
    FROM mensajes m1
    INNER JOIN (
        SELECT sala, MIN(id) AS primer_id
        FROM mensajes
        GROUP BY sala
    ) m2 ON m1.sala = m2.sala AND m1.id = m2.primer_id
    GROUP BY m1.sala
    ORDER BY m1.sala ASC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Salas del chat</title>
    <link rel="stylesheet" href="salas.css">
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
            <img src="iconos/Biblia.png" alt="Biblia" title="Estudio Bíblico">
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
        <a href="http://localhost/Geolocalizador/Menu/LiteraturaBiblica/index.php">Estudio Bíblico</a>
        <a href="logout.php">Cerrar Sesión</a>
        <button class="close-btn" onclick="toggleSidebarMenu()">Cerrar</button>
    </div>

    <!-- Fondo oscuro -->
    <div class="overlay" id="overlay" onclick="toggleSidebarMenu()"></div>

<div class="container">
    <header class="top-header">
        <button onclick="location.href='http://localhost/Geolocalizador/Menu/LiteraturaBiblica/index.php'" class="back-btn" title="Volver"></button>
        <h1>Sistema de Chat Bíblico</h1>
    </header>

    <div class="salas-layout">
        <!-- Contenedor de salas -->
        <div class="salas-scroll">
            <h3>Salas disponibles</h3>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="sala-item">
                        <a class="sala-btn" href="chat.php?sala=<?= urlencode($row['sala']) ?>">
                            <?= htmlspecialchars($row['sala']) ?>
                            <?= $row['creador'] === $_SESSION['username'] ? ' (tú)' : '' ?>
                        </a>
                        <?php if ($row['creador'] === $_SESSION['username'] || $_SESSION['rol'] === 'admin'): ?>
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

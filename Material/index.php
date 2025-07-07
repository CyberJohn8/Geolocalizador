<?php
session_start();
$conn = new mysqli("localhost", "root", "", "geolocalizador");
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

// Verificar si el usuario es administrador
$es_admin = ($_SESSION['rol'] ?? '') === 'admin';

// CRUD solo para administradores
if ($_SERVER["REQUEST_METHOD"] == "POST" && $es_admin) {
    $tabla = $_POST["tabla"];
    $titular = $_POST["titular"];
    $descripcion = $_POST["descripcion"];
    $enlace = $_POST["enlace"];

    if (isset($_POST["agregar"])) {
        $conn->query("INSERT INTO $tabla (Titular, Descripcion, Enlace) VALUES ('$titular', '$descripcion', '$enlace')");
    }

    if (isset($_POST["editar"])) {
        $id = $_POST["id"];
        $conn->query("UPDATE $tabla SET Titular='$titular', Descripcion='$descripcion', Enlace='$enlace' WHERE id=$id");
    }
}

// Eliminar también solo si es admin
if (isset($_GET["eliminar"], $_GET["tabla"]) && $es_admin) {
    $id = $_GET["eliminar"];
    $tabla = $_GET["tabla"];
    $conn->query("DELETE FROM $tabla WHERE id=$id");
}

// Obtener datos para mostrar
$result_es = $conn->query("SELECT * FROM sitios_web");
$result_en = $conn->query("SELECT * FROM sitios_web_otros");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sitios Web Recomendados</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Material.css">
    <script>
        function filtrarTabla() {
            let input = document.getElementById("buscador").value.toLowerCase();
            document.querySelectorAll("tbody tr").forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
            });
        }
    </script>
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


    <div class="content">
        <header>
            <button onclick="history.back()" class="return-button" title="Volver"></button>
            <h1>Material Literario en Español e Inglés</h1>
        </header>

        <input type="text" id="buscador" onkeyup="filtrarTabla()" placeholder="Buscar...">

        <!-- Español -->
        <h2>Material en Español</h2>
        <div class="card-container">
            <?php while ($row = $result_es->fetch_assoc()): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($row['Titular']) ?></h3>
                    <p><?= htmlspecialchars($row['Descripcion']) ?></p>
                    <a href="<?= htmlspecialchars($row['Enlace']) ?>" target="_blank" class="btn-enlace largo">Ir al sitio</a>
                    <?php if ($es_admin): ?>
                        <form method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="tabla" value="sitios_web">
                            <button name="eliminar" class="eliminar">Eliminar</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>


        <?php if ($es_admin): ?>
        <form method="POST">
            <h3>Agregar nuevo (Español)</h3>
            <input type="hidden" name="tabla" value="sitios_web">
            <input type="text" name="titular" placeholder="Titular" required>
            <input type="text" name="descripcion" placeholder="Descripción" required>
            <input type="url" name="enlace" placeholder="Enlace" required>
            <button name="agregar">Agregar</button>
        </form>
        <?php endif; ?>

        <!-- Inglés -->
        <h2>Material en Inglés</h2>
        <div class="card-container">
            <?php while ($row = $result_en->fetch_assoc()): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($row['Titular']) ?></h3>
                    <p><?= htmlspecialchars($row['Descripcion']) ?></p>
                    <a href="<?= htmlspecialchars($row['Enlace']) ?>" target="_blank" class="btn-enlace largo">Visit</a>
                    <?php if ($es_admin): ?>
                        <form method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="tabla" value="sitios_web_otros">
                            <button name="eliminar" class="eliminar">Eliminar</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>


        <?php if ($es_admin): ?>
        <form method="POST">
            <h3>Agregar nuevo (Inglés)</h3>
            <input type="hidden" name="tabla" value="sitios_web_otros">
            <input type="text" name="titular" placeholder="Title" required>
            <input type="text" name="descripcion" placeholder="Description" required>
            <input type="url" name="enlace" placeholder="Link" required>
            <button name="agregar">Agregar</button>
        </form>
        <?php endif; ?>

    </div>

</body>
</html>

<?php
$conn->close();
?>

<?php
    session_start();

    // Establecer la zona horaria de Caracas
    date_default_timezone_set('America/Caracas');

    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "geolocalizador");
    if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

    // === Comprobación si es invitado ===
    $esInvitado = (!isset($_SESSION['rol']) || $_SESSION['rol'] === 'invitado');

    // === CRUD para administradores ===
    $editando = false;
    $eventoEdit = null;

    if (!$esInvitado) {
        if (isset($_GET['editar']) && $_SESSION['rol'] === 'admin') {
            $idEditar = intval($_GET['editar']);
            $res = $conn->query("SELECT * FROM eventos WHERE id = $idEditar");
            if ($res->num_rows > 0) {
                $eventoEdit = $res->fetch_assoc();
                $editando = true;
            }
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && $_SESSION['rol'] === 'admin') {
            $detalles = $_POST['detalles'];
            $ubicacion = $_POST['ubicacion'];
            $fecha = date("Y-m-d H:i:s"); // Hora actual en Caracas

            if (!empty($_POST['evento_id'])) {
                $id = intval($_POST['evento_id']);
                $stmt = $conn->prepare("UPDATE eventos SET detalles=?, ubicacion=? WHERE id=?");
                $stmt->bind_param("ssi", $detalles, $ubicacion, $id);
            } else {
                $stmt = $conn->prepare("INSERT INTO eventos (detalles, ubicacion, fecha_publicacion) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $detalles, $ubicacion, $fecha);
            }

            $stmt->execute();
            $stmt->close();
            header("Location: index.php");
            exit();
        }

        if (isset($_GET['eliminar']) && $_SESSION['rol'] === 'admin') {
            $idEliminar = intval($_GET['eliminar']);
            $conn->query("DELETE FROM eventos WHERE id = $idEliminar");
            header("Location: index.php");
            exit();
        }
    }

    // === Filtros para todos los usuarios ===
    $condiciones = [];
    $params = [];
    $tipos = "";

    if (!empty($_GET['buscar'])) {
        $condiciones[] = "(detalles LIKE ? OR ubicacion LIKE ?)";
        $params[] = "%" . $_GET['buscar'] . "%";
        $params[] = "%" . $_GET['buscar'] . "%";
        $tipos .= "ss";
    }

    if (!empty($_GET['estado'])) {
        $condiciones[] = "ubicacion LIKE ?";
        $params[] = "%" . $_GET['estado'] . "%";
        $tipos .= "s";
    }

    if (!empty($_GET['desde'])) {
        $condiciones[] = "fecha_publicacion >= ?";
        $params[] = $_GET['desde'];
        $tipos .= "s";
    }

    if (!empty($_GET['hasta'])) {
        $condiciones[] = "fecha_publicacion <= ?";
        $params[] = $_GET['hasta'];
        $tipos .= "s";
    }

    $sql = "SELECT * FROM eventos";
    if (!empty($condiciones)) {
        $sql .= " WHERE " . implode(" AND ", $condiciones);
    }
    $sql .= " ORDER BY fecha_publicacion DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($tipos, ...$params);
    }
    $stmt->execute();
    $eventos = $stmt->get_result();
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eventos</title>
    <link rel="stylesheet" href="Eventos.css">
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

<div class="evento-page">

    <!-- Contenido principal -->
    <div class="main-content">
        <header class="event-header">
            <button class="return-button" onclick="location.href='http://localhost/Geolocalizador/Menu/index.php'"></button>
            <h1>Cartelera de Eventos</h1>

            <div class="header-right">
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                    <a href="crud_eventos.php" class="crud-btn">✏️ Administrar</a>
                <?php endif; ?>
                </div>
        </header>

        <div class="caracas-time" id="caracasTime"></div>


        <div class="event-body">
            <!-- Lista de eventos -->
            <section class="event-list">
                <?php while ($evento = $eventos->fetch_assoc()): ?>
                    <div class="event-card">
                        <h3><?= htmlspecialchars($evento['detalles']) ?></h3>
                        <p><?= htmlspecialchars($evento['ubicacion']) ?> - <?= $evento['fecha_publicacion'] ?></p>
                    </div>
                <?php endwhile; ?>
            </section>

            <!-- Filtros -->
            <aside class="event-filters">
                <form method="GET">
                    <label for="buscar">Buscar Palabra Clave</label>
                    <div class="search-box">
                        <input type="text" name="buscar" id="buscar" placeholder="Buscar..." value="<?= $_GET['buscar'] ?? '' ?>">
                        <button type="submit">🔍</button>
                    </div>

                    <label for="estado">Estado</label>
                    <select name="estado" id="estado">
                        <option value="">Selecciona...</option>
                        <!-- Opciones dinámicas si las tienes -->
                        <option value="Amazonas">Amazonas</option>
                        <option value="Anzoátegui">Anzoátegui</option>
                        <option value="Apure">Apure</option>
                        <option value="Aragua">Aragua</option>
                        <option value="Barinas">Barinas</option>
                        <option value="Bolívar">Bolívar</option>
                        <option value="Carabobo">Carabobo</option>
                        <option value="Cojedes">Cojedes</option>
                        <option value="Delta Amacuro">Delta Amacuro</option>
                        <option value="Distrito Capital">Distrito Capital</option>
                        <option value="Falcon">Falcón</option>
                        <option value="Guarico">Guárico</option>
                        <option value="Lara">Lara</option>
                        <option value="Mérida">Mérida</option>
                        <option value="Miranda">Miranda</option>
                        <option value="Monagas">Monagas</option>
                        <option value="Nueva Esparta">Nueva Esparta</option>
                        <option value="Portuguesa">Portuguesa</option>
                        <option value="Sucre">Sucre</option>
                        <option value="Tachira">Táchira</option>
                        <option value="Trujillo">Trujillo</option>
                        <option value="Yaracuy">Yaracuy</option>
                        <option value="Zulia">Zulia</option>
                    </select>

                    <label>Rango de Fecha</label>
                    <input type="date" name="desde" id="desde" value="<?= $_GET['desde'] ?? '' ?>">
                    <input type="date" name="hasta" id="hasta" value="<?= $_GET['hasta'] ?? '' ?>">
                </form>
            </aside>
        </div>
    </div>
</div>

<script>
    function actualizarHoraCaracas() {
        const fechaHora = new Date();
        const opcionesHora = {
            timeZone: 'America/Caracas',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };

        const opcionesFecha = {
            timeZone: 'America/Caracas',
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };

        const horaCaracas = new Intl.DateTimeFormat('es-VE', opcionesHora).format(fechaHora);
        const fechaCaracas = new Intl.DateTimeFormat('es-VE', opcionesFecha).format(fechaHora);

        document.getElementById('caracasTime').textContent = `Hora Caracas: ${horaCaracas} — ${fechaCaracas}`;
    }

    setInterval(actualizarHoraCaracas, 1000);
    actualizarHoraCaracas();
</script>


</body>
</html>

<?php $conn->close(); ?>

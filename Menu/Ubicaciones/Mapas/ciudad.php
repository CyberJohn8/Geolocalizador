<?php
session_start();
include 'conexion.php';

// Validar parámetros
if (!isset($_GET['ciudad']) || !isset($_GET['estado'])) {
    die("Parámetros inválidos. No se recibió ciudad o estado.");
}

// Obtener y procesar parámetros
$estado = $_GET['estado'];
$ciudadesParam = $_GET['ciudad'];
$ciudades = array_map('trim', explode(',', $ciudadesParam));

// Crear placeholders
$placeholders = implode(',', array_fill(0, count($ciudades), '?'));

// Consulta SQL
$sql = "SELECT * FROM iglesias WHERE estado = ? AND ciudad IN ($placeholders)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conn->error);
}

// Construir parámetros dinámicamente
$params = array_merge([$estado], $ciudades);
$types = str_repeat('s', count($params));
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();
$datos_iglesias = $result->fetch_all(MYSQLI_ASSOC);

// Mapas por ciudad
$mapa_ciudades = [
    'Valencia' => [
        'img' => 'IMG/ciudades/valencia.png',
        'botones' => [
            ['texto' => 'AV. ANZOATEGUI', 'top' => '40%', 'left' => '40%'],
            ['texto' => 'BÁRBULA', 'top' => '60%', 'left' => '55%'],
            ['texto' => 'BELLA VISTA I', 'top' => '75%', 'left' => '30%'],
            ['texto' => 'BELLO MONTE II', 'top' => '6%', 'left' => '50%'],
            ['texto' => 'COMUNIDAD SIMÓN BOLÍVAR', 'top' => '12%', 'left' => '50%'],
            ['texto' => 'FLOR AMARILLO', 'top' => '18%', 'left' => '60%'],
            ['texto' => 'FUNDACIÓN CAP', 'top' => '20%', 'left' => '40%'],
            ['texto' => 'GONZALEZ PLAZA', 'top' => '22%', 'left' => '56%'],
            ['texto' => 'LA BOCAINA', 'top' => '50%', 'left' => '30%'],
            ['texto' => 'LA FLORIDA', 'top' => '40%', 'left' => '20%'],
            ['texto' => 'PARCELAS II DEL SOCORRO', 'top' => '66%', 'left' => '44%'],
            ['texto' => 'LOS GUAYOS', 'top' => '35%', 'left' => '40%'],
            ['texto' => 'LOS MANGUITOS', 'top' => '30%', 'left' => '20%'],
            ['texto' => 'TARAPIO', 'top' => '60%', 'left' => '30%'],
            ['texto' => 'PRIMERO DE MAYO', 'top' => '26%', 'left' => '32%'],
            ['texto' => 'SAN DIEGO', 'top' => '66%', 'left' => '28%'],
        ]
    ],
    'Puerto Cabello' => [
        'img' => 'IMG/ciudades/puerto_cabello.png',
        'botones' => [
            ['texto' => 'EL CAMBUR', 'top' => '54%', 'left' => '26%'],
            ['texto' => 'BARTOLOMÉ SALOM', 'top' => '50%', 'left' => '60%'],
            ['texto' => 'BORBURATA', 'top' => '36%', 'left' => '68%'],
            ['texto' => 'COLINAS DE SANTA CRUZ', 'top' => '27%', 'left' => '48%'],
            ['texto' => 'EL FORTÍN', 'top' => '22%', 'left' => '56%'],
            ['texto' => 'EL MILAGRO', 'top' => '17%', 'left' => '44%'],
            ['texto' => 'EL PALITO', 'top' => '14%', 'left' => '22%'],
            ['texto' => 'LA LIBERTAD', 'top' => '20%', 'left' => '34%'],
            ['texto' => 'LA SORPRESA', 'top' => '12%', 'left' => '36%'],
            ['texto' => 'MORILLO', 'top' => '8%', 'left' => '28%'],
            ['texto' => 'VALLE SECO', 'top' => '32%', 'left' => '60%'],
            ['texto' => 'CALLE SUCRE', 'top' => '14%', 'left' => '56%'],
            ['texto' => 'SAN ESTEBAN PUEBLO ', 'top' => '42%', 'left' => '54%'],
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars(implode(', ', $ciudades)) ?> - Iglesias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="ciudades.css">
</head>
<body>
     <!-- MENú LATERAL CON ÍCONOS -->
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
            <a href="javascript:history.back()" class="btn-volver"></a>
            <h1><?= htmlspecialchars(implode(', ', $ciudades)) ?>, <?= htmlspecialchars($estado) ?></h1>
        </header>

        <?php foreach ($ciudades as $ciudadItem): ?>
            <?php if (isset($mapa_ciudades[$ciudadItem])): ?>
                <div class="mapa-contenedor-con-leyenda">
                    <div class="mapa-container">
                        <img src="<?= $mapa_ciudades[$ciudadItem]['img'] ?>" alt="Mapa de <?= htmlspecialchars($ciudadItem) ?>" class="mapa-ciudad">
                        <?php foreach ($mapa_ciudades[$ciudadItem]['botones'] as $btn): ?>
                            <button class="btn-mapa" style="top: <?= $btn['top'] ?>; left: <?= $btn['left'] ?>;" onclick="filtrarPorZona('<?= $btn['texto'] ?>')">
                                <?= $btn['texto'] ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Leyenda a la derecha -->
                    <div class="leyenda-interactiva">
                        <h3>Leyenda de <?= htmlspecialchars(implode(', ', $ciudades)) ?></h3>
                        <ul>
                        <?php foreach ($mapa_ciudades[$ciudadItem]['botones'] as $btn): ?>
                            <li>
                                <button onclick="filtrarPorZona('<?= $btn['texto'] ?>')">
                                    <?= isset($btn['leyenda']) ? $btn['leyenda'] : $btn['texto'] ?>
                                </button>
                            </li>
                        <?php endforeach; ?>

                        </ul>
                    </div>
                </div>

            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- MODAL DETALLES -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h3>Detalles de la Asamblea</h3>
            <p><strong>Asamblea:</strong> <span id="det-asamblea"></span></p>
            <p><strong>Número:</strong> <span id="det-numero"></span></p>
            <p><strong>Ciudad:</strong> <span id="det-ciudad"></span></p>
            <p><strong>Estado:</strong> <span id="det-estado"></span></p>
            <p><strong>Dirección:</strong> <span id="det-direccion"></span></p>
            <ul>
                <li><strong>Domingo:</strong> <span id="det-domingo"></span></li>
                <li><strong>Lunes:</strong> <span id="det-lunes"></span></li>
                <li><strong>Martes:</strong> <span id="det-martes"></span></li>
                <li><strong>Miércoles:</strong> <span id="det-miercoles"></span></li>
                <li><strong>Jueves:</strong> <span id="det-jueves"></span></li>
                <li><strong>Viernes:</strong> <span id="det-viernes"></span></li>
                <li><strong>Sábado:</strong> <span id="det-sabado"></span></li>
            </ul>
            <p><strong>Obras:</strong> <span id="det-obras"></span></p>
            <p><strong>Google Maps:</strong> 
                <a id="det-mapa" href="#" target="_blank" class="btn-mapa">
                    <i class="fas fa-map"></i> Ver ubicación
                </a>
            </p>
        </div>
    </div>

    <script>
        const datosIglesias = <?= json_encode($datos_iglesias, JSON_UNESCAPED_UNICODE) ?>;

        function normalizar(texto) {
            return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim().toLowerCase();
        }

        function filtrarPorZona(zona) {
            const zonaNormalizada = normalizar(zona);

            const iglesia = datosIglesias.find(ig =>
                normalizar(ig.asamblea).includes(zonaNormalizada)
            );

            if (iglesia) {
                mostrarDetalles(iglesia);
            } else {
                alert("No se encontraron datos para la asamblea: " + zona);
            }
        }

        function mostrarDetalles(data) {
            document.getElementById("det-asamblea").textContent = data.asamblea;
            document.getElementById("det-numero").textContent = data.numero;
            document.getElementById("det-ciudad").textContent = data.ciudad;
            document.getElementById("det-estado").textContent = data.estado;
            document.getElementById("det-direccion").textContent = data.direccion;
            document.getElementById("det-domingo").textContent = data.domingo;
            document.getElementById("det-lunes").textContent = data.lunes;
            document.getElementById("det-martes").textContent = data.martes;
            document.getElementById("det-miercoles").textContent = data.miercoles;
            document.getElementById("det-jueves").textContent = data.jueves;
            document.getElementById("det-viernes").textContent = data.viernes;
            document.getElementById("det-sabado").textContent = data.sabado;
            document.getElementById("det-obras").textContent = data.obras;
            document.getElementById("det-mapa").href = data.GoogleMaps;
            document.getElementById("modal").style.display = "block";
        }

        function cerrarModal() {
            document.getElementById("modal").style.display = "none";
        }
    </script>
</body>
</html>
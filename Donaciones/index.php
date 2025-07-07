<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "instituciones");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener tipos únicos
$tipos = [];
$resultadoTipos = $conexion->query("SELECT DISTINCT Tipo_Institucion FROM instituciones ORDER BY Tipo_Institucion ASC");
if ($resultadoTipos && $resultadoTipos->num_rows > 0) {
    while ($fila = $resultadoTipos->fetch_assoc()) {
        $tipos[] = $fila['Tipo_Institucion'];
    }
}

// AJAX: instituciones por tipo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['getInstituciones']) && isset($_POST['tipo'])) {
    $tipo = $conexion->real_escape_string($_POST['tipo']);
    $res = $conexion->query("SELECT id, institucion FROM instituciones WHERE Tipo_Institucion = '$tipo' ORDER BY institucion ASC");
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

// AJAX: datos de institución
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['getDatos']) && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    $res = $conexion->query("SELECT banco, telefono, ci_rif, director, Correo FROM instituciones WHERE id = $id LIMIT 1");
    $data = $res->fetch_assoc();
    echo json_encode($data);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Donaciones por Institución</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AenfiCmVkBjABTrDQ2aERyHXJcEWOflaeX5P_eNIdk_tBPPucmQzjjcvwkYH2azhJTyAyvXM-ghnY_QU&currency=USD"></script>
    
    <link rel="stylesheet" href="Donaciones.css">
    <style>
        .main-content { margin-left: 220px; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .pago-datos, .metodos { background: #f9f9f9; border-left: 4px solid #0a74da; padding: 15px; margin-top: 20px; display: none; }
        label { font-weight: bold; }
        select, input { padding: 6px; width: 100%; margin-bottom: 15px; }
        .quick-buttons button { margin: 5px 5px 0 0; padding: 8px 12px; }
    </style>
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



<div class="main-content">
    <div class="header">
        <div class="header-left">
            <button onclick="history.back()" class="return-button" title="Volver"></button>
        </div>
        <div class="header-center">
            <h1>Donaciones para las Instituciones en Venezuela</h1>
        </div>
    </div>

    <div class="content">
        <h2>Selecciona una Institución</h2>
        <?php if (isset($_SESSION['user_id']) && in_array($_SESSION['rol'], ['usuario', 'admin'])): ?>
        <form id="donacion-form">
            <label for="moneda">Moneda:</label>
            <select id="moneda" onchange="actualizarMetodo()">
                <option value="VES" selected>Bolívares (VES)</option>
                <option value="USD">Dólares (USD)</option>
            </select>

            <div id="seleccion-institucion">
                <label for="tipo">Tipo de institución:</label>
                <select id="tipo" name="tipo" onchange="cargarInstituciones()" required>
                    <option value="">-- Selecciona un tipo --</option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($tipo) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="institucion">Institución:</label>
                <select id="institucion" name="institucion" onchange="mostrarDatos()" required>
                    <option value="">-- Selecciona una institución --</option>
                </select>
            </div>

            <!--<label for="monto">Monto:</label>
            <input type="number" id="monto" name="monto" min="1" required>

            <div class="quick-buttons">
                <p>Suma rápida:</p>
                <button type="button" onclick="setMonto(5)">+5</button>
                <button type="button" onclick="setMonto(10)">+10</button>
                <button type="button" onclick="setMonto(20)">+20</button>
                <button type="button" onclick="setMonto(50)">+50</button>
            </div>-->
        </form>

        <div id="datos-donacion" class="pago-datos">
            <h3>Datos para Donar:</h3>
            <p><strong>Banco:</strong> <span id="banco"></span></p>
            <p><strong>Teléfono:</strong> <span id="telefono"></span></p>
            <p><strong>CI/RIF:</strong> <span id="ci_rif"></span></p>
            <p><strong>Director:</strong> <span id="director"></span></p>
            <p><strong>Correo:</strong> <span id="correo"></span></p>
        </div>

        <div id="paypal-button-container" class="metodos"></div>

        <?php else: ?>
            <p>Por favor inicie sesión como usuario o administrador para acceder a las opciones de donación.</p>
        <?php endif; ?>
    </div>
</div>
<script>
    function cargarInstituciones() {
        const tipo = document.getElementById('tipo').value;
        const institucion = document.getElementById('institucion');
        institucion.innerHTML = '<option value="">Cargando...</option>';

        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'getInstituciones=1&tipo=' + encodeURIComponent(tipo)
        })
        .then(res => res.json())
        .then(data => {
            institucion.innerHTML = '<option value="">-- Selecciona una institución --</option>';
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.institucion;
                institucion.appendChild(option);
            });
            document.getElementById('datos-donacion').style.display = 'none';
        });
    }

    function mostrarDatos() {
        const id = document.getElementById('institucion').value;
        if (!id) return;
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'getDatos=1&id=' + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('banco').textContent = data.banco || 'N/D';
            document.getElementById('telefono').textContent = data.telefono || 'N/D';
            document.getElementById('ci_rif').textContent = data.ci_rif || 'N/D';
            document.getElementById('director').textContent = data.director || 'N/D';
            document.getElementById('correo').textContent = data.Correo || 'N/D';
            document.getElementById('datos-donacion').style.display = 'block';
            actualizarMetodo();
        });
    }

    function actualizarMetodo() {
        const moneda = document.getElementById('moneda').value;
        const seccionInstitucion = document.getElementById('seleccion-institucion');
        document.getElementById('paypal-button-container').style.display = (moneda === 'USD') ? 'block' : 'none';
        document.getElementById('pago-movil').style.display = (moneda !== 'USD') ? 'block' : 'none';
        seccionInstitucion.style.display = (moneda !== 'USD') ? 'block' : 'none';
    }

    function setMonto(valor) {
        const input = document.getElementById('monto');
        input.value = (parseInt(input.value) || 0) + valor;
    }

    paypal.Buttons({
        createOrder: function(data, actions) {
            const monto = document.getElementById('monto').value || '1';
            return actions.order.create({
                purchase_units: [{ amount: { value: monto } }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                alert('Gracias por tu donación, ' + details.payer.name.given_name + '!');
            });
        }
    }).render('#paypal-button-container');
</script>
</body>
</html>
<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
// Incluir archivo de conexión (mysqli)
//require "https://directorioasambleasvzla.com/conexion.php"; // tu archivo de conexión
require __DIR__ . "/../../conexion.php";
 // tu archivo de conexión

//$conn = new mysqli("localhost", "root", "", "directorio");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Obtener tipos únicos
$tipos = [];
$resultadoTipos = $conn->query("SELECT DISTINCT Tipo_Institucion FROM instituciones ORDER BY Tipo_Institucion ASC");
if ($resultadoTipos && $resultadoTipos->num_rows > 0) {
    while ($fila = $resultadoTipos->fetch_assoc()) {
        $tipos[] = $fila['Tipo_Institucion'];
    }
}

// AJAX: instituciones por tipo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['getInstituciones']) && isset($_POST['tipo'])) {
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $res = $conn->query("SELECT id, institucion FROM instituciones WHERE Tipo_Institucion = '$tipo' ORDER BY institucion ASC");
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
    $res = $conn->query("SELECT banco, telefono, ci_rif, director, Correo FROM instituciones WHERE id = $id LIMIT 1");
    $data = $res->fetch_assoc();
    echo json_encode($data);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donaciones por Institución</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AenfiCmVkBjABTrDQ2aERyHXJcEWOflaeX5P_eNIdk_tBPPucmQzjjcvwkYH2azhJTyAyvXM-ghnY_QU&currency=USD"></script>
    
    <link rel="stylesheet" href="Donaciones.css">
    <link rel="stylesheet" href="modo-telefono.css">

    <style>
        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&display=swap');

        @import url('https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap');

        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&family=Oleo+Script&display=swap');

        @import url('https://fonts.googleapis.com/css2?family=Sansation&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap');







        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&family=Oleo+Script&display=swap');

        /* === FONDO CON CAPAS === */
        body {
        font-family: 'Sansation', sans-serif;
        margin: 0;
        height: 200vh;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        }

        body::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('https://directorioasambleasvzla.com/iconos/Fonfo_Mapa_Color.png');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: top;
        opacity: 0.7;
        z-index: -1;
        }

        /* === CONTENIDO PRINCIPAL === */
        .main-content {
        padding: 20px;
        max-width: 1000px;
        margin: 0;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-sizing: border-box;
        border-radius: 8px;
        z-index: 1;

        margin-top: 20px;
        }

        /* === HEADER === */
        .header {
        display: flex;
        align-items: center;
        justify-content: space-around;
        padding: 10px 10px;
        width: 100%;
        box-sizing: border-box;
        position: relative;
        z-index: 2;
        }


        .header-center h1 {
        margin: 0;
        font-family: 'Oleo Script', cursive;
        font-weight: normal;
        font-size: 38px;
        color: #2a3e42;
        }

        /* === BOTÓN DE RETORNO === */
        .return-button {
        width: 40px;
        height: 40px;
        background-image: url('https://directorioasambleasvzla.com/iconos/Retorno.png');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        border: none;
        background-color: transparent;
        cursor: pointer;
        transition: transform 0.3s ease;
        margin-right: 20px;
        }

        .return-button:hover {
        transform: scale(1.2);
        }




        .content {
        background: #A2B0BE;
        border-radius: 10px;

        max-width: 800px;
        }



        html, body {
        height: 100%;
        scroll-behavior: smooth;
        }

        /* Eliminar scroll visible del body y html */
        html, /* Oculta la barra de scroll vertical, pero permite hacer scroll */
        body {
            overflow-y: scroll; /* asegura que el scroll siga funcionando */
            scrollbar-width: none; /* para Firefox */
        }

        /* Para navegadores WebKit como Chrome, Edge, Safari */
        body::-webkit-scrollbar {
            width: 0px;
            background: transparent; /* opcional, por si quieres un fondo invisible */
        }


        .contenido {
            overflow-y: scroll;
            scrollbar-width: none;
        }

        .contenido::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }





        /* === TÍTULO SECUNDARIO === */
        h2 {
        text-align: center;
        margin-top: 0;
        font-family: 'Sansation', sans-serif;
        font-size: 24px;
        color: #2a3e42;
        }

        /* === FORMULARIO === */
        form {
        max-width: 800px;
        margin: auto;
        text-align: left;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.4);
        box-sizing: border-box;
        }

        label {
        font-weight: bold;
        display: block;
        margin-top: 10px;
        color: #192E2F;
        }

        select,
        input[type='number'] {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
        }

        /* === BOTONES RÁPIDOS (si usas) === */
        .quick-buttons button {
        margin: 5px 5px 0 0;
        padding: 8px 12px;
        font-weight: bold;
        border-radius: 6px;
        background-color: #5a7684;
        color: #fff;
        border: none;
        cursor: pointer;
        }

        .quick-buttons button:hover {
        background-color: #3b5c68;
        }

        /* === BLOQUES DE INFORMACIÓN === */
        .pago-datos,
        .metodos {
        border-left: 4px solid #0a74da;
        padding: 15px;
        margin-top: 20px;
        display: none;
        border-radius: 8px;
        }

        .pago-datos h3,
        .metodos h3 {
        margin-top: 0;
        font-family: 'Rakkas', cursive;
        font-size: 20px;
        color: #192E2F;
        }
























        @media screen and (max-width: 600px) {
        /* === ESTILOS GENERALES === */
        body {
            margin: 0;
            padding: 10px;
            height: auto;
            font-family: 'Sansation', sans-serif;
            background-image: url('https://directorioasambleasvzla.com/iconos/Fondo_Mapa_Tlf.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center top;
            overflow-x: hidden;
        }

        /* === CONTENEDOR PRINCIPAL === */
        .main-content {
            width: 100%;
            padding: 15px;
            margin: 0 auto;
            box-sizing: border-box;


            margin-top: 40px;
        }


        /* === ENCABEZADO === */
        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            text-align: center;
            padding: 10px 0;
        }

        .header .header-center h1 {
            font-size: 26px;
        }

        .header-left {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .header-center {
            width: 100%;
        }

        .header-center h1 {
            font-family: 'Oleo Script', cursive;
            font-size: 22px;
            margin: 0;
            line-height: 1.3;
            color: #2a3e42;
        }

        .return-button {
            display: none !important;
            /*
            width: 36px;
            height: 36px;
            background-image: url('https://directorioasambleasvzla.com/iconos/Retorno.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            background-color: transparent;
            border: none;
            cursor: pointer;/**/
        }

        /* === CONTENIDO === */
        .content {
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .content h2 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #2a3e42;
            text-align: center;
        }

        /* === FORMULARIO === */
        form {
            width: 100%;
            padding: 10px 0;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }

        select,
        input[type='number'] {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        /* === BOTONES DE MONTO RÁPIDO === */
        .quick-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-top: 10px;
        }

        .quick-buttons button {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 6px;
            border: none;
            background-color: #5a7684;
            color: #EAE4D5;
            cursor: pointer;
        }

        /* === DATOS DE DONACIÓN Y PAYPAL === */
        .pago-datos,
        .metodos {
            margin-top: 20px;
            padding: 12px;
            font-size: 14px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .pago-datos h3,
        .metodos h3 {
            font-family: 'Rakkas', cursive;
            font-size: 18px;
            margin: 0 0 10px 0;
            color: #2a3e42;
        }

        .pago-datos p {
            margin: 5px 0;
            font-size: 14px;
        }

        }

        

























































































        /*==============================================
        =========EXCLUSIVO PARA DONACIONES==============
        ==============================================*/



        /* Menú por defecto: vertical (computadora) */
        .menu-nav {
        position: fixed;
        top: 0;
        left: 0;
        width: 100px;
        height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: start;
        padding-top: 0px;
        z-index: 1001;
        background-color: #637983;
        }

        /* Botones del menú *
        .icon-btn {
        background: none;
        border: none;
        margin: 10px 0;
        padding: 5px;
        cursor: pointer;
        }

        .icon-btn img {
        width: 28px;
        height: 28px;
        }/**/



        /* === SIDEBAR === */
        /* Botón hamburguesa (puedes ocultarlo si ya hay barra de íconos) */
        .menu-toggle {
        display: none;
        }

        /* Barra lateral con íconos */
        .icon-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100px;
        height: 100vh;
        background-color: #637983;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 5px;
        z-index: 1003;
        }

        .icon-btn {
        background: none;
        border: none;
        margin: 5px 0;
        cursor: pointer;
        padding: 5px;
        width: 100%;
        transition: background 0.3s;
        }

        .icon-btn:hover {
        background-color: #2a3e42;
        }

        .icon-btn img {
        width: auto;
        height: 70%;
        /*filter: invert(1);/**/
        }

        /* Panel emergente */
        .sidebar {
        position: fixed;
        top: 0;
        left: -300px;
        width: 250px;
        height: 100%;
        background-color: transparent/*#263C3EA6/**/;
        color: #EAE4D5;
        padding: 20px;
        z-index: 1004;
        transition: left 0.3s ease;
        }

        .sidebar.active {
        left: 100px; /* aparece justo al lado del menú de íconos */
        }

        .sidebar h2 {
        font-family: 'Oleo Script', cursive;
        color: #EAE4D5;
        
        font-weight: normal;
        font-size: 38px;
        }

        .sidebar a {
        color: #EAE4D5;
        text-decoration: none;
        display: block;
        margin: 10px 0;
        padding: 10px;
        border-radius: 5px;

        
        font-weight: normal;
        font-size: 24px;
        }

        .sidebar a:hover {
        background-color: #2a3e42;
        }

        .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1002;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        }

        .overlay.active {
        opacity: 1;
        pointer-events: auto;
        }

        .sidebar .close-btn {
        padding: 15px 25px;
        border: none;
        /*border-radius: 10px;/**/
        font-size: 16px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        cursor: pointer;
        transition: background 0.3s;

        border-radius: 15px 15px 40px 15px;
        margin: 10px;
        }

        .sidebar .close-btn:hover {
        transform: scale(1.25);
        }



        /*==================/DISEÑO PARA CELULARES 768PX/==================*/
        /* RESPONSIVE */
        @media (max-width: 768px) {
        /*==================/MENÚ LATERAL/==================*/
        .menu-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 55px;
            display: flex;
            flex-direction: row;
            justify-content: space-between; /* ← Cambiado para mover extremos */    
            align-items: center;
            padding: 0 0;
            z-index: 1001;

            border-radius: 0 0 20px 20px;
        }

        .icon-btn {
            width: auto;
            height: 80%;
        }

        .icon-btn img {
            width: 32px;
            height: 32px;
            /*filter: invert(1);/**/
        }

        .solo-pc {
            display: none !important;
        }





        /*==================/MENÚ EMERGENTE OCULTO/==================*/
        /* Panel emergente */
        .sidebar {
            left: -300px;
            width: 250px;
            height: 100%;
            background-color: #637983/*transparent/*#263C3EA6*/;
            color: #EAE4D5;
            padding: 20px;
        }

        .sidebar.active {
            left: 0px; /* aparece justo al lado del menú de íconos */
        }

        .sidebar h2 {
            font-family: 'Oleo Script', cursive;
            color: #EAE4D5;
            
            font-weight: normal;
            font-size: 26px;
        }

        .sidebar a {
            color: #EAE4D5;
            border-radius: 0px;
            color: #EAE4D5;        
            font-weight: normal;
            font-size: 16px;

            border-bottom: 1px solid #263C3E;
        }

        .sidebar a:hover {
            background-color: #2a3e42;
        }



        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1002;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .sidebar .close-btn {    
            border-radius: 45px 15px 45px 15px;
            background-color: #A2B0BE;
            color: #192E2F;
        }

        .sidebar .close-btn:hover {
            transform: scale(1.25);
            background-color: #192E2F;
            color: #A2B0BE;
        }
        }







        /*==================/DISEÑO PARA CELULARES 280PX/==================*/
        /* RESPONSIVE */
        @media (max-width: 280px) {
        /*==================/MENÚ LATERAL/==================*/
        /*==================/MENÚ LATERAL/==================*/
        .menu-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 55px;
            display: flex;
            flex-direction: row;
            justify-content: space-between; /* ← Cambiado para mover extremos */    
            align-items: center;
            padding: 0 0;
            z-index: 1001;

            border-radius: 0 10px 0 10px;
        }

        .icon-btn {
            width: auto;
            height: 80%;
        }

        .icon-btn img {
            width: 32px;
            height: 32px;
            /*filter: invert(1);/**/
        }

        .solo-pc {
            display: none !important;
        }





        /*==================/MENÚ EMERGENTE OCULTO/==================*/
        /* Panel emergente */
        .sidebar {
            left: -300px;
            width: 150px;
            height: 100%;
            background-color: #637983/*transparent/*#263C3EA6*/;
            color: #EAE4D5;
            padding: 20px;
        }

        .sidebar.active {
            left: 0px; /* aparece justo al lado del menú de íconos */
        }

        .sidebar h2 {
            font-family: 'Oleo Script', cursive;
            color: #EAE4D5;
            
            font-weight: normal;
            font-size: 26px;
        }

        .sidebar a {
            color: #EAE4D5;
            border-radius: 0px;
            color: #EAE4D5;        
            font-weight: normal;
            font-size: 16px;

            border-bottom: 1px solid #263C3E;
        }

        .sidebar a:hover {
            background-color: #2a3e42;
        }



        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1002;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .sidebar .close-btn {    
            border-radius: 45px 15px 45px 15px;
            background-color: #A2B0BE;
            color: #192E2F;
        }

        .sidebar .close-btn:hover {
            transform: scale(1.25);
            background-color: #192E2F;
            color: #A2B0BE;
        }
        }









        @media (max-width: 360px) {
        .sidebar {
            width: 200px; /* Más delgado en pantallas pequeñas */
        }

        .sidebar h2 {
            font-size: 20px;
        }

        .sidebar a {
            font-size: 14px;
        }
        }



    </style>
</head>
<body>

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
                    // La navegación normal funciona, pero podemos monitorear
                });
            });
            
            // Verificar modo PWA
            if (window.matchMedia('(display-mode: standalone)').matches) {
                console.log('Modo PWA activado - Rutas relativas funcionando');
            }
        });
    </script>



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
            <!--<label for="moneda">Moneda:</label>
            <select id="moneda" onchange="actualizarMetodo()">
                <option value="VES" selected>Bolívares (VES)</option>
                <option value="USD">Dólares (USD)</option>
            </select>-->

            <!-- SECCIÓN QUE SE OCULTA CON DÓLARES -->
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
        </form>

        <!-- DATOS PARA TRANSFERENCIA / PAGO MÓVIL -->
        <div id="datos-donacion" class="pago-datos">
            <h3>Datos para Donar:</h3>
            <p><strong>Banco:</strong> <span id="banco"></span></p>
            <p><strong>Teléfono:</strong> <span id="telefono"></span></p>
            <p><strong>CI/RIF:</strong> <span id="ci_rif"></span></p>
            <p><strong>Director:</strong> <span id="director"></span></p>
            <p><strong>Correo:</strong> <span id="correo"></span></p>
        </div>

        
        <!-- BOTÓN DE PAYPAL SOLO PARA USD -->
        <div id="paypal-button-container" class="metodos"></div>

        <!-- Nota para quienes donan por PayPal -->
        <div id="paypal-nota" class="metodos" style="display: none; font-size: 14px; margin-top: 10px;">
            <h4>Nota: Esta cuenta de PayPal está bloqueada y no se tiene planeado conservar esta función en el 
                producto final, sólo está presente para que el proyecto cumpla los requisitos para la 
                Universidad, por lo que se agradece tener precaución y no transferir aquí.
            </h4>
        
            <p><strong>Nota:</strong> Después de realizar tu donación por PayPal, por favor escribe una notificación al correo: <a href="mailto:directorioasambleas@gmail.com">directorioasambleas@gmail.com</a></p>
        </div>


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
        });
    }

    function actualizarMetodo() {
        const moneda = document.getElementById('moneda').value;
        const paypalContainer = document.getElementById('paypal-button-container');
        const paypalNota = document.getElementById('paypal-nota');
        const seccionInstitucion = document.getElementById('seleccion-institucion');
        const datosDonacion = document.getElementById('datos-donacion');

        if (moneda === 'USD') {
            paypalContainer.style.display = 'block';
            paypalNota.style.display = 'block';
            seccionInstitucion.style.display = 'none';
            datosDonacion.style.display = 'none';
        } else {
            paypalContainer.style.display = 'none';
            paypalNota.style.display = 'none';
            seccionInstitucion.style.display = 'block';
        }
    }


    function setMonto(valor) {
        const input = document.getElementById('monto');
        input.value = (parseInt(input.value) || 0) + valor;
    }

    // PayPal SDK
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{ amount: { value: '10' } }] // Puedes reemplazar '10' por un input si lo deseas
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                alert('Gracias por tu donación, ' + details.payer.name.given_name + '!');
            });
        }
    }).render('#paypal-button-container');

    // Ejecutar al cargar la página
    window.addEventListener('DOMContentLoaded', actualizarMetodo);
</script>




</body>
</html>

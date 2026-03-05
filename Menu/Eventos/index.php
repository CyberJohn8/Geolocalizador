<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
// Incluir archivo de conexión (mysqli)
//require "https://directorioasambleasvzla.com/conexion.php"; // tu archivo de conexión
require __DIR__ . "/../../conexion.php"; // tu archivo de conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

date_default_timezone_set("America/Caracas");
?>



<?php
    // Incluir archivo de conexión (mysqli)
    //require "https://directorioasambleasvzla.com/conexion.php"; // tu archivo de conexión
    require __DIR__ . "/../../conexion.php";
 // tu archivo de conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");



    // Parámetros GET
    $buscar = $_GET['buscar'] ?? '';
    $desde = $_GET['desde'] ?? '';
    $hasta = $_GET['hasta'] ?? '';

    // Consulta base
    $sql = "SELECT * FROM eventos WHERE 1";

    // Filtro por palabra clave
    if (!empty($buscar)) {
        $buscar_sanitizado = $conn->real_escape_string($buscar);
        $sql .= " AND (detalles LIKE '%$buscar_sanitizado%' OR ubicacion LIKE '%$buscar_sanitizado%')";
    }

    // Filtro por fechas
    if (!empty($desde)) {
        $desde_sanitizado = $conn->real_escape_string($desde);
        $sql .= " AND fecha_publicacion >= '$desde_sanitizado'";
    }
    if (!empty($hasta)) {
        $hasta_sanitizado = $conn->real_escape_string($hasta);
        $sql .= " AND fecha_publicacion <= '$hasta_sanitizado 23:59:59'";
    }

    $sql .= " ORDER BY fecha_publicacion DESC";

    // Ejecutar consulta
    $eventos = $conn->query($sql);
    if (!$eventos) {
        die("Error al obtener eventos: " . $conn->error);
    }

    // Obtener el último evento (sin filtro)
    $ultimo_evento = $conn->query("SELECT id, fecha_publicacion FROM eventos ORDER BY fecha_publicacion DESC LIMIT 1");
    if ($ultimo_evento && $ultimo_evento->num_rows > 0) {
        $fila = $ultimo_evento->fetch_assoc();
        $id_ultimo = $fila['id'];
        $fecha_ultimo = $fila['fecha_publicacion'];

        // Si es la primera visita, guarda la sesión
        if (!isset($_SESSION['ultimo_evento_visto'])) {
            $_SESSION['ultimo_evento_visto'] = $id_ultimo;
        }

        // Si hay un evento más reciente al que ya vio el usuario, muestra notificación
        if ($id_ultimo > $_SESSION['ultimo_evento_visto']) {
            echo "<script>
                setTimeout(() => {
                    alert('¡Hay un nuevo evento publicado!');
                }, 500);
            </script>";

            // Actualiza el ID para que no se repita la alerta en la misma sesión
            $_SESSION['ultimo_evento_visto'] = $id_ultimo;
        }
    }
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <link rel="stylesheet" href="Eventos.css">


    <style>
        
        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&display=swap');

        @import url('https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap');

        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&family=Oleo+Script&display=swap');




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



        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&family=Oleo+Script&display=swap');

        /* === FONDO === */
        html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Sansation', sans-serif;
        overflow-x: hidden;
        overflow-y: auto;
        position: relative;
        }

        /* Fondo con imagen desenfocada */
        body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        /*background-image: url('https://directorioasambleasvzla.com/iconos/Fonfo_Mapa_Color.jpg');/**/
        background-image: url('https://directorioasambleasvzla.com/iconos/Fonfo_Mapa_Color.png');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: top center;
        opacity: 0.7;
        z-index: -1;
        }

        /* === LAYOUT GENERAL === */
        /* === LAYOUT GENERAL CENTRADO === */
        .evento-page {
            display: flex;
            flex-direction: column;       /* Asegura que el contenido se apile verticalmente si no hay sidebar */
            height: auto;
            min-height: 100vh;
            max-width: 760px;            /* Limita el ancho máximo */
            width: 90%;                   /* Ajusta el ancho base según pantalla */
            margin: 0 auto;               /* Centrado horizontal automático */
            font-weight: normal;

            /* Opcionales de estilo visual */
            /* box-shadow: 0 0 10px rgba(0,0,0,0.05); */
            /* background-color: rgba(255,255,255,0.8); */
            /* border-radius: 10px; */
        }
        



        /* === CONTENIDO PRINCIPAL === */
        .main-content {
        flex-grow: 1;
        padding: 20px;
        display: flex;
        flex-direction: column;
        }

        /* === ENCABEZADO === */
        .event-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        }

        .event-header h1 {
        font-family: 'Oleo Script', cursive;
        flex-grow: 1;
        text-align: center;
        
        font-weight: normal;
        font-size: 38px;
        color: #2a3e42;
        }

        .return-button {
        background: url('https://directorioasambleasvzla.com/iconos/Retorno.png') no-repeat center;
        background-size: contain;
        width: 40px;
        height: 40px;
        border: none;
        cursor: pointer;
        }

        .return-button:hover {
        transform: scale(1.25);
        }

        .user-icon img {
        width: 32px;
        }

        .header-right {
        display: flex;
        align-items: center;
        gap: 20px;
        }

        .crud-btn {
            background-color: #5a7684;
            color: #EAE4D5;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .crud-btn:hover {
            background-color: #3b5c68;
        }

        /*.caracas-time {
            font-size: 15px;
            color: #444;
            font-weight: bold;
            text-align: right;
            line-height: 1.4;
        }/**/





        .caracas-time {
        text-align: center; /* Si el contenido dentro es texto o inline */
        margin-top: 15px; /* Para darle un poco de espacio del header */
        /* Otros estilos que ya tengas */
        }

        /* === CONTENIDO DE LA PÁGINA === */
        .event-body {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 30px;
        }

        /* === LISTA DE EVENTOS === */
        .event-list {
        font-weight: normal;
        font-family: 'Sansation', sans-serif;


        
        /* Propiedades que ya tenías */
        flex: 2;
        display: flex;
        flex-direction: column;
        gap: 16px;
        overflow-y: auto;
        max-height: calc(100vh - var(--header-height, 150px) - var(--some-other-margin, 40px)); /* O tu altura específica */

        /* --- INICIO: Propiedades para hacer el scroll invisible --- */

        /* Para navegadores basados en Webkit (Chrome, Safari, Edge, Opera) */
        &::-webkit-scrollbar {
            width: 0; /* Ancho de la barra de desplazamiento vertical */
            height: 0; /* Altura de la barra de desplazamiento horizontal */
        }

        /* Para Firefox */
        scrollbar-width: none; /* 'none' para ocultar, 'thin' para una barra delgada */
        /* scrollbar-color: transparent transparent; /* Opcional: para asegurar que los "pulgares" también sean transparentes */

        /* Para Microsoft Edge (antiguo) y algunos otros */
        -ms-overflow-style: none; /* Elimina la barra de desplazamiento de IE/Edge (antiguo) */

        /* --- FIN: Propiedades para hacer el scroll invisible --- */
        }

        .event-card {
        background-color: #607d8b;
        color: #EAE4D5;
        border-radius: 12px;
        padding: 15px 20px;
        box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.1);

        
        font-weight: normal;
        font-family: 'Sansation', sans-serif;
        }

        .event-card h3 {
        margin: 0;
        font-size: 20px;


        
        font-weight: normal;
        font-family: 'Sansation', sans-serif;
        }

        .event-card p {
        font-size: 14px;
        margin-top: 5px;


        
        font-weight: normal;
        font-family: 'Sansation', sans-serif;
        }

        /* === FILTROS === */
        .event-filters {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 15px;
        min-width: 200px;
        }

        .event-filters label {
        font-weight: bold;
        color: #2a3e42;
        }

        .event-filters form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        }

        /* Caja de búsqueda */
        .search-box {
        display: flex;
        align-items: center;
        background-color: #607d8b;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .search-box input {
        border: none;
        padding: 10px;
        flex-grow: 1;
        background: none;
        color: #EAE4D5;
        }

        .search-box input[type="text"]::placeholder {
            color: #EAE4D5; /* Cambia este color si quieres algo más claro u oscuro */
            opacity: 1;     /* Asegura que no sea semitransparente */
        }

        .search-box button {
        background-color: #3b5c68;
        border: none;
        padding: 10px 12px;
        cursor: pointer;
        color: #EAE4D5;
        }

        /* Selects y fechas */
        .event-filters select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            position: relative;
            background-repeat: no-repeat;
            background-image: url('https://directorioasambleasvzla.com/iconos/Barrita.png'); /* ← reemplaza por tu imagen */
            background-position: right 12px center;
            background-size: 18px;
            cursor: pointer;


            color: #EAE4D5;
            border-radius: 10px;
        }

        .event-filters select,
        .event-filters input[type="date"] {
        padding: 10px;
        border: none;
        background-color: #607d8b;
        color: #EAE4D5;
        border-radius: 6px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        appearance: none;
        }

        .event-filters button {
        margin-top: 10px; 
        background-color: #607d8b;
        color: #EAE4D5;
        padding: 8px 12px; 
        border: none; 
        border-radius: 6px; 
        cursor: pointer;
        }

        .event-filters button:hover {
        background-color: #3b5c68;
        color: #EAE4D5;
        }
















        /* === ESTILOS RESPONSIVE PARA TELÉFONOS === */
        @media screen and (max-width: 768px) {
        body {
            width: 100%;
            margin: 0;
            padding: 0;
            background-image: url('https://directorioasambleasvzla.com/iconos/Fondo_Mapa_Tlf.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding-top: 20%; /* espacio para el menú */
            box-sizing: border-box;
        }

        /* === LAYOUT GENERAL CENTRADO === */
        .evento-page {
            display: flex;
            flex-direction: column;       /* Asegura que el contenido se apile verticalmente si no hay sidebar */
            height: auto;
            min-height: 100vh;
            max-width: 760px;            /* Limita el ancho máximo */
            width: 90%;                   /* Ajusta el ancho base según pantalla */
            margin: 0 auto;               /* Centrado horizontal automático */
            font-weight: normal;

            /* Opcionales de estilo visual */
            /* box-shadow: 0 0 10px rgba(0,0,0,0.05); */
            /* background-color: rgba(255,255,255,0.8); */
            /* border-radius: 10px; */
        }


        .main-content {
            margin-left: -0%;   /*-30%*/
            padding: 10px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .event-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .event-header h1 {
            font-size: 26px;
            margin: 10px 0;
        }

        .event-header .return-button {
            display: none !important;
        }

        .header-right {
            display: flex;
            justify-content: center;
            margin-top: 5px;

            font-size: 14px;
        }

        /* Mostrar botón de administrador en móviles */
        .header-right .crud-btn {
            display: inline-block !important;
        }

        .event-body {
            display: flex;
            flex-direction: column;
            padding: 0;
        }

        .event-filters {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        .event-filters form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .event-filters label {
            font-size: 14px;
            font-weight: bold;
        }

        .event-filters input,
        .event-filters select {
            width: 100%;
            padding: 8px;
            font-size: 14px;
        }

        .event-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .event-card {
            display: flex;
            flex-direction: column;
            padding: 15px;
            border-radius: 10px;
            background-color: #607d8b;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            text-align: left;
        }

        .event-card h3 {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .event-card p {
            font-size: 13px;
            margin: 0;
        }

        .caracas-time {
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
            color: #333;
        }
        }











        /* === BOTÓN DE FILTRO SOLO EN TELÉFONOS === */
        .filtro-toggle-btn {
        display: none;
        background-color: #5a7684;
        color: #EAE4D5;
        padding: 10px 20px;
        margin: 10px auto;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        }

        .filtro-toggle-btn:hover {
        background-color: #3b5c68;
        }

        /* === MODAL PARA FILTRO EN TELÉFONOS === */
        @media screen and (max-width: 768px) {
        .filtro-toggle-btn {
            display: block;

            margin-bottom: -40px;
        }

        .event-filters {
            display: none;
            position: fixed;
            top: 60px;
            left: 5%;
            width: 90%;
            background: #EEEFF1;
            z-index: 9999;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .event-filters.mostrar-filtros {
            display: block;
        }

        .event-filters form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .event-filters select,
        .event-filters input[type="date"],
        .event-filters input[type="text"] {
            width: 90%;
        }

        .event-filters .search-box {
            flex-direction: row;
            align-items: stretch;
        }

        .event-filters .search-box input {
            flex-grow: 1;
        }

        .event-filters .search-box button {
            padding: 10px;
        }
        }

        /* === OVERLAY DEL MODAL FILTRO EN TELÉFONOS === */
        .filtro-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.4);
        z-index: 9998;
        }

        .filtro-overlay.activo {
        display: block;
        }











    </style>
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




    <!-- Notificaciones 
    <div id="notificacion" class="notificacion">¡Nuevo evento publicado!</div>

    <style>
        .notificacion {
            display: none;
            position: fixed;
            top: 0; left: 50%;
            transform: translateX(-50%);
            background: #4caf50;
            color: white;
            padding: 15px 30px;
            border-radius: 0 0 10px 10px;
            z-index: 9999;
            font-weight: bold;
        }
    </style>

    <script>
        setTimeout(() => {
            const notif = document.getElementById("notificacion");
            if (notif) notif.style.display = "block";
        }, 800);
    </script>-->


<div class="evento-page">

    <!-- Contenido principal -->
    <div class="main-content">
        <header class="event-header">
            <button class="return-button" onclick="window.history.back()"></button>
            <h1>Cartelera de Eventos</h1>

            <div class="header-right">
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                    <a href="crud_eventos.php" class="crud-btn">Administrar</a>
                <?php endif; ?>
                </div>
        </header>

        <div class="caracas-time" id="caracasTime"></div>
            

        <div id="filtro-overlay" class="filtro-overlay" onclick="cerrarFiltro()"></div>
        <!-- Botón para abrir filtros en teléfonos -->
        <button class="filtro-toggle-btn" onclick="toggleFiltro()">Filtrar Eventos</button>


        <div class="event-body">
            <!-- Lista de eventos -->
            <section class="event-list">
                <?php while ($evento = $eventos->fetch_assoc()): ?>
                    <div class="event-card">
                        <div class="event-card-content">
                            <h3><?= htmlspecialchars($evento['detalles']) ?></h3>
                            <p><?= htmlspecialchars($evento['ubicacion']) ?></p>
                        </div>
                        <div class="event-card-date">
                            <?= date("d/m/Y", strtotime($evento['fecha_publicacion'])) ?><br>
                            <?= date("h:i A", strtotime($evento['fecha_publicacion'])) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>

            <!-- Filtros -->
            <aside class="event-filters">
                <form id="Filtros" method="GET">
                    <label for="buscar">Buscar Palabra Clave</label>
                    <div class="search-box">
                        <input type="text" name="buscar" id="buscar" placeholder="Buscar..." value="<?= $_GET['buscar'] ?? '' ?>">
                        <button type="submit">🔍</button>
                    </div>

                    <label>Rango de Fecha</label>
                    <input type="date" name="desde" id="desde" value="<?= $_GET['desde'] ?? '' ?>">
                    <input type="date" name="hasta" id="hasta" value="<?= $_GET['hasta'] ?? '' ?>">

                    <!-- Botón para limpiar los filtros -->
                    <button id="button_Limpiar" type="button_Limpiar_Filtros" onclick="window.location.href='<?= strtok($_SERVER["REQUEST_URI"], "?") ?>'">
                        Limpiar Filtros
                    </button>
                    
                    <!-- Script para el Botón para limpiar los filtros -->
                    <script>
                        //Obtener la referencias a los elementos
                        const BTNLimpiar = document.getElementById('button_Limpiar');
                        const formulario = document.getElementById('Filtros');

                        //Establecer el evento del Boton
                        BTNLimpiar.addEventListener('click', function(){
                            //Resetear Formulario para vaciar los campos
                            formulario.reset();
                            
                            //Vaciar input de fechas
                            document.getElementById('desde').value = '';
                            document.getElementById('hasta').value = '';
                        });
                    </script>
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

<script>
    function toggleFiltro() {
    const filtro = document.querySelector(".event-filters");
    const overlay = document.getElementById("filtro-overlay");

    filtro.classList.toggle("mostrar-filtros");
    overlay.classList.toggle("activo");
    }

    function cerrarFiltro() {
    document.querySelector(".event-filters")?.classList.remove("mostrar-filtros");
    document.getElementById("filtro-overlay")?.classList.remove("activo");
    }

    // Cerrar al enviar el formulario (modo móvil)
    document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".event-filters form");
    if (form) {
        form.addEventListener("submit", function () {
        cerrarFiltro();
        });
    }
    });
</script>

<?php $conn->close(); ?>

</body>
</html>


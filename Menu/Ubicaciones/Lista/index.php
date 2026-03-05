<?php
session_start();
// Incluir archivo de conexión
//require "https://directorioasambleasvzla.com/conexion.php"; // tu archivo de conexión
require __DIR__ . "/../../../conexion.php";
 // tu archivo de conexión

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT id, asamblea, numero, ciudad, estado, direccion, lunes, martes, miercoles, jueves, viernes, sabado, obras, GoogleMaps, Fehca_Fundacion FROM iglesias";
$result = $conn->query($sql);

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Iglesias</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="Lista.css">
    <script>
        function filtrarTabla() {
            let input = document.getElementById("buscador").value.toLowerCase();
            let columna = document.getElementById("columna").value;
            let filas = document.querySelectorAll("tbody tr");

            filas.forEach(fila => {
                let celdas = fila.getElementsByTagName("td");
                let valor = "";
                switch (columna) {
                    case "asamblea":
                        valor = celdas[0].innerText.toLowerCase();
                        break;
                    case "numero":
                        valor = celdas[1].innerText.toLowerCase();
                        break;
                    case "ciudad":
                        valor = celdas[2].innerText.toLowerCase();
                        break;
                    case "estado":
                        valor = celdas[3].innerText.toLowerCase();
                        break;
                    default:
                        valor = fila.innerText.toLowerCase();
                        break;
                }
                fila.style.display = valor.includes(input) ? "" : "none";
            });
        }
    </script>


    <style>
        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&display=swap');

        @import url('https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap');

        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&family=Oleo+Script&display=swap');







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




        /* === FONDO CON CAPAS === */
        /* Estilos generales */
        /* Estilos generales */
        body {
            font-family: 'Sansation', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        /* Estilos generales */
        body::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://directorioasambleasvzla.com/iconos/Fonfo_Mapa_Color.png'); /* tu imagen */
            background-size: cover; /* Mostrar la imagen completa */
            background-repeat: no-repeat;
            background-position: top;
            opacity: 0.7;
            z-index: -1;
            
            justify-content: center;
            align-items: center;
            font-family: 'Sansation', sans-serif;
            display: flex;
            margin: 0;
            justify-content: center;
            align-items: center;
        }


        /* Asegura que el contenido esté por encima del overlay */
        .container, .contenido, form {
            position: relative;
            z-index: 1;
            color: #EAE4D5;
        }

        .contento {
            margin-left: 100px;
            padding: 20px;
        }

        /* ---------- ENCABEZADO ---------- */
        header {
            position: relative;
            /*background: transparent; /*  */
            /*backdrop-filter: blur(8px); /* Difumina el fondo detrás */
            /*-webkit-backdrop-filter: blur(8px);/**/
            padding: 15px 30px;
            max-width: 90%;
            color: #192E2FD9;
            display: flex;
            justify-content: space-between;
            z-index: 2;
            font-family: 'Oleo Script', sans-serif;
            text-align: center;
        }

        header h1 {
            font-family: 'Oleo Script', cursive;
            font-weight: normal;
            font-size: 38px;
            color: #2a3e42;


            display: flex;
            flex-direction: column;
            align-items: center; /* Centra los elementos hijos horizontalmente */
            margin: 0 auto; /* Centra el contenedor .contenido en la página */
            box-sizing: border-box; /* Asegura que el padding no afecte el ancho total */
            border-radius: 8px;
        }

        header .return-button {
            width: 35px;
            height: 35px;
            background-image: url('https://directorioasambleasvzla.com/iconos/Retorno.png'); /* Asegúrate de que la ruta sea correcta */
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border: none;
            cursor: pointer;
            z-index: 1003;
            background-color: transparent;
            
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            font-family: 'Oleo Script', sans-serif;
            cursor: pointer;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        header button:hover {
            transform: scale(1.25);
        }











































        /* ----- CONTENEDOR DEL BUSCADOR Y FILTROS ----- */
        .filtros-container {
            width: 90%;
            margin: 30px auto 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            font-family: 'Sansation', sans-serif;
            color: #EAE4D5;
        }

        /* ----- SELECT Y INPUT ----- */
        .filtros-container select,
        .filtros-container input[type="text"] {
            padding: 12px 16px;
            font-size: 15px;
            /*border: 1px solid #ccc;/**/
            border-radius: 8px;
            background-color: #637983;
            /*box-shadow: 0 2px 6px rgba(0,0,0,0.1);/**/
            transition: all 0.3s ease;
            min-width: 220px;


            color: #EAE4D5;
            height: 100%;
        }



        .filtros-container select,
        .filtros-container {
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

        .filtros-container input[type="text"] {
            flex: 1;
            max-width: 500px;
            height: 50%;


            color: #EAE4D5;
            border-radius: 10px;
        }

        .filtros-container select:focus,
        .filtros-container input[type="text"]:focus {
            outline: none;

            
            color: #EAE4D5;
        }

        .filtros-container input[type="text"]::placeholder {
            color: #EAE4D5; /* Cambia este color si quieres algo más claro u oscuro */
            opacity: 1;     /* Asegura que no sea semitransparente */
        }

        /* Escondido por defecto pero con estilo si se activa */
        #selector-estado {
            display: none;


            
            color: #EAE4D5;
        }






        /* ---------- TABLA DE DATOS ESTILO MODERNO ---------- */
        /* Evita conflictos */
        table {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
        }

        th, td {
            text-align: center;
            padding: 8px 6px;
        }

        /* Anchos de columnas sincronizados border-right: 10px;*/
        .col1 { width: 30%; }
        .col2 { width: 10%; }
        .col3 { width: 20%; }
        .col4 { width: 20%; }
        .col5 { width: 10%; }

        /* Tabla contenedor con sombra */
        .tabla-contenedor {
            width: 95%;
            margin: 20px auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Encabezado */
        .tabla-cabecera th {
            background: linear-gradient(to right, #637983, #637983);
            color: #EAE4D5;
            font-size: 18px;
            font-family: 'Sansation', sans-serif;
            border-radius: 30px 0 30px 0;
            font-weight: normal;
        }

        /* Cuerpo scroll */
        .tabla-scroll {
            max-height: 300px;
            overflow-y: scroll; /* Permite desplazamiento vertical */
            scrollbar-width: none;     /* Firefox */
            -ms-overflow-style: none;  /* Internet Explorer 10+ */

            
            margin-top: 20px;
            border-radius: 10px;
            border: 3px solid #637983;
        }

        .tabla-scroll::-webkit-scrollbar {
            display: none; /* Chrome, Safari y Opera */
        }

        /* Cuerpo tabla */
        .tabla-cuerpo td {
            background: transparent;
            font-family: 'Sansation', sans-serif;
            font-size: 16px;
            color: #333;


            border-bottom: 1px solid #637983;
            border-right: 2px solid #637983;
            border-left: 2px solid #637983;
        }

        td.espacio, th.espacio {
            width: 5px; /* o el ancho que prefieras */
            border: none;
            background: transparent;
            padding: 0;
        }


        
        


        /* Botones */
        .btn-detalles {
            margin-right: 16px;
        }


        /* ---------- BOTONES ---------- */
        .btn-detalles {
            background: #637983;
            color: #EAE4D5;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn-detalles:hover {
            background: #A2B0BE;
        }

        .btn-mapa i {
            font-size: 18px;
            color: #637983;
            transition: transform 0.2s ease;
        }
        .btn-mapa:hover i {
            transform: scale(1.2);
            color: #A2B0BE;
        }



        /* ---------- MODAL ---------- */
        .modal {
            display: none;
            position: fixed;
            z-index: 1008;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #EAE4D5;
            font-family: 'Sansation', sans-serif;
            font-size: 16px;
            color: #192E2F;
            padding: 20px 15px;
            max-width: 800px;
            width: 90%;
            /*max-height: 90%;/**/
            margin: auto;
            border-radius: 12px;
            text-align: left;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);

        }

        .close {
            float: right;
            font-size: 28px;
            cursor: pointer;
            color: red;
        }

        .close:hover {
            color: darkred;
        }

        /* ---------- LAYOUT ---------- */
        .container {
            display: flex;
            height: 100vh;
        }

        .content {
            margin-left: 240px;
            padding: 20px;
        }











        /*==================/DISEÑO PARA CELULARES 768PX/==================*/
        /* RESPONSIVE */
        @media (max-width: 768px) {
            
            /*==================/CUERPO/==================*/
            /*==================/CUERPO/==================*/
            body {
                width: 100%;
                min-height: 100vh;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                position: relative;

                
                background-image: url('https://directorioasambleasvzla.com/iconos/Fondo_Mapa_Tlf.png'); /* tu imagen */
                padding-top: 20px; /* para que el menú no tape el contenido */
            }   



            .contento {
                margin-left: 00px;
                padding: 20px;
            }/**/

            .contento,
            .container,
            .content,
            .contenido,
            form {
                margin: 0;
                padding: 10px;
                width: 100%;
                box-sizing: border-box;
            }

            header {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 10px;
            }

            header h1 {
                font-size: 26px;
                margin-bottom: 10px;
            }

            header .return-button {
                display: none !important;
            }

            /*Tabla de contenido*/
            .tabla-contenedor {
                width: 100%;
                margin: 10px 0;
            }

            .tabla-cabecera th,
            .tabla-cuerpo td {
                font-size: 12px;
                padding: 6px 4px;
            }

            .tabla-scroll {
                max-height: 250px;
            }

            .btn-detalles,
            .btn-mapa {
                font-size: 12px;
                padding: 6px 8px;
            }


            /* Oculta encabezados */
            th.col2, th.col3, 
            td.col2, td.col3, 
            td.espacio:nth-child(4), /* después de col1 */
            td.espacio:nth-child(6), /* después de col2 */
            td.espacio:nth-child(8)  /* después de col3 */
            {
                display: none !important;
            }

            /* Opcional: cambia a una sola columna visible */
            /*Columna Asamblea*/
            .tabla-cabecera th.col1,
            .tabla-cuerpo td.col1 {
                width: 50%;
            }

            /*Columna Estado*/
            .tabla-cabecera th.col4,
            .tabla-cuerpo td.col4 {
                width: 20%;
            }

            /*Columna Botones*/
            .tabla-cabecera th.col5,
            .tabla-cuerpo td.col5 {
                width: 30%;
            }


            /*Modal de detalles*/
            /*Modal de detalles*/
            .modal-content {
                
                        padding: 5px;
                        width: 90%;
                        max-height: 96%;
                        margin: auto;
                        border-radius: 12px;
                        position: relative;
                /* Añade estas propiedades para controlar el overflow */
                overflow-y: auto;
                overflow-x: hidden;
            }

            /* Asegurar que el modal-body también controle el overflow */
            .modal {
                max-height: 100%;
                overflow: visible;
            }

            /* Opcional: mejorar el scroll en móviles */
            .modal-content::-webkit-scrollbar {
                width: 4px;
            }

            .modal-content::-webkit-scrollbar-thumb {
                background: #aaa;
                border-radius: 10px;
            }

            
        }





        /* DISEÑO FILTROS */  
        @media only screen and (max-width: 600px) {
            .filtros-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            }
        
            /* Agrupar selector y buscador en una sola fila */
            .filtros-container .fila-buscador {
            display: flex;
            width: 100%;
            gap: 1%;
            }
        
            .filtros-container select#columna,
            .filtros-container input#buscador,
            .filtros-container select#selector-estado {
            flex: 1;
            min-width: 0;
            }
        
            /* Asegura que solo uno se muestre */
            /* Quita el !important para permitir que JS lo controle */
            #selector-estado {
                width: 100%;
            }
        
        
            #buscador[style*="none"] {
            display: none !important;
            }
        
            /* Ordenar */
            #ordenar {
            width: 40%;

            margin-left: 25%;
            }
        }
        







        @media screen and (min-width: 481px) and (max-width: 768px) {
            /* ajustes para tablets */
        }



        /* === FORMATO ADAPTADO PARA TELÉFONOS (≤ 480px de ancho) === */
        @media screen and (max-width: 280px) {
            
            /*==================/CUERPO/==================*/
            body {
                width: 100%;
                min-height: 100vh;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                position: relative;

                
                background-image: url('https://directorioasambleasvzla.com/iconos/Fondo_Mapa_Tlf.png'); /* tu imagen */
                padding-top: 20px; /* para que el menú no tape el contenido */
            }  

            .contento {
                margin-left: 00px;
                padding: 20px;
            }/**/

            .contento,
            .container,
            .content,
            .contenido,
            form {
                margin: 0;
                padding: 10px;
                width: 100%;
                box-sizing: border-box;
            }

            header {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 10px;
            }

            header h1 {
                font-size: 20px;
                margin-bottom: 10px;
            }

            header .return-button {
                display: none !important;
            }

            .tabla-contenedor {
                width: 100%;
                margin: 10px 0;
            }

            .tabla-cabecera th,
            .tabla-cuerpo td {
                font-size: 12px;
                padding: 6px 4px;
            }

            .tabla-scroll {
                max-height: 250px;
            }

            .btn-detalles,
            .btn-mapa {
                font-size: 12px;
                padding: 6px 8px;
            }

            .modal-content {
                width: 90%;
                padding: 15px;
            }

            .icon-sidebar {
                width: 50px;
            }

            .icon-btn img {
                width: 20px;
                height: 20px;
            }

            .content {
                margin-left: 60px;
                padding: 10px;
            }

        }


































































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
    </style>



    
    <style>
        
        /* Por defecto en pantallas grandes, muestra el nombre completo */
        .estado-abrev {
            display: none !important; 
        }
        .estado-nombre {
            display: inline !important;
        }

        /* En móviles, muestra el acrónimo y oculta el nombre largo */
        @media only screen and (max-width: 600px) {
            .estado-abrev {
                display: inline !important;
            }
            .estado-nombre {
                display: none !important;
            }
        }
    </style>
    <style>
        .espacio .tlf-no {
            display: none;
        }
        .espacio {
            display: none;
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



<div class="contento">
    <header>
        <button onclick="window.history.back()" class="return-button" title="Volver"></button>
        <h1>Asambleas Congregadas al Nombre de Señor en Venezuela</h1>
    </header>

    <div class="filtros-container">
        <div class="fila-buscador">
            <select id="columna" onchange="filtrarTabla()">
                <option value="all">Buscar todo</option>
                <option value="0">Asamblea</option>
                <option value="1">Número</option>
                <option value="2">Ciudad</option>
                <option value="3">Estado</option>
            </select>

            <input class="BarradeBuscador" type="text" id="buscador" onkeyup="filtrarTabla()" placeholder="Buscar...">
        </div>


        <select id="selector-estado" onchange="filtrarTabla()">
            <option value="">Selecciona un estado</option>
            <option value="Amazonas ama">Amazonas</option>
            <option value="Anzoátegui anz">Anzoátegui</option>
            <option value="Apure apu">Apure</option>
            <option value="Aragua ara">Aragua</option>
            <option value="Barinas bar">Barinas</option>
            <option value="Bolívar bol">Bolívar</option>
            <option value="Carabobo cara">Carabobo</option>
            <option value="Cojedes coj">Cojedes</option>
            <option value="Delta Amacuro del">Delta Amacuro</option>
            <option value="Distrito Capital dis CAPI">Distrito Capital</option>
            <option value="Falcon fal">Falcón</option>
            <option value="Guarico gua">Guárico</option>
            <option value="Lara lar">Lara</option>
            <option value="Mérida mer">Mérida</option>
            <option value="Miranda mir">Miranda</option>
            <option value="Monagas mon">Monagas</option>
            <option value="Nueva Esparta nue">Nueva Esparta</option>
            <option value="Portuguesa por">Portuguesa</option>
            <option value="Sucre suc">Sucre</option>
            <option value="Tachira tac">Táchira</option>
            <option value="Trujillo tru">Trujillo</option>
            <option value="Yaracuy yar">Yaracuy</option>
            <option value="Zulia zul">Zulia</option>
            <option value="Frontera Colombia FRON colo col">--Colombia--</option>
        </select>



        <script>
            function filtrarTabla() {
                const columna = document.getElementById("columna").value;
                const buscador = document.getElementById("buscador");
                const selectorEstado = document.getElementById("selector-estado");

                let input = "";

                if (columna === "3") {
                    input = selectorEstado.value.toLowerCase();
                } else {
                    input = buscador.value.toLowerCase();
                }

                const palabrasClave = input.split(" ").filter(Boolean);
                const filas = document.querySelectorAll(".tabla-cuerpo tbody tr");

                filas.forEach(fila => {
                    const celdas = fila.getElementsByTagName("td");

                    if (columna === "all") {
                        // Si input está vacío, mostrar todo
                        if (input.trim() === "") {
                            fila.style.display = "";
                            return;
                        }

                        const textoFila = fila.innerText.toLowerCase();
                        fila.style.display = textoFila.includes(input) ? "" : "none";
                    } else {
                        let indiceReal;
                        switch (columna) {
                            case "0": indiceReal = 0; break; // Asamblea
                            case "1": indiceReal = 2; break; // Número
                            case "2": indiceReal = 4; break; // Ciudad
                            case "3": indiceReal = 6; break; // Estado
                            default: indiceReal = 0;
                        }

                        const textoCelda = celdas[indiceReal]?.innerText.toLowerCase() || "";
                        const abrev = celdas[indiceReal]?.getAttribute("data-abrev")?.toLowerCase() || "";
                        const contenido = textoCelda + " " + abrev;

                        // Si no hay palabras clave, mostrar todo
                        if (palabrasClave.length === 0) {
                            fila.style.display = "";
                        } else {
                            const coincide = palabrasClave.some(p => contenido.includes(p));
                            fila.style.display = coincide ? "" : "none";
                        }
                    }
                });
            }








            document.getElementById("columna").addEventListener("change", () => {
                const columna = document.getElementById("columna").value;
                const buscador = document.getElementById("buscador");
                const selectorEstado = document.getElementById("selector-estado");

                if (columna === "3") {
                    buscador.style.display = "none";
                    selectorEstado.style.display = "inline-block";
                } else {
                    buscador.style.display = "inline-block";
                    selectorEstado.style.display = "none";
                }

                filtrarTabla();
            });
        </script>

        <!-- Ordenar tabla por -->
        <div >
            <label for="ordenar"></label>
            <select id="ordenar" onchange="ordenarTabla()" >
                <option value="">Ordenar por: </option>
                <option value="asamblea-az">Asamblea A-Z</option>
                <option value="asamblea-za">Asamblea Z-A</option>
                <option value="numero-asc">Número Ascendente</option>
                <option value="numero-desc">Número Descendente</option>
                <option value="estado-az">Estado A-Z</option>
                <option value="estado-za">Estado Z-A</option>
            </select>

            <script>
                function ordenarTabla() {
                    const select = document.getElementById("ordenar").value;
                    const tbody = document.querySelector(".tabla-cuerpo tbody");
                    const filas = Array.from(tbody.querySelectorAll("tr"));

                    let columna = 0;
                    let tipo = "texto";
                    let asc = true;

                    switch (select) {
                        case "asamblea-az": columna = 0; tipo = "texto"; asc = true; break;
                        case "asamblea-za": columna = 0; tipo = "texto"; asc = false; break;
                        case "numero-asc": columna = 2; tipo = "numero"; asc = true; break;
                        case "numero-desc": columna = 2; tipo = "numero"; asc = false; break;
                        case "estado-az": columna = 6; tipo = "texto"; asc = true; break;
                        case "estado-za": columna = 6; tipo = "texto"; asc = false; break;
                        default: return;
                    }

                    filas.sort((a, b) => {
                        let valA = a.cells[columna]?.innerText.trim() || "";
                        let valB = b.cells[columna]?.innerText.trim() || "";

                        if (tipo === "numero") {
                            valA = parseFloat(valA) || 0;
                            valB = parseFloat(valB) || 0;
                        } else {
                            valA = valA.toLowerCase();
                            valB = valB.toLowerCase();
                        }

                        if (valA < valB) return asc ? -1 : 1;
                        if (valA > valB) return asc ? 1 : -1;
                        return 0;
                    });

                    filas.forEach(fila => tbody.appendChild(fila));
                }
            </script>


        </div>
    </div>

    <script>
        // Configura el filtro correctamente al cargar la página
        window.addEventListener("DOMContentLoaded", () => {
            const columna = document.getElementById("columna").value;
            const buscador = document.getElementById("buscador");
            const selectorEstado = document.getElementById("selector-estado");

            if (columna === "3") {
                buscador.style.display = "none";
                selectorEstado.style.display = "inline-block";
            } else {
                buscador.style.display = "inline-block";
                selectorEstado.style.display = "none";
            }
        });
    </script>

    

    <div class="tabla-contenedor">
        <!-- Encabezado fijo -->
        <table class="tabla-cabecera">
            <thead>
                <tr>
                    <th class="col1">Asamblea</th>
                    <th class="espacio"></th>
                    <th class="col2">Número</th>
                    <th class="espacio"></th>
                    <th class="col3">Ciudad</th>
                    <th class="espacio"></th>
                    <th class="col4">Estado</th>
                    <th class="espacio"></th>
                    <th class="col5">Detalles<br>Ubicación</th>
                </tr>
            </thead>
        </table>

        <!-- Cuerpo con scroll -->
        <div class="tabla-scroll">
            <table class="tabla-cuerpo">
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $estado = $row['estado'];
                            
                            if ($estado === "Frontera Colombia") {
                                $abrev = "COLO";
                            } elseif ($estado === "Distrito Capital") {
                                $abrev = "CAPI";
                            } else {
                                $abrev = strtoupper(substr($estado, 0, 4));
                            }
                            
                        ?>
                        <tr>
                            <td class="col1"><?= htmlspecialchars($row['asamblea']) ?></td>
                            <td class="espacio"></td>

                            <td class="col2"><?= htmlspecialchars($row['numero']) ?></td>
                            <td class="espacio tlf-no"></td>

                            <td class="col3"><?= htmlspecialchars($row['ciudad']) ?></td>
                            <td class="espacio tlf-no"></td>

                            <td class="col4" data-abrev="<?= $abrev ?>">
                                <span class="estado-nombre"><?= htmlspecialchars($estado) ?></span>
                                <span class="estado-abrev"><?= $abrev ?></span>
                            </td>
                            <td class="espacio"></td>

                            <td class="col5">
                                <button class="btn-detalles" onclick='mostrarDetalles(<?= json_encode($row, JSON_UNESCAPED_UNICODE) ?>)'>Ver</button>
                                <a class="btn-mapa" 
                                    href="<?= htmlspecialchars($row['GoogleMaps']) ?>" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    title="Ver en Google Maps">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>

                            </td>
                        </tr>

                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr><td colspan="9">No hay datos disponibles</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function actualizarEstadosParaMovil() {
            const isMobile = window.innerWidth <= 600;
            const celdasEstado = document.querySelectorAll('.col4');

            celdasEstado.forEach(td => {
                const nombreCompleto = td.textContent.trim();
                const abreviatura = td.dataset.abrev;

                if (isMobile && abreviatura) {
                    td.textContent = abreviatura;
                } else {
                    td.textContent = nombreCompleto.length === 3 ? td.getAttribute('data-original') || abreviatura : nombreCompleto;
                }
            });
        }

        // Guarda el nombre completo al cargar
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.col4').forEach(td => {
                td.setAttribute('data-original', td.textContent.trim());
            });
            actualizarEstadosParaMovil();
        });

        // También actualiza si cambias el tamaño del navegador
        window.addEventListener('resize', actualizarEstadosParaMovil);


        
    </script>


</div>

<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h3>Detalles de la Asamblea</h3>

        <p id="wrap-asamblea"><strong>Asamblea:</strong> <span id="det-asamblea"></span></p>
        <p id="wrap-numero"><strong>Número:</strong> <span id="det-numero"></span></p>
        <p id="wrap-fecha"><strong>Fecha de Fundación:</strong> <span id="det-Fehca_Fundacion"></span></p>
        <p id="wrap-ciudad"><strong>Ciudad:</strong> <span id="det-ciudad"></span></p>
        <p id="wrap-estado"><strong>Estado:</strong> <span id="det-estado"></span></p>
        <p id="wrap-direccion"><strong>Dirección:</strong> <span id="det-direccion"></span></p>

        <ul id="wrap-horarios">
            <li id="wrap-domingo"><strong>Domingo:</strong> <span id="det-domingo"></span></li>
            <li id="wrap-lunes"><strong>Lunes:</strong> <span id="det-lunes"></span></li>
            <li id="wrap-martes"><strong>Martes:</strong> <span id="det-martes"></span></li>
            <li id="wrap-miercoles"><strong>Miércoles:</strong> <span id="det-miercoles"></span></li>
            <li id="wrap-jueves"><strong>Jueves:</strong> <span id="det-jueves"></span></li>
            <li id="wrap-viernes"><strong>Viernes:</strong> <span id="det-viernes"></span></li>
            <li id="wrap-sabado"><strong>Sábado:</strong> <span id="det-sabado"></span></li>
        </ul>

        <p id="wrap-obras"><strong>Obras:</strong> <span id="det-obras"></span></p>

        <p id="wrap-mapa"><strong>Google Maps:</strong> 
            <a id="det-mapa" href="#" target="_blank" class="btn-mapa">
                <i class="fas fa-map-marker-alt"></i> Ver ubicación
            </a>
        </p>
    </div>
</div>

<style>
    #det-obras ul {
        list-style-type: disc;
        margin: 5px 0 5px 20px;
        padding: 0;
    }

    #det-obras li {
        margin-bottom: 4px;
        text-align: left;
    }

    /* Estilo del contenedor de obras con scroll */
    .obras-scroll {
        max-height: 100px;       /* ajusta según el tamaño del modal */
        overflow-y: auto;         /* activa el scroll vertical */
        padding-right: 8px;
        margin-top: 5px;
        /*border: 1px solid #ccc;   /* opcional, para delimitar el área */
        border-radius: 6px;
        background: transparent;      /* color suave de fondo */
    }

    /* Lista interna de obras */
    .obras-scroll ul {
        list-style-type: disc;
        margin: 8px 0 8px 20px;
        padding: 0;
    }

    .obras-scroll li {
        margin-bottom: 5px;
        line-height: 1.4;
        text-align: left;
        word-wrap: break-word;
    }

    /* Scrollbar personalizada (opcional) */
    .obras-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .obras-scroll::-webkit-scrollbar-thumb {
        background: #aaa;
        border-radius: 10px;
    }

    .obras-scroll::-webkit-scrollbar-thumb:hover {
        background: #888;
    }


</style>

<script>
    // Función para mostrar u ocultar un campo según su valor
    function toggleCampo(wrapperId, valor, excepciones = []) {
        const wrap = document.getElementById(wrapperId);
        const span = wrap ? wrap.querySelector("span, a") : null;

        if (!wrap || !span) return;

        const limpio = (valor || "").trim();
        const normalizado = limpio.toLowerCase();
        const excepcionesNormalizadas = excepciones.map(e => e.trim().toLowerCase());

        if (!limpio || excepcionesNormalizadas.includes(normalizado)) {
            wrap.style.display = "none";
        } else {
            span.textContent = limpio;
            wrap.style.display = "block";
        }
    }

    // Función para mostrar el modal de detalles
    function mostrarDetalles(iglesia) {
        const modal = document.getElementById("modal");

        // Datos principales
        toggleCampo("wrap-asamblea", iglesia.asamblea);
        toggleCampo("wrap-numero", iglesia.numero);
        toggleCampo("wrap-fecha", iglesia.Fehca_Fundacion);
        toggleCampo("wrap-ciudad", iglesia.ciudad);
        toggleCampo("wrap-estado", iglesia.estado);
        toggleCampo("wrap-direccion", iglesia.direccion);

        // Horarios (ocultar si dice "Sin reuniones")
        toggleCampo("wrap-domingo", iglesia.domingo, ["Sin reuniones", "Sin reuniones."]);
        toggleCampo("wrap-lunes", iglesia.lunes, ["Sin reuniones", "Sin reuniones."]);
        toggleCampo("wrap-martes", iglesia.martes, ["Sin reuniones", "Sin reuniones."]);
        toggleCampo("wrap-miercoles", iglesia.miercoles, ["Sin reuniones", "Sin reuniones."]);
        toggleCampo("wrap-jueves", iglesia.jueves, ["Sin reuniones", "Sin reuniones."]);
        toggleCampo("wrap-viernes", iglesia.viernes, ["Sin reuniones", "Sin reuniones."]);
        toggleCampo("wrap-sabado", iglesia.sabado, ["Sin reuniones", "Sin reuniones."]);

        // Obras
        // Obras — mostrar con formato de lista y scroll si hay muchas
        const wrapObras = document.getElementById("wrap-obras");
        const spanObras = document.getElementById("det-obras");

        if (iglesia.obras && iglesia.obras.trim() !== "" && iglesia.obras.toLowerCase().trim() !== "sin obras que atender") {
            const obrasLimpias = iglesia.obras.trim()
                .split(/\r?\n+/) // separar por saltos de línea
                .filter(linea => linea.trim() !== ""); // eliminar vacíos

            if (obrasLimpias.length > 0) {
                let listaHTML = `
                    <div class="obras-scroll">
                        <ul>
                `;
                obrasLimpias.forEach(linea => {
                    listaHTML += `<li>${linea.trim()}</li>`;
                });
                listaHTML += `
                        </ul>
                    </div>
                `;
                spanObras.innerHTML = listaHTML;
                wrapObras.style.display = "block";
            } else {
                wrapObras.style.display = "none";
            }
        } else {
            wrapObras.style.display = "none";
        }



        // Google Maps (manejo especial con <a>)
        const wrapMapa = document.getElementById("wrap-mapa");
        const linkMapa = document.getElementById("det-mapa");

        if (iglesia.GoogleMaps && iglesia.GoogleMaps.trim() !== "") {
            linkMapa.setAttribute("data-url", iglesia.GoogleMaps.trim());
            wrapMapa.style.display = "block";
        } else {
            wrapMapa.style.display = "none";
        }

        modal.style.display = "flex";
    }

    // Función para cerrar el modal
    function cerrarModal() {
        document.getElementById("modal").style.display = "none";
    }

    // Cerrar modal si se hace clic fuera del contenido
    window.addEventListener("click", function (event) {
        const modal = document.getElementById("modal");
        if (event.target === modal) {
            cerrarModal();
        }
    });

    /*========== Solo en caso de no tener Enlace GoogleMaps ==========*/
    document.addEventListener("DOMContentLoaded", function () {
        document.addEventListener("click", function (e) {
            if (e.target && e.target.id === "det-mapa") {
                const url = e.target.getAttribute("data-url");

                if (!url || url === "#" || url.trim() === "") {
                    e.preventDefault();
                    alert("Enlace no disponible por ahora.\n\nSi lo tiene, favor hacerlo llegar al correo:\n\ndirectorioasambleas@gmail.com\n\nIndicando claramente el nombre de la asamblea y el estado en el que se encuentra.\n\nGracias de antemano.");
                } else {
                    e.preventDefault();
                    window.open(url, '_blank');
                }
            }
        });
    });
</script>


    <style>
        /*Modal de detalles*/
        .modal-content {
            width: 90%;
            max-height: 90vh;
            padding: 10px;
            
            font-size: 14px;
            margin: 5px auto;
            margin-top: 5%;
            
            /* Añade estas propiedades para controlar el overflow */
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Asegurar que el modal-body también controle el overflow */
        .modal {
            /**max-height: 100%;/** */
            overflow: visible;
        }

        /* Opcional: mejorar el scroll en móviles */
        .modal-content::-webkit-scrollbar {
            width: 4px;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: #aaa;
            border-radius: 10px;
        }
    </style>


<?php $conn->close(); ?>
</body>
</html>

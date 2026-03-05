<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$estados = [
    "Amazonas", "Anzoátegui", "Apure", "Aragua", "Barinas", "Bolívar",
    "Carabobo", "Cojedes", "Delta Amacuro", "Falcón", "Guárico",
    "Lara", "Mérida", "Miranda", "Monagas", "Nueva Esparta",
    "Portuguesa", "Sucre", "Táchira", "Trujillo", "Vargas",
    "Yaracuy", "Zulia", "Frontera Colombia"
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Venezuela</title>
    <link rel="stylesheet" href="Mapa.css"> <!-- Reutilizamos el mismo CSS -->
    
    <!--<style>
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











        /* === FONDO CON CAPAS === */
        /* === FONDO CON CAPAS === */
        /* Asegura que todo ocupe el alto completo */
        html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Sansation', sans-serif;
        }

        /* Fondo de pantalla completo */
        body::before {
        content: "";
        position: fixed; /* para que no se repita al hacer scroll */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('https://directorioasambleasvzla.com/iconos/Fonfo_Mapa_Color.png');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: top center;
        opacity: 0.7;
        z-index: -1;
        }

        /* Asegura que se muestre scroll si el contenido es más largo */
        body {
        overflow-x: hidden;
        overflow-y: auto;
        position: relative;
        background-color: #fff; /* fallback si la imagen no carga */
        }

        /* Asegurar que el contenido esté por encima del fondo */
        .container, .contenido, form {
            position: relative;
            z-index: 1;
        }





        /* === ENCABEZADO (TÍTULO Y BOTÓN VOLVER) === */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-left: 80px; /* espacio por sidebar */
            padding: 20px;

            text-align: center;
        }

        header h1 {
            font-family: 'Oleo Script', cursive;/**/
            font-weight: normal;
            font-size: 38px;
            color: #2a3e42;

            display: flex;
            flex-direction: column;
            align-items: center; /* Centra los elementos hijos horizontalmente */
            margin: 0 auto; /* Centra el contenedor .contenido en la página */
            box-sizing: border-box; /* Asegura que el padding no afecte el ancho total */
            border-radius: 8px;

            text-align: center;
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

            margin-left: 80px;
        }

        header button:hover {
            transform: scale(1.25);
        }


        /* === MAPA Y BOTONES DE ESTADOS === */
        .mapa-leyenda-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-top: 10px;
            
            padding: 10px;
            margin-top: -20px /* espacio arriba del mapa y la leyenda */
        }

        .mapa-wrapper {
            display: flex;
            /*flex-direction: column;/**/
            align-items: center;
            justify-content: center;
            padding: 10px;
            width: 100%;
        }

        .mapa-container {
            position: relative;
            max-width: 800px;
            
            margin-right: -80px; /* espacio a la derecha del mapa */
        }

        .mapa-container img {
            height: 70%;
            width: 70%;
        }

        /* Botones interactivos sobre el mapa */
        .estado-btn {
            position: absolute;
            background-color: #5a7684;
            color: #EAE4D5;
            border: none;
            border-radius: 8px;
            padding: 3px 6px;
            font-size: 10px;
            cursor: pointer;
            transition: background 0.3s;

            
            z-index: 9008;
        }

        .estado-btn:hover {
            background-color: #3b5c68;
        }

        .lista-leyenda {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .lista-leyenda li button {
            width: 100%;
            padding: 8px 10px;
            background-color: #5a7684;
            color: #EAE4D5;
            border: none;
            border-radius: 5px;
            margin-bottom: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .lista-leyenda li button:hover {
            background-color: #3b5c68;
        }

        /* Botón cerrar (X) */
        .cerrar {
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }


        /* === POSICIONES ESTIMADAS DE CADA ESTADO (AJUSTA SEGÚN NECESARIO) === */
        /* Agrega más estados con sus coordenadas estimadas */
        /* Posicionamiento de los botones (Ajustar según sea necesario) */
        .btn-Amazonas {top: 70%; left: 36%;}
        .btn-Anzoategui {top: 22%; left: 44%;}
        .btn-Apure {top: 44%; left: 22%;}
        .btn-Aragua {top: 16%; left: 29%;}          /*--Los tres--*/
        .btn-Barinas {top: 34%; left: 14%;}
        .btn-bolivar { top: 50%; left: 50%; }
        .btn-carabobo { top: 14%; left: 26%; }    /*--Los tres--*/
        .btn-Cojedes {top: 24%; left: 25%;}
        .btn-Delta_Amacuro {top: 26%; left: 60%;}
        .btn-Falcon {top: 6%; left: 18%;}
        .btn-Guárico {top: 26%; left: 32%;}
        .btn-Lara {top: 16%; left: 16%;}
        .btn-Merida {top: 30%; left: 8%;}
        .btn-miranda { top: 15%; left: 36%; }
        .btn-Monagas {top: 20%; left: 50%;}
        .btn-Nueva_Esparta {top: 6%; left: 46%;}
        .btn-Portuguesa {top: 24%; left: 18%;}
        .btn-Sucre {top: 12%; left: 50%;}
        .btn-Tachira {top: 36%; left: 4%;}
        .btn-Trujillo {top: 22%; left: 12%;}
        .btn-Vargas {top: 11%; left: 34%;}
        .btn-Yaracuy {top: 14%; left: 22%;}          /*--Los tres--*/
        .btn-zulia { top: 18%; left: 4%; }

        .btn-Frontera_Colombia {top: 60%; left: 12%;}
        .btn-DistritoCapital {top: 14%; left: 31.5%;}

        /*====================/UBICACIONES DE LOS BOTONES/====================*/
        @media only screen and (max-width: 600px) {
            /* === POSICIONES ESTIMADAS DE CADA ESTADO (AJUSTA SEGÚN NECESARIO) === */
            .btn-Amazonas {top: 70%; left: 50%;}          /*--1--*/
            .btn-Anzoategui {top: 22%; left: 62%;}          /*--2--*/
            .btn-Apure {top: 44%; left: 28%;}          /*--3--*/
            .btn-Aragua {top: 16%; left: 42%;}          /*--4 Los tres--*/
            .btn-Barinas {top: 34%; left: 20%;}          /*--5--*/
            .btn-bolivar { top: 50%; left: 70%; }          /*--6--*/
            .btn-carabobo { top: 14%; left: 37%; }    /*--7 Los tres--*/
            .btn-Cojedes {top: 24%; left: 35%;}          /*--8--*/
            .btn-Delta_Amacuro {top: 26%; left: 86%;}          /*--9--*/
            .btn-Falcon {top: 8%; left: 22%;}          /*--10--*/
            .btn-Guárico {top: 26%; left: 48%;}          /*--11--*/
            .btn-Lara {top: 16%; left: 22%;}          /*--12--*/
            .btn-Merida {top: 30%; left: 12%;}          /*--13--*/
            .btn-miranda { top: 15%; left: 50%; }          /*--14--*/
            .btn-Monagas {top: 20%; left: 72%;}          /*--15--*/
            .btn-Nueva_Esparta {top: 6%; left: 66%;}          /*--16--*/
            .btn-Portuguesa {top: 24%; left: 24%;}          /*--17--*/
            .btn-Sucre {top: 12%; left: 72%;}          /*--18--*/
            .btn-Tachira {top: 36%; left: 6%;}          /*--19--*/
            .btn-Trujillo {top: 22%; left: 18%;}          /*--20--*/
            .btn-Vargas {top: 11%; left: 44%;}          /*--21--*/
            .btn-Yaracuy {top: 14%; left: 32%;}          /*--22 Los tres--*/
            .btn-zulia { top: 18%; left: 4%; }          /*--23--*/

            .btn-Frontera_Colombia {top: 60%; left: 12%;}          /*-- --*/
            .btn-DistritoCapital {top: 15%; left: 46%;}          /*--*--*/
        }


        /* === TABLA LATERAL DE LEYENDA === */
        .tabla-leyenda {
            padding: 10px;
            border-radius: 12px;
            max-height: 450px;
            width: 260px;
            font-family: 'Sansation', sans-serif;
            
            margin-left: -80px; /* espacio a la izquierda de la leyenda */
            margin-bottom: 80px /* espacio debajo de la leyenda */
        }

        .tabla-leyenda {
            overflow-y: scroll;           /* activa el scroll vertical */
            scrollbar-width: none;        /* Firefox */
            -ms-overflow-style: none;     /* Internet Explorer y Edge */
        }

        .tabla-leyenda::-webkit-scrollbar {
            display: none;                /* Chrome, Safari y Opera */  
        }

        .tabla-leyenda h3 {
            font-family: 'Sansation', cursive;
            font-size: 24px;
            margin-bottom: 10px;
            color: #2a3e42;
            text-align: center;
            padding-bottom: 8px;
        }

        .tabla-leyenda table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden; /* importante para recortar bordes */

            
            border-radius: 10px;
            border: 3px solid #637983;
            overflow-y: auto;
        }

        .tabla-leyenda td {
            padding: 6px 0;
            
            border-bottom: 2px dotted #637983;
        }

        .tabla-leyenda button {
            width: 100%;
            background-color: transparent;
            color: #192E2F;
            border: none;
            border-radius: 8px;
            padding: 2px 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;

            
            text-align: left;
            margin-left: 20px;
        }

        .tabla-leyenda button:hover {
            transform: scale(1.03);
        }

        /* Botón para abrir modal */
        .btn-leyenda {
            background-color: #007bff;
            color: #EAE4D5;
            padding: 10px 16px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 5px;

        }


        /* Lista de leyenda */
        .lista-leyenda button {
            width: 100%;
            padding: 8px 10px;
            background-color: #007bff;
            color: #EAE4D5;
            border: none;
            text-align: left;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 6px;
            transition: background-color 0.3s;
        }

        .lista-leyenda button:hover {
            background-color: #0056b3;
        }













        @media only screen and (min-width: 800px) {
            /* Zoom Controls */
            .zoom-controls {
                display: none !important;
            }

            .zoom-controls button {
                display: none !important;
            }

            .zoom-controls button:hover {
                display: none !important;
            }
        }
        

        @media only screen and (max-width: 768px) {
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
                padding-top: 20%; /* para que el menú no tape el contenido */
            } 

            .content {
                margin-top: auto;
                padding: 10px;
                overflow-y: scroll; /* Permite desplazamiento vertical */
                scrollbar-width: none;     /* Firefox */
                -ms-overflow-style: none;  /* Internet Explorer 10+ */
            }

            .content::-webkit-scrollbar {
                display: none; /* Chrome, Safari y Opera */
            }
        
            /* ENCABEZADO */
            header {
                flex-direction: column;
                padding: 10px;
                border-radius: 0;
                text-align: center;


                margin-left: 00px; /* espacio por sidebar */
            }

            header h1 {
                font-size: 26px;
                line-height: 1.3;
                padding: 0 10px;

                text-align: center;
            }

            header .return-button {
                display: none !important;
            }
        
            
        }

        /*====================/FORMATO PARA MAPA Y LEYENDA/====================*/
        @media only screen and (max-width: 600px) {
            /* Asegura que el mapa y la leyenda estén uno debajo del otro */
            .mapa-wrapper {
                display: flex;
                flex-direction: column;
                align-items: center; /* Centra los contenidos horizontalmente */
                gap: 0; /* Espacio entre mapa y leyenda */
                padding: 10px 0;
            }
            
            /* Zoom Controls */
            .zoom-controls {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin: 15px 0;
            }

            .zoom-controls button {
                font-size: 16px;
                padding: 6px 12px;
                background-color: #607d8b;
                color: #EAE4D5;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .zoom-controls button:hover {
                background-color: #455a64;
            }
            
            
            /* Contenedor general */
            .zoom-wrapper-phone {
                width: 100%;
                overflow: hidden;
                margin: 0 auto;
                border-radius: 10px;
                position: relative;
                border: 3px solid #ccc;

                touch-action: auto;
            }
        
            /* Elemento que se escalará */
            .zoom-wrapper-phone .mapa-container {
            transform-origin: center center;
            transition: transform 0.2s ease;
            width: 100%;
            max-width: 100%;
            }
        
            .zoom-wrapper-phone img {
            width: 100%;
            height: auto;
            display: block;
            }
        
            /* Botones en el mapa */
            .estado-btn {
            position: absolute;
            background-color: #5a7684;
            color: #EAE4D5;
            border: none;
            border-radius: 6px;
            padding: 2px 5px;
            font-size: 9px;
            cursor: pointer;
            z-index: 5;
            }
        
            /* Botones de zoom */
            .zoom-controls-phone {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 15px 0;
            }
        
            .zoom-controls-phone button {
            font-size: 18px;
            padding: 6px 12px;
            background-color: #607d8b;
            color: #EAE4D5;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            }
        
            .zoom-controls-phone button:hover {
            background-color: #455a64;
            }
        
            /* Leyenda */
            .tabla-leyenda {
            margin: 0 auto;
            width: 100%;
            max-width: 250px;
            color: #EAE4D5;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            
            margin-top: 10px;
            }
        
            .leyenda-container table {
            width: 100%;
            margin-top: 10px;
            }
        
            .leyenda-container td {
            padding: 6px;
            }
        
            .leyenda-container button {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            background-color: #A2B0BE;
            color: #2a3e42;
            border: none;
            border-radius: 5px;
            }
        
            .leyenda-container button:hover {
            background-color: #8fa4b4;
            }
        }


        /*====================/TELÉFONOS/====================*/
        @media only screen and (max-width: 280px) {
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
                padding-top: 20%; /* para que el menú no tape el contenido */
            } 

            .content {
                margin-top: auto;
                padding: 10px;
                overflow-y: scroll; /* Permite desplazamiento vertical */
                scrollbar-width: none;     /* Firefox */
                -ms-overflow-style: none;  /* Internet Explorer 10+ */
            }

            .content::-webkit-scrollbar {
                display: none; /* Chrome, Safari y Opera */
            }
        
            /* ENCABEZADO */
            header {
                flex-direction: column;
                padding: 10px;
                border-radius: 0;
            }

            header h1 {
                font-size: 20px;
                line-height: 1.3;
                padding: 0 10px;
            }

            header .return-button {
                display: none !important;
            }
        
            
        }
        

    </style>-->

    <style>
        /* === POSICIONES ESTIMADAS DE CADA ESTADO (AJUSTA SEGÚN NECESARIO) === */
        /* Agrega más estados con sus coordenadas estimadas */
        /* Posicionamiento de los botones (Ajustar según sea necesario) */
        .btn-Amazonas {top: 70%; left: 36%;}
        .btn-Anzoategui {top: 22%; left: 44%;}
        .btn-Apure {top: 44%; left: 22%;}
        .btn-Aragua {top: 16%; left: 29%;}          /*--Los tres--*/
        .btn-Barinas {top: 34%; left: 14%;}
        .btn-bolivar { top: 50%; left: 50%; }
        .btn-carabobo { top: 14%; left: 26%; }    /*--Los tres--*/
        .btn-Cojedes {top: 24%; left: 25%;}
        .btn-Delta_Amacuro {top: 26%; left: 60%;}
        .btn-Falcon {top: 6%; left: 18%;}
        .btn-Guárico {top: 26%; left: 32%;}
        .btn-Lara {top: 16%; left: 16%;}
        .btn-Merida {top: 30%; left: 8%;}
        .btn-miranda { top: 15%; left: 36%; }
        .btn-Monagas {top: 20%; left: 50%;}
        .btn-Nueva_Esparta {top: 6%; left: 46%;}
        .btn-Portuguesa {top: 24%; left: 18%;}
        .btn-Sucre {top: 12%; left: 50%;}
        .btn-Tachira {top: 36%; left: 4%;}
        .btn-Trujillo {top: 22%; left: 12%;}
        .btn-Vargas {top: 11%; left: 34%;}
        .btn-Yaracuy {top: 14%; left: 22%;}          /*--Los tres--*/
        .btn-zulia { top: 18%; left: 4%; }

        .btn-Frontera_Colombia {top: 60%; left: 12%;}
        .btn-DistritoCapital {top: 14%; left: 31.5%;}

        /*====================/UBICACIONES DE LOS BOTONES/====================*/
        @media only screen and (max-width: 600px) {
            /* === POSICIONES ESTIMADAS DE CADA ESTADO (AJUSTA SEGÚN NECESARIO) === */
            .btn-Amazonas {top: 70%; left: 50%;}          /*--1--*/
            .btn-Anzoategui {top: 22%; left: 62%;}          /*--2--*/
            .btn-Apure {top: 44%; left: 28%;}          /*--3--*/
            .btn-Aragua {top: 16%; left: 42%;}          /*--4 Los tres--*/
            .btn-Barinas {top: 34%; left: 20%;}          /*--5--*/
            .btn-bolivar { top: 50%; left: 70%; }          /*--6--*/
            .btn-carabobo { top: 14%; left: 37%; }    /*--7 Los tres--*/
            .btn-Cojedes {top: 24%; left: 35%;}          /*--8--*/
            .btn-Delta_Amacuro {top: 26%; left: 86%;}          /*--9--*/
            .btn-Falcon {top: 8%; left: 22%;}          /*--10--*/
            .btn-Guárico {top: 26%; left: 48%;}          /*--11--*/
            .btn-Lara {top: 16%; left: 22%;}          /*--12--*/
            .btn-Merida {top: 30%; left: 12%;}          /*--13--*/
            .btn-miranda { top: 15%; left: 50%; }          /*--14--*/
            .btn-Monagas {top: 20%; left: 72%;}          /*--15--*/
            .btn-Nueva_Esparta {top: 6%; left: 66%;}          /*--16--*/
            .btn-Portuguesa {top: 24%; left: 24%;}          /*--17--*/
            .btn-Sucre {top: 12%; left: 72%;}          /*--18--*/
            .btn-Tachira {top: 36%; left: 6%;}          /*--19--*/
            .btn-Trujillo {top: 22%; left: 18%;}          /*--20--*/
            .btn-Vargas {top: 11%; left: 44%;}          /*--21--*/
            .btn-Yaracuy {top: 14%; left: 32%;}          /*--22 Los tres--*/
            .btn-zulia { top: 18%; left: 4%; }          /*--23--*/

            .btn-Frontera_Colombia {top: 60%; left: 12%;}          /*-- --*/
            .btn-DistritoCapital {top: 15%; left: 46%;}          /*--*--*/
        }
    </style>



    <style>
        .zoom-controls {
            display: none !important;
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



    <div class="content">
        <header>
            <button onclick="location.href='/Menu/Submenu.php'" class="return-button" title="Volver"></button>
            <h1>Mapa de las Asambleas en Venezuela</h1>
        </header>

        <div class="zoom-controls">
            <button onclick="zoomIn()">+</button>
            <button onclick="zoomOut()">−</button>
            <button onclick="resetZoom()">🔄</button>
        </div>

        <div class="mapa-leyenda-wrapper">

            <div class="mapa-wrapper">

                <div class="zoom-wrapper-phone" id="zoomWrapper">

                    <!-- Contenedor del Mapa -->
                    <div class="mapa-container">
                        <img src="https://directorioasambleasvzla.com/iconos/Mapa.png" alt="Mapa de Venezuela">

                        <!-- Botones interactivos -->
                        <button class="estado-btn btn-DistritoCapital" onclick="window.location.href='Estados.php?estado=Distrito Capital'" title="Distrito Capital">*</button>
                        <button class="estado-btn btn-Amazonas" onclick="window.location.href='Estados.php?estado=Amazonas'" title="Amazonas">1</button>
                        <button class="estado-btn btn-Anzoategui" onclick="window.location.href='Estados.php?estado=Anzoátegui'" title="Anzoátegui">2</button>
                        <button class="estado-btn btn-miranda" onclick="window.location.href='Estados.php?estado=Miranda'" title="Miranda">14</button>
                        <button class="estado-btn btn-zulia" onclick="window.location.href='Estados.php?estado=Zulia'" title="Zulia">23</button>
                        <button class="estado-btn btn-bolivar" onclick="window.location.href='Estados.php?estado=Bolívar'" title="Bolívar">6</button>
                        <button class="estado-btn btn-Apure" onclick="window.location.href='Estados.php?estado=Apure'" title="Apure">3</button>
                        <button class="estado-btn btn-Barinas" onclick="window.location.href='Estados.php?estado=Barinas'" title="Barinas">5</button>
                        <button class="estado-btn btn-Cojedes" onclick="window.location.href='Estados.php?estado=Cojedes'" title="Cojedes">8</button>
                        <button class="estado-btn btn-Falcon" onclick="window.location.href='Estados.php?estado=Falcón'" title="Falcón">10</button>
                        <button class="estado-btn btn-Guárico" onclick="window.location.href='Estados.php?estado=Guárico'" title="Guárico">11</button>
                        <button class="estado-btn btn-Lara" onclick="window.location.href='Estados.php?estado=Lara'" title="Lara">12</button>
                        <button class="estado-btn btn-Merida" onclick="window.location.href='Estados.php?estado=Mérida'" title="Mérida">13</button>
                        <button class="estado-btn btn-Monagas" onclick="window.location.href='Estados.php?estado=Monagas'" title="Monagas">15</button>
                        <button class="estado-btn btn-Nueva_Esparta" onclick="window.location.href='Estados.php?estado=Nueva Esparta'" title="Nueva Esparta">16</button>
                        <button class="estado-btn btn-Portuguesa" onclick="window.location.href='Estados.php?estado=Portuguesa'" title="Portuguesa">17</button>
                        <button class="estado-btn btn-Sucre" onclick="window.location.href='Estados.php?estado=Sucre'" title="Sucre">18</button>
                        <button class="estado-btn btn-Tachira" onclick="window.location.href='Estados.php?estado=Táchira'" title="Táchira">19</button>
                        <button class="estado-btn btn-Trujillo" onclick="window.location.href='Estados.php?estado=Trujillo'" title="Trujillo">20</button>
                        <button class="estado-btn btn-Vargas" onclick="window.location.href='Estados.php?estado=Vargas'" title="Vargas">21</button>
                        <button class="estado-btn btn-Delta_Amacuro" onclick="window.location.href='Estados.php?estado=Delta Amacuro'" title="Delta Amacuro">9</button>
                        <button class="estado-btn btn-Yaracuy" onclick="window.location.href='Estados.php?estado=Yaracuy'" title="Yaracuy">22</button>
                        <button class="estado-btn btn-Aragua" onclick="window.location.href='Estados.php?estado=Aragua'" title="Aragua">4</button>
                        <button class="estado-btn btn-carabobo" onclick="window.location.href='Estados.php?estado=Carabobo'" title="Carabobo">7</button>
                        <button class="estado-btn btn-Frontera_Colombia" onclick="window.location.href='Estados.php?estado=Frontera Colombia'" title="Frontera Colombia">Colombia</button>
                    </div>

                </div>

                

                <!-- Tabla Leyenda a la derecha -->
                <div class="tabla-leyenda">
                    <h3>Leyenda</h3>
                    <table>
                        <tbody>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Amazonas'">1 - Amazonas</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Anzoátegui'">2 - Anzoátegui</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Apure'">3 - Apure</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Aragua'">4 - Aragua</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Barinas'">5 - Barinas</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Bolívar'">6 - Bolívar</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Carabobo'">7 - Carabobo</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Cojedes'">8 - Cojedes</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Delta Amacuro'">9 - Delta Amacuro</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Falcón'">10 - Falcón</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Guárico'">11 - Guárico</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Lara'">12 - Lara</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Mérida'">13 - Mérida</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Miranda'">14 - Miranda</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Monagas'">15 - Monagas</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Nueva Esparta'">16 - Nueva Esparta</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Portuguesa'">17 - Portuguesa</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Sucre'">18 - Sucre</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Táchira'">19 - Táchira</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Trujillo'">20 - Trujillo</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Vargas'">21 - Vargas</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Yaracuy'">22 - Yaracuy</button></td></tr>
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Zulia'">23 - Zulia</button></td></tr>

                            <tr><td><button onclick="window.location.href='Estados.php?estado=Distrito Capital'">* - Distrito Capital</button></td></tr>
                            
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Frontera Colombia'">Colombia</button></td></tr>
                        </tbody>
                    </table>
                </div>
            
            <!--</div>-->
                            
        </div>
    
    </div>

    
    <script>
        // Zoom solo disponible si existe el contenedor responsive
        const zoomWrapper = document.querySelector(".zoom-wrapper-phone");
        const zoomTarget = zoomWrapper ? zoomWrapper.querySelector(".mapa-container") : null;

        let zoomPhoneLevel = 1;
        const zoomStep = 0.1;
        const maxZoom = 2.5;
        const minZoom = 0.7;

        let currentTranslate = { x: 0, y: 0 };
        let isDragging = false;
        let startX = 0;
        let startY = 0;

        function zoomIn() {
            if (!zoomTarget) return;
            zoomPhoneLevel = Math.min(maxZoom, zoomPhoneLevel + zoomStep);
            applyZoomAndPan();
        }

        function zoomOut() {
            if (!zoomTarget) return;
            zoomPhoneLevel = Math.max(minZoom, zoomPhoneLevel - zoomStep);
            applyZoomAndPan();
        }

        function resetZoom() {
            if (!zoomTarget) return;
            zoomPhoneLevel = 1;
            currentTranslate = { x: 0, y: 0 };
            applyZoomAndPan();
        }

        function applyZoomAndPan() {
            zoomTarget.style.transform = `scale(${zoomPhoneLevel}) translate(${currentTranslate.x}px, ${currentTranslate.y}px)`;
            zoomTarget.style.transformOrigin = "center center";
        }

        function getEventPoint(e) {
            if (e.touches && e.touches.length > 0) {
                return { x: e.touches[0].clientX, y: e.touches[0].clientY };
            } else {
                return { x: e.clientX, y: e.clientY };
            }
        }

        function startDrag(e) {
            if (!zoomTarget) return;
            e.preventDefault();
            isDragging = true;
            const point = getEventPoint(e);
            startX = point.x - currentTranslate.x;
            startY = point.y - currentTranslate.y;

            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', endDrag);
            document.addEventListener('touchmove', drag, { passive: false });
            document.addEventListener('touchend', endDrag);
        }

        function drag(e) {
            if (!isDragging || !zoomTarget) return;
            const point = getEventPoint(e);
            currentTranslate.x = point.x - startX;
            currentTranslate.y = point.y - startY;
            applyZoomAndPan();
        }

        function endDrag() {
            isDragging = false;
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', endDrag);
            document.removeEventListener('touchmove', drag);
            document.removeEventListener('touchend', endDrag);
        }

        // Solo si hay zoomWrapper (modo teléfono), añade eventos
        if (zoomWrapper) {
            zoomWrapper.addEventListener('mousedown', startDrag);
            zoomWrapper.addEventListener('touchstart', startDrag, { passive: false });
        }










        let initialDistance = null;
        let lastTouchZoom = 1;

        function getDistance(touches) {
            const dx = touches[0].clientX - touches[1].clientX;
            const dy = touches[0].clientY - touches[1].clientY;
            return Math.sqrt(dx * dx + dy * dy);
        }

        function handleTouchStart(e) {
            if (!zoomTarget || e.touches.length !== 2) return;

            initialDistance = getDistance(e.touches);
            lastTouchZoom = zoomPhoneLevel;
            e.preventDefault();  // evita el doble scroll del navegador
        }

        function handleTouchMove(e) {
            if (!zoomTarget || e.touches.length !== 2 || initialDistance === null) return;

            const currentDistance = getDistance(e.touches);
            const scaleChange = currentDistance / initialDistance;
            let newZoom = lastTouchZoom * scaleChange;

            // Limitar el zoom
            newZoom = Math.max(minZoom, Math.min(maxZoom, newZoom));

            zoomPhoneLevel = newZoom;
            applyZoomAndPan();
            e.preventDefault();
        }

        function handleTouchEnd(e) {
            if (e.touches.length < 2) {
                initialDistance = null;
            }
        }

        // Activar zoom táctil solo si es móvil
        if (zoomWrapper && window.innerWidth <= 768) {
            zoomWrapper.addEventListener('touchstart', handleTouchStart, { passive: false });
            zoomWrapper.addEventListener('touchmove', handleTouchMove, { passive: false });
            zoomWrapper.addEventListener('touchend', handleTouchEnd);
        }















        // ⚠️ Desactivar todas las funciones de zoom temporalmente
        function zoomIn() {}
        function zoomOut() {}
        function resetZoom() {}
        if (zoomWrapper) {
            zoomWrapper.removeEventListener('mousedown', startDrag);
            zoomWrapper.removeEventListener('touchstart', startDrag);
            zoomWrapper.removeEventListener('touchstart', handleTouchStart);
            zoomWrapper.removeEventListener('touchmove', handleTouchMove);
            zoomWrapper.removeEventListener('touchend', handleTouchEnd);
        }

    </script>

</body>
</html>



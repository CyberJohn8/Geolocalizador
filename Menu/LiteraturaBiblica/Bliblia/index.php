<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

    // ✅ Leer el archivo JSON de la Biblia
    $jsonFilePath = __DIR__ . "/biblia.json";
    
    if (!file_exists($jsonFilePath)) {
        die("Error: No se encontró el archivo biblia.json");
    }
    
    $jsonContent = file_get_contents($jsonFilePath);
    $bibliaData = json_decode($jsonContent, true);
    
    if ($bibliaData === null) {
        die("Error: No se pudo decodificar el archivo JSON");
    }

    // Asegura que PHP también envíe cabeceras UTF-8
    header('Content-Type: text/html; charset=utf-8');

    // === Obtener libros con su número de capítulos ===
    $libros = [];
    $librosInfo = [];
    
    // Procesar datos para obtener información de libros
    foreach ($bibliaData as $versiculo) {
        $libro = $versiculo['libro'];
        
        if (!isset($librosInfo[$libro])) {
            $librosInfo[$libro] = [
                'libro' => $libro,
                'max_chapter' => $versiculo['capitulo']
            ];
        } else {
            if ($versiculo['capitulo'] > $librosInfo[$libro]['max_chapter']) {
                $librosInfo[$libro]['max_chapter'] = $versiculo['capitulo'];
            }
        }
    }
    
    // Ordenar los libros según el orden canónico
    $ordenCanonico = [
        // Viejo Testamento
        'Génesis' => 1, 'Éxodo' => 2, 'Levítico' => 3, 'Números' => 4, 'Deuteronomio' => 5,
        'Josué' => 6, 'Jueces' => 7, 'Rut' => 8, '1 Samuel' => 9, '2 Samuel' => 10,
        '1 Reyes' => 11, '2 Reyes' => 12, '1 Crónicas' => 13, '2 Crónicas' => 14,
        'Esdras' => 15, 'Nehemías' => 16, 'Ester' => 17, 'Job' => 18, 'Salmos' => 19,
        'Proverbios' => 20, 'Eclesiastés' => 21, 'Cantares' => 22, 'Isaías' => 23,
        'Jeremías' => 24, 'Lamentaciones' => 25, 'Ezequiel' => 26, 'Daniel' => 27,
        'Oseas' => 28, 'Joel' => 29, 'Amós' => 30, 'Abdías' => 31, 'Jonás' => 32,
        'Miqueas' => 33, 'Nahúm' => 34, 'Habacuc' => 35, 'Sofonías' => 36,
        'Hageo' => 37, 'Zacarías' => 38, 'Malaquías' => 39,
        // Nuevo Testamento
        'Mateo' => 40, 'Marcos' => 41, 'Lucas' => 42, 'Juan' => 43, 'Hechos' => 44,
        'Romanos' => 45, '1 Corintios' => 46, '2 Corintios' => 47, 'Gálatas' => 48,
        'Efesios' => 49, 'Filipenses' => 50, 'Colosenses' => 51, '1 Tesalonicenses' => 52,
        '2 Tesalonicenses' => 53, '1 Timoteo' => 54, '2 Timoteo' => 55, 'Tito' => 56,
        'Filemón' => 57, 'Hebreos' => 58, 'Santiago' => 59, '1 Pedro' => 60,
        '2 Pedro' => 61, '1 Juan' => 62, '2 Juan' => 63, '3 Juan' => 64, 'Judas' => 65,
        'Apocalipsis' => 66
    ];
    
    // Ordenar los libros según el orden canónico
    uasort($librosInfo, function($a, $b) use ($ordenCanonico) {
        $ordenA = isset($ordenCanonico[$a['libro']]) ? $ordenCanonico[$a['libro']] : 999;
        $ordenB = isset($ordenCanonico[$b['libro']]) ? $ordenCanonico[$b['libro']] : 999;
        
        return $ordenA <=> $ordenB;
    });
    
    $libros = array_values($librosInfo);

    // === Manejar la lectura de versículos ===
    $versiculos = [];
    $capitulos_disponibles = [];
    $libro_seleccionado = $_POST['libro'] ?? ($libros[0]['libro'] ?? 'Génesis');
    $capitulo_seleccionado = intval($_POST['capitulo'] ?? 1);

    // Capítulos disponibles para el libro seleccionado
    $capitulosLibro = [];
    foreach ($bibliaData as $versiculo) {
        if ($versiculo['libro'] === $libro_seleccionado) {
            $capitulosLibro[$versiculo['capitulo']] = true;
        }
    }
    
    $capitulos_disponibles = array_keys($capitulosLibro);
    sort($capitulos_disponibles);

    // Leer versículos si se solicitó
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        foreach ($bibliaData as $versiculo) {
            if ($versiculo['libro'] === $libro_seleccionado && 
                $versiculo['capitulo'] === $capitulo_seleccionado) {
                $versiculos[] = [
                    'versiculo' => $versiculo['versiculo'],
                    'texto' => $versiculo['texto']
                ];
            }
        }
        
        // Ordenar por número de versículo
        usort($versiculos, function($a, $b) {
            return $a['versiculo'] <=> $b['versiculo'];
        });
    }

    // === Mostrar un versículo aleatorio destacado ===
    $versiculo_destacado = null;
    if (!empty($bibliaData)) {
        $randomIndex = array_rand($bibliaData);
        $versiculo_destacado = $bibliaData[$randomIndex];
    }
?>




<!DOCTYPE html>
<html lang="es">
<head>
  <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lector Bíblico</title>
  <link rel="stylesheet" href="Biblica.css">
  
    <style>
        
        /* === RESET Y CONFIGURACIÓN GENERAL === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Eliminar scroll visible del body y html */
        html, body {
            overflow-y: scroll;
            scrollbar-width: none;
            height: 100%;
            font-family: 'Sansation', sans-serif;
            color: #2a3e42;
        }

        /* Para navegadores WebKit como Chrome, Edge, Safari */
            /* === ESTILOS GENERALES === */
            body {
                font-family: 'Sansation', sans-serif;
                background-image: url('https://directorioasambleasvzla.com/iconos/Fonfo_Mapa_Color.png');
                background-size: cover;
                background-position: center;
                margin: 0;
                padding: 0;
                min-height: 100vh;
            }

            /* === LAYOUT PRINCIPAL === */
            .contenedor-principal {
                display: grid;
                grid-template-columns: 1fr 300px;
                grid-template-rows: auto auto 1fr auto;
                gap: 20px;
                max-width: 1000px;
                margin: 0 auto; /* Esto debería ser suficiente */
                padding: 20px;
                min-height: 80vh;
                width: 100%;
                justify-self: center; /* Centra el grid dentro del contenedor */
            }

            /* === ENCABEZADO === */
            header {
                grid-column: 1 / -1;
                grid-row: 1;
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 0;
            }

            header h1 {
                flex-grow: 1;
                text-align: center;
                font-family: 'Oleo Script', cursive;
                font-size: 42px;
                color: #2a3e42;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
                margin: 0;
            }

            header button {
                background-image: url('https://directorioasambleasvzla.com/iconos/Retorno.png');
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
                background-color: transparent;
                width: 45px;
                height: 45px;
                border: none;
                cursor: pointer;
                transition: transform 0.3s ease;
            }

            header button:hover {
                transform: scale(1.1);
            }

            /* === VERSÍCULO DESTACADO (DEBAJO DEL TÍTULO) === */
            .versiculo-destacado {
                grid-column: 1 / -1;
                grid-row: 2;
                background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
                padding: 25px;
                border-radius: 15px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                border-left: 5px solid #2a3e42;
                text-align: center;
                margin-bottom: 0;
            }

            .versiculo-destacado p {
                font-size: 20px;
                font-style: italic;
                color: #2a3e42;
                line-height: 1.6;
                margin: 0;
            }

            .versiculo-destacado strong {
                color: #1e2e30;
                font-weight: 600;
            }

            /* === CONTENEDOR DE VERSÍCULOS CON SCROLL === */
            .contenedor-versiculos {
            grid-column: 1;
            grid-row: 3;
            background: transparent;
            border-radius: 15px;
            border: 2px solid #637983; /* Borde sólido de 2px */
            padding: 0;
            display: flex;
            flex-direction: column;
            max-height: 60vh;
            min-height: 400px;
            }

            #bible-text {
                flex: 1;
                overflow-y: auto;
                padding: 25px;
                margin: 0;
                scrollbar-width: thin;
                scrollbar-color: #637983 transparent;
            }

            #bible-text::-webkit-scrollbar {
                width: 8px;
            }

            #bible-text::-webkit-scrollbar-track {
                background: transparent;
                border-radius: 4px;
            }

            #bible-text::-webkit-scrollbar-thumb {
                background: #637983;
                border-radius: 4px;
            }

            #bible-text::-webkit-scrollbar-thumb:hover {
                background: #2a3e42;
            }

            #bible-text p {
                font-size: 18px;
                margin-bottom: 20px;
                line-height: 1.7;
                color: #333;
                text-align: justify;
                padding: 8px 0;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }

            #bible-text p:last-child {
                border-bottom: none;
            }

            #bible-text strong {
                color: #2a3e42;
                font-weight: 700;
                margin-right: 8px;
                font-size: 16px;
                background: rgba(42, 62, 66, 0.1);
                padding: 2px 6px;
                border-radius: 4px;
            }

            /* === PANEL DE CONTROLES (DERECHA) === */
            .panel-controles {
                grid-column: 2;
                grid-row: 3;
                background: transparent;
                border-radius: 15px;
                padding: 25px;
                display: flex;
                flex-direction: column;
                gap: 20px;
                max-height: 60vh;
            }

            /* === FORMULARIO DE SELECCIÓN === */
            .formulario-seleccion {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .grupo-campo {
                display: flex;
                flex-direction: column;
            }

            .grupo-campo label {
                font-weight: 600;
                margin-bottom: 8px;
                color: #EAE4D5;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .formulario-seleccion select {
                width: 100%;
                padding: 12px 15px;
                border-radius: 8px;
                border: 2px solid #EAE4D5;
                background: #637983;
                font-size: 16px;
                color: #EAE4D5;
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .formulario-seleccion select:hover {
                border-color: #637983;
            }

            .formulario-seleccion select:focus {
                outline: none;
                border-color: #637983;
                box-shadow: 0 0 0 3px rgba(42, 62, 66, 0.1);
            }

            .formulario-seleccion button {
                padding: 12px 20px;
                background: linear-gradient(135deg, #637983 0%, #637983 100%);
                color: #EAE4D5;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 16px;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-top: 10px;
            }

            .formulario-seleccion button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(42, 62, 66, 0.4);
            }

            /* === CONTROLES DE AUDIO (ABAJO) === */
            .controles-audio {
                grid-column: 1 / -1;
                grid-row: 4;
                background: #637983;
                border-radius: 15px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                padding: 20px;
                text-align: center;
                margin-top: 20px;
            }

            .botones-audio {
                display: flex;
                justify-content: center;
                gap: 15px;
                flex-wrap: wrap;
            }

            .btn-audio {
                border: none;
                border-radius: 10px;
                padding: 12px 20px;
                cursor: pointer;
                font-weight: bold;
                font-size: 14px;
                transition: all 0.3s ease;
                /*box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);/**/
                min-width: 120px;/**/
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .btn-iniciar {
                /*background: linear-gradient(135deg, #28a745, #20c997);/**/
                background: transparent;
                color: #EAE4D5;
            }

            .btn-pausar {
                /*background: linear-gradient(135deg, #ffc107, #fd7e14);/**/
                background: transparent;
                color: #EAE4D5;
            }

            .btn-reanudar {
                /*background: linear-gradient(135deg, #17a2b8, #6f42c1);/**/
                background: transparent;
                color: #EAE4D5;
            }

            .btn-detener {
                /*background: linear-gradient(135deg, #dc3545, #c82333);/**/
                background: transparent;
                color: #EAE4D5;
            }

            .btn-audio img {
                width: 20px;
                height: 20px;
            }

            .btn-audio:hover:not(:disabled) {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);/***/
            }

            .btn-audio:disabled {
                background: linear-gradient(135deg, #6c757d, #495057);
                color: #adb5bd;
                cursor: not-allowed;
                transform: none;
                box-shadow: none;
                opacity: 0.6;

                display: none;
            }

            /* === EFECTO KARAOKE === */
            #bible-text p.activo {
                background: linear-gradient(135deg, #fff9e6 0%, #ffe9b3 100%);
                border-left: 4px solid #e0a800;
                padding: 15px 20px;
                margin: 10px -20px;
                border-radius: 0 8px 8px 0;
                box-shadow: 0 2px 10px rgba(224, 168, 0, 0.2);
                transition: all 0.3s ease;
            }

        /* === RESPONSIVE === */
        @media screen and (max-width: 1024px) {
            .contenedor-principal {
                grid-template-columns: 1fr;
                grid-template-rows: auto auto auto 1fr auto;
                gap: 15px;
            }

            .panel-controles {
                grid-column: 1;
                grid-row: 3;
                padding: 15px;
                max-height: none;
            }

            .contenedor-versiculos {
                grid-column: 1;
                grid-row: 4;
                max-height: 50vh;
            }

            .controles-audio {
                grid-column: 1;
                grid-row: 5;
            }
        }

        @media screen and (max-width: 768px) {
            .contenedor-principal {
                padding: 15px;
            }

            header h1 {
                font-size: 32px;
            }

            /* BOTONES DE AUDIO EN FILA PARA TABLETS */
            .botones-audio {
                flex-direction: row; /* Cambiado de column a row */
                justify-content: center; /* Centra los botones */
                flex-wrap: wrap; /* Permite que se envuelvan si no caben */
                gap: 10px;
            }

            .btn-audio {
                width: auto; /* Cambiado de 100% a auto */
                max-width: 110px; /* Un poco más ancho para mejor visualización */
                min-width: 90px; /* Ancho mínimo consistente */
            }

            .contenedor-versiculos {
                max-height: 40vh;
            }
        }

        @media screen and (max-width: 480px) {
            .contenedor-principal {
                grid-template-rows: auto auto auto 1fr auto;
                gap: 12px;
            }

            header h1 {
                font-size: 28px;
            }

            .versiculo-destacado {
                padding: 15px;
            }

            .versiculo-destacado p {
                font-size: 16px;
            }

            /* PANEL DE CONTROLES REDISEÑADO PARA MÓVIL */
            .panel-controles {
                grid-column: 1;
                grid-row: 3;
                padding: 15px;
                max-height: none;
            }

            .formulario-seleccion {
                flex-direction: row;
                gap: 10px;
                align-items: flex-end;
            }

            .grupo-campo {
                flex: 1;
                min-width: 0;
            }

            .grupo-campo label {
                font-size: 12px;
                margin-bottom: 5px;
            }

            .formulario-seleccion select {
                padding: 10px 8px;
                font-size: 14px;
            }

            .formulario-seleccion button {
                padding: 10px 15px;
                font-size: 14px;
                margin-top: 0;
                white-space: nowrap;
            }

            /* CONTENEDOR DE VERSÍCULOS */
            .contenedor-versiculos {
                grid-column: 1;
                grid-row: 4;
                max-height: 45vh;
                min-height: 300px;
            }

            #bible-text {
                padding: 15px;
            }

            #bible-text p {
                font-size: 15px;
                margin-bottom: 15px;
                line-height: 1.5;
            }

            /* CONTROLES DE AUDIO EN FILA PARA MÓVILES */
            .controles-audio {
                grid-column: 1;
                grid-row: 5;
                padding: 15px;
            }

            .botones-audio {
                flex-direction: row; /* Mantener en fila */
                justify-content: center;
                flex-wrap: wrap; /* Envolver si es necesario */
                gap: 8px;
            }

            .btn-audio {
                padding: 10px 12px;
                font-size: 12px;
                min-width: 80px; /* Ancho mínimo para móviles */
                max-width: 100px;
            }
        }

        /* RESPONSIVE EXTRA PARA PANTALLAS MUY PEQUEÑAS */
        @media screen and (max-width: 360px) {
            .formulario-seleccion {
                flex-direction: column;
                gap: 8px;
            }

            .grupo-campo {
                width: 100%;
            }

            .formulario-seleccion button {
                width: 100%;
            }

            /* BOTONES DE AUDIO MANTENIÉNDOSE EN FILA PERO MÁS COMPACTOS */
            .botones-audio {
                flex-direction: row; /* Mantener en fila incluso en pantallas pequeñas */
                justify-content: space-between; /* Distribuir espacio uniformemente */
                flex-wrap: nowrap; /* Evitar que se envuelvan */
                gap: 5px;
            }

            .btn-audio {
                padding: 8px 10px;
                font-size: 11px;
                min-width: 70px;
                max-width: 75px;
                flex: 1; /* Que ocupen el espacio disponible equitativamente */
            }

            /* Opcional: Ocultar texto en botones y solo mostrar iconos en pantallas muy pequeñas */
            @media screen and (max-width: 320px) {
                .btn-audio {
                    padding: 8px 5px;
                    font-size: 0; /* Ocultar texto */
                    min-width: 50px;
                    max-width: 55px;
                }
                
                .btn-audio::before {
                    content: attr(title); /* Mostrar tooltip al hover */
                    font-size: 11px;
                    position: absolute;
                    bottom: -25px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: #333;
                    color: white;
                    padding: 4px 8px;
                    border-radius: 4px;
                    white-space: nowrap;
                    opacity: 0;
                    transition: opacity 0.3s;
                    pointer-events: none;
                }
                
                .btn-audio:hover::before {
                    opacity: 1;
                }
            }
        }

        /* === ANIMACIONES === */
        .contenedor-versiculos,
        .panel-controles,
        .controles-audio,
        .versiculo-destacado {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }






























        /* === CONTROL PERSONALIZADO DE TAMAÑO DE LETRA === */
        .control-tamano-personalizado {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #EAE4D5;

            background: #637983;
            border-radius: 15px;
            color: #EAE4D5;
            padding-right: 20px;
            padding-left: 20px;
        }

        .control-deslizador {
            position: relative;
            margin: 15px 0 10px 0;
        }

        .deslizador-tamano {
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: #EAE4D5;
            outline: none;
            -webkit-appearance: none;
        }

        .deslizador-tamano::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #637983;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .deslizador-tamano::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #637983;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .marcas-tamano {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 11px;
            color: #637983;
        }

        .marcas-tamano span {
            cursor: pointer;
            padding: 2px 4px;
            border-radius: 3px;
            transition: background-color 0.2s;
        }

        .marcas-tamano span:hover {
            background-color: #EAE4D5;
        }/* * */

        .botones-rapidos {
            display: flex;
            justify-content: space-between;
            gap: 5px;
            margin-top: 10px;
        }

        .btn-rapido {
            flex: 1;
            padding: 6px 2px;
            border: 1px solid #637983;
            background: #637983;
            color: #EAE4D5;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            min-height: 30px;
            font-size: 12px;
        }

        .btn-rapido:hover {
            background: #637983;
            color: white;
            transform: translateY(-1px);
        }

        .btn-rapido.activo {
            background: #384A49;
            color: white;
            border-color: #384A49;
        }

        /* Tamaños específicos para los botones rápidos */
        .botones-rapidos button:nth-child(1) { font-size: 12px; }
        .botones-rapidos button:nth-child(2) { font-size: 14px; }
        .botones-rapidos button:nth-child(3) { font-size: 16px; }
        .botones-rapidos button:nth-child(4) { font-size: 18px; }
        .botones-rapidos button:nth-child(5) { font-size: 20px; }

        #valor-tamano {
            font-weight: bold;
            color: #384A49;
            background: #EAE4D5;
            padding: 2px 6px;
            border-radius: 4px;
            min-width: 30px;
            display: inline-block;
            text-align: center;
        }

        /* === RESPONSIVE PARA EL CONTROL DE TAMAÑO === */
        @media screen and (max-width: 480px) {
            .control-tamano-personalizado {
                margin-top: 15px;
                padding-top: 15px;
            }
            
            .botones-rapidos {
                gap: 3px;
            }
            
            .btn-rapido {
                padding: 4px 1px;
                font-size: 10px;
                min-height: 26px;
            }
            
            .marcas-tamano {
                font-size: 10px;
            }
            
            .deslizador-tamano {
                height: 4px;
            }
            
            .deslizador-tamano::-webkit-slider-thumb {
                width: 16px;
                height: 16px;
            }
        }

        /* === ESTILOS PARA EL CONTROL DE TAMAÑO EN MÓVILES === */

        /* Botón para abrir modal en móviles (oculto por defecto) */**/
        .btn-tamano-movil {
            display: none;
            background: #637983;
            color: #EAE4D5;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            width: 100%;
            justify-content: center;
        }

        .btn-tamano-movil:hover {
            background: #2a3e42;
            transform: translateY(-2px);
        }

        .btn-tamano-movil img {
            width: 20px;
            height: 20px;
        }

        /* Modal para control de tamaño */
        .modal-tamano {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .modal-tamano.activo {
            display: flex;
        }

        .modal-contenido {
            background: #EAE4D5;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            animation: fadeInUp 0.3s ease-out;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h3 {
            margin: 0;
            color: #2a3e42;
            font-family: 'Sansation', sans-serif;
        }

        .btn-cerrar-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #637983;
            transition: color 0.3s;
        }

        .btn-cerrar-modal:hover {
            color: #2a3e42;
        }

        .modal-body {
            padding: 20px;
            border-radius: 15px;
            background: #637983;
            color: #EAE4D5;
        }

        .control-tamano-modal {
            margin: 0;
            padding: 0;
            background: transparent;
        }

        .control-deslizador-modal {
            position: relative;
            margin: 15px 0 10px 0;
        }

        .deslizador-tamano-modal {
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: #EAE4D5;
            outline: none;
            -webkit-appearance: none;
        }

        .deslizador-tamano-modal::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #637983;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .deslizador-tamano-modal::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #637983;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .marcas-tamano-modal {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 11px;
            color: #637983;
        }

        .marcas-tamano-modal span {
            cursor: pointer;
            padding: 2px 4px;
            border-radius: 3px;
            transition: background-color 0.2s;
        }

        .marcas-tamano-modal span:hover {
            background-color: #EAE4D5;
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            text-align: right;
        }

        .btn-aplicar-tamano {
            background: #637983;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-aplicar-tamano:hover {
            background: #2a3e42;
            transform: translateY(-2px);
        }

        /* === RESPONSIVE: MOSTRAR BOTÓN EN MÓVILES, OCULTAR CONTROL ORIGINAL === */
        @media screen and (max-width: 1024px) {
            .control-tamano-personalizado {
                display: none !important;
            }
            
            .btn-tamano-movil {
                display: flex;
                justify-content: center;
            }
        }

        /* Para pantallas grandes, mantener control original y ocultar botón */
        @media screen and (min-width: 1025px) {
            .btn-tamano-movil {
                display: none !important;
            }
            
            .control-tamano-personalizado {
                display: block !important;
            }
        }



































        /* Estado inicial de los controles de audio */
        .controles-audio {
            display: none;
        }

        /* Estado inicial del versículo aleatorio */
        .versiculo-destacado {
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


<main>
    <div class="contenedor-principal">
        <!-- Encabezado -->
        <header>
            <button onclick="window.history.back()" class="btn-retorno"></button>
            <h1>Lector de la Biblia</h1>
        </header>

        <!-- Versículo aleatorio destacado (DEBAJO DEL TÍTULO) -->
        <?php if ($versiculo_destacado): ?>
        <div id="Versiculo_Aleatorio" class="versiculo-destacado">
            <p>
                <strong><?= $versiculo_destacado['libro'] . " " . $versiculo_destacado['capitulo'] . ":" . $versiculo_destacado['versiculo'] ?>:</strong>
                <?= $versiculo_destacado['texto'] ?>
            </p>
        </div>
        <?php endif; ?>

        <!-- Contenedor de versículos con scroll -->
        <div class="contenedor-versiculos">
            <?php if (!empty($versiculos)): ?>
                <div id="bible-text">
                    <?php foreach ($versiculos as $verso): ?>
                    <p><strong><?= $verso['versiculo'] ?>:</strong> <?= $verso['texto'] ?></p>
                    <?php endforeach; ?>
                </div>
                <script>
                    // Aplicar el tamaño guardado cuando se cargan nuevos versículos
                    setTimeout(() => {
                        aplicarTamanoAContenidoNuevo();
                    }, 100);
                </script>
            <?php endif; ?>
        </div>

        <!-- Panel de controles (derecha) -->
        <div class="panel-controles">
            <form method="POST" class="formulario-seleccion">
                <div class="grupo-campo">
                    <label for="libro" style="color: #384A49">Libro:</label>
                    <select name="libro" id="libro" onchange="this.form.submit()">
                        <!-- Viejo Testamento -->
                        <optgroup label="📜 Viejo Testamento">
                            <?php
                            $viejo_testamento = [
                                'Génesis','Éxodo','Levítico','Números','Deuteronomio',
                                'Josué','Jueces','Rut','1 Samuel','2 Samuel','1 Reyes','2 Reyes',
                                '1 Crónicas','2 Crónicas','Esdras','Nehemías','Ester',
                                'Job','Salmos','Proverbios','Eclesiastés','Cantares',
                                'Isaías','Jeremías','Lamentaciones','Ezequiel','Daniel',
                                'Oseas','Joel','Amós','Abdías','Jonás','Miqueas','Nahúm',
                                'Habacuc','Sofonías','Hageo','Zacarías','Malaquías'
                            ];

                            foreach ($libros as $libro):
                                if (in_array($libro['libro'], $viejo_testamento)):
                            ?>
                            <option value="<?= htmlspecialchars($libro['libro']) ?>"
                                <?= $libro_seleccionado === $libro['libro'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($libro['libro']) ?>
                            </option>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </optgroup>

                        <!-- Nuevo Testamento -->
                        <optgroup label="✝️ Nuevo Testamento">
                            <?php
                            $nuevo_testamento = [
                                'Mateo','Marcos','Lucas','Juan','Hechos','Romanos',
                                '1 Corintios','2 Corintios','Gálatas','Efesios','Filipenses','Colosenses',
                                '1 Tesalonicenses','2 Tesalonicenses','1 Timoteo','2 Timoteo','Tito','Filemón',
                                'Hebreos','Santiago','1 Pedro','2 Pedro','1 Juan','2 Juan','3 Juan','Judas','Apocalipsis'
                            ];

                            foreach ($libros as $libro):
                                if (in_array($libro['libro'], $nuevo_testamento)):
                            ?>
                            <option value="<?= htmlspecialchars($libro['libro']) ?>"
                                <?= $libro_seleccionado === $libro['libro'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($libro['libro']) ?>
                            </option>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </optgroup>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label for="capitulo" style="color: #384A49">Capítulo:</label>
                    <select name="capitulo" id="capitulo">
                        <?php foreach ($capitulos_disponibles as $cap): ?>
                        <option value="<?= $cap ?>" <?= $capitulo_seleccionado === intval($cap) ? 'selected' : '' ?>>
                            <?= $cap ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="button" onclick="manejarClickLeer()">Leer</button>





                <!-- CONTROL PERSONALIZADO DE TAMAÑO DE LETRA -->
                <div class="grupo-campo control-tamano-personalizado">
                    <label for="tamano-letra" style="color: #EAE4D5">
                        Tamaño letra: <span id="valor-tamano">16</span>px
                    </label>
                    <div class="control-deslizador">
                        <input type="range" 
                            id="tamano-letra" 
                            min="12" 
                            max="30" 
                            value="16"
                            step="1"
                            class="deslizador-tamano"><!-- value original = 16 -->
                        <div class="marcas-tamano">
                            <span style="color: #EAE4D5">12</span>
                            <span style="color: #EAE4D5">16</span>
                            <span style="color: #EAE4D5">20</span>
                            <span style="color: #EAE4D5">24</span>
                            <span style="color: #EAE4D5">30</span>
                        </div>
                    </div>
                    <!--<div class="botones-rapidos">
                        <button type="button" class="btn-rapido" onclick="establecerTamanoRapido(14)">A</button>
                        <button type="button" class="btn-rapido" onclick="establecerTamanoRapido(16)">A</button>
                        <button type="button" class="btn-rapido" onclick="establecerTamanoRapido(18)">A</button>
                        <button type="button" class="btn-rapido" onclick="establecerTamanoRapido(20)">A</button>
                        <button type="button" class="btn-rapido" onclick="establecerTamanoRapido(22)">A</button>
                    </div>-->
                </div>
                
                <!-- Botón para abrir modal en móviles/tablets -->
                <button type="button" id="btn-tamano-movil" class="btn-tamano-movil">
                    <span>Tamaño</span>
                </button>
            </form>
            
            <!-- Modal para control de tamaño en móviles/tablets -->
            <div id="modal-tamano" class="modal-tamano">
                <div class="modal-contenido">
                    <div class="modal-header">
                        <h3>Ajustar tamaño de texto</h3>
                        <button type="button" class="btn-cerrar-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="control-tamano-modal">
                            <label for="tamano-letra-modal" style="color: #EAE4D5">
                                Tamaño letra: <span id="valor-tamano-modal">16</span>px
                            </label>
                            <div class="control-deslizador-modal">
                                <input type="range" 
                                    id="tamano-letra-modal" 
                                    min="12" 
                                    max="30" 
                                    value="16"
                                    step="1"
                                    class="deslizador-tamano-modal">
                                <div class="marcas-tamano-modal">
                                    <span style="color: #EAE4D5">12</span>
                                    <span style="color: #EAE4D5">16</span>
                                    <span style="color: #EAE4D5">20</span>
                                    <span style="color: #EAE4D5">24</span>
                                    <span style="color: #EAE4D5">30</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-aplicar-tamano">Aplicar</button>
                    </div>
                </div>
            </div>

            <script>
                // === CONTROL PERSONALIZADO DE TAMAÑO DE LETRA ===
                let tamanoLetraActual = 16; // Tamaño por defecto

                // Inicializar el control deslizante
                document.addEventListener('DOMContentLoaded', function() {
                    const deslizador = document.getElementById('tamano-letra');
                    const deslizadorModal = document.getElementById('tamano-letra-modal');
                    const valorDisplay = document.getElementById('valor-tamano');
                    const valorDisplayModal = document.getElementById('valor-tamano-modal');
                    const modal = document.getElementById('modal-tamano');
                    const btnAbrirModal = document.getElementById('btn-tamano-movil');
                    const btnCerrarModal = document.querySelector('.btn-cerrar-modal');
                    const btnAplicarTamano = document.querySelector('.btn-aplicar-tamano');
                    
                    // Cargar preferencia guardada
                    const tamanoGuardado = localStorage.getItem('tamanoLetraPersonalizado');
                    if (tamanoGuardado) {
                        tamanoLetraActual = parseInt(tamanoGuardado);
                        deslizador.value = tamanoLetraActual;
                        deslizadorModal.value = tamanoLetraActual;
                        valorDisplay.textContent = tamanoLetraActual;
                        valorDisplayModal.textContent = tamanoLetraActual;
                        aplicarTamanoLetra(tamanoLetraActual);
                    }
                    
                    // Evento para el deslizador principal
                    deslizador.addEventListener('input', function() {
                        const nuevoTamano = parseInt(this.value);
                        valorDisplay.textContent = nuevoTamano;
                        aplicarTamanoLetra(nuevoTamano);
                    });
                    
                    // Evento para el deslizador del modal
                    deslizadorModal.addEventListener('input', function() {
                        const nuevoTamano = parseInt(this.value);
                        valorDisplayModal.textContent = nuevoTamano;
                    });
                    
                    // Eventos para las marcas del control principal
                    const marcas = document.querySelectorAll('.marcas-tamano span');
                    marcas.forEach(marca => {
                        marca.addEventListener('click', function() {
                            const tamano = parseInt(this.textContent);
                            deslizador.value = tamano;
                            valorDisplay.textContent = tamano;
                            aplicarTamanoLetra(tamano);
                        });
                    });
                    
                    // Eventos para las marcas del modal
                    const marcasModal = document.querySelectorAll('.marcas-tamano-modal span');
                    marcasModal.forEach(marca => {
                        marca.addEventListener('click', function() {
                            const tamano = parseInt(this.textContent);
                            deslizadorModal.value = tamano;
                            valorDisplayModal.textContent = tamano;
                        });
                    });
                    
                    // Abrir modal
                    btnAbrirModal.addEventListener('click', function() {
                        modal.classList.add('activo');
                    });
                    
                    // Cerrar modal
                    btnCerrarModal.addEventListener('click', function() {
                        modal.classList.remove('activo');
                    });
                    
                    // Aplicar tamaño desde modal
                    btnAplicarTamano.addEventListener('click', function() {
                        const nuevoTamano = parseInt(deslizadorModal.value);
                        aplicarTamanoLetra(nuevoTamano);
                        deslizador.value = nuevoTamano;
                        valorDisplay.textContent = nuevoTamano;
                        modal.classList.remove('activo');
                    });
                    
                    // Cerrar modal al hacer clic fuera
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            modal.classList.remove('activo');
                        }
                    });
                    
                    // Aplicar tamaño inicial
                    aplicarTamanoLetra(tamanoLetraActual);
                });

                // Función para aplicar el tamaño de letra
                function aplicarTamanoLetra(tamano) {
                    const bibleText = document.getElementById('bible-text');
                    if (bibleText) {
                        // Aplicar a todos los párrafos dentro del contenedor
                        const parrafos = bibleText.querySelectorAll('p');
                        parrafos.forEach(p => {
                            p.style.fontSize = tamano + 'px';
                        });
                        
                        // Actualizar estado visual
                        actualizarBotonesRapidos(tamano);
                        
                        // Guardar preferencia
                        tamanoLetraActual = tamano;
                        localStorage.setItem('tamanoLetraPersonalizado', tamano);
                    }
                }

                // Función para botones rápidos
                function establecerTamanoRapido(tamano) {
                    const deslizador = document.getElementById('tamano-letra');
                    const valorDisplay = document.getElementById('valor-tamano');
                    const deslizadorModal = document.getElementById('tamano-letra-modal');
                    const valorDisplayModal = document.getElementById('valor-tamano-modal');
                    
                    deslizador.value = tamano;
                    deslizadorModal.value = tamano;
                    valorDisplay.textContent = tamano;
                    valorDisplayModal.textContent = tamano;
                    aplicarTamanoLetra(tamano);
                }

                // Actualizar estado visual de botones rápidos
                function actualizarBotonesRapidos(tamanoActual) {
                    const botones = document.querySelectorAll('.btn-rapido');
                    botones.forEach(boton => {
                        // Extraer el tamaño del onclick
                        const match = boton.getAttribute('onclick').match(/\((\d+)\)/);
                        if (match) {
                            const tamanoBoton = parseInt(match[1]);
                            if (tamanoBoton === tamanoActual) {
                                boton.classList.add('activo');
                            } else {
                                boton.classList.remove('activo');
                            }
                        }
                    });
                }

                // Función para aplicar el tamaño cuando se cargan nuevos versículos
                function aplicarTamanoAContenidoNuevo() {
                    aplicarTamanoLetra(tamanoLetraActual);
                }
            </script>







            

        </div>

        










        

        <!-- Controles de audio (abajo) -->
        <div id="Audio_Controles" class="controles-audio">
            <div class="botones-audio">
                <button id="btnIniciar" class="btn-audio btn-iniciar" onclick="iniciarLectura()" title="Iniciar nueva lectura">
                    <img src="http://pwagacandsv.rf.gd/iconos/Play.png" alt="Iniciar">
                </button>
                <button id="btnPausar" class="btn-audio btn-pausar" onclick="pausarLectura()" title="Pausar lectura actual" disabled>
                    <img src="http://pwagacandsv.rf.gd/iconos/Pausa.png" alt="Pausar">
                </button>
                <button id="btnReanudar" class="btn-audio btn-reanudar" onclick="reanudarLectura()" title="Reanudar desde pausa" disabled>
                    <img src="http://pwagacandsv.rf.gd/iconos/Reanudar.png" alt="Reanudar">
                </button>
                <button id="btnDetener" class="btn-audio btn-detener" onclick="detenerLectura()" title="Detener y reiniciar" disabled>
                    <img src="http://pwagacandsv.rf.gd/iconos/Detener.png" alt="Detener">
                </button>
            </div>
        </div>

        <!-- JavaScript de los Controles de audio -->
        <!-- JavaScript de los Controles de audio MEJORADO -->
        <script>
            // =============================================
            // SISTEMA DE LECTURA DE VOZ OFFLINE - CORREGIDO
            // =============================================
            
            // Variables globales para control del estado
            let synth = window.speechSynthesis;
            let vocesDisponibles = [];
            let timeoutEntreVersiculos = null; // Control para el timeout entre versículos
            
            let estado = {
                leyendo: false,
                pausado: false,
                indiceVersiculo: 0,
                utteranceActual: null,
                versiculos: [],
                vozSeleccionada: null,
                tiempoEntreVersiculos: 800,
                versiculoActual: null,
                // Nuevo: control de timeout activo
                timeoutActivo: false
            };

            // =============================================
            // INICIALIZACIÓN DEL SISTEMA
            // =============================================

            function inicializarSistemaVoz() {
                console.log('🔊 Inicializando sistema de voz offline...');
                cargarVocesDisponibles();
                
                if (speechSynthesis.onvoiceschanged !== undefined) {
                    speechSynthesis.onvoiceschanged = cargarVocesDisponibles;
                }
                
                // Múltiples intentos de carga de voces
                [500, 1000, 2000].forEach(timeout => {
                    setTimeout(cargarVocesDisponibles, timeout);
                });
            }

            function cargarVocesDisponibles() {
                const voces = synth.getVoices();
                if (voces.length === 0) return;
                
                vocesDisponibles = voces;
                seleccionarMejorVoz();
            }

            function seleccionarMejorVoz() {
                const vocesEspanolLocal = vocesDisponibles.filter(v => 
                    v.lang.startsWith('es') && v.localService
                );
                
                if (vocesEspanolLocal.length > 0) {
                    estado.vozSeleccionada = vocesEspanolLocal[0];
                } else if (vocesDisponibles.length > 0) {
                    estado.vozSeleccionada = vocesDisponibles[0];
                }
            }

            // =============================================
            // BOTONES SEPARADOS - MEJORADOS
            // =============================================

            /**
            * BOTÓN PLAY - Solo inicia nueva lectura
            */
            function iniciarLectura() {
                console.log('🟢 SOLO PLAY: Iniciando nueva lectura');
                
                // Validaciones básicas
                if (!('speechSynthesis' in window)) {
                    alert('❌ Tu navegador no soporta lectura de voz.');
                    return;
                }

                if (!estado.vozSeleccionada) {
                    alert('❌ No se encontraron voces disponibles.');
                    return;
                }

                // SIEMPRE reiniciar cuando se presiona Play
                detenerLecturaCompletamente();
                estado.indiceVersiculo = 0;

                // Obtener versículos
                estado.versiculos = Array.from(document.querySelectorAll("#bible-text p"));
                
                if (estado.versiculos.length === 0) {
                    alert("📭 No hay versículos para leer.");
                    return;
                }

                // Actualizar estado
                estado.leyendo = true;
                estado.pausado = false;
                
                console.log(`📖 Iniciando lectura de ${estado.versiculos.length} versículos`);
                
                // Actualizar botones
                actualizarBotones();
                
                // Comenzar lectura
                leerVersiculoActual();
            }

            /**
            * BOTÓN PAUSA - Solo pausa la lectura actual
            */
            function pausarLectura() {
                console.log('⏸️ SOLO PAUSA: Pausando lectura');
                
                if (estado.leyendo && !estado.pausado) {
                    // Cancelar cualquier timeout pendiente entre versículos
                    if (timeoutEntreVersiculos) {
                        clearTimeout(timeoutEntreVersiculos);
                        timeoutEntreVersiculos = null;
                        estado.timeoutActivo = false;
                        console.log('⏹️ Timeout entre versículos cancelado');
                    }
                    
                    // Pausar síntesis de voz
                    if (synth.speaking) {
                        synth.pause();
                    }
                    
                    estado.pausado = true;
                    console.log(`📌 Pausado en versículo ${estado.indiceVersiculo + 1}`);
                }
                
                actualizarBotones();
            }

            /**
            * BOTÓN REANUDAR - Solo reanuda desde donde se pausó
            */
            function reanudarLectura() {
                console.log('🔵 SOLO REANUDAR: Reanudando lectura');
                
                if (estado.pausado && estado.leyendo) {
                    estado.pausado = false;
                    
                    if (synth.paused) {
                        // Reanudar síntesis técnicamente pausada
                        synth.resume();
                        console.log(`▶️ Reanudando versículo ${estado.indiceVersiculo + 1}`);
                    } else {
                        // Continuar con el versículo actual (no había síntesis activa)
                        // Esto pasa cuando se pausó durante el timeout entre versículos
                        console.log(`▶️ Continuando desde pausa en versículo ${estado.indiceVersiculo}`);
                        leerVersiculoActual();
                    }
                }
                
                actualizarBotones();
            }

            /**
            * BOTÓN STOP - Detiene completamente y reinicia
            */
            function detenerLectura() {
                console.log('🛑 SOLO STOP: Deteniendo lectura');
                detenerLecturaCompletamente();
                actualizarBotones();
            }

            // =============================================
            // NÚCLEO DE LA LECTURA - CORREGIDO
            // =============================================

            /**
            * Lee el versículo actual manteniendo el control del estado
            */
            function leerVersiculoActual() {
                // Verificar condiciones para leer
                if (!estado.leyendo || estado.pausado) {
                    console.log('🚫 No se puede leer - Estado:', { 
                        leyendo: estado.leyendo, 
                        pausado: estado.pausado 
                    });
                    return;
                }

                // Verificar fin del capítulo
                if (estado.indiceVersiculo >= estado.versiculos.length) {
                    console.log('🎉 Capítulo completado');
                    finalizarLectura();
                    return;
                }

                // Obtener versículo actual
                const versiculo = estado.versiculos[estado.indiceVersiculo];
                const texto = versiculo.innerText.trim();

                if (!texto) {
                    console.log(`📭 Versículo ${estado.indiceVersiculo + 1} vacío, saltando...`);
                    estado.indiceVersiculo++;
                    programarSiguienteVersiculo();
                    return;
                }

                // Resaltar visualmente
                resaltarVersiculoActual(versiculo);

                // Crear y configurar utterance
                const utterance = new SpeechSynthesisUtterance(texto);
                configurarUtterance(utterance);

                // Configurar eventos
                utterance.onstart = () => {
                    console.log(`🎤 INICIANDO: Versículo ${estado.indiceVersiculo + 1}`);
                    estado.versiculoActual = versiculo;
                    estado.timeoutActivo = false; // No hay timeout activo durante lectura
                };

                utterance.onend = () => {
                    console.log(`✅ TERMINADO: Versículo ${estado.indiceVersiculo + 1}`);
                    
                    // SOLO avanzar si estamos en estado activo y no pausado
                    if (estado.leyendo && !estado.pausado) {
                        estado.indiceVersiculo++;
                        programarSiguienteVersiculo();
                    }
                };

                utterance.onerror = (event) => {
                    console.error(`❌ ERROR: Versículo ${estado.indiceVersiculo + 1} -`, event.error);
                    
                    // En error, avanzar solo si estamos activos
                    if (estado.leyendo && !estado.pausado) {
                        estado.indiceVersiculo++;
                        programarSiguienteVersiculo();
                    }
                };

                // Reproducir
                reproducirUtterance(utterance);
            }

            /**
            * Programa el siguiente versículo con control de timeout
            */
            function programarSiguienteVersiculo() {
                // Limpiar timeout anterior si existe
                if (timeoutEntreVersiculos) {
                    clearTimeout(timeoutEntreVersiculos);
                }
                
                // Solo programar si estamos en estado activo
                if (estado.leyendo && !estado.pausado) {
                    estado.timeoutActivo = true;
                    timeoutEntreVersiculos = setTimeout(() => {
                        estado.timeoutActivo = false;
                        leerVersiculoActual();
                    }, estado.tiempoEntreVersiculos);
                    
                    console.log(`⏱️ Timeout programado para siguiente versículo en ${estado.tiempoEntreVersiculos}ms`);
                }
            }

            function configurarUtterance(utterance) {
                utterance.rate = 0.85;
                utterance.pitch = 1.0;
                utterance.volume = 1.0;
                utterance.lang = 'es-ES';
                
                if (estado.vozSeleccionada) {
                    utterance.voice = estado.vozSeleccionada;
                }
            }

            function reproducirUtterance(utterance) {
                try {
                    // Limpiar cualquier utterance anterior
                    if (synth.speaking) {
                        synth.cancel();
                    }
                    
                    // Pequeño delay para estabilidad
                    setTimeout(() => {
                        if (estado.leyendo && !estado.pausado) {
                            synth.speak(utterance);
                        }
                    }, 50);
                    
                } catch (error) {
                    console.error('❌ Error al reproducir:', error);
                    // En error, continuar con siguiente versículo
                    if (estado.leyendo && !estado.pausado) {
                        estado.indiceVersiculo++;
                        programarSiguienteVersiculo();
                    }
                }
            }

            // =============================================
            // FUNCIONES AUXILIARES - MEJORADAS
            // =============================================

            function detenerLecturaCompletamente() {
                try {
                    // Cancelar síntesis
                    synth.cancel();
                } catch (error) {
                    console.log('⚠️ Error al cancelar síntesis:', error);
                }
                
                // Cancelar timeout entre versículos
                if (timeoutEntreVersiculos) {
                    clearTimeout(timeoutEntreVersiculos);
                    timeoutEntreVersiculos = null;
                }
                
                // Reiniciar TODO
                estado.leyendo = false;
                estado.pausado = false;
                estado.indiceVersiculo = 0;
                estado.utteranceActual = null;
                estado.versiculoActual = null;
                estado.timeoutActivo = false;
                
                // Limpiar efectos visuales
                document.querySelectorAll("#bible-text p").forEach(v => v.classList.remove("activo"));
                
                console.log('🛑 Lectura completamente detenida y reiniciada');
            }

            function finalizarLectura() {
                // Limpiar timeout
                if (timeoutEntreVersiculos) {
                    clearTimeout(timeoutEntreVersiculos);
                    timeoutEntreVersiculos = null;
                }
                
                estado.leyendo = false;
                estado.pausado = false;
                estado.timeoutActivo = false;
                document.querySelectorAll("#bible-text p").forEach(v => v.classList.remove("activo"));
                actualizarBotones();
                console.log('🎊 Lectura finalizada correctamente');
            }

            function resaltarVersiculoActual(versiculo) {
                estado.versiculos.forEach(v => v.classList.remove("activo"));
                versiculo.classList.add("activo");
                versiculo.scrollIntoView({ 
                    behavior: "smooth", 
                    block: "center" 
                });
            }

            /**
            * Actualiza el estado visual de los botones según el estado actual
            */
            function actualizarBotones() {
                const btnIniciar = document.getElementById("btnIniciar");
                const btnPausar = document.getElementById("btnPausar");
                const btnReanudar = document.getElementById("btnReanudar");
                const btnDetener = document.getElementById("btnDetener");

                // Estado por defecto (inactivo)
                if (!estado.leyendo && !estado.pausado) {
                    btnIniciar.disabled = false;
                    btnPausar.disabled = true;
                    btnReanudar.disabled = true;
                    btnDetener.disabled = true;
                }
                // Leyendo activamente
                else if (estado.leyendo && !estado.pausado) {
                    btnIniciar.disabled = true;
                    btnPausar.disabled = false;
                    btnReanudar.disabled = true;
                    btnDetener.disabled = false;
                }
                // En pausa
                else if (estado.pausado) {
                    btnIniciar.disabled = true;
                    btnPausar.disabled = true;
                    btnReanudar.disabled = false;
                    btnDetener.disabled = false;
                }

                console.log('🔘 Estado botones:', {
                    iniciar: btnIniciar.disabled ? 'disabled' : 'enabled',
                    pausar: btnPausar.disabled ? 'disabled' : 'enabled', 
                    reanudar: btnReanudar.disabled ? 'disabled' : 'enabled',
                    detener: btnDetener.disabled ? 'disabled' : 'enabled',
                    timeoutActivo: estado.timeoutActivo
                });
            }

            // =============================================
            // INICIALIZACIÓN
            // =============================================

            document.addEventListener('DOMContentLoaded', function() {
                inicializarSistemaVoz();
                actualizarBotones(); // Estado inicial de botones
            });

            window.addEventListener('beforeunload', detenerLecturaCompletamente);

        </script>










        <!-- JavaScript para control de visibilidad -->
        <script>
            // === CONTROL DE VISIBILIDAD DE ELEMENTOS CON PERSISTENCIA ===
            
            // Función principal que maneja el clic en "Leer"
            function manejarClickLeer() {
                // Guardar estado ANTES de enviar el formulario
                localStorage.setItem('elementosVisibilidad', 'ocultos');
                
                console.log('🔘 Estado guardado: versículo aleatorio oculto, controles de audio visibles');
                
                // Enviar el formulario después de un pequeño delay
                setTimeout(() => {
                    document.querySelector('.formulario-seleccion').submit();
                }, 100);
            }

            // Función para aplicar el estado guardado al cargar la página
            function aplicarEstadoVisibilidad() {
                const estadoGuardado = localStorage.getItem('elementosVisibilidad');
                const versiculoAleatorio = document.getElementById("Versiculo_Aleatorio");
                const audioControles = document.getElementById("Audio_Controles");
                
                if (estadoGuardado === 'ocultos') {
                    // Estado cuando se ha hecho clic en "Leer"
                    if (versiculoAleatorio) {
                        versiculoAleatorio.style.display = 'none';
                    }
                    if (audioControles) {
                        audioControles.style.display = 'block';
                    }
                    console.log('🔘 Estado aplicado: elementos ocultos (desde localStorage)');
                } else {
                    // Estado por defecto (primera carga)
                    if (audioControles) {
                        audioControles.style.display = 'none';
                    }
                    if (versiculoAleatorio) {
                        versiculoAleatorio.style.display = 'block';
                    }
                    console.log('🔘 Estado por defecto aplicado');
                }
            }

            // Función para resetear el estado (si necesitas volver al inicio)
            function resetearEstadoVisibilidad() {
                localStorage.removeItem('elementosVisibilidad');
                aplicarEstadoVisibilidad();
                console.log('🔘 Estado reseteado a valores por defecto');
            }

            // Inicializar al cargar la página
            document.addEventListener('DOMContentLoaded', function() {
                aplicarEstadoVisibilidad();
                
                // También aplicar estado cuando se cargan nuevos versículos
                setTimeout(aplicarEstadoVisibilidad, 200);
            });

            // Limpiar estado si el usuario cierra la pestaña/navegador (opcional)
            window.addEventListener('beforeunload', function() {
                // Si quieres que se resetee al salir, descomenta esta línea:
                // localStorage.removeItem('elementosVisibilidad');
            });
        </script>
    </div>
</main>






</body>
</html>












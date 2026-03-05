<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["rol"])) {
    $_SESSION["rol"] = "invitado";
}

// Incluir archivo de conexión (mysqli)
//require "https://directorioasambleasvzla.com/conexion.php"; // tu archivo de conexión
require __DIR__ . "/../../../conexion.php";
 // tu archivo de conexión

// Asegurar codificación UTF-8
$conn->set_charset("utf8");

// ---------------------------------------------------------
// ➤ 1. Obtener un himno específico por ID (AJAX)
// ---------------------------------------------------------
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM himnos WHERE Numero = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $himno = $result->fetch_assoc();

    if ($himno) {
        echo json_encode($himno, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["error" => "Himno no encontrado"]);
    }
    exit;
}

// ---------------------------------------------------------
// ➤ 2. Obtener TODOS los himnos
// ---------------------------------------------------------
$sql = "SELECT Numero, `Primera_linea`, Letra, Tema FROM himnos ORDER BY Numero ASC";
$result = $conn->query($sql);

$himnos = [];

if ($result && $result->num_rows > 0) {
    $himnos = $result->fetch_all(MYSQLI_ASSOC);
}
?>









<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Himnario Bíblico</title>
    <link rel="stylesheet" href="Himnario.css">

    <style>
        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&display=swap');

        @import url('https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap');

        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&family=Oleo+Script&display=swap');

        /* === FUENTES (igual que en Biblia) === */
        @import url('https://fonts.googleapis.com/css2?family=Sansation&family=Oleo+Script&display=swap');

        /* === MODAL === */
        #modal {
            position: fixed;
            top: 0; 
            left: 0;
            width: 100%; 
            height: 100%;
            background-color: rgba(162, 176, 190, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            padding: 10px;
            box-sizing: border-box;
        }

        #modal.active {
            visibility: visible;
            opacity: 1;
        }

        /* === CONTENIDO DEL MODAL === */
        #contenido-modal {
            position: relative;
            background: #EAE4D5;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 20px;
            max-width: 1000px;
            width: 90%;
            max-height: 90vh;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            animation: slideUp 0.35s ease forwards;
            scrollbar-color: #637983 transparent;
            
            /* FLEXBOX PARA DISTRIBUCIÓN ÓPTIMA */
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* === CONTENIDO PRINCIPAL (OCUPA TODO EL ESPACIO DISPONIBLE) === */
        #contenido-modal .modal-content {
            flex: 1; /* OCUPA TODO EL ESPACIO DISPONIBLE */
            display: flex;
            flex-direction: column;
            overflow: hidden; /* IMPORTANTE: Evitar scroll duplicado */
            min-height: 0; /* PERMITE QUE EL FLEX:1 FUNCIONE CORRECTAMENTE */
        }

        /* === ENCABEZADO DEL MODAL === */
        #contenido-modal h2 {
            font-family: 'Sansation', sans-serif;
            margin: 10px 0 8px 0;
            font-size: 28px;
            color: #2a3e42;
            text-align: left;
            line-height: 1.2;
            flex-shrink: 0; /* NO SE REDUZCA */
        }

        /* Subinformación */
        #contenido-modal p {
            font-size: 16px;
            color: #333;
            margin: 3px 0;
            text-align: right;
            flex-shrink: 0; /* NO SE REDUZCA */
        }

        /* === LETRA DEL HIMNO (OCUPA TODO EL ESPACIO RESTANTE) === */
        #contenido-modal pre {
            white-space: pre-wrap;
            line-height: 1.6;
            font-size: 18px;
            padding: 12px;
            border-radius: 10px;
            margin-top: 10px;
            color: #1c1c1c;
            font-family: 'Sansation', cursive;
            text-align: center;
            
            /* OCUPA TODO EL ESPACIO DISPONIBLE */
            flex: 1; 
            overflow-y: auto; /* SCROLL INTERNO */
            min-height: 150px; /* ALTURA MÍNIMA */
            
            /* ESTILOS DE SCROLL */
            scrollbar-width: thin;
            scrollbar-color: #637983 #f0f0f0;
        }

        #contenido-modal pre::-webkit-scrollbar {
            width: 6px;
        }

        #contenido-modal pre::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 3px;
        }

        #contenido-modal pre::-webkit-scrollbar-thumb {
            background: #637983;
            border-radius: 3px;
        }

        /* === CONTROL DE TAMAÑO (FIJO EN LA PARTE INFERIOR) === */
        #contenido-modal .control-tamano-personalizado {
            margin-top: 15px;
            padding: 12px 15px;
            border-top: 1px solid #d4cdbc;
            background: #637983;
            border-radius: 12px;
            color: #EAE4D5;
            flex-shrink: 0; /* TAMAÑO FIJO - NO SE REDUZCA */
        }

        /* === BOTÓN DE CIERRE === */
        #contenido-modal button {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 38px;
            height: 38px;
            border: none;
            background: transparent;
            color: rgba(42,62,66,0.9);
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
        }

        #contenido-modal button:hover {
            transform: rotate(90deg);
        }

        /* === ANIMACIONES === */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* === RESPONSIVE MEJORADO === */
        @media (max-width: 768px) {
            #modal {
                padding: 5px;
            }
            
            #contenido-modal {
                padding: 15px;
                width: 95%;
                max-height: 95vh;
                height: auto; /* PERMITE CRECER NATURALMENTE */
            }
            
            #contenido-modal h2 {
                font-size: 22px;
                margin: 5px 0 6px 0;
            }
            
            #contenido-modal p {
                font-size: 14px;
                margin: 2px 0;
            }
            
            #contenido-modal pre {
                font-size: 16px;
                padding: 10px;
                margin-top: 8px;
                line-height: 1.5;
                min-height: 120px; /* REDUCIDO PARA MÓVILES */
            }
            
            #contenido-modal .control-tamano-personalizado {
                padding: 10px 12px;
                margin-top: 12px; /* MÁS ESPACIO EN MÓVILES */
            }
            
            #contenido-modal button {
                width: 32px;
                height: 32px;
                font-size: 18px;
                top: 8px;
                right: 8px;
            }
        }

        @media (max-width: 480px) {
            #modal {
                padding: 2px;
            }
            
            #contenido-modal {
                padding: 10px 8px;
                width: 98%;
                max-height: 98vh;
                border-radius: 12px;
            }
            
            #contenido-modal h2 {
                font-size: 20px;
                margin: 3px 0 4px 0;
            }
            
            #contenido-modal p {
                font-size: 13px;
            }
            
            #contenido-modal pre {
                font-size: 15px;
                padding: 8px;
                margin-top: 6px;
                line-height: 1.4;
                min-height: 100px; /* MÍNIMO PARA PANTALLAS PEQUEÑAS */
            }
            
            #contenido-modal .control-tamano-personalizado {
                padding: 8px 10px;
                margin-top: 10px;
                border-radius: 8px;
            }
            
            .control-deslizador {
                margin: 10px 0 8px 0;
            }
            
            .marcas-tamano {
                margin-top: 6px;
                font-size: 10px;
            }
        }

        /* ESTILO ESPECIAL PARA PANTALLAS MUY PEQUEÑAS EN ORIENTACIÓN VERTICAL */
        @media (max-width: 480px) and (max-height: 700px) {
            #contenido-modal {
                max-height: 96vh;
                padding: 8px 6px;
            }
            
            #contenido-modal pre {
                min-height: 80px; /* AÚN MÁS PEQUEÑO EN PANTALLAS CORTAS */
                font-size: 14px;
                padding: 6px;
            }
            
            #contenido-modal h2 {
                font-size: 18px;
                margin: 2px 0 3px 0;
            }
            
            #contenido-modal .control-tamano-personalizado {
                padding: 6px 8px;
                margin-top: 8px;
            }
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












    <header>
        <button onclick="window.history.back()" class="btn-retorno"></button>
        <h1 style="font-family: 'Oleo Script', cursive;">Himnario Bíblico</h1>
    </header>

    <div class="himnario-container">
        <div class="buscador-container">
            <input type="text" id="buscador" placeholder="Buscar por número, título o tema..."> 
            <img class="Lupa" src="https://directorioasambleasvzla.com/iconos/Lupa.png" alt="Buscar">
        </div>

        <div id="contenedor-lista">
            <ul id="lista-himnos">
            <?php foreach ($himnos as $row): ?>
                <li onclick="mostrarHimno(<?= $row['Numero'] ?>)">
                    <strong><?= $row['Numero'] ?> - <?= htmlspecialchars($row['Primera_linea']) ?></strong><br>
                    <small><?= htmlspecialchars($row['Tema']) ?></small>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        document.getElementById("buscador").addEventListener("input", function() {
        const filtro = this.value.toLowerCase().trim();
        const himnos = document.querySelectorAll("#lista-himnos li");
        let resultados = 0;

        himnos.forEach(li => {
            const texto = li.textContent.toLowerCase();
            if (texto.includes(filtro)) {
            li.style.display = "block";
            resultados++;
            } else {
            li.style.display = "none";
            }
        });

        // Si no hay resultados, mostrar mensaje
        if (!document.getElementById("noResultados")) {
            const msg = document.createElement("p");
            msg.id = "noResultados";
            msg.style.textAlign = "center";
            msg.style.color = "#555";
            msg.style.display = "none";
            msg.textContent = "❌ No se encontraron himnos.";
            document.getElementById("contenedor-lista").appendChild(msg);
        }
        document.getElementById("noResultados").style.display = resultados === 0 ? "block" : "none";
        });
    </script>




        <!-- Modal -->
        <div id="modal">
            <div id="contenido-modal">
                <button onclick="cerrarModal()">✖</button>
                
                <div class="modal-content"> <!-- ADDED: Contenedor para contenido scrollable -->
                    <h2 id="titulo"></h2>
                    <p><strong>Número:</strong> <span id="numero"></span></p>
                    <p><strong>Tema:</strong> <span id="autor"></span></p>
                    <pre id="letra"></pre>
                </div>

                <!-- CONTROL PERSONALIZADO DE TAMAÑO DE LETRA -->
                <div class="grupo-campo control-tamano-personalizado">
                    <label for="tamano-letra" style="color: #EAE4D5; display: none">
                        Tamaño letra: <span id="valor-tamano">16</span>px
                    </label>
                    <div class="control-deslizador">
                        <input type="range" 
                            id="tamano-letra" 
                            min="12" 
                            max="30" 
                            value="16"
                            step="1"
                            class="deslizador-tamano">
                        <div class="marcas-tamano">
                            <span style="color: #EAE4D5">12</span>
                            <span style="color: #EAE4D5">16</span>
                            <span style="color: #EAE4D5">20</span>
                            <span style="color: #EAE4D5">24</span>
                            <span style="color: #EAE4D5">30</span>
                        </div>
                    </div>
                </div>
            </div>


                <script>
                    // === CONTROL PERSONALIZADO DE TAMAÑO DE LETRA ===
                    let tamanoLetraActual = 16; // Tamaño por defecto

                    // Inicializar el control deslizante
                    document.addEventListener('DOMContentLoaded', function() {
                        const deslizador = document.getElementById('tamano-letra');
                        const valorDisplay = document.getElementById('valor-tamano');
                        
                        // Cargar preferencia guardada
                        const tamanoGuardado = localStorage.getItem('tamanoLetraPersonalizado');
                        if (tamanoGuardado) {
                            tamanoLetraActual = parseInt(tamanoGuardado);
                            deslizador.value = tamanoLetraActual;
                            valorDisplay.textContent = tamanoLetraActual;
                            aplicarTamanoLetra(tamanoLetraActual);
                        }
                        
                        // Evento para el deslizador
                        deslizador.addEventListener('input', function() {
                            const nuevoTamano = parseInt(this.value);
                            valorDisplay.textContent = nuevoTamano;
                            aplicarTamanoLetra(nuevoTamano);
                        });
                        
                        // Eventos para las marcas
                        const marcas = document.querySelectorAll('.marcas-tamano span');
                        marcas.forEach(marca => {
                            marca.addEventListener('click', function() {
                                const tamano = parseInt(this.textContent);
                                deslizador.value = tamano;
                                valorDisplay.textContent = tamano;
                                aplicarTamanoLetra(tamano);
                            });
                        });
                        
                        // Aplicar tamaño inicial
                        aplicarTamanoLetra(tamanoLetraActual);
                    });

                    // Función para aplicar el tamaño de letra - CORREGIDA
                    function aplicarTamanoLetra(tamano) {
                        const letra = document.getElementById('letra');
                        if (letra) {
                            // Aplicar directamente al elemento pre (CORRECCIÓN PRINCIPAL)
                            letra.style.fontSize = tamano + 'px';
                            
                            // Actualizar estado visual de botones rápidos
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
                        
                        deslizador.value = tamano;
                        valorDisplay.textContent = tamano;
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

                    // Función para aplicar el tamaño cuando se muestran nuevos himnos
                    function aplicarTamanoAContenidoNuevo() {
                        aplicarTamanoLetra(tamanoLetraActual);
                    }
                </script>

                <style>
                    /* === CONTROL PERSONALIZADO DE TAMAÑO DE LETRA === */
                    .control-tamano-personalizado {
                        margin-top: 10px;
                        padding-top: 10px;
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
                        height: 10px;
                        border-radius: 50%;
                        background: #637983;
                        cursor: pointer;
                        border: 2px solid white;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                    }

                    .deslizador-tamano::-moz-range-thumb {
                        width: 20px;
                        height: 10px;
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
                        background: white;
                        color: #384A49;
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
                        color: #EAE4D5;
                        /*background: #f0f0f0;/** */
                        padding: 2px 6px;
                        border-radius: 4px;
                        min-width: 30px;
                        display: inline-block;
                        text-align: center;
                    }

                    /* === RESPONSIVE PARA EL CONTROL DE TAMAÑO === */
                    @media screen and (max-width: 680px) {
                        .control-tamano-personalizado {
                            margin-top: 2px;
                            padding-top: 2px;
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
                </style>
        </div>


        

        
    



    <script>
        function mostrarHimno(id) {
            fetch("?id=" + id)
                .then(res => res.json())
                .then(data => {
                    document.getElementById("titulo").textContent = data.Primera_linea;
                    document.getElementById("numero").textContent = data.Numero;
                    document.getElementById("autor").textContent = data.Tema || "Sin tema";
                    document.getElementById("letra").textContent = data.Letra;
                    document.getElementById("modal").classList.add("active");
                });
        }



        function cerrarModal() {
            document.getElementById("modal").classList.remove("active");
        }
    </script>
</body>
</html>

<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir archivo de conexión
//require __DIR__ . "/../../../conexion.php"; // tu archivo de conexión
require __DIR__ . "/../../../conexion.php";
 // tu archivo de conexión

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");

$abreviaturasEstados = [
    'Amazonas' => 'Amaz',
    'Anzoátegui' => 'Anzo',
    'Apure' => 'Apur',
    'Aragua' => 'Arag',
    'Barinas' => 'Bari',
    'Bolívar' => 'Bolí',
    'Carabobo' => 'Cara',
    'Cojedes' => 'Coje',
    'Delta Amacuro' => 'DA',
    'Distrito Capital' => 'DC',
    'Falcón' => 'Falc',
    'Guárico' => 'Guár',
    'Lara' => 'Lara',
    'Mérida' => 'Méri',
    'Miranda' => 'Mira',
    'Monagas' => 'Mona',
    'Nueva Esparta' => 'NE',
    'Portuguesa' => 'Port',
    'Sucre' => 'Sucr',
    'Táchira' => 'Tách',
    'Trujillo' => 'Truj',
    'La Guaira' => 'Guai',
    'Yaracuy' => 'Yara',
    'Zulia' => 'Zuli',

    
    'Frontera Colombia' => 'Colom'
];

$sql = "SELECT id, asamblea, numero, ciudad, estado, direccion, domingo, lunes, martes, miercoles, jueves, viernes, sabado, obras, GoogleMaps, coordenadas, Fehca_Fundacion FROM iglesias";
$result = $conn->query($sql);

$iglesias = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['abreviatura'] = $abreviaturasEstados[$row['estado']] ?? '';
        $iglesias[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iglesias Cercanas</title>
    <link rel="stylesheet" href="Cercanas.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    
    <style>
        @media (max-width: 768px) {
            .ocultar-movil {
                display: none;
            }
        }



        /* Mostrar solo nombre completo en pantallas grandes */
        .estado-completo {
            display: inline;
        }
        .estado-abreviado {
            display: none;
        }

        /* En pantallas menores a 768px, mostrar abreviatura y ocultar nombre completo */
        @media (max-width: 767px) {
            .estado-completo {
                display: none;
            }
            .estado-abreviado {
                display: inline;
            }





            /* Ocultar columnas específicas */
            .col2, .col3,  
            th.col2, th.col3 {
                display: none !important;
            }

            .col2, .col3,  
            td.col2, td.col3 {
                display: none !important;
            }



            .espacio-tlf-no {
                display: none;
            }
        }


        @media screen and (max-width: 768px) {
            .tlf-no {
                display: none !important;
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

<main class="content">
    <header>
        <button onclick="window.history.back()" class="return-button" title="Volver"></button>
        <h1>Lista de las Asambleas más Cercanas</h1>
    </header>

    <button onclick="usarUbicacion()">Mostrar más cercanas</button>

    <!-- Botón para móviles -->
    <button class="btn-filtros-mobile" onclick="abrirModalFiltros()">Filtros</button>

    <!-- Filtros visibles en pantallas grandes -->
    <div class="filtros" id="filtrosContenedor">
        <div class="filtro-item">
            <label for="cantidadCercanas">Mostrar:</label>
            <select id="cantidadCercanas">
                <option value="3">3 más cercanas</option>
                <option value="5">5 más cercanas</option>
                <option value="8">8 más cercanas</option>
            </select>
        </div>

        <div class="filtro-item">
            <label for="unidad">Unidad:</label>
            <select id="unidad">
                <option value="km">Kilómetros</option>
                <option value="mi">Millas</option>
            </select>
        </div>

        <div class="filtro-item filtro-slider">
            <label for="rangoDistancia">Distancia máxima:</label>
            <div class="slider-contenedor">
                <input type="range" id="rangoDistancia" min="1" max="120" value="70"
                    oninput="document.getElementById('distanciaValor').textContent = this.value" />
                <span><span id="distanciaValor">70</span> <span id="unidadTexto">km</span></span>
            </div>
        </div>
    </div>

    <!-- Modal solo en móviles, donde se mostrará el mismo contenedor .filtros -->
    <div id="modalFiltros" class="modal-filtros">
        <div class="modal-contenido-filtros">
            <span class="cerrar" onclick="cerrarModalFiltros()">&times;</span>
            <h3>Parámetros de búsqueda</h3>
            <div id="filtrosEnModal"></div> <!-- Aquí moveremos .filtros en móviles -->
            <button onclick="usarUbicacion(); cerrarModalFiltros();">🔍 Buscar</button>
        </div>
    </div>



    <script>
        function abrirModalFiltros() {
            const modal = document.getElementById("modalFiltros");
            const filtros = document.getElementById("filtrosContenedor");
            const destino = document.getElementById("filtrosEnModal");

            // Solo mover si estamos en móvil
            if (window.innerWidth <= 768 && filtros && destino) {
                destino.innerHTML = ""; // Limpia para evitar duplicados
                destino.appendChild(filtros);
            }

            modal.style.display = "flex"; // Para usar Flex (no block)
        }

        function cerrarModalFiltros() {
            const modal = document.getElementById("modalFiltros");
            const filtros = document.getElementById("filtrosContenedor");
            const destinoOriginal = document.body.querySelector(".filtros-original") || document.body;

            // Devuelve los filtros al lugar original
            if (window.innerWidth <= 768 && filtros) {
                destinoOriginal.appendChild(filtros);
            }

            modal.style.display = "none";
        }

        // Cerrar al hacer clic fuera del contenido
        window.addEventListener("click", function (e) {
            const modal = document.getElementById("modalFiltros");
            const contenido = document.querySelector(".modal-contenido-filtros");
            if (e.target === modal) {
                cerrarModalFiltros();
            }
        });
    </script>


    <div id="loadingModal" class="loading-modal" style="display: none;">
        <div class="loading-modal-content">
            <h3>Calculando distancias...</h3>
            <div class="loading-bar">
                <div class="loading-bar-inner" id="progressBar"></div>
            </div>
            <p id="progressText">0%</p>
        </div>
    </div>





    <div class="tabla-contenedor">
        <!-- Encabezado fijo -->
        <table class="tabla-cabecera">
            <thead>
                <tr>
                    <th class="col1">Asamblea</th>
                    <th class="espacio"></th>
                    <th class="col2 tlf-no ocultar-movil">Número</th>
                    <th class="espacio tlf-no"></th>
                    <th class="col3 tlf-no ocultar-movil">Ciudad</th>
                    <th class="espacio tlf-no"></th>
                    <th class="col4">Estado</th>
                    <th class="espacio"></th>
                    <th class="col5">Detalles</th>
                    <th class="espacio tlf-no"></th>
                    <th class="col5">Mapa</th>
                    <th class="espacio"></th>
                    <th class="col5">Distancia</th>
                </tr>
            </thead>
        </table>

        <!-- Cuerpo con scroll -->
        <div class="tabla-scroll">
            <table class="tabla-cuerpo">
                <tbody id="tabla-body">
                    <?php foreach ($iglesias as $row): ?>
                        <tr data-coordenadas="<?= htmlspecialchars($row['coordenadas']) ?>">
                            <td class="col1"><?= htmlspecialchars($row['asamblea']) ?></td>
                            <td class="espacio"></td>

                            <td class="col2 tlf-no ocultar-movil"><?= htmlspecialchars($row['numero']) ?></td>
                            <td class="espacio tlf-no"></td>

                            <td class="col3 tlf-no ocultar-movil"><?= htmlspecialchars($row['ciudad']) ?></td>
                            <td class="espacio tlf-no"></td>

                            <td class="col4">
                                <span class="estado-completo"><?= htmlspecialchars($row['estado']) ?></span>
                                <span class="estado-abreviado"><?= htmlspecialchars($row['abreviatura']) ?></span>
                            </td>
                            <td class="espacio"></td>

                            <td class="col5">
                                <button class="btn-detalles"
                                    onclick='mostrarDetalles(<?= json_encode($row, JSON_UNESCAPED_UNICODE) ?>)'>Ver</button>
                            </td>
                            <td class="espacio tlf-no"></td>

                            <td class="col5">
                                <a class="btn-mapa" 
                                    href="#"
                                    data-url="<?= htmlspecialchars($row['GoogleMaps']) ?>" 
                                    title="Ver en Google Maps">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>
                            </td>
                            <td class="espacio"></td>

                            <td class="col5 distancia-celda"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


                            
    <script>
        // Pasa los datos de PHP a JavaScript
        const iglesiasData = <?= json_encode($iglesias, JSON_UNESCAPED_UNICODE); ?>;

        // Actualiza el texto de la unidad en el slider
        document.getElementById("unidad").addEventListener("change", function () {
            document.getElementById("unidadTexto").textContent = this.value;
        });

        // Convertir grados a radianes
        function toRad(deg) {
            return (deg * Math.PI) / 180;
        }

        // Cálculo de distancia entre dos coordenadas (Haversine)
        function calcularDistancia(lat1, lon1, lat2, lon2) {
            const R = 6371; // km
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        async function usarUbicacion() {
            if (!navigator.geolocation) {
                alert("Tu navegador no admite geolocalización.");
                return;
            }

            const modal = document.getElementById("loadingModal");
            modal.style.display = "block";

            navigator.geolocation.getCurrentPosition(position => {
                const userLat = position.coords.latitude;
                const userLon = position.coords.longitude;

                const unidad = document.getElementById("unidad").value;
                const maxDistancia = parseFloat(document.getElementById("rangoDistancia").value);
                const cantidad = parseInt(document.getElementById("cantidadCercanas").value);
                const multiplicador = unidad === "km" ? 1 : 0.621371;

                let iglesiasConDistancia = [];

                iglesiasData.forEach(iglesia => {
                    if (!iglesia.coordenadas) return;

                    const [lat, lon] = iglesia.coordenadas.split(",").map(Number);
                    if (isNaN(lat) || isNaN(lon)) return;

                    const distancia = calcularDistancia(userLat, userLon, lat, lon) * multiplicador;

                    if (distancia <= maxDistancia) {
                        iglesiasConDistancia.push({ ...iglesia, distancia });
                    }
                });

                iglesiasConDistancia.sort((a, b) => a.distancia - b.distancia);
                iglesiasConDistancia = iglesiasConDistancia.slice(0, cantidad);

                const tbody = document.getElementById("tabla-body");
                tbody.innerHTML = "";

                if (iglesiasConDistancia.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7">No se encontraron asambleas dentro del rango especificado.</td></tr>`;
                } else {
                    iglesiasConDistancia.forEach(ig => {
                        const fila = document.createElement("tr");
                        fila.innerHTML = `
                            <td>${ig.asamblea}</td>
                            <td class="ocultar-movil">${ig.numero}</td>
                            <td class="ocultar-movil">${ig.ciudad}</td>
                            <td>${ig.estado}</td>
                            <td><button class='btn-detalles' onclick='mostrarDetalles(${JSON.stringify(ig)})'>Ver</button></td>
                            <td><a class='btn-mapa' href='${ig.GoogleMaps}' target='_blank'><i class='fas fa-map'></i></a></td>
                            <td>${ig.distancia.toFixed(2)} ${unidad}</td>
                        `;

                        tbody.appendChild(fila);
                    });
                }

                modal.style.display = "none";
            }, error => {
                alert("No se pudo obtener tu ubicación.");
                console.error(error);
                modal.style.display = "none";
            });
        }
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.btn-mapa').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const url = this.getAttribute('data-url');

                    if (!url || url === '#' || url.trim() === '') {
                        e.preventDefault();
                        alert("Enlace no disponible por ahora.\n\nSi lo tiene, favor hacerlo llegar al correo:\n\ndirectorioasambleas@gmail.com\n\nIndicando claramente el nombre de la asamblea y el estado en el que se encuentra.\n\nGracias de antemano.");
                    } else {
                        // Abrir manualmente en nueva pestaña
                        window.open(url, '_blank');
                    }
                });
            });
        });
    </script>


    <script>
        //Cambiar el titular de la columna en la tabla
        document.addEventListener("DOMContentLoaded", function () {
            if (window.innerWidth <= 768) {
                // Cambiar texto de encabezados largos por uno corto
                const ths = document.querySelectorAll("th");

                ths.forEach(th => {
                if (th.textContent.trim() === "Detalles") {
                    th.textContent = "Ver";
                } else if (th.textContent.trim() === "Distancia") {
                    th.textContent = "Km";
                }
                });
            }
        });


        //Cambiar el contenido dentro de la columna
        const isMobile = window.innerWidth <= 768;

        if (isMobile) {
        // Cambiar texto del botón "Ver" por ícono o texto corto
        document.querySelectorAll(".btn-detalles").forEach(btn => {
            btn.textContent = "Ver"; // o "Ver", o "👁️"
        });

        // Acortar formato de distancia si ya fue renderizado
        document.querySelectorAll(".distancia-celda").forEach(td => {
            const text = td.textContent.trim();
            const match = text.match(/^([\d.]+)\s*(km|mi)$/i);
            if (match) {
            const valor = parseFloat(match[1]).toFixed(0);
            const unidad = match[2];
            td.textContent = `${valor}${unidad}`;
            }
        });
        }

    </script>









    <!-- MODAL DETALLES -->
    <div id="modalDetalles" class="modal-detalles">
    <div class="modal-contenido">
        <span class="cerrar-modal" onclick="cerrarModal()">&times;</span>
        <h2>Detalles de la Iglesia</h2>
        <div id="detallesContenido"></div>
    </div>
    </div>

    <script>
        // Función para generar un campo solo si tiene valor válido
        function campoSiExiste(label, valor, excepciones = []) {
        if (!valor || excepciones.includes(valor.trim())) {
            return "";
        }
        return `<p><strong>${label}:</strong> ${valor}</p>`;
        }

        // Función principal para mostrar el modal
        function mostrarDetalles(iglesia) {
        const modal = document.getElementById("modalDetalles");
        const contenido = document.getElementById("detallesContenido");

        // Procesar las obras (formato con lista y scroll)
        let obrasHTML = "";
        if (iglesia.obras && iglesia.obras.trim() !== "" && iglesia.obras.toLowerCase().trim() !== "sin obras que atender") {
            const obrasList = iglesia.obras.trim().split(/\r?\n+/).filter(linea => linea.trim() !== "");
            if (obrasList.length > 0) {
            obrasHTML = `
                <div class="wrap-obras">
                <p><strong>Obras:</strong></p>
                <div class="obras-scroll">
                    <ul>
                    ${obrasList.map(linea => `<li>${linea.trim()}</li>`).join("")}
                    </ul>
                </div>
                </div>`;
            }
        }

        // Google Maps link
        const googleMapsHTML = iglesia.GoogleMaps && iglesia.GoogleMaps.trim() !== ""
            ? `<p><a id="det-mapa" href="#" data-url="${iglesia.GoogleMaps}"><i class="fas fa-map-marker-alt"></i> Ver en Google Maps</a></p>`
            : "";

        // Armar todo el contenido
        contenido.innerHTML = `
            ${campoSiExiste("Asamblea", iglesia.asamblea)}
            ${campoSiExiste("Número", iglesia.numero)}
            ${campoSiExiste("Fecha de Fundación", iglesia.Fehca_Fundacion)}
            ${campoSiExiste("Ciudad", iglesia.ciudad)}
            ${campoSiExiste("Estado", iglesia.estado)}
            ${campoSiExiste("Dirección", iglesia.direccion)}

            <div class="detalles-grid">
            ${campoSiExiste("Domingo", iglesia.domingo, ["Sin reuniones."])}
            ${campoSiExiste("Lunes", iglesia.lunes, ["Sin reuniones."])}
            ${campoSiExiste("Martes", iglesia.martes, ["Sin reuniones."])}
            ${campoSiExiste("Miércoles", iglesia.miercoles, ["Sin reuniones."])}
            ${campoSiExiste("Jueves", iglesia.jueves, ["Sin reuniones."])}
            ${campoSiExiste("Viernes", iglesia.viernes, ["Sin reuniones."])}
            ${campoSiExiste("Sábado", iglesia.sabado, ["Sin reuniones."])}
            </div>

            ${obrasHTML}
            ${googleMapsHTML}
        `;

        modal.style.display = "flex";
        }

        // Cerrar modal
        function cerrarModal() {
        document.getElementById("modalDetalles").style.display = "none";
        }

        // Cerrar si se hace clic fuera del contenido
        window.addEventListener("click", function (event) {
        const modal = document.getElementById("modalDetalles");
        if (event.target === modal) {
            cerrarModal();
        }
        });

        /*========== Enlace GoogleMaps seguro ==========*/
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
        

        /* ===== Sección Obras ===== */
        .wrap-obras {
            margin-top: 10px;
        }

        .obras-scroll {
            max-height: 100px;
            overflow-y: auto;
            /*border: 1px solid #ccc;*/
            border-radius: 6px;
            background: transparent;
            padding: 8px 10px;
        }

        .obras-scroll ul {
            list-style-type: disc;
            margin: 6px 0 6px 20px;
            padding: 0;
        }

        .obras-scroll li {
            margin-bottom: 5px;
            line-height: 1.4;
        }

        /* Scrollbar personalizada */
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

    <style>
        /* ---------- MODAL DETALLES ---------- */
        .modal-detalles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            backdrop-filter: blur(3px);
        }

        .modal-contenido {
            background-color: #EAE4D5;
            color: #192E2F;
            padding: 20px 15px;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            /*height: 90%;/**/
            font-family: 'Sansation', sans-serif;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            position: relative;
            animation: fadeIn 0.3s ease-in-out;

            font-size: 16px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cerrar-modal {
            position: absolute;
            top: 10px;
            right: 14px;
            font-size: 30px;
            color: #c0392b;
            cursor: pointer;
            font-weight: bold;
            transition: color 0.2s;
        }

        .cerrar-modal:hover {
            color: #e74c3c;
        }

        #detallesContenido p {
            margin: 4px 0;
            line-height: 1.5;
        }

        #detallesContenido a {
            color: #E6CDB7;
            text-decoration: none;
            font-weight: bold;
        }

        #detallesContenido a:hover {
            text-decoration: underline;
        }

        .detalles-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 10px;
        }

        #det-mapa {
            display: inline-block;
            padding: 10px 16px;
            background-color: #34495e;
            color: #E6CDB7;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
        }

        #det-mapa:hover {
            background-color: #E6CDB7;
            color: #34495e;
        }



        @media only screen and (max-width: 600px) {
    
            /*Modal de detalles*/
            .modal-detalles {
                max-height: 100%;
                overflow: visible;
                padding: 10px 0; /* Añade padding vertical para respirar */
            }

            .modal-contenido {
                padding: 15px;
                width: 90%;
                max-height: 85vh; /* Reduce ligeramente para dar margen */
                margin: auto;
                border-radius: 12px;
                position: relative;
                
                /* Control de overflow mejorado */
                overflow-y: auto;
                overflow-x: hidden;
                
                /* Asegura que el scroll sea suave */
                -webkit-overflow-scrolling: touch;
            }
            
            #detallesContenido {
                max-height: 100%;
                overflow: visible;
            }
            
            #detallesContenido p {
                margin: 8px 0;
                line-height: 1.3; /* Mejor legibilidad */
            }
            
            #detallesContenido a {
                text-decoration: none;
                font-weight: bold;
            }
            
            #detallesContenido a:hover {
                text-decoration: underline;
            }
            
            .detalles-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px 10px;
            }

            /* Scrollbar más delgada para móviles */
            .modal-contenido::-webkit-scrollbar {
                width: 4px;
            }
            
            .modal-contenido::-webkit-scrollbar-thumb {
                background: #aaa;
                border-radius: 10px;
            }
        }
    </style>

</main>





</body>
</html>

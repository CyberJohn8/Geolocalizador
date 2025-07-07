<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "geolocalizador";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

if (empty($estado)) {
    die("Por favor, especifica un estado en la URL. Ej: estado.php?estado=Zulia");
}

// Obtener nombre del archivo de mapa
$nombreEstado = strtolower(str_replace(['á','é','í','ó','ú','ñ',' '], ['a','e','i','o','u','n','_'], $estado));
$mapa_path = "mapas/" . $nombreEstado . ".jpg";
if (!file_exists($mapa_path)) $mapa_path = "mapas/default.jpg";

// Obtener todas las iglesias del estado
$sql = "SELECT * FROM iglesias WHERE estado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $estado);
$stmt->execute();
$result = $stmt->get_result();
$datos_iglesias = [];
while ($row = $result->fetch_assoc()) $datos_iglesias[] = $row;

// Preparar datos por Asamblea
$iglesiasPorAsamblea = [];
foreach ($datos_iglesias as $iglesia) {
    $clave = trim($iglesia['asamblea']);
    $iglesiasPorAsamblea[$clave] = $iglesia;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asambleas en <?php echo htmlspecialchars($estado); ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Estado_Dinamico.css">

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
            <button onclick="history.back()" class="return-button" title="Volver"></button>
            <h1>Lista de Asambleas en <?php echo htmlspecialchars($estado); ?></h1>
        </header>

        <!--<div class="filtros-container">
            <select id="columna" onchange="filtrarTabla()">
                <option value="all">Buscar todo</option>
                <option value="0">Asamblea</option>
                <option value="1">Número</option>
                <option value="2">Ciudad</option>
            </select>

            <input class="BarradeBuscador" type="text" id="buscador" onkeyup="filtrarTabla()" placeholder="Buscar..." style="color: #EAE4D5; ">


            <script>
                function filtrarTabla() {
                    let columna = document.getElementById("columna").value;
                    let input = columna === "3"
                        ? document.getElementById("selector-estado").value.toLowerCase()
                        : document.getElementById("buscador").value.toLowerCase();

                    let filas = document.querySelectorAll("tbody tr");

                    filas.forEach(fila => {
                        let celdas = fila.getElementsByTagName("td");
                        if (columna === "all") {
                            let textoFila = fila.innerText.toLowerCase();
                            fila.style.display = textoFila.includes(input) ? "" : "none";
                        } else {
                            let textoCelda = celdas[columna].innerText.toLowerCase();
                            fila.style.display = textoCelda.includes(input) ? "" : "none";
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

            <!- Ordenar tabla por ->
            <div style="margin: 10px 0;">
                <label for="ordenar"></label>
                <select id="ordenar" onchange="ordenarTabla()" style="padding: 10px;">
                    <option value="">Ordenar por: </option>
                    <option value="asamblea-az">Asamblea A-Z</option>
                    <option value="asamblea-za">Asamblea Z-A</option>
                    <option value="numero-asc">Número Ascendente</option>
                    <option value="numero-desc">Número Descendente</option>
                </select>

                <script>
                    function ordenarTabla() {
                        const select = document.getElementById("ordenar").value;
                        const tbody = document.querySelector("tbody");
                        const filas = Array.from(tbody.querySelectorAll("tr"));

                        let columna = 0; // Default: Asamblea
                        let tipo = "texto";
                        let asc = true;

                        switch (select) {
                            case "asamblea-az": columna = 0; tipo = "texto"; asc = true; break;
                            case "asamblea-za": columna = 0; tipo = "texto"; asc = false; break;
                            case "numero-asc": columna = 1; tipo = "numero"; asc = true; break;
                            case "numero-desc": columna = 1; tipo = "numero"; asc = false; break;
                            case "estado-az": columna = 3; tipo = "texto"; asc = true; break;
                            case "estado-za": columna = 3; tipo = "texto"; asc = false; break;
                            default: return;
                        }

                        filas.sort((a, b) => {
                            let valA = a.cells[columna].innerText.trim();
                            let valB = b.cells[columna].innerText.trim();

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
        </div>-->

        <div class="mapa-contenedor-flex">
            <!-- Contenedor del mapa -->
            <div class="mapa-container">
                <img src="IMG/<?php echo strtolower($estado); ?>.png" alt="Mapa de <?php echo $estado ?>">
                <!-- Botones encima del mapa -->
                <!-- 
                    Botones encima del mapa - ejemplo para el estado Zulia 
                -->
                <?php if ($estado === 'Zulia'): ?>
                    <button class="ciudad-btn ciudad-cabimas" onclick="mostrarDetallesPorAsamblea('Cabimas')">Cabimas</button>
                    <button class="ciudad-btn ciudad-Cuatricentenario" onclick="mostrarDetallesPorAsamblea('Cuatricentenario')">Cuatricentenario</button>
                    <button class="ciudad-btn ciudad-ElProgreso" onclick="mostrarDetallesPorAsamblea('El Progreso')">El Progreso</button>
                    <button class="ciudad-btn ciudad-LaRepelona" onclick="mostrarDetallesPorAsamblea('La Repelona')">La Repelona</button>
                    <button class="ciudad-btn ciudad-Perijá" onclick="mostrarDetallesPorAsamblea('La Villa del Rosario (Perijá)')">La Villa del Rosario (Perijá)</button>
                    <button class="ciudad-btn ciudad-LosJobitos" onclick="mostrarDetallesPorAsamblea('Los Jobitos')">Los Jobitos</button>
                    <button class="ciudad-btn ciudad-SanFrancisco" onclick="mostrarDetallesPorAsamblea('San Francisco')">San Francisco</button>
                    <button class="ciudad-btn ciudad-Zipayare" onclick="mostrarDetallesPorAsamblea('Zipayare')">Zipayare</button>
                
                <?php elseif ($estado === 'Lara'): ?>
                    <button class="ciudad-btn ciudad-CALLE23" onclick="mostrarDetallesPorAsamblea('CALLE 23')">Calle 23</button>
                    <button class="ciudad-btn ciudad-AltodePuebloNuevo" onclick="mostrarDetallesPorAsamblea('Alto de Pueblo Nuevo')">Alto de Pueblo Nuevo</button>
                    <button class="ciudad-btn ciudad-Carora" onclick="mostrarDetallesPorAsamblea('Carora')">Carora</button>
                    <button class="ciudad-btn ciudad-Cauderales" onclick="mostrarDetallesPorAsamblea('Cauderales')">Cauderales</button>
                    <button class="ciudad-btn ciudad-Duaca" onclick="mostrarDetallesPorAsamblea('Duaca')">Duaca</button>
                    <button class="ciudad-btn ciudad-ElCarmen" onclick="mostrarDetallesPorAsamblea('El Carmen')">El Carmen</button>
                    <button class="ciudad-btn ciudad-LaMontanita" onclick="mostrarDetallesPorAsamblea('La Montañita')">La Montañita</button>
                    <button class="ciudad-btn ciudad-LaReluciente" onclick="mostrarDetallesPorAsamblea('La Reluciente')">La Reluciente</button>
                    <button class="ciudad-btn ciudad-LaRepresa" onclick="mostrarDetallesPorAsamblea('La Represa')">La Represa</button>
                    <button class="ciudad-btn ciudad-Moroturo" onclick="mostrarDetallesPorAsamblea('Moroturo')">Moroturo</button>
                    <button class="ciudad-btn ciudad-SanJacinto" onclick="mostrarDetallesPorAsamblea('San Jacinto')">San Jacinto</button>
                
                <?php elseif ($estado === 'Amazonas'): ?>
                    <button class="ciudad-btn ciudad-PuertoAyacucho" onclick="mostrarDetallesPorAsamblea('Puerto Ayacucho')">Puerto Ayacucho</button>
                
                <?php elseif ($estado === 'Anzoátegui'): ?>
                    <button class="ciudad-btn ciudad-ElTigrito" onclick="mostrarDetallesPorAsamblea('EL TIGRITO ')">El Tigrito</button>
                    <button class="ciudad-btn ciudad-PuertolaCruz" onclick="mostrarDetallesPorAsamblea('Puerto la Cruz')">Puerto la Cruz</button>
                    <button class="ciudad-btn ciudad-Orinoco" onclick="mostrarDetallesPorAsamblea('CIUDAD ORINOCO (SOLEDAD)')">Soledad</button>

                    <!-- Revisar Bario Antonio José de Sucre -->
                <?php elseif ($estado === 'Apure'): ?>
                    <button class="ciudad-btn ciudad-BarioAntonioJosédeSucre" onclick="mostrarDetallesPorAsamblea('Bario Antonio José de Sucre')">Bario Antonio José de Sucre</button>
                    <button class="ciudad-btn ciudad-ElNegro" onclick="mostrarDetallesPorAsamblea('El Negro')">El Negro</button>
                    <button class="ciudad-btn ciudad-Elorza" onclick="mostrarDetallesPorAsamblea('Elorza')">Elorza</button>
                    <button class="ciudad-btn ciudad-Guasdualito" onclick="mostrarDetallesPorAsamblea('Guasdualito')">Guasdualito</button>
                    <button class="ciudad-btn ciudad-SanFernando" onclick="mostrarDetallesPorAsamblea('San Fernando')">San Fernando</button>

                <?php elseif ($estado === 'Aragua'): ?>
                    <button class="ciudad-btn ciudad-BarrioSanCarlos" onclick="mostrarDetallesPorAsamblea('BARRIO SAN CARLOS')">Barrio San Carlos</button>
                    <button class="ciudad-btn ciudad-Coromoto" onclick="mostrarDetallesPorAsamblea('COROMOTO')">Coromoto</button>
                    <button class="ciudad-btn ciudad-DIEZDEDICIEMBRE" onclick="mostrarDetallesPorAsamblea('DIEZ DE DICIEMBRE')">Diez de Diciembre</button>

                    <button class="ciudad-btn ciudad-CarmendeCura" onclick="mostrarDetallesPorAsamblea('Carmen de Cura')">Carmen de Cura</button>
                    <button class="ciudad-btn ciudad-LaPica" onclick="mostrarDetallesPorAsamblea('LA PICA')">La Pica</button>
                    <button class="ciudad-btn ciudad-PaloNegroCentro" onclick="mostrarDetallesPorAsamblea('PALO NEGRO, CENTRO')">Palo Negro, Centro</button>
                    <button class="ciudad-btn ciudad-RosariodePaya" onclick="mostrarDetallesPorAsamblea('Rosario de Paya')">Rosario de Paya</button>
                    <button class="ciudad-btn ciudad-PuebloNuevo" onclick="mostrarDetallesPorAsamblea('Pueblo Nuevo')">Pueblo Nuevo</button>
                    <button class="ciudad-btn ciudad-SanMateo" onclick="mostrarDetallesPorAsamblea('San Mateo')">San Mateo</button>
                    <button class="ciudad-btn ciudad-SantaRita" onclick="mostrarDetallesPorAsamblea('Santa Rita')">Santa Rita</button>
                    <button class="ciudad-btn ciudad-Zuata" onclick="mostrarDetallesPorAsamblea('Zuata')">Zuata</button>

                <?php elseif ($estado === 'Barinas'): ?>
                    <button class="ciudad-btn ciudad-Barrancas" onclick="mostrarDetallesPorAsamblea('Barrancas')">Barrancas</button>
                    <button class="ciudad-btn ciudad-Barinas" onclick="mostrarDetallesPorAsamblea('Barinas')">Barinas</button>
                    <button class="ciudad-btn ciudad-Barinitas" onclick="mostrarDetallesPorAsamblea('Barinitas')">Barinitas</button>
                    <button class="ciudad-btn ciudad-Guamito" onclick="mostrarDetallesPorAsamblea('Guamito')">Guamito</button>
                    <button class="ciudad-btn ciudad-LosRastrojos" onclick="mostrarDetallesPorAsamblea('Los Rastrojos')">Los Rastrojos</button>

                <?php elseif ($estado === 'Bolívar'): ?>
                    <button class="ciudad-btn ciudad-CaicaradelOrinoco" onclick="mostrarDetallesPorAsamblea('Caicara del Orinoco')">Caicara del Orinoco</button>
                    <button class="ciudad-btn ciudad-CUYUNÍ" onclick="mostrarDetallesPorAsamblea('CIUDAD BOLÍVAR - CUYUNÍ')">Cuyuní</button>
                    <button class="ciudad-btn ciudad-LaSabanita" onclick="mostrarDetallesPorAsamblea('LA SABANITA')">La Sabanita</button>

                    <button class="ciudad-btn ciudad-PuertoOrdaz" onclick="mostrarDetallesPorAsamblea('Puerto Ordaz')">Puerto Ordaz</button>
                    <button class="ciudad-btn ciudad-SanFélix" onclick="mostrarDetallesPorAsamblea('San Félix')">San Félix</button>
                    <button class="ciudad-btn ciudad-SantaElena" onclick="mostrarDetallesPorAsamblea('SANTA ELENA DE UAIRÉN')">Santa Elena de Uairén</button>
                    <button class="ciudad-btn ciudad-Tumerremo" onclick="mostrarDetallesPorAsamblea('TUMEREMO')">Tumeremo</button>
                    <button class="ciudad-btn ciudad-SantaRosadelBuey" onclick="mostrarDetallesPorAsamblea('Santa Rosa del Buey')">Santa Rosa del Buey</button>

                <?php elseif ($estado === 'Carabobo'): ?>
                    <button class="ciudad-btn ciudad-Alpargatón" onclick="mostrarDetallesPorAsamblea('Alpargatón')">Alpargatón</button>
                    <button class="ciudad-btn ciudad-Bejuma" onclick="mostrarDetallesPorAsamblea('Bejuma')">Bejuma</button>
                    <button class="ciudad-btn ciudad-Boqueron" onclick="mostrarDetallesPorAsamblea('Boqueron')">Boqueron</button>
                    <button class="ciudad-btn ciudad-CampoCarabobo" onclick="mostrarDetallesPorAsamblea('Campo Carabobo')">Campo Carabobo</button>
                    <button class="ciudad-btn ciudad-Canoabo" onclick="mostrarDetallesPorAsamblea('Canoabo')">Canoabo</button>
                    <button class="ciudad-btn ciudad-Chirgua" onclick="mostrarDetallesPorAsamblea('Chirgua')">Chirgua</button>
                    <button class="ciudad-btn ciudad-Guacara" onclick="mostrarDetallesPorAsamblea('Guacara')">Guacara</button>
                    <button class="ciudad-btn ciudad-Güigüe" onclick="mostrarDetallesPorAsamblea('Güigüe')">Güigüe</button>
                    <button class="ciudad-btn ciudad-Juaniquero" onclick="mostrarDetallesPorAsamblea('Juaniquero')">Juaniquero</button>
                    <button class="ciudad-btn ciudad-LaCompañía" onclick="mostrarDetallesPorAsamblea('La Compañía')">La Compañía</button>
                    <button class="ciudad-btn ciudad-LaJobera" onclick="mostrarDetallesPorAsamblea('La Jobera')">La Jobera</button>
                    <button class="ciudad-btn ciudad-LaLagunita" onclick="mostrarDetallesPorAsamblea('La Lagunita')">La Lagunita</button>
                    <button class="ciudad-btn ciudad-LaSabana" onclick="mostrarDetallesPorAsamblea('LA SABANA - CANOABO')">Canoabo</button>
                    <button class="ciudad-btn ciudad-LasTrincheras" onclick="mostrarDetallesPorAsamblea('Las Trincheras')">Las Trincheras</button>
                    <button class="ciudad-btn ciudad-LosCaracaros" onclick="mostrarDetallesPorAsamblea('Los Caracaros')">Los Caracaros</button>
                    <button class="ciudad-btn ciudad-Mariara" onclick="mostrarDetallesPorAsamblea('Mariara')">Mariara</button>
                    <button class="ciudad-btn ciudad-Patanemo" onclick="mostrarDetallesPorAsamblea('PRIMAVERA')">Primavera</button>
                    <button class="ciudad-btn ciudad-SanEsteban" onclick="mostrarDetallesPorAsamblea('SAN ESTEBAN PUEBLO')">San Esteban</button>
                    <button class="ciudad-btn ciudad-SanJoaquin" onclick="mostrarDetallesPorAsamblea('San Joaquin')">San Joaquin</button>
                    <button class="ciudad-btn ciudad-SanPablo" onclick="mostrarDetallesPorAsamblea('San Pablo')">San Pablo</button>
                    <button class="ciudad-btn ciudad-Tocuyito" onclick="mostrarDetallesPorAsamblea('Tocuyito')">Tocuyito</button>

                    
                    <button class="ciudad-btn ciudad-Morón" onclick="mostrarDetallesPorAsamblea('Morón')">Morón</button>
                    
                    <button class="ciudad-btn ciudad-PuertoCabello" onclick="window.location.href='ciudad.php?estado=Carabobo&ciudad=Puerto Cabello,San Esteban'">Puerto Cabello</button>
                    <button class="ciudad-btn ciudad-Valencia" onclick="window.location.href='ciudad.php?estado=Carabobo&ciudad=Valencia'">Valencia</button>



                <?php elseif ($estado === 'Cojedes'): ?>
                    <button class="ciudad-btn ciudad-BuenosAires" onclick="mostrarDetallesPorAsamblea('Buenos Aires')">Buenos Aires</button>
                    <button class="ciudad-btn ciudad-BarrioEzequielZamora" onclick="mostrarDetallesPorAsamblea('Barrio Ezequiel Zamora')">Barrio Ezequiel Zamora</button>
                    <button class="ciudad-btn ciudad-BarrioNuevo" onclick="mostrarDetallesPorAsamblea('Barrio Nuevo')">Barrio Nuevo</button>
                    <button class="ciudad-btn ciudad-ElBaul" onclick="mostrarDetallesPorAsamblea('El Baul')">El Baul</button>
                    <button class="ciudad-btn ciudad-ElMuertico" onclick="mostrarDetallesPorAsamblea('El Muertico')">El Muertico</button>
                    <button class="ciudad-btn ciudad-ElPenitente" onclick="mostrarDetallesPorAsamblea('El Penitente')">El Penitente</button>
                    <button class="ciudad-btn ciudad-Genareño" onclick="mostrarDetallesPorAsamblea('Genareño')">Genareño</button>
                    <button class="ciudad-btn ciudad-LaChorrera" onclick="mostrarDetallesPorAsamblea('La Chorrera')">La Chorrera</button>
                    <button class="ciudad-btn ciudad-LasVegas" onclick="mostrarDetallesPorAsamblea('Las Vegas')">Las Vegas</button>
                    <button class="ciudad-btn ciudad-LosColorados" onclick="mostrarDetallesPorAsamblea('Los Colorados')">Los Colorados</button>
                    <button class="ciudad-btn ciudad-Manrique" onclick="mostrarDetallesPorAsamblea('Manrique')">Manrique</button>
                    <button class="ciudad-btn ciudad-PuenteOnoto" onclick="mostrarDetallesPorAsamblea('Puente Onoto')">Puente Onoto</button>
                    <button class="ciudad-btn ciudad-SanCarlos" onclick="mostrarDetallesPorAsamblea('San Carlos')">San Carlos</button>
                    <button class="ciudad-btn ciudad-Tinaco" onclick="mostrarDetallesPorAsamblea('Tinaco')">Tinaco</button>
                    <button class="ciudad-btn ciudad-Tinaquillo" onclick="mostrarDetallesPorAsamblea('Tinaquillo')">Tinaquillo</button>

                <?php elseif ($estado === 'Delta Amacuro'): ?>
                    <button class="ciudad-btn ciudad-Tucupita" onclick="mostrarDetallesPorAsamblea('Tucupita')">Tucupita</button>



                <?php elseif ($estado === 'Distrito Capital'): ?>
                    <button class="ciudad-btn ciudad-ElCementerio" onclick="mostrarDetallesPorAsamblea('El Cementerio')">El Cementerio</button>
                    <button class="ciudad-btn ciudad-ElValle" onclick="mostrarDetallesPorAsamblea('El Valle')">El Valle</button>
                    <button class="ciudad-btn ciudad-LasAdjuntas" onclick="mostrarDetallesPorAsamblea('Las Adjuntas')">Las Adjuntas</button>
                    <button class="ciudad-btn ciudad-LaVega" onclick="mostrarDetallesPorAsamblea('La Vega')">La Vega</button>
                    <button class="ciudad-btn ciudad-LosFlores" onclick="mostrarDetallesPorAsamblea('Los Flores')">Los Flores</button>
                    <button class="ciudad-btn ciudad-Petare" onclick="mostrarDetallesPorAsamblea('Petare')">Petare</button>



                <?php elseif ($estado === 'Falcón'): ?>
                    <button class="ciudad-btn ciudad-BuenaVista" onclick="mostrarDetallesPorAsamblea('Buena Vista')">Buena Vista</button>
                    <button class="ciudad-btn ciudad-Chichiriviche" onclick="mostrarDetallesPorAsamblea('Chichiriviche')">Chichiriviche</button>
                    <button class="ciudad-btn ciudad-Churuguara" onclick="mostrarDetallesPorAsamblea('Churuguara')">Churuguara</button>
                    <button class="ciudad-btn ciudad-Coro" onclick="mostrarDetallesPorAsamblea('Coro')">Coro</button>
                    <button class="ciudad-btn ciudad-Dabajuro" onclick="mostrarDetallesPorAsamblea('Dabajuro')">Dabajuro</button>
                    <button class="ciudad-btn ciudad-ElMene" onclick="mostrarDetallesPorAsamblea('El Mene')">El Mene</button>
                    <button class="ciudad-btn ciudad-JacuraPuebloNuevo" onclick="mostrarDetallesPorAsamblea('Jacura, Pueblo Nuevo')">Jacura, Pueblo Nuevo</button>
                    <button class="ciudad-btn ciudad-LaGuacharaca" onclick="mostrarDetallesPorAsamblea('La Guacharaca')">La Guacharaca</button>
                    <button class="ciudad-btn ciudad-Mirimire" onclick="mostrarDetallesPorAsamblea('Mirimire')">Mirimire</button>
                    <button class="ciudad-btn ciudad-PalmaSola" onclick="mostrarDetallesPorAsamblea('Palma Sola')">Palma Sola</button>
                    <button class="ciudad-btn ciudad-PuertoCumarebo" onclick="mostrarDetallesPorAsamblea('Puerto Cumarebo')">Puerto Cumarebo</button>
                    <button class="ciudad-btn ciudad-PuntoFijo" onclick="mostrarDetallesPorAsamblea('Punto Fijo')">Punto Fijo</button>
                    <button class="ciudad-btn ciudad-SantaCruzdeBucaral" onclick="mostrarDetallesPorAsamblea('Santa Cruz de Bucaral')">Santa Cruz de Bucaral</button>
                    <button class="ciudad-btn ciudad-SectorUnión" onclick="mostrarDetallesPorAsamblea('Sector Unión')">Sector Unión</button>
                    <button class="ciudad-btn ciudad-Tocópero" onclick="mostrarDetallesPorAsamblea('Tocópero')">Tocópero</button>
                    <button class="ciudad-btn ciudad-Tucacas" onclick="mostrarDetallesPorAsamblea('Tucacas')">Tucacas</button>
                    <button class="ciudad-btn ciudad-Yaracal" onclick="mostrarDetallesPorAsamblea('Yaracal')">Yaracal</button>

                
                <?php elseif ($estado === 'Guarico'): ?>
                    <button class="ciudad-btn ciudad-AltagraciadeOrituco" onclick="mostrarDetallesPorAsamblea('Altagracia de Orituco')">Altagracia de Orituco</button>
                    <button class="ciudad-btn ciudad-SanJuandelosMorros" onclick="mostrarDetallesPorAsamblea('San Juan de los Morros')">San Juan de los Morros</button>
                    <button class="ciudad-btn ciudad-Tiguigue" onclick="mostrarDetallesPorAsamblea('Tigüigüe')">Tigüigüe</button>
                    <button class="ciudad-btn ciudad-ValledelaPascua" onclick="mostrarDetallesPorAsamblea('Valle de la Pascua')">Valle de la Pascua</button>
                    <button class="ciudad-btn ciudad-Zaraza" onclick="mostrarDetallesPorAsamblea('Zaraza')">Zaraza</button>

                <?php elseif ($estado === 'Mérida'): ?>
                    <button class="ciudad-btn ciudad-ElVigia" onclick="mostrarDetallesPorAsamblea('El Vigia')">El Vigia</button>
                    <button class="ciudad-btn ciudad-Chamita" onclick="mostrarDetallesPorAsamblea('CHAMITA')">Chamita</button>
                    <button class="ciudad-btn ciudad-Jají" onclick="mostrarDetallesPorAsamblea('Jají')">Jají</button>
                    <button class="ciudad-btn ciudad-LosPróceres" onclick="mostrarDetallesPorAsamblea('Los Próceres')">Los Próceres</button>

                <?php elseif ($estado === 'Miranda'): ?>
                    <button class="ciudad-btn ciudad-Caucagua" onclick="mostrarDetallesPorAsamblea('Caucagua')">Caucagua</button>
                    <button class="ciudad-btn ciudad-Charallave" onclick="mostrarDetallesPorAsamblea('Charallave')">Charallave</button>
                    <button class="ciudad-btn ciudad-Guarenas" onclick="mostrarDetallesPorAsamblea('Guarenas')">Guarenas</button>
                    <button class="ciudad-btn ciudad-Guatire" onclick="mostrarDetallesPorAsamblea('Guatire')">Guatire</button>
                    <button class="ciudad-btn ciudad-LaMata" onclick="mostrarDetallesPorAsamblea('La Mata')">La Mata</button>
                    <button class="ciudad-btn ciudad-LosTeques" onclick="mostrarDetallesPorAsamblea('Los Teques')">Los Teques</button>
                    <button class="ciudad-btn ciudad-OcumaredelTuy" onclick="mostrarDetallesPorAsamblea('Ocumare del Tuy')">Ocumare del Tuy</button>
                    <button class="ciudad-btn ciudad-SantaLucía" onclick="mostrarDetallesPorAsamblea('Santa Lucía')">Santa Lucía</button>

                <?php elseif ($estado === 'Monagas'): ?>
                    <button class="ciudad-btn ciudad-Maturin" onclick="mostrarDetallesPorAsamblea('Maturin')">Maturin</button>

                <?php elseif ($estado === 'Nueva Esparta'): ?>
                    <button class="ciudad-btn ciudad-Porlamar" onclick="mostrarDetallesPorAsamblea('PORLAMAR - BARRIO ACHÍPANO')">Porlamar</button>

                <?php elseif ($estado === 'Portuguesa'): ?>
                    <button class="ciudad-btn ciudad-Acarigua" onclick="mostrarDetallesPorAsamblea('Acarigua')">Acarigua</button>
                    <button class="ciudad-btn ciudad-Biscucuy" onclick="mostrarDetallesPorAsamblea('Biscucuy')">Biscucuy</button>
                    <button class="ciudad-btn ciudad-CorozoLargo" onclick="mostrarDetallesPorAsamblea('Corozo Largo')">Corozo Largo</button>
                    <button class="ciudad-btn ciudad-Guanare" onclick="mostrarDetallesPorAsamblea('Guanare')">Guanare</button>
                    <button class="ciudad-btn ciudad-Guanarito" onclick="mostrarDetallesPorAsamblea('Guanarito')">Guanarito</button>
                    <button class="ciudad-btn ciudad-LosPuertosdePayara" onclick="mostrarDetallesPorAsamblea('Los Puertos de Payara')">Los Puertos de Payara</button>
                    <button class="ciudad-btn ciudad-SanJosédelasMajaguas" onclick="mostrarDetallesPorAsamblea('San José de las Majaguas')">San José de las Majaguas</button>
                    <button class="ciudad-btn ciudad-SanRafaeldeOnoto" onclick="mostrarDetallesPorAsamblea('San Rafael de Onoto')">San Rafael de Onoto</button>
                    <button class="ciudad-btn ciudad-TapadePiedra" onclick="mostrarDetallesPorAsamblea('Tapa de Piedra')">Tapa de Piedra</button>

                <?php elseif ($estado === 'Sucre'): ?>
                    <button class="ciudad-btn ciudad-Carúpano" onclick="mostrarDetallesPorAsamblea('Carúpano')">Carúpano</button>
                    <button class="ciudad-btn ciudad-ElMango" onclick="mostrarDetallesPorAsamblea('El Mango')">El Mango</button>
                    <button class="ciudad-btn ciudad-LosAltosdeSucre" onclick="mostrarDetallesPorAsamblea('Los Altos de Sucre')">Los Altos de Sucre</button>
                    <button class="ciudad-btn ciudad-SanPedrito" onclick="mostrarDetallesPorAsamblea('San Pedrito')">San Pedrito</button>
                    <button class="ciudad-btn ciudad-SantaFe" onclick="mostrarDetallesPorAsamblea('Santa Fe')">Santa Fe</button>
                    <button class="ciudad-btn ciudad-Zurita" onclick="mostrarDetallesPorAsamblea('Zurita')">Zurita</button>

                <?php elseif ($estado === 'Táchira'): ?>
                    <button class="ciudad-btn ciudad-SanAntonio" onclick="mostrarDetallesPorAsamblea('San Antonio')">San Antonio</button>
                    <button class="ciudad-btn ciudad-SanCristobal" onclick="mostrarDetallesPorAsamblea('San Cristobal')">San Cristobal</button>

                <?php elseif ($estado === 'Trujillo'): ?>
                    <button class="ciudad-btn ciudad-Batatal" onclick="mostrarDetallesPorAsamblea('Batatal')">Batatal</button>
                    <button class="ciudad-btn ciudad-ElCenizo" onclick="mostrarDetallesPorAsamblea('El Cenizo')">El Cenizo</button>
                    <button class="ciudad-btn ciudad-SabanadeMendoza" onclick="mostrarDetallesPorAsamblea('Sabana de Mendoza')">Sabana de Mendoza</button>
                    <button class="ciudad-btn ciudad-Valera" onclick="mostrarDetallesPorAsamblea('Valera')">Valera</button>

                <?php elseif ($estado === 'Vargas'): ?>
                    <button class="ciudad-btn ciudad-CatialaMar" onclick="mostrarDetallesPorAsamblea('Catia la Mar')">Catia la Mar</button>

                <?php elseif ($estado === 'Yaracuy'): ?>
                    <button class="ciudad-btn ciudad-Albarico" onclick="mostrarDetallesPorAsamblea('Albarico')">Albarico</button>
                    <button class="ciudad-btn ciudad-Aroa" onclick="mostrarDetallesPorAsamblea('Aroa')">Aroa</button>
                    <button class="ciudad-btn ciudad-Carabobo" onclick="mostrarDetallesPorAsamblea('Carabobo')">Carabobo</button>
                    <button class="ciudad-btn ciudad-Chivacoa" onclick="mostrarDetallesPorAsamblea('Chivacoa')">Chivacoa</button>
                    <button class="ciudad-btn ciudad-ElGuayabo" onclick="mostrarDetallesPorAsamblea('El Guayabo')">El Guayabo</button>
                    <button class="ciudad-btn ciudad-Escondido" onclick="mostrarDetallesPorAsamblea('Escondido')">Escondido</button>
                    <button class="ciudad-btn ciudad-LaCandelaria" onclick="mostrarDetallesPorAsamblea('La Candelaria')">La Candelaria</button>
                    <button class="ciudad-btn ciudad-LaIndependencia" onclick="mostrarDetallesPorAsamblea('La Independencia')">La Independencia</button>
                    <button class="ciudad-btn ciudad-LaOcho" onclick="mostrarDetallesPorAsamblea('La Ocho')">La Ocho</button>
                    <button class="ciudad-btn ciudad-LaRaya" onclick="mostrarDetallesPorAsamblea('La Raya')">La Raya</button>
                    <button class="ciudad-btn ciudad-Marín" onclick="mostrarDetallesPorAsamblea('Marín')">Marín</button>
                    <button class="ciudad-btn ciudad-Nirgua" onclick="mostrarDetallesPorAsamblea('Nirgua')">Nirgua</button>
                    <button class="ciudad-btn ciudad-Obonte" onclick="mostrarDetallesPorAsamblea('Obonte')">Obonte</button>
                    <button class="ciudad-btn ciudad-Salom" onclick="mostrarDetallesPorAsamblea('Salom')">Salom</button>
                    <button class="ciudad-btn ciudad-SanFelipe" onclick="mostrarDetallesPorAsamblea('San Felipe')">San Felipe</button>
                    <button class="ciudad-btn ciudad-SanJosédeCarúpano" onclick="mostrarDetallesPorAsamblea('San José de Carúpano')">San José de Carúpano</button>
                    <button class="ciudad-btn ciudad-SanMateodeNirgua" onclick="mostrarDetallesPorAsamblea('San Mateo de Nirgua')">San Mateo de Nirgua</button>
                    <button class="ciudad-btn ciudad-Temerla" onclick="mostrarDetallesPorAsamblea('Temerla')">Temerla</button>
                    <button class="ciudad-btn ciudad-Yaritagua" onclick="mostrarDetallesPorAsamblea('Yaritagua')">Yaritagua</button>





                <?php elseif ($estado === 'Frontera Colombia'): ?>
                        <button class="ciudad-btn ciudad-Cali" onclick="mostrarDetallesPorAsamblea('Cali')">Cali</button>
                        <button class="ciudad-btn ciudad-Cartagena" onclick="mostrarDetallesPorAsamblea('Cartagena')">Cartagena</button>
                        <button class="ciudad-btn ciudad-CaucasiaAntioquia" onclick="mostrarDetallesPorAsamblea('Caucasia Antioquia')">Caucasia Antioquia</button>
                        <button class="ciudad-btn ciudad-Macaján" onclick="mostrarDetallesPorAsamblea('Macaján')">Macaján</button>
                        <button class="ciudad-btn ciudad-LaSierpe" onclick="mostrarDetallesPorAsamblea('La Sierpe')">La Sierpe</button>
                        <button class="ciudad-btn ciudad-Manguitos" onclick="mostrarDetallesPorAsamblea('Manguitos')">Manguitos</button>
                        <button class="ciudad-btn ciudad-Manizales" onclick="mostrarDetallesPorAsamblea('Manizales')">Manizales</button>
                        <button class="ciudad-btn ciudad-ManuelaBeltránBarranquilla" onclick="mostrarDetallesPorAsamblea('Manuela Beltrán - Barranquilla')">Manuela Beltrán - Barranquilla</button>
                        <button class="ciudad-btn ciudad-Medellín" onclick="mostrarDetallesPorAsamblea('Medellín')">Medellín</button>
                        <button class="ciudad-btn ciudad-Pasto" onclick="mostrarDetallesPorAsamblea('Pasto')">Pasto</button>
                        <button class="ciudad-btn ciudad-SincelejoSantaMaría" onclick="mostrarDetallesPorAsamblea('Sincelejo - Santa María')">Sincelejo - Santa María</button>
                        <button class="ciudad-btn ciudad-Tesoro" onclick="mostrarDetallesPorAsamblea('Tesoro')">Tesoro</button>
                        <button class="ciudad-btn ciudad-Soacha" onclick="mostrarDetallesPorAsamblea('Soacha')">Soacha</button>
                        <button class="ciudad-btn ciudad-Valledupar" onclick="mostrarDetallesPorAsamblea('Valledupar')">Valledupar</button>
                        <button class="ciudad-btn ciudad-VilladelRosarioCucuta" onclick="mostrarDetallesPorAsamblea('Villa del Rosario - Cucuta')">Villa del Rosario - Cucuta</button>
                        <button class="ciudad-btn ciudad-Villavicencio" onclick="mostrarDetallesPorAsamblea('Villavicencio')">Villavicencio</button>

                <?php endif; ?>

            </div>

            <!-- Leyenda interactiva (replica los botones del mapa) -->
            <div class="leyenda-interactiva">
                <h3>Leyenda de <?php echo $estado ?></h3>
                <ul>
                    <?php if ($estado === 'Zulia'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Cabimas')">Cabimas</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Cuatricentenario')">Cuatricentenario</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Progreso')">El Progreso</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Repelona')">La Repelona</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Villa del Rosario (Perijá)')">La Villa del Rosario (Perijá)</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Los Jobitos')">Los Jobitos</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Francisco')">San Francisco</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Zipayare')">Zipayare</button></li>
                    
                    
                    
                    <?php elseif ($estado === 'Lara'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('CALLE 23')">Calle 23</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Alto de Pueblo Nuevo')">Alto de Pueblo Nuevo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Carora')">Carora</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Cauderales')">Cauderales</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Duaca')">Duaca</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Carmen')">El Carmen</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Montañita')">La Montañita</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Reluciente')">La Reluciente</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Represa')">La Represa</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Moroturo')">Moroturo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Jacinto')">San Jacinto</button></li>
                
                    <?php elseif ($estado === 'Amazonas'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Puerto Ayacucho')">Puerto Ayacucho</button></li>
                    
                    <?php elseif ($estado === 'Anzoátegui'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('EL TIGRITO ')">El Tigrito</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Puerto la Cruz')">Puerto la Cruz</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('CIUDAD ORINOCO (SOLEDAD)')">Soledad</button></li>

                        <!-- Revisar Bario Antonio José de Sucre -->
                    <?php elseif ($estado === 'Apure'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Bario Antonio José de Sucre')">Bario Antonio José de Sucre</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Negro')">El Negro</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Elorza')">Elorza</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Guasdualito')">Guasdualito</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Fernando')">San Fernando</button></li>

                    <?php elseif ($estado === 'Aragua'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('BARRIO SAN CARLOS')">Barrio San Carlos</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('COROMOTO')">Coromoto</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('DIEZ DE DICIEMBRE')">Diez de Diciembre</button></li>

                        <li><button onclick="mostrarDetallesPorAsamblea('Carmen de Cura')">Carmen de Cura</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('LA PICA')">La Pica</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('PALO NEGRO, CENTRO')">Palo Negro, Centro</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Rosario de Paya')">Rosario de Paya</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Pueblo Nuevo')">Pueblo Nuevo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Mateo')">San Mateo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Santa Rita')">Santa Rita</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Zuata')">Zuata</button></li>

                    <?php elseif ($estado === 'Barinas'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Barrancas')">Barrancas</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Barinas')">Barinas</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Barinitas')">Barinitas</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Guamito')">Guamito</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Los Rastrojos')">Los Rastrojos</button></li>

                    <?php elseif ($estado === 'Bolívar'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Caicara del Orinoco')">Caicara del Orinoco</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('CIUDAD BOLÍVAR - CUYUNÍ')">Cuyuní</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('LA SABANITA')">La Sabanita</button></li>

                        <li><button onclick="mostrarDetallesPorAsamblea('Puerto Ordaz')">Puerto Ordaz</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Félix')">San Félix</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('SANTA ELENA DE UAIRÉN')">Santa Elena de Uairén</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('TUMEREMO')">Tumeremo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Santa Rosa del Buey')">Santa Rosa del Buey</button></li>

                    <?php elseif ($estado === 'Carabobo'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Alpargatón')">Alpargatón</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Bejuma')">Bejuma</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Boqueron')">Boqueron</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Campo Carabobo')">Campo Carabobo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Canoabo')">Canoabo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Chirgua')">Chirgua</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Guacara')">Guacara</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Güigüe')">Güigüe</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Juaniquero')">Juaniquero</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Compañía')">La Compañía</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Jobera')">La Jobera</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Lagunita')">La Lagunita</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('LA SABANA - CANOABO')">Canoabo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Las Trincheras')">Las Trincheras</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Los Caracaros')">Los Caracaros</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Mariara')">Mariara</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('PRIMAVERA')">Primavera</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('SAN ESTEBAN PUEBLO')">San Esteban</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Joaquin')">San Joaquin</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Pablo')">San Pablo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Tocuyito')">Tocuyito</button></li>

                        
                        <li><button onclick="mostrarDetallesPorAsamblea('Morón')">Morón</button></li>
                        
                        <li><button onclick="window.location.href='ciudad.php?estado=Carabobo&ciudad=Puerto Cabello,San Esteban'">Puerto Cabello</button></li>
                        <li><button onclick="window.location.href='ciudad.php?estado=Carabobo&ciudad=Valencia'">Valencia</button></li>



                    <?php elseif ($estado === 'Cojedes'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Buenos Aires')">Buenos Aires</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Barrio Ezequiel Zamora')">Barrio Ezequiel Zamora</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Barrio Nuevo')">Barrio Nuevo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Baul')">El Baul</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Muertico')">El Muertico</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Penitente')">El Penitente</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Genareño')">Genareño</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Chorrera')">La Chorrera</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Las Vegas')">Las Vegas</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Los Colorados')">Los Colorados</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Manrique')">Manrique</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Puente Onoto')">Puente Onoto</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Carlos')">San Carlos</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Tinaco')">Tinaco</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Tinaquillo')">Tinaquillo</button></li>

                    <?php elseif ($estado === 'Delta Amacuro'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Tucupita')">Tucupita</button></li>



                    <?php elseif ($estado === 'Distrito Capital'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Cementerio')">El Cementerio</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Valle')">El Valle</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Las Adjuntas')">Las Adjuntas</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Vega')">La Vega</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Los Flores')">Los Flores</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Petare')">Petare</button></li>



                    <?php elseif ($estado === 'Falcón'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Buena Vista')">Buena Vista</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Chichiriviche')">Chichiriviche</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Churuguara')">Churuguara</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Coro')">Coro</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Dabajuro')">Dabajuro</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Mene')">El Mene</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Jacura, Pueblo Nuevo')">Jacura, Pueblo Nuevo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Guacharaca')">La Guacharaca</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Mirimire')">Mirimire</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Palma Sola')">Palma Sola</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Puerto Cumarebo')">Puerto Cumarebo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Punto Fijo')">Punto Fijo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Santa Cruz de Bucaral')">Santa Cruz de Bucaral</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Sector Unión')">Sector Unión</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Tocópero')">Tocópero</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Tucacas')">Tucacas</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Yaracal')">Yaracal</button></li>

                    
                    <?php elseif ($estado === 'Guarico'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Altagracia de Orituco')">Altagracia de Orituco</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Juan de los Morros')">San Juan de los Morros</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Tigüigüe')">Tigüigüe</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Valle de la Pascua')">Valle de la Pascua</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Zaraza')">Zaraza</button></li>

                    <?php elseif ($estado === 'Mérida'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Vigia')">El Vigia</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('CHAMITA')">Chamita</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Jají')">Jají</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Los Próceres')">Los Próceres</button></li>

                    <?php elseif ($estado === 'Miranda'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Caucagua')">Caucagua</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Charallave')">Charallave</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Guarenas')">Guarenas</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Guatire')">Guatire</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Mata')">La Mata</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Los Teques')">Los Teques</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Ocumare del Tuy')">Ocumare del Tuy</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Santa Lucía')">Santa Lucía</button></li>

                    <?php elseif ($estado === 'Monagas'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Maturin')">Maturin</button></li>

                    <?php elseif ($estado === 'Nueva Esparta'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('PORLAMAR - BARRIO ACHÍPANO')">Porlamar</button></li>

                    <?php elseif ($estado === 'Portuguesa'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Acarigua')">Acarigua</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Biscucuy')">Biscucuy</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Corozo Largo')">Corozo Largo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Guanare')">Guanare</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Guanarito')">Guanarito</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Los Puertos de Payara')">Los Puertos de Payara</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San José de las Majaguas')">San José de las Majaguas</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Rafael de Onoto')">San Rafael de Onoto</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Tapa de Piedra')">Tapa de Piedra</button></li>

                    <?php elseif ($estado === 'Sucre'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Carúpano')">Carúpano</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Mango')">El Mango</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Los Altos de Sucre')">Los Altos de Sucre</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Pedrito')">San Pedrito</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Santa Fe')">Santa Fe</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Zurita')">Zurita</button></li>

                    <?php elseif ($estado === 'Táchira'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Antonio')">San Antonio</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Cristobal')">San Cristobal</button></li>

                    <?php elseif ($estado === 'Trujillo'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Batatal')">Batatal</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Cenizo')">El Cenizo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Sabana de Mendoza')">Sabana de Mendoza</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Valera')">Valera</button></li>

                    <?php elseif ($estado === 'Vargas'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Catia la Mar')">Catia la Mar</button></li>

                    <?php elseif ($estado === 'Yaracuy'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Albarico')">Albarico</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Aroa')">Aroa</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Carabobo')">Carabobo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Chivacoa')">Chivacoa</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('El Guayabo')">El Guayabo</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Escondido')">Escondido</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Candelaria')">La Candelaria</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Independencia')">La Independencia</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Ocho')">La Ocho</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Raya')">La Raya</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Marín')">Marín</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Nirgua')">Nirgua</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Obonte')">Obonte</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Salom')">Salom</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Felipe')">San Felipe</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San José de Carúpano')">San José de Carúpano</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('San Mateo de Nirgua')">San Mateo de Nirgua</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Temerla')">Temerla</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Yaritagua')">Yaritagua</button></li>





                    <?php elseif ($estado === 'Frontera Colombia'): ?>
                        <li><button onclick="mostrarDetallesPorAsamblea('Cali')">Cali</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Cartagena')">Cartagena</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Caucasia Antioquia')">Caucasia Antioquia</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Macaján')">Macaján</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('La Sierpe')">La Sierpe</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Manguitos')">Manguitos</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Manizales')">Manizales</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Manuela Beltrán - Barranquilla')">Manuela Beltrán - Barranquilla</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Medellín')">Medellín</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Pasto')">Pasto</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Sincelejo - Santa María')">Sincelejo - Santa María</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Tesoro')">Tesoro</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Soacha')">Soacha</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Valledupar')">Valledupar</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Villa del Rosario - Cucuta')">Villa del Rosario - Cucuta</button></li>
                        <li><button onclick="mostrarDetallesPorAsamblea('Villavicencio')">Villavicencio</button></li>

                    
                    <?php endif; ?>
                </ul>
            </div>


        </div>

            
    </div>

    <!-- MODAL -->
    <!-- Modal para mostrar los detalles -->
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
        // Convertimos a objeto indexado por asamblea
        const iglesiasData = <?php
            $iglesiasPorAsamblea = [];
            foreach ($datos_iglesias as $row) {
                $clave = strtoupper(trim($row['asamblea']));
                $iglesiasPorAsamblea[$clave] = $row;
            }
            echo json_encode($iglesiasPorAsamblea, JSON_UNESCAPED_UNICODE);
        ?>;

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
            document.getElementById("det-mapa").href = data.GoogleMaps || "#";

            document.getElementById("modal").style.display = "block";
        }

        function cerrarModal() {
            document.getElementById("modal").style.display = "none";
        }

        function mostrarDetallesPorAsamblea(asamblea) {
            const clave = asamblea.trim().toUpperCase();
            const data = iglesiasData[clave];
            if (data) {
                mostrarDetalles(data);
            } else {
                alert("No se encontró información para la asamblea: " + asamblea);
            }
        }
    </script>


</body>
</html>
<?php $conn->close(); ?>

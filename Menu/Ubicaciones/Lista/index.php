
<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "geolocalizador";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT id, asamblea, numero, ciudad, estado, direccion, domingo, lunes, martes, miercoles, jueves, viernes, sabado, obras, GoogleMaps FROM iglesias";
$result = $conn->query($sql);
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
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
                if (columna === "all") {
                    let textoFila = fila.innerText.toLowerCase();
                    fila.style.display = textoFila.includes(input) ? "" : "none";
                } else {
                    let textoCelda = celdas[columna].innerText.toLowerCase();
                    fila.style.display = textoCelda.includes(input) ? "" : "none";
                }
            });
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
            document.getElementById("modal").style.display = "block";
        }

        function cerrarModal() {
            document.getElementById("modal").style.display = "none";
        }
    </script>
</head>
<body>
    <!-- Íconos visibles solo en escritorio -->
    <!-- CONTENEDOR DEL MENÚ -->
    <nav class="menu-nav">
        <button class="icon-btn btn-menu" onclick="toggleSidebarMenu()" title="Menú">
            <img src="iconos/Menu.png" alt="Menú">
        </button>
        <button class="icon-btn solo-pc" onclick="location.href='http://localhost/Geolocalizador/Menu/index.php'" title="Inicio">
            <img src="iconos/Inicio.png" alt="Inicio">
        </button>
        <button class="icon-btn solo-pc" onclick="location.href='http://localhost/Geolocalizador/Menu/Submenu.php'" title="Ubicación">
            <img src="iconos/ubicaciones.png" alt="Ubicación">
        </button>
        <button class="icon-btn solo-pc" onclick="location.href='http://localhost/Geolocalizador/Menu/Eventos/index.php'" title="Eventos">
            <img src="iconos/eventos.png" alt="Eventos">
        </button>
        <?php if ($_SESSION["rol"] !== "invitado") : ?>
            <button class="icon-btn solo-pc" onclick="location.href='http://localhost/Geolocalizador/Menu/Donaciones/index.php'" title="Donaciones">
                <img src="iconos/donation.png" alt="Donaciones">
            </button>
        <?php endif; ?>
        <button class="icon-btn solo-pc" onclick="location.href='http://localhost/Geolocalizador/Menu/Material/index.php'" title="Material Literario">
            <img src="iconos/material.png" alt="Material Literario">
        </button>
        <button class="icon-btn solo-pc" onclick="location.href='http://localhost/Geolocalizador/Menu/LiteraturaBiblica/index.php'" title="Biblia">
            <img src="iconos/Biblia.png" alt="Estudio Bíblico">
        </button>
        <button class="icon-btn btn-sesion" onclick="location.href='http://localhost/Geolocalizador/Menu/logout.php'" title="Cerrar Sesión">
            <img src="iconos/Sesion.png" alt="Cerrar Sesión">
        </button>
    </nav>


    <!-- Menú emergente (sidebar) para celular -->
    <div class="sidebar mobile-only" id="sidebarMenu">
        <h2>Menú</h2>
        <a href="http://localhost/Geolocalizador/Menu/index.php">Inicio</a>
        <a href="http://localhost/Geolocalizador/Menu/Submenu.php">Ubicación</a>
        <a href="http://localhost/Geolocalizador/Menu/Eventos/index.php">Eventos</a>
        <?php if ($_SESSION["rol"] !== "invitado") : ?>
            <a href="http://localhost/Geolocalizador/Menu/Donaciones/index.php">Donaciones</a>
        <?php endif; ?>
        <a href="http://localhost/Geolocalizador/Menu/Material/index.php">Material Literario</a>
        <a href="http://localhost/Geolocalizador/Menu/LiteraturaBiblica/index.php">Estudio Bíblico</a>
        <?php if ($_SESSION["rol"] !== "invitado") : ?>
            <a href="http://localhost/Geolocalizador/Cuenta/index.php">Gestionar Sesión</a>
        <?php endif; ?>
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
    </script>



<div class="contento">
    <header>
        <button onclick="location.href='http://localhost/Geolocalizador/Menu/Submenu.php'" class="return-button" title="Volver"></button>
        <h1>Lista de las Asambleas Congregadas en Venezuela</h1>
    </header>

    <div class="filtros-container">
        <select id="columna" onchange="filtrarTabla()">
            <option value="all">Buscar todo</option>
            <option value="0">Asamblea</option>
            <option value="1">Número</option>
            <option value="2">Ciudad</option>
            <option value="3">Estado</option>
        </select>

        <input class="BarradeBuscador" type="text" id="buscador" onkeyup="filtrarTabla()" placeholder="Buscar...">

        <select id="selector-estado" onchange="filtrarTabla()">
            <option value="">Selecciona un estado</option>
            <option value="Amazonas">Amazonas</option>
            <option value="Anzoátegui">Anzoátegui</option>
            <option value="Apure">Apure</option>
            <option value="Aragua">Aragua</option>
            <option value="Barinas">Barinas</option>
            <option value="Bolívar">Bolívar</option>
            <option value="Carabobo">Carabobo</option>
            <option value="Cojedes">Cojedes</option>
            <option value="Delta Amacuro">Delta Amacuro</option>
            <option value="Distrito Capital">Distrito Capital</option>
            <option value="Falcon">Falcón</option>
            <option value="Guarico">Guárico</option>
            <option value="Lara">Lara</option>
            <option value="Mérida">Mérida</option>
            <option value="Miranda">Miranda</option>
            <option value="Monagas">Monagas</option>
            <option value="Nueva Esparta">Nueva Esparta</option>
            <option value="Portuguesa">Portuguesa</option>
            <option value="Sucre">Sucre</option>
            <option value="Tachira">Táchira</option>
            <option value="Trujillo">Trujillo</option>
            <option value="Yaracuy">Yaracuy</option>
            <option value="Zulia">Zulia</option>
        </select>


        <script>
            function filtrarTabla() {
                const columna = document.getElementById("columna").value;
                const input = columna === "3"
                    ? document.getElementById("selector-estado").value.toLowerCase()
                    : document.getElementById("buscador").value.toLowerCase();

                const filas = document.querySelectorAll(".tabla-cuerpo tbody tr");

                filas.forEach(fila => {
                    const celdas = fila.getElementsByTagName("td");

                    if (columna === "all") {
                        const textoFila = fila.innerText.toLowerCase();
                        fila.style.display = textoFila.includes(input) ? "" : "none";
                    } else {
                        let indiceReal;
                        switch (columna) {
                            case "0": indiceReal = 0; break; // Asamblea
                            case "1": indiceReal = 2; break; // Número
                            case "2": indiceReal = 4; break; // Ciudad
                            case "3": indiceReal = 6; break; // Estado
                            default: indiceReal = 0; break;
                        }

                        const textoCelda = celdas[indiceReal]?.innerText.toLowerCase() || "";
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
                            <tr>
                                <td class="col1"><?= htmlspecialchars($row['asamblea']) ?></td>
                                <td class="espacio"></td>

                                <td class="col2"><?= htmlspecialchars($row['numero']) ?></td>
                                <td class="espacio"></td>

                                <td class="col3"><?= htmlspecialchars($row['ciudad']) ?></td>
                                <td class="espacio"></td>

                                <td class="col4"><?= htmlspecialchars($row['estado']) ?></td>
                                <td class="espacio"></td>

                                <td class="col5">
                                    <button class="btn-detalles" onclick='mostrarDetalles(<?= json_encode($row, JSON_UNESCAPED_UNICODE) ?>)'>Ver</button>
                                    <a class="btn-mapa" href="<?= htmlspecialchars($row['GoogleMaps']) ?>" target="_blank" title="Ver en Google Maps">
                                        <i class="fas fa-map"></i>
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



</div>

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
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>

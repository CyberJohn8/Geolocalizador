<?php
    session_start();

    $conn = new mysqli("localhost", "root", "", "geolocalizador");
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = "SELECT id, asamblea, numero, ciudad, estado, direccion, domingo, lunes, martes, miercoles, jueves, viernes, sabado, obras, GoogleMaps, coordenadas FROM iglesias";
    $result = $conn->query($sql);

    $iglesias = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $iglesias[] = $row;
        }
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iglesias Cercanas</title>
    <link rel="stylesheet" href="Cercanas.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
    // Actualiza el texto de la unidad
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
                if (!iglesia.Coordenadas) return;
                const [lat, lon] = iglesia.Coordenadas.split(",").map(Number);
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
                        <td>${ig.Asamblea}</td>
                        <td>${ig.Numero}</td>
                        <td>${ig.Ciudad}</td>
                        <td>${ig.Estado}</td>
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

<script>
    // Actualiza el texto de la unidad
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
                if (!iglesia.Coordenadas) return;
                const [lat, lon] = iglesia.Coordenadas.split(",").map(Number);
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
                        <td>${ig.Asamblea}</td>
                        <td>${ig.Numero}</td>
                        <td>${ig.Ciudad}</td>
                        <td>${ig.Estado}</td>
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

<main class="content">
    <header>
        <button onclick="location.href='http://localhost/Geolocalizador/Menu/Submenu.php'" class="return-button" title="Volver"></button>
        <h1>Lista de las Asambleas Congregadas en Venezuela más Cercanas</h1>
    </header>

    <button onclick="usarUbicacion()">📍 Mostrar más cercanas</button>

    <div class="filtros">
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
                <input type="range" id="rangoDistancia" min="1" max="120" value="70" oninput="document.getElementById('distanciaValor').textContent = this.value" />
                <span><span id="distanciaValor">70</span> <span id="unidadTexto">km</span></span>
            </div>
        </div>
    </div>

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
                    <th class="col2">Número</th>
                    <th class="espacio"></th>
                    <th class="col3">Ciudad</th>
                    <th class="espacio"></th>
                    <th class="col4">Estado</th>
                    <th class="espacio"></th>
                    <th class="col5">Detalles</th>
                    <th class="espacio"></th>
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

                            <td class="col2"><?= htmlspecialchars($row['numero']) ?></td>
                            <td class="espacio"></td>

                            <td class="col3"><?= htmlspecialchars($row['ciudad']) ?></td>
                            <td class="espacio"></td>

                            <td class="col4"><?= htmlspecialchars($row['estado']) ?></td>
                            <td class="espacio"></td>

                            <td class="col5">
                                <button class="btn-detalles"
                                    onclick='mostrarDetalles(<?= json_encode($row, JSON_UNESCAPED_UNICODE) ?>)'>Ver</button>
                            </td>
                            <td class="espacio"></td>

                            <td class="col5">
                                <a class="btn-mapa" href="<?= htmlspecialchars($row['GoogleMaps']) ?>" target="_blank" title="Ver en Google Maps">
                                    <i class="fas fa-map"></i>
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
                            <td>${ig.numero}</td>
                            <td>${ig.ciudad}</td>
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











    <div id="modalDetalles" class="modal-detalles">
        <div class="modal-contenido">
            <span class="cerrar-modal" onclick="cerrarModal()">&times;</span>
            <h2>Detalles de la Iglesia</h2>
            <div id="detallesContenido"></div>
        </div>
    </div>


    <script>
        // Función para mostrar el modal de detalles
        function mostrarDetalles(iglesia) {
            const modal = document.getElementById("modalDetalles");
            const contenido = document.getElementById("detallesContenido");

            contenido.innerHTML = `
                <p><strong>Asamblea:</strong> ${iglesia.asamblea}</p>
                <p><strong>Número:</strong> ${iglesia.numero}</p>
                <p><strong>Ciudad:</strong> ${iglesia.ciudad}</p>
                <p><strong>Estado:</strong> ${iglesia.estado}</p>
                <p><strong>Dirección:</strong> ${iglesia.direccion}</p>

                <div class="detalles-grid">
                    <p><strong>Domingo:</strong> ${iglesia.domingo || 'No especificado'}</p>
                    <p><strong>Lunes:</strong> ${iglesia.lunes || 'No especificado'}</p>
                    <p><strong>Martes:</strong> ${iglesia.martes || 'No especificado'}</p>
                    <p><strong>Miércoles:</strong> ${iglesia.miercoles || 'No especificado'}</p>
                    <p><strong>Jueves:</strong> ${iglesia.jueves || 'No especificado'}</p>
                    <p><strong>Viernes:</strong> ${iglesia.viernes || 'No especificado'}</p>
                    <p><strong>Sábado:</strong> ${iglesia.sabado || 'No especificado'}</p>
                </div>

                <p><strong>Obras:</strong> ${iglesia.obras || 'No especificado'}</p>
                <p><a href="${iglesia.GoogleMaps}" target="_blank">Ver en Google Maps</a></p>
            `;

            modal.style.display = "flex";
        }


        // Función para cerrar el modal
        function cerrarModal() {
            document.getElementById("modalDetalles").style.display = "none";
        }

        // Cerrar modal si se hace clic fuera del contenido
        window.addEventListener("click", function (event) {
            const modal = document.getElementById("modalDetalles");
            if (event.target === modal) {
                cerrarModal();
            }
        });
    </script>
</main>





</body>
</html>

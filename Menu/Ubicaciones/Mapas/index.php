<?php
session_start();

$estados = [
    "Amazonas", "Anzoátegui", "Apure", "Aragua", "Barinas", "Bolívar",
    "Carabobo", "Cojedes", "Delta Amacuro", "Falcón", "Guarico",
    "Lara", "Mérida", "Miranda", "Monagas", "Nueva Esparta",
    "Portuguesa", "Sucre", "Táchira", "Trujillo", "Vargas",
    "Yaracuy", "Zulia", "Frontera Colombia"
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Venezuela</title>
    <link rel="stylesheet" href="Mapa.css"> <!-- Reutilizamos el mismo CSS -->
    
    
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
            <button onclick="location.href='http://localhost/Geolocalizador/Menu/Submenu.php'" class="return-button" title="Volver"></button>
            <h1>Mapa de las Asambleas en Venezuela</h1>
        </header>

        <div class="mapa-leyenda-wrapper">

            <!-- Contenedor del Mapa -->
            <div class="mapa-container">
                    <img src="Mapa.png" alt="Mapa de Venezuela">

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
                    <button class="estado-btn btn-Guarico" onclick="window.location.href='Estados.php?estado=Guarico'" title="Guárico">11</button>
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
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Guarico'">11 - Guárico</button></td></tr>
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

                            <tr><td><button onclick="window.location.href='Estados.php?estado=Amazonas'">* - Distrito Capital</button></td></tr>
                            
                            <tr><td><button onclick="window.location.href='Estados.php?estado=Frontera Colombia'">Colombia</button></td></tr>
                        </tbody>
                    </table>
                </div>

            </div>


            
        </div>

</body>
</html>



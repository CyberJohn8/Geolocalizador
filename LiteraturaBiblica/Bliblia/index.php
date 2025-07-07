<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "db_biblia");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener libros con su número de capítulos
$libros = [];
$resultado = $conexion->query("
    SELECT b.id, b.modern_name, MAX(v.chapter) AS max_chapter
    FROM books b
    JOIN verses v ON b.id = v.book_id
    GROUP BY b.id, b.modern_name
    ORDER BY b.id
");

while ($fila = $resultado->fetch_assoc()) {
    $libros[] = $fila;
}

// Manejar la lectura de versículos
$versiculos = [];
$capitulos_disponibles = [];
$libro_seleccionado = $_POST['libro'] ?? $libros[0]['modern_name'];
$capitulo_seleccionado = intval($_POST['capitulo'] ?? 1);

// Obtener ID del libro seleccionado
$stmt = $conexion->prepare("SELECT id FROM books WHERE modern_name = ?");
$stmt->bind_param("s", $libro_seleccionado);
$stmt->execute();
$res = $stmt->get_result();
$book = $res->fetch_assoc();

if ($book) {
    $book_id = $book['id'];

    // Capítulos disponibles para el libro
    $capitulos_res = $conexion->query("SELECT DISTINCT chapter FROM verses WHERE book_id = $book_id ORDER BY chapter ASC");
    while ($row = $capitulos_res->fetch_assoc()) {
        $capitulos_disponibles[] = $row['chapter'];
    }

    // Leer versículos si se solicitó
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $stmt = $conexion->prepare("SELECT verse, text FROM verses WHERE book_id = ? AND chapter = ? ORDER BY verse ASC");
        $stmt->bind_param("ii", $book_id, $capitulo_seleccionado);
        $stmt->execute();
        $resultado = $stmt->get_result();

        while ($fila = $resultado->fetch_assoc()) {
            $versiculos[] = $fila;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lector Bíblico</title>
  <link rel="stylesheet" href="Biblica.css">
  
  
  <style>
    select, button {
        padding: 10px;
        border-radius: 5px;
        margin: 5px;
        border: 1px solid #ccc;
    }
    #bible-text {
        margin-top: 30px;
        text-align: left;
        background: white;
        padding: 20px;
        border-radius: 10px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<!-- Menú lateral con íconos fijos -->
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


<main>
  <div>
    <header>
      <button onclick="location.href='http://localhost/Geolocalizador/Menu/LiteraturaBiblica/index.php'" style="margin-bottom: 15px;"></button>
      <h1>Lector de la Biblia</h1>
    </header>

    <!-- Formulario de selección -->
    <form method="POST" style="text-align: center; margin-top: 40px;">
      <label for="libro">Libro:</label>
      <select name="libro" id="libro" onchange="this.form.submit()">
        <?php foreach ($libros as $libro): ?>
          <option value="<?= htmlspecialchars($libro['modern_name']) ?>" <?= $libro_seleccionado === $libro['modern_name'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($libro['modern_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="capitulo">Capítulo:</label>
      <select name="capitulo" id="capitulo">
        <?php foreach ($capitulos_disponibles as $cap): ?>
          <option value="<?= $cap ?>" <?= $capitulo_seleccionado === intval($cap) ? 'selected' : '' ?>><?= $cap ?></option>
        <?php endforeach; ?>
      </select>

      <button type="submit">Leer</button>
    </form>

    <!-- Mostrar versículos -->
    <?php if (!empty($versiculos)): ?>
      <div id="bible-text">
        <?php foreach ($versiculos as $verso): ?>
          <p><strong><?= $verso['verse'] ?>:</strong> <?= $verso['text'] ?></p>
        <?php endforeach; ?>
      </div>
    <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
      <div id="bible-text"><p>No se encontraron versículos para esta selección.</p></div>
    <?php endif; ?>
  </div>
</main>
</body>
</html>

<?php
session_start();

// Solo administradores
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "geolocalizador");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer zona horaria de Caracas
date_default_timezone_set("America/Caracas");

// Variables
$mensaje = "";
$editando = false;
$eventoEdit = null;

// === Editar ===
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM eventos WHERE id = $id");
    if ($res->num_rows > 0) {
        $eventoEdit = $res->fetch_assoc();
        $editando = true;
    }
}

// === Eliminar ===
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM eventos WHERE id = $id");
    header("Location: crud_eventos.php");
    exit();
}

// === Guardar/Actualizar ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $detalles = $_POST['detalles'];
    $ubicacion = $_POST['ubicacion'];
    $fecha = date("Y-m-d H:i:s");

    if (!empty($_POST['evento_id'])) {
        // Actualizar
        $id = intval($_POST['evento_id']);
        $stmt = $conn->prepare("UPDATE eventos SET detalles=?, ubicacion=? WHERE id=?");
        $stmt->bind_param("ssi", $detalles, $ubicacion, $id);
    } else {
        // Insertar nuevo
        $stmt = $conn->prepare("INSERT INTO eventos (detalles, ubicacion, fecha_publicacion) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $detalles, $ubicacion, $fecha);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: crud_eventos.php");
    exit();
}

// === Obtener todos los eventos ===
$result = $conn->query("SELECT * FROM eventos ORDER BY fecha_publicacion DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Eventos</title>
    <link rel="stylesheet" href="crud_eventos.css">
</head>
<body>

<div class="container">
    <button class="return-button" onclick="history.back()"></button>
    <h1>Gestión de Eventos</h1>

    <!-- Formulario -->
    <form method="POST">
        <input type="hidden" name="evento_id" value="<?= $editando ? $eventoEdit['id'] : '' ?>">
        
        <label for="detalles">Detalles:</label>
        <textarea name="detalles" id="detalles" rows="3" required><?= $editando ? htmlspecialchars($eventoEdit['detalles']) : '' ?></textarea>
        
        <label for="ubicacion">Ubicación:</label>
        <input type="text" name="ubicacion" id="ubicacion" required value="<?= $editando ? htmlspecialchars($eventoEdit['ubicacion']) : '' ?>">

        <button type="submit"><?= $editando ? "Actualizar" : "Publicar" ?></button>
        <?php if ($editando): ?>
            <a href="crud_eventos.php" class="cancelar">Cancelar</a>
        <?php endif; ?>
    </form>

    <!-- Lista de eventos -->
    <h2>Eventos Existentes</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="evento">
            <h3><?= htmlspecialchars($row['detalles']) ?></h3>
            <p>📍 <?= htmlspecialchars($row['ubicacion']) ?></p>
            <small>📅 Publicado el: <?= $row['fecha_publicacion'] ?></small><br>
            <a href="crud_eventos.php?editar=<?= $row['id'] ?>">✏️ Editar</a>
            <a href="crud_eventos.php?eliminar=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este evento?')" style="color:red;">🗑 Eliminar</a>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>

<?php $conn->close(); ?>

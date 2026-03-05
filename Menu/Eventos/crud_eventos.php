<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: text/html; charset=utf-8'); // Asegura codificación UTF-8

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: https://directorioasambleasvzla.com//index.php");
    exit();
}

// Incluir archivo de conexión (mysqli)
//require __DIR__ . "/../../../conexion.php"; // tu archivo de conexión
require __DIR__ . "/../../conexion.php";
 // tu archivo de conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4"); // Asegura correcta codificación en la conexión

date_default_timezone_set("America/Caracas");

$editando = false;
$eventoEdit = null;

// Obtener evento a editar
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $res = $conn->query("SELECT * FROM eventos WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $eventoEdit = $res->fetch_assoc();
        $editando = true;
    }
}

// Eliminar evento
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM eventos WHERE id = $id");
    header("Location: crud_eventos.php");
    exit();
}

// Guardar o actualizar evento
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $detalles = $_POST['detalles'];
    $ubicacion = $_POST['ubicacion'];
    $fecha = date("Y-m-d H:i:s");
    $admin = $_SESSION['username'] ?? 'Administrador';
    $usuario_id = $_SESSION['id'] ?? null;

    if (!empty($_POST['evento_id'])) {
        $id = intval($_POST['evento_id']);
        $stmt = $conn->prepare("UPDATE eventos SET detalles=?, ubicacion=? WHERE id=?");
        $stmt->bind_param("ssi", $detalles, $ubicacion, $id);
        $accion = "editado";
    } else {
        $stmt = $conn->prepare("INSERT INTO eventos (detalles, ubicacion, fecha_publicacion, usuario_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $detalles, $ubicacion, $fecha, $usuario_id);
        $accion = "publicado";
    }

    $stmt->execute();
    $stmt->close();

    $_SESSION['evento_notificacion'] = [
        'accion' => $accion,
        'detalles' => $detalles,
        'ubicacion' => $ubicacion,
        'fecha' => $fecha,
        'admin' => $admin
    ];

    header("Location: crud_eventos.php");
    exit();
}

// Obtener todos los eventos
$result = $conn->query("SELECT * FROM eventos ORDER BY fecha_publicacion DESC");
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Eventos</title>
    <link rel="stylesheet" href="crud_eventos.css">
</head>
<body>
<div class="container">
    <button class="return-button" onclick="window.history.back()"></button>
    <h1>Gestión de Eventos</h1>

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

    <h2>Eventos Existentes</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="evento">
            <h3><?= htmlspecialchars($row['detalles']) ?></h3>
            <p><?= htmlspecialchars($row['ubicacion']) ?></p>
            <small>Publicado el: <?= $row['fecha_publicacion'] ?></small><br>
            <a href="crud_eventos.php?editar=<?= $row['id'] ?>">Editar</a>
            <a href="crud_eventos.php?eliminar=<?= $row['id'] ?>" class="eliminar" onclick="return confirm('¿Eliminar este evento?')">Eliminar</a>
        </div>
    <?php endwhile; ?>
</div>

<?php if (isset($_SESSION['evento_notificacion'])): ?>
<script>
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        navigator.serviceWorker.register('sw.js').then(function(reg) {
            console.log('Service Worker registrado');
            reg.showNotification("Nuevo evento <?= $_SESSION['evento_notificacion']['accion'] ?>", {
                body: "<?= $_SESSION['evento_notificacion']['detalles'] ?> en <?= $_SESSION['evento_notificacion']['ubicacion'] ?>",
                icon: "https://directorioasambleasvzla.com//https://directorioasambleasvzla.com/iconos/eventos.png",
                tag: "evento-<?= time() ?>"
            });
        }).catch(function(err) {
            console.error('Error registrando SW:', err);
        });
    }
</script>
<?php unset($_SESSION['evento_notificacion']); ?>
<?php endif; ?>






<!-- Indicador de estado de notificaciones -->
<div id="noti-status" style="position: fixed; bottom: 10px; right: 10px; background: #fff8dc; border: 1px solid #ccc; padding: 10px 15px; border-radius: 8px; font-size: 14px; color: #333; box-shadow: 0 0 5px rgba(0,0,0,0.1); display: none;"></div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const notiStatus = document.getElementById('noti-status');
    notiStatus.style.display = "block";

    if (!("Notification" in window)) {
        notiStatus.textContent = "❌ Este navegador no admite notificaciones.";
        notiStatus.style.background = "#ffcdd2";
        return;
    }

    if (Notification.permission === "granted") {
        notiStatus.textContent = "✅ Notificaciones activadas.";
        notiStatus.style.background = "#c8e6c9";
    } else if (Notification.permission === "denied") {
        notiStatus.textContent = "❌ Notificaciones denegadas. Actívalas desde la configuración del navegador.";
        notiStatus.style.background = "#ffcdd2";
    } else {
        // Solicitar permiso
        Notification.requestPermission().then(permission => {
        if (permission === "granted") {
            notiStatus.textContent = "✅ Notificaciones activadas.";
            notiStatus.style.background = "#c8e6c9";
        } else {
            notiStatus.textContent = "❌ No se activaron las notificaciones.";
            notiStatus.style.background = "#ffcdd2";
        }
        });
    }

    // Ocultar mensaje después de 8 segundos
    setTimeout(() => {
        notiStatus.style.display = "none";
    }, 8000);
    });
</script>



</body>
</html>

<?php $conn->close(); ?>

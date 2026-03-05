<?php
session_start();
// Incluir archivo de conexión (mysqli)
require __DIR__ . "/../../../conexion.php"; // tu archivo de conexión
//require __DIR__ . "/../../../conexion.php"; // tu archivo de conexión

// Validar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$rol = $_SESSION['rol'];

// Obtener la sala actual desde GET o POST
$sala = isset($_GET['sala']) ? $_GET['sala'] : 'General';

// Editar mensaje propio
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_id'], $_POST['mensaje_editado'])) {
    $mensaje_editado = $conn_chat->real_escape_string($_POST['mensaje_editado']);
    $editar_id = intval($_POST['editar_id']);

    $stmt = $conn_chat->prepare("SELECT nombre FROM mensajes WHERE id = ?");
    $stmt->bind_param("i", $editar_id);
    $stmt->execute();
    $stmt->bind_result($autor);
    $stmt->fetch();
    $stmt->close();

    if ($autor === $_SESSION['username']) {
        $stmt = $conn_chat->prepare("UPDATE mensajes SET mensaje = CONCAT(?, ' (editado)') WHERE id = ?");
        $stmt->bind_param("si", $mensaje_editado, $editar_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: chat.php?sala=" . urlencode($sala));
    exit();
}

// Insertar nuevo mensaje
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['mensaje']) && empty($_POST['editar_id'])) {
    $mensaje = $conn_chat->real_escape_string($_POST['mensaje']);
    $stmt = $conn_chat->prepare("INSERT INTO mensajes (sala, nombre, mensaje) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $sala, $username, $mensaje);
    $stmt->execute();
    $stmt->close();
}

// Obtener mensajes de la sala
$sql = "SELECT id, nombre, mensaje, fecha FROM mensajes WHERE sala = ? ORDER BY fecha ASC";
$stmt = $conn_chat->prepare($sql);
$stmt->bind_param("s", $sala);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Sala <?php echo htmlspecialchars($sala); ?></title>
    <link rel="stylesheet" href="chat.css">
</head>
<body>



<div class="container">
    <div class="chat-box">
        <header class="top-header">
            <button onclick="location.href='salas.php'" class="back-btn"></button>
            <h1>Sistema de Chat Bíblico</h1>
        </header>

        <h2>Sala: <?php echo htmlspecialchars($sala); ?></h2>

        <div class="mensaje-container">
            <div id="chat-scroll">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        $id = $row['id'];
                        $nombre = htmlspecialchars($row['nombre']);
                        $mensaje = htmlspecialchars($row['mensaje']);
                        $fecha = htmlspecialchars($row['fecha']);
                        $esPropio = $nombre === $_SESSION['username'];
                        $clase = $esPropio ? 'mensaje propio' : 'mensaje';
                    ?>
                    <div class="<?= $clase ?>">
                        <span class="nombre"><?php echo $esPropio ? 'Tú' : $nombre; ?>:</span>
                        <span><?php echo $mensaje; ?></span><br>
                        <span class="fecha"><?php echo $fecha; ?></span>
                        <?php if ($esPropio): ?>
                            <a href="#" class="editar-link" data-id="<?= $id ?>" data-mensaje="<?= $mensaje ?>">Editar</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>

            <form class="formulario-chat" method="POST">
                <textarea name="mensaje" rows="2" placeholder="Escribe tu mensaje..." required></textarea>
                <button type="submit">Enviar</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div id="modal-editar" class="modal">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" name="editar_id" id="editar_id">
            <label>Editar mensaje:</label>
            <textarea name="mensaje_editado" id="mensaje_editado" required></textarea>
            <button type="submit">Guardar cambios</button>
            <button type="button" onclick="cerrarModal()">Cancelar</button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.editar-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const mensaje = this.dataset.mensaje;
            document.getElementById('editar_id').value = id;
            document.getElementById('mensaje_editado').value = mensaje;
            document.getElementById('modal-editar').style.display = 'flex';
        });
    });

    function cerrarModal() {
        document.getElementById('modal-editar').style.display = 'none';
    }

    // Scroll al final
    window.addEventListener('load', () => {
        const chatScroll = document.getElementById('chat-scroll');
        chatScroll.scrollTop = chatScroll.scrollHeight;
    });
</script>
</body>
</html>


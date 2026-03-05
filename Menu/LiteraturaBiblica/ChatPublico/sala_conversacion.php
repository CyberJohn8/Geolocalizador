<?php
/* ============================================================
   AQUI SE CHATEA EN UNA DE LAS SALAS
============================================================ */






session_start();
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos (chat)// Incluir archivo de conexión (mysqli)
//require "https://directorioasambleasvzla.com/conexion.php"; // tu archivo de conexión
require __DIR__ . "/../../../conexion.php";
 // tu archivo de conexión

// Crear conexión con soporte UTF-8 total
$conn = new mysqli($host, $user, $pass, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

if (!$conn->set_charset("utf8mb4")) {
    echo "Error al establecer utf8mb4: " . $conn->error;
    exit();
}

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$rol = $_SESSION['rol'];

// Validar nombre de la sala
if (!isset($_GET['sala']) || !preg_match('/^[\p{L}0-9\s\-_]+$/u', $_GET['sala'])) {
    die("Sala inválida.");
}
$sala = $_GET['sala'];

// Editar mensaje
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['editar_id'], $_POST['mensaje_editado'])) {
    $mensaje_editado = trim($_POST['mensaje_editado']);
    $editar_id = intval($_POST['editar_id']);

    // Verificar autor
    $stmt = $conn->prepare("SELECT nombre FROM mensajes WHERE id = ?");
    $stmt->bind_param("i", $editar_id);
    $stmt->execute();
    $stmt->bind_result($autor);
    $stmt->fetch();
    $stmt->close();

    if ($autor === $username) {
        $mensaje_editado .= " (editado)";
        $stmt = $conn->prepare("UPDATE mensajes SET mensaje = ? WHERE id = ?");
        $stmt->bind_param("si", $mensaje_editado, $editar_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: sala_conversacion.php?sala=" . urlencode($sala));
    exit();
}

// Nuevo mensaje
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['mensaje']) && empty($_POST['editar_id'])) {
    $mensaje = trim($_POST['mensaje']);
    $stmt = $conn->prepare("INSERT INTO mensajes (sala, nombre, mensaje) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $sala, $username, $mensaje);
    $stmt->execute();
    $stmt->close();
}

// Obtener mensajes
$stmt = $conn->prepare("SELECT id, nombre, mensaje, fecha FROM mensajes WHERE sala = ? ORDER BY fecha ASC");
$stmt->bind_param("s", $sala);
$stmt->execute();
$result = $stmt->get_result();

// Recuperar datos si no están en $_SESSION pero sí hay user_id
if (!isset($_SESSION['username']) && isset($_SESSION['user_id'])) {
    $conn_users = new mysqli($host, $user, $pass, "bivvhbte_directorio");
    $conn_users->set_charset("utf8mb4");
    $stmt = $conn_users->prepare("SELECT username, rol FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($username, $rol);
    if ($stmt->fetch()) {
        $_SESSION['username'] = $username;
        $_SESSION['rol'] = $rol;
    }
    $stmt->close();
    $conn_users->close();
}

// SOLO PARA DEPURAR:
//echo "Usuario actual: " . htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <title>Chat - Sala <?php echo htmlspecialchars($sala, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="chat.css">


    <style>
        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&display=swap');

        @import url('https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap');

        /* === FUENTES === */
        @import url('https://fonts.googleapis.com/css2?family=Rakkas&family=Sansation&family=Oleo+Script&display=swap');

        /* Eliminar scroll visible del body y html */
        html, /* Oculta la barra de scroll vertical, pero permite hacer scroll */
        body {
            overflow-y: scroll; /* asegura que el scroll siga funcionando */
            scrollbar-width: none; /* para Firefox */
        }

        /* Para navegadores WebKit como Chrome, Edge, Safari */
        body::-webkit-scrollbar {
            width: 0px;
            background: transparent; /* opcional, por si quieres un fondo invisible */
        }


        .contenido {
            overflow-y: scroll;
            scrollbar-width: none;
        }

        .contenido::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }



        /* === FONDO CON CAPAS === */
        /* === FONDO CON CAPAS === */
        /* Asegura que todo ocupe el alto completo */
        html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Sansation', sans-serif;
        }

        /* Fondo de pantalla completo */
        body::before {
        content: "";
        position: fixed; /* para que no se repita al hacer scroll */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('https://directorioasambleasvzla.com/iconos/Fonfo_Mapa_Color.png');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: top center;
        opacity: 0.7;
        z-index: -1;
        }

        /* Asegura que se muestre scroll si el contenido es más largo */
        body {
        overflow-x: hidden;
        overflow-y: auto;
        position: relative;
        background-color: #fff; /* fallback si la imagen no carga */
        }






        /* === CONTENIDO PRINCIPAL === */
        .container {
        display: flex;
        flex-direction: column;
        align-items: center;  /* CENTRA horizontalmente */
        justify-content: center;
        width: 100%;
        padding: 20px;
        }

        .chat-box {
        width: 100%;
        max-width: 500px;
        background: rgba(255,255,255,0.95);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin: 0 auto;
        }

        /* === ENCABEZADO === */
        .top-header {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 30px;
        }

        .top-header h1 {
        font-family: 'Oleo Script', cursive;
        font-weight: normal;
        font-size: 38px;
        color: #2a3e42;
        margin: 0 auto;
        }

        .back-btn {
        background-image: url('https://directorioasambleasvzla.com/iconos/Retorno.png');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        background-color: transparent;
        border: none;
        width: 40px;
        height: 40px;
        cursor: pointer;
        transition: transform 0.3s ease;
        }

        .back-btn:hover {
        transform: scale(1.2);
        }

        /* === TÍTULO DE LA SALA === */
        .container h2 {
        font-family: 'Oleo Script', cursive;
        font-size: 24px;
        color: #2a3e42;
        margin-bottom: 20px;
        text-align: center;
        font-weight: normal;
        }

        /* === MENSAJES GENERALES (ya existentes) === */
        #chat-scroll {
        max-height: 250px;
        overflow-y: auto;
        padding: 15px;
        border: 1px solid #ccc;
        background: #f9f9f9;
        border-radius: 10px;
        font-size: 15px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        }

        /* Mensaje base */
        .mensaje {
        max-width: 70%;
        padding: 12px 15px;
        border-radius: 12px;
        background-color: #d0e1f9;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: relative;
        align-self: flex-start;
        word-break: break-word;
        }

        /* Mensaje propio */
        .mensaje.propio {
        background-color: #E6CDB7;
        align-self: flex-end;
        }

        /* Nombre del usuario */
        .mensaje .nombre {
        font-weight: bold;
        color: #2a3e42;
        display: block;
        margin-bottom: 4px;
        }

        /* Fecha en la parte inferior derecha */
        .mensaje .fecha {
        font-size: 0.8em;
        color: #666;
        text-align: right;
        display: block;
        margin-top: 8px;
        }

        /* Ajustes del formulario */
        .formulario-chat {
        display: flex;
        margin-top: 15px;
        gap: 10px;
        }

        .formulario-chat textarea {
        flex: 1;
        border-radius: 10px;
        padding: 10px;
        border: 1px solid #ccc;
        font-family: inherit;
        resize: none;
        min-height: 60px;
        }

        .formulario-chat button {
        background: #007bff;
        color: #EAE4D5;
        border: none;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
        }

        .formulario-chat button:hover {
        background: #0056b3;
        }


        /* === FORMULARIO DE MENSAJE === */
        .formulario-chat {
        display: flex;
        gap: 12px;
        align-items: center;
        }

        .formulario-chat textarea {
        flex: 1;
        resize: none;
        padding: 14px 18px;
        font-size: 16px;
        border: 2px solid #a2b0be;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.08);
        font-family: 'Sansation', sans-serif;
        outline: none;
        }

        .formulario-chat button {
        background-color: #637983;
        border: none;
        padding: 14px 18px;
        border-radius: 12px;
        color: #EAE4D5;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s ease;
        }

        .formulario-chat button:hover {
        background-color: #4b5f66;
        }

        /*MODAL PARA EDITAR MENSAJES*/
        .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
        }
        .modal-content {
        background: #EAE4D5;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        }
        .modal textarea {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        resize: vertical;
        }
        .modal button {
        margin-top: 10px;
        }
















        /* === RESPONSIVE === */
        @media screen and (max-width: 768px) {
        body {
            background-image: url('https://directorioasambleasvzla.com/iconos/Fondo_Mapa_Tlf.png'); /* tu imagen */
        } 

        .container {
            padding: 10px 0px;
            align-items: center;
        }

        .chat-box {
            width: 90%;
            max-width: 600px;
            padding: 0px;
            margin: 0 auto;
        }

        .top-header {
            margin-bottom: 0px;
        }

        .top-header h1 {
            font-size: 26px;
        }

        .container h2 {
            margin-bottom: 00px;
        }

        .top-header .back-btn {
            display: none !important;
        }

        .formulario-chat {
            flex-direction: column;
        }

        .formulario-chat textarea,
        .formulario-chat button {
            width: 100%;
        }

        .mensaje-container {
            width: 90%;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            margin: 0 auto;
        }
        
        }










        @media screen and (max-width: 300px) {
        .container {
            padding: 15px 10px;
            align-items: center;
        }

        .chat-box {
            width: 100%;
            padding: 15px;
            border-radius: 10px;
            background: rgba(255,255,255,0.95);
        }

        .top-header {
            flex-direction: column;
            gap: 8px;
        }

        .top-header h1 {
            font-size: 20px;
            text-align: center;
        }

        .mensaje-container {
            gap: 12px;
        }

        #chat-scroll {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            font-size: 14px;
        }

        .mensaje,
        .mensaje.propio {
            font-size: 14px;
            padding: 8px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .mensaje .nombre,
        .mensaje.propio .nombre {
            font-weight: bold;
            display: block;
        }

        .formulario-chat {
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }

        .formulario-chat textarea {
            width: 100%;
            font-size: 14px;
            padding: 8px;
            border-radius: 8px;
        }

        .formulario-chat button {
            width: 100%;
            padding: 10px;
            font-size: 14px;
        }

        .editar-link {
            font-size: 12px;
            display: inline-block;
            margin-top: 4px;
        }
        }

    </style>
</head>
<body>

<div class="container">
    <div class="chat-box">
        <header class="top-header">
            <button onclick="window.history.back()" class="back-btn" title="Volver"></button>
            <h1>Sistema de Chat Bíblico</h1>
        </header>

        <h2>Sala: <?php echo htmlspecialchars($sala, ENT_QUOTES, 'UTF-8'); ?></h2>

        <div class="mensaje-container">
            <div id="chat-scroll">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        $id = $row['id'];
                        $nombre_original = $row['nombre'];
                        $esPropio = $nombre_original === $username;

                        $nombre = htmlspecialchars($nombre_original, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $mensaje = htmlspecialchars($row['mensaje'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $fecha = htmlspecialchars($row['fecha']);

                        $clase = $esPropio ? 'mensaje propio' : 'mensaje';
                    ?>
                    <div class="<?= $clase ?>">
                        <span class="nombre"><?php echo $esPropio ? 'Tú' : $nombre; ?>:</span>
                        <span><?= nl2br($mensaje); ?></span>
                        <span class="fecha"><?= $fecha; ?></span>
                        <?php if ($esPropio): ?>
                            <a href="#" class="editar-link" data-id="<?= $id ?>" data-mensaje="<?= htmlspecialchars($row['mensaje'], ENT_QUOTES, 'UTF-8') ?>">🖉 Editar</a>
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
            document.getElementById('editar_id').value = this.dataset.id;
            document.getElementById('mensaje_editado').value = this.dataset.mensaje;
            document.getElementById('modal-editar').style.display = 'flex';
        });
    });

    function cerrarModal() {
        document.getElementById('modal-editar').style.display = 'none';
    }

    window.addEventListener('load', () => {
        const chatScroll = document.getElementById('chat-scroll');
        chatScroll.scrollTop = chatScroll.scrollHeight;
    });
</script>
</body>
</html>

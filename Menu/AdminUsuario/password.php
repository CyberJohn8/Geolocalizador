<?php
/* ============================================================
   CONFIGURACIÓN GENERAL 
   (Funciona tanto en Localhost como en Servidor en Línea)
============================================================ */

// Seguridad de sesión (compatible con localhost y hosting)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Activar errores en modo local
if (in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'])) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

/* ============================================================
   CARGAR CONEXIÓN (Localhost o Servidor)
============================================================ */

$isLocal = in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);

if ($isLocal) {
    require "/conexion.php";  // Conexión en XAMPP
} else {
    require __DIR__ . "/conexion.php"; // Conexión en hosting
}

// Validación general
if (!isset($conn)) {
    die("❌ Error: No se pudo cargar la conexión.");
}

/* ============================================================
   PROTECCIÓN CSRF
   ============================================================ */

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

function verify_csrf_token($token) {
    return isset($_SESSION["csrf_token"]) && hash_equals($_SESSION["csrf_token"], $token);
}

/* ============================================================
   PROTEGER ACCESO
   ============================================================ */

if (!isset($_SESSION["user_id"])) {
    header("Location: Iniciar_Sesion.php");
    exit();
}

/* ============================================================
   PROCESAR CAMBIO DE CONTRASEÑA
   ============================================================ */

$mensaje = "";
$mensaje_tipo = ""; // success | error

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validación CSRF
    if (!verify_csrf_token($_POST["csrf_token"] ?? "")) {
        $mensaje = "❌ Token inválido. Recargue la página.";
        $mensaje_tipo = "error";
    } else {

        $actual = trim($_POST["actual"] ?? "");
        $nueva = trim($_POST["nueva"] ?? "");
        $confirm = trim($_POST["confirm"] ?? "");

        if ($nueva !== $confirm) {
            $mensaje = "❌ Las contraseñas nuevas no coinciden.";
            $mensaje_tipo = "error";

        } else {

            $user_id = intval($_SESSION["user_id"]);

            // Obtener contraseña actual
            $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $usuario = $result->fetch_assoc();

            if (!$usuario) {
                $mensaje = "❌ Usuario no encontrado.";
                $mensaje_tipo = "error";

            } else {

                $hash_actual = $usuario["password"];

                // Validar contraseña actual
                if (!password_verify($actual, $hash_actual) && $actual !== $hash_actual) {
                    $mensaje = "❌ La contraseña actual es incorrecta.";
                    $mensaje_tipo = "error";
                } else {

                    // Guardar nueva contraseña
                    $nuevo_hash = password_hash($nueva, PASSWORD_BCRYPT);

                    $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $nuevo_hash, $user_id);

                    if ($stmt->execute()) {
                        $mensaje = "✅ Contraseña actualizada correctamente.";
                        $mensaje_tipo = "success";
                    } else {
                        $mensaje = "❌ Error al actualizar la contraseña.";
                        $mensaje_tipo = "error";
                    }
                }
            }
        }
    }
}
?>

<!-- ============================================================
     INTERFAZ HTML DEL FORMULARIO
============================================================ -->

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cambiar Contraseña</title>

<style>
    /* === FUENTES === */
    @import url('https://fonts.googleapis.com/css2?family=Sansation:wght@400;700&family=Rakkas&family=Oleo+Script&display=swap');

    /* === RESET GENERAL === */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* 💡 MEJORA: Asegurar que el tamaño del texto base sea adecuado para móviles */
    body {
        font-family: 'Sansation', sans-serif;
        min-height: 100vh;
        background-color: #EEEFF1;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        /* Reducimos el padding general del body para dejar más espacio al contenido en móviles */
        padding: 10px; 
        font-size: 16px; /* Base font size */
    }

    /* === FONDO CON IMAGEN DE MAPA === */
    body::after {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('https://directorioasambleasvzla.com/iconos/Fonfo_Mapa.jpg');
        background-size: cover;
        background-position: center;
        opacity: 0.6;
        z-index: -1;
    }

    /* === CONTENEDOR PRINCIPAL (MODIFICADO a .contenedor) === */
    .contenedor {
        background: #EEEFF1;
        /* Ajustamos el padding para el tamaño por defecto (escritorio/tablet) */
        padding: 30px 40px; 
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 420px;
        z-index: 10;
        /* 💡 MEJORA: Añadimos un pequeño margen para asegurar que no se pegue en los bordes */
        margin: 10px; 
    }

    /* === TITULOS === */
    h1, h2, h3 {
        font-size: 24px;
        margin-bottom: 20px;
        color: #2c3e50;
        text-align: center;
        font-weight: 700;
    }

    /* === FORMULARIO === */
    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-top: 10px;
        margin-bottom: 5px;
        font-weight: 700;
        color: #2c3e50;
        font-size: 15px;
    }

    input[type="text"],
    input[type="password"],
    input[type="email"] {
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #c0c0c0;
        margin-bottom: 15px;
        font-size: 16px;
        transition: border-color 0.3s;
        /* 💡 MEJORA: Aseguramos que el input ocupe todo el ancho disponible */
        width: 100%; 
    }

    input[type="text"]:focus,
    input[type="password"]:focus,
    input[type="email"]:focus {
        border-color: #2c3e50;
        outline: none;
        box-shadow: 0 0 0 2px rgba(44, 62, 80, 0.2);
    }

    /* === BOTONES === */
    button {
        margin-top: 20px;
        padding: 14px;
        background-color: #2c3e50;
        color: #E6CDB7;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 17px;
        font-weight: 700;
        transition: all 0.3s ease;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* 💡 MEJORA: Aseguramos que el botón ocupe todo el ancho disponible */
        width: 100%; 
    }

    button:hover {
        background-color: #1a252f;
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    /* ... (El resto de estilos de enlaces y mensajes se mantiene igual, ya están bien) ... */
    .volver {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #2c3e50;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.3s;
    }

    .volver:hover {
        color: #4a6c8b;
        text-decoration: underline;
    }

    .mensaje {
        margin-bottom: 20px;
        padding: 12px;
        border-radius: 8px;
        font-weight: bold;
        text-align: center;
        font-size: 15px;
    }

    .mensaje.exito {
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
    }

    .mensaje.error {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }


    /* === RESPONSIVE PARA MÓVILES (Optimizado) === */
    @media screen and (max-width: 480px) {
        /*
         * Mantener align-items: center; aquí asegura que, si la altura
         * de la pantalla es mayor que el formulario, este siga centrado
         * verticalmente. Si el contenido es más alto que la pantalla,
         * el navegador permitirá el scroll normalmente.
         */
        body {
            align-items: center; /* Cambiado de 'flex-start' a 'center' para centrar verticalmente */
            padding-top: 20px;
        }
        
        .contenedor {
            padding: 20px 15px; 
            max-width: 95%; 
        }

        h1, h2, h3 {
            font-size: 22px;
            margin-bottom: 15px; /* Reducimos el margen inferior de los títulos */
        }

        /* Ajustamos el tamaño del texto y padding de inputs/botones */
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            font-size: 15px;
            padding: 10px;
            margin-bottom: 10px; /* Reducimos el margen inferior */
        }

        button {
            font-size: 16px; /* Hacemos el texto del botón ligeramente más grande */
            padding: 12px; /* Reducimos el padding del botón */
            margin-top: 15px; /* Reducimos el margen superior del botón */
        }
        
        label {
            font-size: 14px; /* Hacemos las etiquetas ligeramente más pequeñas */
        }
    }
</style>

</head>
<body>

<div class="contenedor">
    <h2>Cambiar Contraseña</h2>

    <?php if ($mensaje !== ""): ?>
        <div class="mensaje <?= $mensaje_tipo ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Contraseña Actual:</label>
        <input type="password" name="actual" required>

        <label>Nueva Contraseña:</label>
        <input type="password" name="nueva" required>

        <label>Confirmar Nueva Contraseña:</label>
        <input type="password" name="confirm" required>

        <button type="submit">Actualizar Contraseña</button>

        
        <p><?= $mensaje ?></p>
        <a href="index.php" class="volver">← Volver a mi cuenta</a>
    </form>
</div>

</body>
</html>

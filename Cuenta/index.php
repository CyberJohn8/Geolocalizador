<?php
session_start();

// Redirigir si no está logueado o si es invitado
if (!isset($_SESSION["username"]) || $_SESSION["rol"] === "invitado") {
    header("Location: ../login.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "conexion.php";

    $usuario = $_SESSION["username"];
    $actual = $_POST["actual"];
    $nueva = $_POST["nueva"];
    $confirmar = $_POST["confirmar"];

    // Obtener la contraseña actual desde la base de datos
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($hash_actual);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($actual, $hash_actual)) {
        $mensaje = "❌ La contraseña actual no es correcta.";
    } elseif ($nueva !== $confirmar) {
        $mensaje = "⚠️ Las nuevas contraseñas no coinciden.";
    } else {
        $hash_nuevo = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hash_nuevo, $usuario);

        if ($stmt->execute()) {
            $mensaje = "✅ Contraseña actualizada correctamente.";
        } else {
            $mensaje = "❌ Error al actualizar la contraseña.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="cuenta.css">
</head>
<body>
    <div class="cuenta-container">
        <h1>Administrar Cuenta</h1>
        <p>Usuario: <strong><?= htmlspecialchars($_SESSION["username"]) ?></strong></p>

        <?php if ($mensaje): ?>
            <p class="mensaje"><?= $mensaje ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Contraseña actual:</label>
            <input type="password" name="actual" required>

            <label>Nueva contraseña:</label>
            <input type="password" name="nueva" required>

            <label>Confirmar nueva contraseña:</label>
            <input type="password" name="confirmar" required>

            <button type="submit">Cambiar contraseña</button>
        </form>

        <a href="../Menu/index.php" class="volver">← Volver al menú</a>
    </div>
</body>
</html>

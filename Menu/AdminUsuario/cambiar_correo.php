<?php
session_start();
require __DIR__ . "/conexion.php"; // tu archivo de conexión
//require __DIR__ . "/../../../conexion.php";
 // tu archivo de conexión

if (!isset($_SESSION["username"])) {
    header("Location: https://directorioasambleasvzla.com/Iniciar_Sesion.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nuevo_correo = trim($_POST["nuevo_correo"]);
    $usuario_actual = $_SESSION["username"];

    if (filter_var($nuevo_correo, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("UPDATE usuarios SET email = ? WHERE username = ?");
        $stmt->bind_param("ss", $nuevo_correo, $usuario_actual);

        if ($stmt->execute()) {
            $mensaje = "✅ Correo electrónico actualizado correctamente.";
        } else {
            $mensaje = "❌ Error al actualizar el correo.";
        }

        $stmt->close();
    } else {
        $mensaje = "⚠️ Correo electrónico no válido.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar correo electrónico</title>
    <link rel="stylesheet" href="cuenta.css">
</head>
<body>
<div class="cuenta-container">
    <h1>Cambiar correo electrónico</h1>

    <form method="post">
        <label>Nuevo correo electrónico:</label>
        <input type="email" name="nuevo_correo" required>
        <button type="submit">Guardar</button>
    </form>

    <p><?= $mensaje ?></p>
    <a href="index.php" class="volver">← Volver a mi cuenta</a>
</div>
</body>
</html>

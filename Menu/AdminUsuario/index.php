<?php
session_start();

// Verificación de sesión (descomentada y corregida)
if (!isset($_SESSION["username"]) || $_SESSION["rol"] === "invitado") {
    header("Location: https://directorioasambleasvzla.com/Iniciar_Sesion.php");
    exit();
}

// Conexión corregida
require __DIR__ . "/conexion.php"; // Ajusta la ruta según donde esté tu archivo

// Verificar que la conexión se estableció
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Obtener datos del usuario desde la base de datos
$usuario = $_SESSION["username"];
$stmt = $conn->prepare("SELECT username, email, rol FROM usuarios WHERE username = ?");

if (!$stmt) {
    die("Error en la preparación: " . $conn->error);
}

$stmt->bind_param("s", $usuario);

if (!$stmt->execute()) {
    die("Error en la ejecución: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Usuario no encontrado");
}

$datos = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Cuenta</title>
  <link rel="stylesheet" href="cuenta.css">
</head>
<body>
  <div class="cuenta-container">
    <h1>Mi Cuenta</h1>
    <p><strong>Usuario:</strong> <?= htmlspecialchars($datos["username"]) ?></p>
    <p><strong>Correo:</strong> <?= htmlspecialchars($datos["email"]) ?></p>
    <p><strong>Rol:</strong> <?= htmlspecialchars($datos["rol"]) ?></p>
  </div>

  <div class="cuenta-container">
    <h2>Administrar</h2>
    <ul>
      <li><a href="cambiar_nombre.php">Cambiar nombre de usuario</a></li>
      <li><a href="cambiar_correo.php">Cambiar correo electrónico</a></li>
      <li><a href="password.php">Cambiar contraseña</a></li>
    </ul>
    
    <a href="../index.php" class="volver">← Volver al menú principal</a>
  </div>
</body>
</html>
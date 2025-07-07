<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$message = "";

// Conexión
$conn = new mysqli("localhost", "root", "", "geolocalizador");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirmPassword"]);

    if ($password !== $confirmPassword) {
        $message = "❌ Las contraseñas no coinciden.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "❌ El correo ya está registrado.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $rol = "usuario"; // puedes cambiarlo a "admin" o lo que necesites

            $stmt = $conn->prepare("INSERT INTO usuarios (username, email, password, rol) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $rol);

            if ($stmt->execute()) {
                $message = "✅ Registro exitoso. Redirigiendo al inicio de sesión...";
                header("refresh:2; url=Iniciar_Sesion.php");
                exit();
            } else {
                $message = "❌ Error al registrar el usuario.";
            }
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="Formulario.css">
</head>
<body>
    <div class="wrapperRegistro">
        <div class="container_Registro">
            <h1>Registro</h1>
            <form method="POST" action="">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>

                <label for="confirmPassword">Confirmar Contraseña:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>

                <button type="submit" class="btn-register" style="background-color: #4D6164;">Registrarse</button>
            </form>

            <?php if (!empty($message)) : ?>
                <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <p>¿Ya tienes cuenta? <a href="Iniciar_Sesion.php">Inicia sesión aquí</a></p>
            <button onclick="location.href='index.html'" class="btn-back">Volver</button>
        </div>
    </div>
</body>

</html>

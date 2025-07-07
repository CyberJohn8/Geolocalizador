<?php
session_start();
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "geolocalizador");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicializar intentos fallidos si no existen
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

$message = "";

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, username, password, rol FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            $hash = $usuario["password"];

            // Validar contraseña
            if (password_verify($password, $hash) || $password === $hash) {
                // Login exitoso → reiniciar intentos
                $_SESSION['login_attempts'] = 0;

                $_SESSION["user_id"] = $usuario["id"];
                $_SESSION["username"] = $usuario["username"];
                $_SESSION["rol"] = $usuario["rol"];
                header("Location: Menu/index.php");
                exit();
            } else {
                $message = "⚠️ Contraseña incorrecta.";
                $_SESSION['login_attempts']++;
            }
        } else {
            $message = "⚠️ Correo no registrado.";
            $_SESSION['login_attempts']++;
        }

        $stmt->close();
    } else {
        $message = "⚠️ Por favor, complete todos los campos.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="Formulario.css">
    
</head>
<body>
    <div class="wrapper">
        <div class="container_Sesion">
            <h1>Iniciar Sesión</h1>
            <form method="POST" action="">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn-login">Ingresar</button>
            </form>

            <?php if (!empty($message)) : ?>
                <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <!-- Mostrar enlace solo después de 3 intentos fallidos -->
            <?php if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 3) : ?>
                <a href="recuperar_contrasena.php" class="forgot-password-link">¿Olvidaste tu contraseña?</a>
            <?php endif; ?>

            <p>¿No tienes cuenta? <a href="Registrarse.php">Regístrate aquí</a></p>
            <button onclick="location.href='Menu/index.php?guest=true'" class="btn-guest">Ingresar como Invitado</button>
            <button onclick="location.href='index.html'" class="btn-guest">Volver</button>
        </div>
    </div>
</body>
</html>

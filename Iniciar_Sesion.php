<?php 
// ✅ CONFIGURACIÓN DE SESIÓN ANTES de session_start()
ini_set('session.gc_maxlifetime', 1800);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();
ob_start();

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers para prevenir cache en PWA
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// ✅ Cargar archivo de conexión
require "conexion.php";

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt'] = time();
}

$message = "";

// ⚠️ VERIFICAR SI HA SUPERADO EL LÍMITE DE INTENTOS
if ($_SESSION['login_attempts'] >= 5) {
    $wait_time = 300; // 5 minutos
    if (time() - $_SESSION['last_attempt'] < $wait_time) {
        $message = "⚠️ Demasiados intentos fallidos. Espere 5 minutos.";
    } else {
        // Resetear intentos después del tiempo de espera
        $_SESSION['login_attempts'] = 0;
    }
}

// ✅ PROCESAR FORMULARIO CON VALIDACIÓN MEJORADA
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($message)) {

    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    // ✅ VALIDAR CAMPOS OBLIGATORIOS
    if (empty($email) || empty($password)) {
        $message = "⚠️ Por favor, complete todos los campos.";
    } else {
        // ✅ PREPARED STATEMENT MEJORADO
        $stmt = $conn->prepare("SELECT id, username, password, rol FROM usuarios WHERE email = ?");
        
        if ($stmt) {
            $stmt->bind_param("s", $email);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $usuario = $result->fetch_assoc();
                    $hash = $usuario["password"] ?? '';
                    
                    // ✅ VERIFICAR QUE TENEMOS HASH VÁLIDO
                    if (!empty($hash)) {
                        // ✅ VERIFICAR CONTRASEÑA (SOLO password_verify)
                        if (password_verify($password, $hash)) {
                            // ✅ LOGIN EXITOSO
                            $_SESSION['login_attempts'] = 0;
                            $_SESSION["user_id"] = $usuario["id"];
                            $_SESSION["username"] = $usuario["username"];
                            $_SESSION["rol"] = $usuario["rol"];
                            
                            // ✅ LIMPIAR BUFFER Y REDIRIGIR
                            ob_clean();
                            header("Location: Menu/index.php");
                            exit();
                        } else {
                            $message = "⚠️ Contraseña incorrecta.";
                            $_SESSION['login_attempts']++;
                            $_SESSION['last_attempt'] = time();
                        }
                    } else {
                        $message = "⚠️ Error en la cuenta de usuario. Contacte al administrador.";
                        $_SESSION['login_attempts']++;
                        $_SESSION['last_attempt'] = time();
                    }
                } else {
                    $message = "⚠️ Correo no registrado.";
                    $_SESSION['login_attempts']++;
                    $_SESSION['last_attempt'] = time();
                }
            } else {
                $message = "⚠️ Error temporal. Intente nuevamente.";
            }
            $stmt->close();
        } else {
            $message = "⚠️ Error del sistema. Contacte al administrador.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- META TAGS ESPECÍFICOS PARA PWA -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Directorio">
    <meta name="mobile-web-app-capable" content="yes">
    
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="Formulario.css">
    
    <!-- Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0066cc">
</head>
<body>
    <div class="wrapper">
        <div class="container_Sesion">
            <h1>Iniciar Sesión</h1>
            
            <!-- ✅ FORM MEJORADO PARA PWA -->
            <form method="POST" action="" id="loginForm">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn-login" id="submitBtn">Ingresar</button>
            </form>

            <?php if (!empty($message)) : ?>
                <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 1) : ?>
                <a href="/recuperar_contrasena.php" class="forgot-password-link">¿Olvidaste tu contraseña?</a>
            <?php endif; ?>

            <p>¿No tienes cuenta? <a href="/Registrarse.php">Regístrate aquí</a></p>
            
            <!-- ✅ BOTONES MEJORADOS PARA PWA -->
            <button type="button" onclick="navigateToPWA('/Menu/index.php?guest=true')" class="btn-guest">Ingresar como Invitado</button>
            <button type="button" onclick="navigateToPWA('/index.php')" class="btn-guest">Volver</button>
        </div>
    </div>

    <script>
        // ✅ MANEJO MEJORADO PARA PWA
        function navigateToPWA(url) {
            const btn = event.target;
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Cargando...';
            
            // Verificar si estamos en PWA
            const isInPWA = window.matchMedia('(display-mode: standalone)').matches || 
                           window.navigator.standalone === true;
            
            if (isInPWA) {
                // En PWA, usar replace para evitar problemas de historial
                window.location.replace(url);
            } else {
                window.location.href = url;
            }
            
            // Timeout de seguridad para restaurar botón
            setTimeout(() => {
                if (btn.disabled) {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            }, 5000);
        }

        // ✅ MANEJO DEL FORMULARIO EN PWA
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Iniciando sesión...';
            
            // Timeout de seguridad
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    alert('La solicitud está tardando. Verifica tu conexión.');
                }
            }, 15000);
        });

        // ✅ DETECCIÓN DE MODO PWA Y MANEJO DE OFFLINE
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('Ejecutando en modo PWA');
            
            // Verificar conexión en PWA
            if (!navigator.onLine) {
                alert('Estás offline. Algunas funciones pueden no estar disponibles.');
            }
        }
    </script>
</body>
</html>
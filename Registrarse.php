<?php
// ✅ CONFIGURACIÓN ANTES de session_start()
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers para PWA
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

session_start();

$message = "";
$message_type = ""; // 'success' o 'error'

// ✅ Conexión a la base de datos
require "conexion.php";

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ✅ Usar null coalescing para evitar warnings
    $username = trim($_POST["username"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $confirmPassword = trim($_POST["confirmPassword"] ?? '');

    // ✅ Validar campos vacíos
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = "❌ Todos los campos son obligatorios.";
        $message_type = "error";
    }
    // ✅ Validar formato de email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ El formato del correo es inválido.";
        $message_type = "error";
    }
    // ✅ Validar longitud mínima de contraseña
    elseif (strlen($password) < 6) {
        $message = "❌ La contraseña debe tener al menos 6 caracteres.";
        $message_type = "error";
    }
    // ✅ Verificar si las contraseñas coinciden
    elseif ($password !== $confirmPassword) {
        $message = "❌ Las contraseñas no coinciden.";
        $message_type = "error";
    } else {
        // ✅ Revisar si el correo ya existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        if (!$stmt) {
            $message = "❌ Error en la consulta.";
            $message_type = "error";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $message = "❌ El correo ya está registrado.";
                $message_type = "error";
            } else {
                // ✅ Registrar usuario nuevo
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $rol = "usuario";

                $insertStmt = $conn->prepare("INSERT INTO usuarios (username, email, password, rol) VALUES (?, ?, ?, ?)");
                
                if ($insertStmt) {
                    $insertStmt->bind_param("ssss", $username, $email, $hashedPassword, $rol);

                    if ($insertStmt->execute()) {
                        $message = "✅ Registro exitoso. Redirigiendo al inicio de sesión...";
                        $message_type = "success";
                        
                        // ✅ Limpiar buffer antes de redirección en PWA
                        ob_clean();
                        header("refresh:2; url=Iniciar_Sesion.php");
                        // Asegurar que se muestre el mensaje antes de redirigir
                        echo '<meta http-equiv="refresh" content="2;url=Iniciar_Sesion.php">';

                    } else {
                        $message = "❌ Error al registrar el usuario: " . $insertStmt->error;
                        $message_type = "error";
                    }
                    $insertStmt->close();
                } else {
                    $message = "❌ Error preparando la consulta.";
                    $message_type = "error";
                }
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <!-- ✅ Usar ruta relativa en lugar de absoluta -->
    <link rel="icon" type="image/x-icon" href="iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ✅ Meta tags específicos para PWA -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="Formulario.css">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0066cc">

    <style>
        /* ✅ Toast mejorado para PWA */
        #toast {
            visibility: hidden;
            min-width: 250px;
            max-width: 90%;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 16px 20px;
            position: fixed;
            z-index: 10000;
            left: 50%;
            bottom: 40px;
            font-size: 16px;
            opacity: 0;
            transform: translateX(-50%);
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        #toast.show {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        #toast.success {
            background-color: #4CAF50;
        }
        #toast.error {
            background-color: #f44336;
        }
        #toast.warning {
            background-color: #ff9800;
        }
        
        /* ✅ Mejoras para formulario en móvil */
        @media (max-width: 480px) {
            .container_Registro {
                padding: 20px !important;
                margin: 10px !important;
            }
            input[type="text"],
            input[type="email"],
            input[type="password"] {
                font-size: 16px !important; /* Evita zoom en iOS */
            }
        }
    </style>
</head>
<body>
    <div class="wrapperRegistro">
        <div class="container_Registro">
            <h1>Registro</h1>
            
            <!-- ✅ Form con ID para JavaScript -->
            <form method="POST" action="" id="registerForm" novalidate>
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" required 
                       minlength="3" maxlength="50"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">

                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required
                       minlength="6">

                <label for="confirmPassword">Confirmar Contraseña:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required
                       minlength="6">

                <button type="submit" class="btn-register" id="submitBtn" style="background-color: #4D6164;">
                    Registrarse
                </button>
            </form>

            <p>¿Ya tienes cuenta? <a href="Iniciar_Sesion.php">Inicia sesión aquí</a></p>
            
            <!-- ✅ Botón mejorado para PWA -->
            <button type="button" onclick="navigateToPWA('index.php')" class="btn-back">
                Volver
            </button>
        </div>
        
        <div id="toast"></div>
    </div>

    <script>
        // ✅ Función para navegación en PWA
        function navigateToPWA(url) {
            const btn = event?.target;
            if (btn) {
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.textContent = 'Cargando...';
                
                // Timeout de seguridad
                setTimeout(() => {
                    if (btn.disabled) {
                        btn.disabled = false;
                        btn.textContent = originalText;
                    }
                }, 3000);
            }
            
            // Verificar si estamos en PWA
            const isInPWA = window.matchMedia('(display-mode: standalone)').matches || 
                           window.navigator.standalone === true;
            
            if (isInPWA) {
                window.location.replace(url);
            } else {
                window.location.href = url;
            }
        }

        // ✅ Validación del formulario en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            
            if (form) {
                // Validar contraseñas coincidentes
                function validatePasswords() {
                    if (password.value && confirmPassword.value) {
                        if (password.value !== confirmPassword.value) {
                            confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                            showToast('Las contraseñas no coinciden', 'error', 3000);
                        } else {
                            confirmPassword.setCustomValidity('');
                        }
                    }
                }
                
                password.addEventListener('input', validatePasswords);
                confirmPassword.addEventListener('input', validatePasswords);
                
                // Manejar envío del formulario
                form.addEventListener('submit', function(e) {
                    const originalText = submitBtn.textContent;
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Registrando...';
                    
                    // Timeout de seguridad para PWA
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                            showToast('El registro está tardando. Verifica tu conexión.', 'warning', 4000);
                        }
                    }, 15000);
                });
            }
            
            // ✅ Mostrar mensaje del servidor si existe
            const message = <?php echo json_encode($message); ?>;
            const messageType = <?php echo json_encode($message_type); ?>;
            
            if (message && messageType) {
                showToast(message, messageType, messageType === 'success' ? 2000 : 4000);
                
                // Si es éxito, redirigir después del toast
                if (messageType === 'success') {
                    setTimeout(() => {
                        navigateToPWA('Iniciar_Sesion.php');
                    }, 2500);
                }
            }
        });

        // ✅ Función para mostrar toast
        function showToast(message, type = 'info', duration = 3000) {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.textContent = message;
                toast.className = '';
                toast.classList.add(type);
                toast.classList.add('show');
                
                setTimeout(() => {
                    toast.classList.remove('show');
                }, duration);
            }
        }
        
        // ✅ Detectar si estamos en PWA
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('PWA detectada en registro');
            document.body.classList.add('pwa-mode');
        }
    </script>
</body>
</html>

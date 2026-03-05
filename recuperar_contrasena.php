<?php
session_start();

// Conexión
require "conexion.php"; // Ajusta a tu ruta real
// require __DIR__ . "/../../../conexion.php";


if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$mensaje = "";

/* ----------------------------------------------------------
   1. GUARDAR CÓDIGO EN LA BASE DE DATOS (PETICIÓN AJAX)
-------------------------------------------------------------*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["guardar_codigo"])) {

    $email  = $_POST["email"] ?? "";
    $codigo = $_POST["codigo"] ?? "";
    $expira = time() + 600; // Expira en 10 minutos

    // Verificar si el correo existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $stmt->close();

        // Guardar código y expiración
        $stmt2 = $conn->prepare("UPDATE usuarios SET reset_token = ?, token_expira = ? WHERE email = ?");
        $stmt2->bind_param("sis", $codigo, $expira, $email);
        $stmt2->execute();
        $stmt2->close();

        // Respuesta para AJAX
        echo "OK";
        exit;

    } else {
        $mensaje = "❌ Correo no registrado.";
    }

    $stmt->close();
}


/* ----------------------------------------------------------
   2. VERIFICAR CÓDIGO DE RECUPERACIÓN
-------------------------------------------------------------*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["codigo_verificar"])) {

    $email  = $_POST["email_verificar"] ?? "";
    $codigo = $_POST["codigo_verificar"] ?? "";

    $stmt = $conn->prepare("SELECT id, username, rol, token_expira 
                                FROM usuarios 
                                WHERE email = ? AND reset_token = ?");
    $stmt->bind_param("ss", $email, $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($usuario = $resultado->fetch_assoc()) {

        if (time() > intval($usuario["token_expira"])) {
            $mensaje = "⚠️ El código ha expirado.";
        } else {
            // Crear sesión
            $_SESSION["user_id"] = $usuario["id"];
            $_SESSION["username"] = $usuario["username"];
            $_SESSION["rol"] = $usuario["rol"];

            // Borrar token después de usarlo
            $stmt2 = $conn->prepare("UPDATE usuarios SET reset_token = NULL, token_expira = NULL WHERE email = ?");
            $stmt2->bind_param("s", $email);
            $stmt2->execute();
            $stmt2->close();

            header("Location: Menu/index.php");
            exit();
        }

    } else {
        $mensaje = "❌ Código incorrecto o correo no registrado.";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
  <meta charset="UTF-8">
  <title>Recuperar Contraseña</title>
  <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="Olvidar.css">
  <script>
    emailjs.init("lrmsECNCw9_vt9osx"); // Tu ID de usuario EmailJS

    function generarYEnviarCodigo() {
      const email = document.getElementById("email").value;
      if (!email) return alert("Ingresa tu correo.");

      const codigo = Math.floor(100000 + Math.random() * 900000);
      const ahora = new Date();
      ahora.setMinutes(ahora.getMinutes() + 10);
      const expira = ahora.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

      emailjs.send("service_e8416xk", "template_z2dzuby", {
        user_email: email,
        codigo: codigo,
        expira: expira
      }).then(() => {
        alert("✅ Código enviado a tu correo.");

        document.getElementById("email_guardado").value = email;
        document.getElementById("codigo_guardado").value = codigo;
        document.getElementById("form-guardar-codigo").submit();

        document.getElementById("email_verificar").value = email;
        document.getElementById("verificacion").style.display = "block";
      }, function(error) {
        console.error("Error:", error);
        alert("❌ No se pudo enviar el código.");
      });
    }
  </script>
</head>
<body>
  <div class="wrapper">
    <div class="container">
      <h2>¿Olvidaste tu contraseña?</h2>
      <p>Ingresa tu correo y te enviaremos un código temporal válido por 10 minutos.</p>

      <!-- Formulario para enviar código -->
      <form id="form-envio" onsubmit="event.preventDefault(); generarYEnviarCodigo();">
        <input type="email" id="email" required placeholder="Tu correo electrónico">
        <button type="submit">Enviar código</button>
      </form>

      <!-- Formulario oculto para guardar el código en la base de datos -->
      <form id="form-guardar-codigo" method="POST" style="display:none;">
        <input type="hidden" name="guardar_codigo" value="1">
        <input type="hidden" name="email" id="email_guardado">
        <input type="hidden" name="codigo" id="codigo_guardado">
      </form>

      <!-- Formulario para verificar el código -->
      <div id="verificacion" style="margin-top:20px; display:<?= isset($_POST['guardar_codigo']) || isset($_POST['codigo_verificar']) ? 'block' : 'none' ?>;">
        <h3>Ingresa el código que recibiste</h3>
        <form method="POST">
          <input type="hidden" name="email_verificar" id="email_verificar" value="<?= htmlspecialchars($_POST['email'] ?? $_POST['email_verificar'] ?? '') ?>">
          <input type="text" name="codigo_verificar" required placeholder="Código de verificación">
          <button type="submit">Verificar código</button>
        </form>
        <?php if (!empty($mensaje)): ?>
          <p style="color:red;"><?= $mensaje ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>

<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/x-icon" href="https://directorioasambleasvzla.com/iconos/icon2-8.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegir Método de Búsqueda</title>
    <link rel="stylesheet" href="MenuLiteraturaBiblica.css"> <!-- Asegúrate de que la ruta es correcta -->
    
    

</head>
<body>

    <div class="container">
        <a href="/Menu/index.php" class="return-button"></a>
        <h2>Estudiar por...</h2>
        
        <div class="submenu">
            <a href="Bliblia/index.php">Biblia</a>
            <a href="Himnos/index.php">Himnario</a>
            <a href="IABiblica/index.php">Consultar IA</a>

            <?php if ($_SESSION["rol"] !== "invitado") : ?>
                <a href="ChatPublico/salas.php">Consultar Chat</a>
        <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elegir Método de Búsqueda</title>
    <link rel="stylesheet" href="MenuLiteraturaBiblica.css"> <!-- Asegúrate de que la ruta es correcta -->
    
    

</head>
<body>

    <div class="container">
        <a href="http://localhost/Geolocalizador/Menu/index.php" class="return-button"></a>
        <h2>Selecciona un método de búsqueda</h2>
        
        <div class="submenu">
            <a href="http://localhost/Geolocalizador/Menu/LiteraturaBiblica/Bliblia/index.php">Leer la Biblia</a>
            <a href="http://localhost/Geolocalizador/Menu/LiteraturaBiblica/IABiblica/index.php">Consultar IA</a>

            <?php if ($_SESSION["rol"] !== "invitado") : ?>
                <a href="http://localhost/Geolocalizador/Menu/LiteraturaBiblica/ChatPublico/salas.php">Consultar Chat</a>
        <?php endif; ?>
        </div>
    </div>

</body>
</html>
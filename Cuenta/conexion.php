<?php
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "geolocalizador";

$conn = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>

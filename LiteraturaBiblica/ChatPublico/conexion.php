<?php
// Conexión a la base de datos de usuarios
$conn_usuarios = new mysqli("localhost", "root", "", "geolocalizador");
if ($conn_usuarios->connect_error) {
    die("Conexión fallida (usuarios): " . $conn_usuarios->connect_error);
}

// Conexión a la base de datos del chat
$conn_chat = new mysqli("localhost", "root", "", "chat_biblico");
if ($conn_chat->connect_error) {
    die("Conexión fallida (chat): " . $conn_chat->connect_error);
}
?>

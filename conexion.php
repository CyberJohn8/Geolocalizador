<?php
// CONEXIÓN MEJORADA CON MANEJO DE ERRORES
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$isLocal = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

if ($isLocal) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $database = "directorio";
} else {
    $host = "10.43.27.85";  
    $user = "bivvhbte_dirasam";
    $pass = "MWgk9nZD6H0RIl";
    $database = "bivvhbte_directorio";
}

// CONEXIÓN CON MANEJO DE ERRORES
$conn = @new mysqli($host, $user, $pass, $database);

if ($conn->connect_error) {
    // Reintentar conexión una vez
    sleep(1);
    $conn = @new mysqli($host, $user, $pass, $database);
    
    if ($conn->connect_error) {
        // Error amigable sin detalles técnicos
        die("Error temporal del servicio. Por favor, intente nuevamente en unos momentos.");
    }
}

$conn->set_charset("utf8mb4");
?>
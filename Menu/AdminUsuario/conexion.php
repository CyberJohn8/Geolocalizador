<?php
/*
 * Archivo de Conexión Único
 * --------------------------
 * Detecta automáticamente si estás en localhost o en el servidor.
 * Retorna una conexión mysqli y el nombre de la base de datos.
 */

// Mostrar errores para depuración (opcional)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Detectar entorno
$isLocal = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

if ($isLocal) {

    // --- CONFIGURACIÓN LOCAL ---
    $host = "localhost";
    $user = "root";
    $pass = "";
    $database = "directorio";

} else {

    // --- CONFIGURACIÓN EN INFINITYFREE ---
    // ⚠️ REEMPLAZAR CON EL HOSTNAME REAL DE TU CUENTA
    $host = "10.43.27.85";  
    $user = "bivvhbte_dirasam";
    $pass = "MWgk9nZD6H0RIl";
    $database = "bivvhbte_directorio";
}

// Crear conexión MySQL
$conn = new mysqli($host, $user, $pass, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Forzar UTF8
$conn->set_charset("utf8mb4");

?>

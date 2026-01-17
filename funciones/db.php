<?php
$host = "localhost";
$user = "root";
$password = "daniela290104";
$database = "venta_smart";
date_default_timezone_set('America/Mexico_City');


// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>

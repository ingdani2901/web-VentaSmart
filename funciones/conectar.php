<?php
@session_start();
date_default_timezone_set('America/Mexico_City');
$usuario = "root";
$contrasena = "daniela290104";
$consulta= new PDO('mysql:host=localhost;dbname=venta_smart', $usuario, $contrasena);

//$usuario = "root";
//$contrasena = "daniela290104";
//$consulta= new PDO('mysql:host=localhost;dbname=venta_smart', $usuario, $contrasena);
?>
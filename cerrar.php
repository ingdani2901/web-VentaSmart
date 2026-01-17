<?php
// Iniciar la sesión y destruirla
session_start();
session_destroy();

// Redirigir al login o a la página principal después de cerrar sesión
header("Location: login.php");
exit;
?>

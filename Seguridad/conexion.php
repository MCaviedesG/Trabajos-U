<?php
// Asegurar que la sesión esté iniciada de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$host = "localhost";
// CAPA 1: Principio de Mínimo Privilegio usando un usuario restringido
$user = "negocioseguro_app";
$pass = "ClaveSegura123!";
$db   = "negocioseguro";

$conn = new mysqli($host, $user, $pass, $db);

// Control defensivo de errores de conexión sin exponer detalles técnicos
if ($conn->connect_error) {
    die("Error crítico de conexión a la base de datos.");
}
?>
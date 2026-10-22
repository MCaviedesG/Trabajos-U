<?php
session_start();
$conn = new mysqli("localhost", "root", "", "negocioseguro");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
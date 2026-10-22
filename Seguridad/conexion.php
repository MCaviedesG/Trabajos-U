<?php
// MEJORA DE SEGURIDAD: Iniciar la sesión de forma segura configurando flags HTTPOnly y SameSite para prevenir ataques de Session Hijacking y XSS.
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// MEJORA DE SEGURIDAD: Ocultar errores detallados de PHP en entorno de producción para no revelar información sensible del servidor o base de datos a un atacante.
error_reporting(0);
ini_set('display_errors', 0);

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'negocioseguro';

try {
    // MEJORA DE SEGURIDAD: Uso de PDO (PHP Data Objects) con charset utf8mb4 para evitar inyecciones SQL basadas en codificación de caracteres.
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO_ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO_FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false // MEJORA DE SEGURIDAD: Desactivar emulación de preparaciones para forzar consultas preparadas reales en el motor MySQL.
    ]);
} catch (PDOException $e) {
    // MEJORA DE SEGURIDAD: Capturar el error y mostrar un mensaje genérico al usuario sin revelar estructuras internas ni credenciales.
    die("Error interno de conexión a la base de datos.");
}
?>
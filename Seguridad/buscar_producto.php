<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($busqueda !== '') {
    try {
        // Conexión mediante PDO configurada con manejo de excepciones estricto
        $pdo = new PDO("mysql:host=localhost;dbname=negocioseguro;charset=utf8mb4", "negocioseguro_app", "ClaveSegura123!");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Uso de consultas preparadas en PDO con parámetros nombrados
        $stmt = $pdo->prepare("SELECT nombre, precio FROM productos WHERE nombre LIKE :busqueda");
        $stmt->bindValue(':busqueda', '%' . $busqueda . '%', PDO::PARAM_STR);
        $stmt->execute();

        // Renderizado seguro previniendo XSS con htmlspecialchars
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo htmlspecialchars($fila['nombre']) . " - $" . htmlspecialchars($fila['precio']) . "<br>";
        }
    } catch (PDOException $e) {
        // Bloque defensivo: Nunca se expone el mensaje técnico de la excepción
        echo "Ocurrió un error al procesar la búsqueda. Intente nuevamente.";
    }
}
?>
<form method="GET">
  Buscar producto: <input type="text" name="q">
  <button type="submit">Buscar</button>
</form>
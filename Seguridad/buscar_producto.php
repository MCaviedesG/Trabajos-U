<?php
include 'conexion.php';
$busqueda = isset($_GET['q']) ? $_GET['q'] : '';
if ($busqueda !== '') {
    $sql = "SELECT * FROM productos WHERE nombre LIKE '%$busqueda%'";
    $resultado = $conn->query($sql);
    while ($fila = $resultado->fetch_assoc()) {
        echo $fila['nombre'] . " - $" . $fila['precio'] . "<br>";
    }
}
?>
<form method="GET">
  Buscar producto: <input type="text" name="q">
  <button type="submit">Buscar</button>
</form>
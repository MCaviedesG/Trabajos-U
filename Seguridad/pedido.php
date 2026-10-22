<?php
include 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'];
    $precio      = $_POST['precio'];
    $cantidad    = $_POST['cantidad'];
    $usuario_id  = $_SESSION['usuario_id'] ?? 0;
    $total = $precio * $cantidad;

    $sql = "INSERT INTO pedidos (usuario_id, producto_id, cantidad, total) VALUES ('$usuario_id','$producto_id','$cantidad','$total')";
    $conn->query($sql);
    echo "Pedido registrado. Total a pagar: $" . $total;
}
?>
<form method="POST">
  <input type="hidden" name="producto_id" value="1">
  <input type="hidden" name="precio" value="3000">
  Cantidad: <input type="number" name="cantidad" value="1">
  <button type="submit">Confirmar pedido</button>
</form>
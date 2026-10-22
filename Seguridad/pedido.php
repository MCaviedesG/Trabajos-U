<?php
include 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'] ?? '';
    $cantidad    = $_POST['cantidad'] ?? '';
    $usuario_id  = $_SESSION['usuario_id'] ?? 0;

    // Validación de autenticación previa
    if ($usuario_id == 0) {
        die("Error: Debe iniciar sesión para realizar un pedido.");
    }

    // Validación estricta de tipos de datos (deben ser enteros positivos)
    if (!filter_var($producto_id, FILTER_VALIDATE_INT) || !filter_var($cantidad, FILTER_VALIDATE_INT) || $cantidad <= 0) {
        die("Error: Los datos del pedido son inválidos.");
    }

    // Prevención de alteración de precios: El precio NUNCA se toma del POST, se consulta en la BD
    $stmt_precio = $conn->prepare("SELECT precio FROM productos WHERE id = ?");
    $stmt_precio->bind_param("i", $producto_id);
    $stmt_precio->execute();
    $res_precio = $stmt_precio->get_result();

    if ($res_precio->num_rows === 0) {
        die("Error: El producto seleccionado no existe.");
    }

    $producto = $res_precio->fetch_assoc();
    $precio_real = $producto['precio'];
    $total = $precio_real * $cantidad;
    $stmt_precio->close();

    // Registro seguro del pedido utilizando una consulta preparada con parámetros tipados
    $stmt_pedido = $conn->prepare("INSERT INTO pedidos (usuario_id, producto_id, cantidad, total) VALUES (?, ?, ?, ?)");
    $stmt_pedido->bind_param("iiid", $usuario_id, $producto_id, $cantidad, $total);

    if ($stmt_pedido->execute()) {
        echo "Pedido registrado exitosamente. Total calculado: $" . number_format($total, 2);
    } else {
        echo "Error al registrar el pedido.";
    }
    $stmt_pedido->close();
}
?>
<form method="POST">
  <input type="hidden" name="producto_id" value="1">
  Cantidad: <input type="number" name="cantidad" value="1">
  <button type="submit">Confirmar pedido</button>
</form>
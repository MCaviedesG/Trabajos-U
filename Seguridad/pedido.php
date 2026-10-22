<?php
require_once 'conexion.php';

// MEJORA DE SEGURIDAD: Validar que el usuario haya iniciado sesión antes de procesar o mostrar la creación del pedido.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // MEJORA DE SEGURIDAD: Castear tipos de datos a enteros (int) para asegurar que se reciban identificadores y cantidades numéricas válidas.
    $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
    $usuario_id = $_SESSION['usuario_id'];

    if ($producto_id && $cantidad && $cantidad > 0) {
        // MEJORA DE SEGURIDAD CRÍTICA (INTEGRIDAD): NUNCA confiar en un campo oculto <input type="hidden" name="precio"> recibido del frontend (evita alteración con F12)[cite: 1.5]. 
        // El precio se consulta directamente desde la base de datos en el backend.
        $stmtProd = $conn->prepare("SELECT precio, stock FROM productos WHERE id = :id LIMIT 1");
        $stmtProd->execute([':id' => $producto_id]);
        $producto = $stmtProd->fetch();

        if ($producto) {
            $precioReal = $producto['precio'];
            $total = $precioReal * $cantidad;

            // MEJORA DE SEGURIDAD: Inserción del pedido usando Prepared Statement para prevenir Inyección SQL.
            $stmtPedido = $conn->prepare("INSERT INTO pedidos (usuario_id, producto_id, cantidad, total) VALUES (:usuario_id, :producto_id, :cantidad, :total)");
            $ok = $stmtPedido->execute([
                ':usuario_id'  => $usuario_id,
                ':producto_id' => $producto_id,
                ':cantidad'    => $cantidad,
                ':total'       => $total
            ]);

            if ($ok) {
                $mensaje = "Pedido registrado correctamente. Total a pagar: $" . number_format($total, 2);
            } else {
                $mensaje = "Error al guardar el pedido.";
            }
        } else {
            $mensaje = "El producto seleccionado no existe.";
        }
    } else {
        $mensaje = "Cantidad o producto no válido.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Realizar Pedido - NegocioSeguro</title>
</head>
<body>
    <h2>Confirmar Pedido</h2>
    <?php if ($mensaje): ?>
        <!-- MEJORA DE SEGURIDAD: Prevenir vulnerabilidades XSS en los mensajes del sistema -->
        <p><strong><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></strong></p>
    <?php endif; ?>

    <form method="POST" action="pedido.php">
        <!-- MEJORA DE SEGURIDAD: Solo enviar la ID del producto. El precio NO se envía en el formulario para evitar manipulación -->
        <input type="hidden" name="producto_id" value="<?php echo (int)($_GET['producto_id'] ?? 1); ?>">
        
        <label>Cantidad:</label>
        <input type="number" name="cantidad" value="1" min="1" required><br><br>
        
        <button type="submit">Confirmar pedido</button>
    </form>
</body>
</html>
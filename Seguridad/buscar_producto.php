<?php
require_once 'conexion.php';

// MEJORA DE SEGURIDAD: Control de acceso / Control de sesión. Verificar que el usuario esté autenticado antes de permitir el acceso.
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$busqueda = trim($_GET['q'] ?? '');
$productos = [];

if ($busqueda !== '') {
    // MEJORA DE SEGURIDAD: Uso de Prepared Statements para búsquedas dinámicas con LIKE, evitando inyección SQL por parámetros GET.
    $stmt = $conn->prepare("SELECT id, nombre, precio, stock FROM productos WHERE nombre LIKE :busqueda");
    $stmt->execute([':busqueda' => "%$busqueda%"]);
    $productos = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Producto - NegocioSeguro</title>
</head>
<body>
    <h2>Búsqueda de Productos</h2>
    <form method="GET" action="buscar_producto.php">
        <!-- MEJORA DE SEGURIDAD: Mantener el valor buscado en el input de forma segura escapándolo con htmlspecialchars() contra Reflected XSS -->
        <input type="text" name="q" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Buscar producto...">
        <button type="submit">Buscar</button>
    </form>

    <hr>

    <?php if ($busqueda !== ''): ?>
        <h3>Resultados para: "<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>"</h3>
        <?php if (!empty($productos)): ?>
            <ul>
                <?php foreach ($productos as $p): ?>
                    <li>
                        <!-- MEJORA DE SEGURIDAD: Escapar la salida de todos los campos extraídos de la BD antes de imprimirlos en el HTML -->
                        <strong><?php echo htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong> - 
                        Price: $<?php echo number_format($p['precio'], 2); ?>
                        <a href="pedido.php?producto_id=<?php echo (int)$p['id']; ?>">Realizar Pedido</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No se encontraron productos.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
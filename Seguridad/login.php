<?php
// MEJORA DE SEGURIDAD: Incluir archivo de conexión seguro
require_once 'conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // MEJORA DE SEGURIDAD: Limpiar espacios innecesarios y validar que los campos no estén vacíos antes de procesar.
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if (!empty($usuario) && !empty($clave)) {
        // MEJORA DE SEGURIDAD: Consulta preparada (Prepared Statement) para prevenir Inyección SQL. Elimina el bypass con ' OR '1'='1.
        $stmt = $conn->prepare("SELECT id, usuario, clave, rol FROM usuarios WHERE usuario = :usuario LIMIT 1");
        $stmt->execute([':usuario' => $usuario]);
        $user = $stmt->fetch();

        // MEJORA DE SEGURIDAD: Verificación segura de contraseñas con hash (password_verify) en lugar de comparar texto plano.
        if ($user && password_verify($clave, $user['clave'])) {
            // MEJORA DE SEGURIDAD: Regenerar la ID de la sesión tras el login exitoso para evitar ataques de Fijación de Sesión (Session Fixation).
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['rol'];

            header("Location: buscar_producto.php");
            exit();
        } else {
            // MEJORA DE SEGURIDAD: Mensaje genérico de error que no revela si falló el usuario o la clave (evita enumeración de usuarios).
            $mensaje = "Usuario o contraseña incorrectos.";
        }
    } else {
        $mensaje = "Por favor, complete todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - NegocioSeguro</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if ($mensaje): ?>
        <!-- MEJORA DE SEGURIDAD: Uso de htmlspecialchars() para evitar XSS al imprimir variables en el HTML -->
        <p style="color:red;"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Usuario:</label>
        <input type="text" name="usuario" required><br><br>
        <label>Clave:</label>
        <input type="password" name="clave" required><br><br>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
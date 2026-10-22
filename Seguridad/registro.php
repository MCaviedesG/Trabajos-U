<?php
require_once 'conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // MEJORA DE SEGURIDAD: Saneamiento inicial de entradas de texto.
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if (!empty($usuario) && !empty($clave)) {
        // MEJORA DE SEGURIDAD: Validar complejidad mínima de la contraseña (ej. al menos 8 caracteres).
        if (strlen($clave) < 8) {
            $mensaje = "La contraseña debe tener al menos 8 caracteres.";
        } else {
            // MEJORA DE SEGURIDAD: Consultar de forma segura mediante Prepared Statements si el usuario ya existe.
            $stmtCheck = $conn->prepare("SELECT id FROM usuarios WHERE usuario = :usuario");
            $stmtCheck->execute([':usuario' => $usuario]);

            if ($stmtCheck->fetch()) {
                $mensaje = "El nombre de usuario ya está registrado.";
            } else {
                // MEJORA DE SEGURIDAD: Encriptación segura de la clave con algoritmo BCRYPT/ARGON2 via password_hash(). Nunca almacenar texto plano.
                $claveHash = password_hash($clave, PASSWORD_DEFAULT);

                // MEJORA DE SEGURIDAD: Insertar mediante Prepared Statements evitando inyección SQL en el registro.
                $stmtInsert = $conn->prepare("INSERT INTO usuarios (usuario, clave) VALUES (:usuario, :clave)");
                $ok = $stmtInsert->execute([
                    ':usuario' => $usuario,
                    ':clave' => $claveHash
                ]);

                if ($ok) {
                    $mensaje = "Registro exitoso. Ahora puedes iniciar sesión.";
                } else {
                    $mensaje = "Error al registrar el usuario.";
                }
            }
        }
    } else {
        $mensaje = "Por favor completa todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - NegocioSeguro</title>
</head>
<body>
    <h2>Registro de Usuario</h2>
    <?php if ($mensaje): ?>
        <!-- MEJORA DE SEGURIDAD: Escapar la salida de datos para evitar ejecución de Scripts (XSS) -->
        <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST" action="registro.php">
        <label>Usuario:</label>
        <input type="text" name="usuario" required><br><br>
        <label>Clave (mínimo 8 caracteres):</label>
        <input type="password" name="clave" required><br><br>
        <button type="submit">Registrarse</button>
    </form>
</body>
</html>
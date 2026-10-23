<?php
include 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitización básica eliminando espacios accidentales
    $usuario = trim($_POST['usuario'] ?? '');
    $clave   = $_POST['clave'] ?? '';

    // PASO 1 y 2: Consultas preparadas con MySQLi y marcador de posición para prevenir Inyección SQL
    $stmt = $conn->prepare("SELECT id, usuario, clave FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        
        // Verificación segura de la contraseña utilizando el hash almacenado
        if (password_verify($clave, $fila['clave'])) {
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['usuario'] = $fila['usuario'];
            
            // Prevención de ataques XSS al renderizar información con htmlspecialchars
            echo "Bienvenido, " . htmlspecialchars($fila['usuario']) . ". Acceso concedido.";
        } else {
            echo "Usuario o clave incorrectos.";
        }
    } else {
        echo "Usuario o clave incorrectos.";
    }
    $stmt->close();
}
?>
<form method="POST">
  Usuario: <input type="text" name="usuario"><br>
  Clave: <input type="password" name="clave"><br>
  <button type="submit">Ingresar</button>
</form>
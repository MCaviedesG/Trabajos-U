<?php
include 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave   = $_POST['clave'] ?? '';

    // Validación mediante Lista Blanca: Solo permite letras, números y guion bajo (3 a 20 caracteres)
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $usuario)) {
        die("Nombre de usuario inválido. Use solo letras, números y guion bajo (3 a 20 caracteres).");
    }

    // Validación de la política de longitud mínima de la contraseña
    if (strlen($clave) < 8) {
        die("La contraseña debe tener al menos 8 caracteres.");
    }

    // Cifrado seguro de la contraseña mediante hash
    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

    // Inserción segura utilizando consultas preparadas
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, clave) VALUES (?, ?)");
    $stmt->bind_param("ss", $usuario, $clave_hash);
    
    if ($stmt->execute()) {
        echo "Usuario registrado con éxito.";
    } else {
        echo "Error al registrar el usuario.";
    }
    $stmt->close();
}
?>
<form method="POST">
  Nuevo usuario: <input type="text" name="usuario"><br>
  Clave: <input type="password" name="clave"><br>
  <button type="submit">Registrarme</button>
</form>
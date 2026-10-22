<?php
include 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave   = $_POST['clave'];
    $sql = "INSERT INTO usuarios (usuario, clave) VALUES ('$usuario', '$clave')";
    $conn->query($sql);
    echo "Usuario registrado con éxito.";
}
?>
<form method="POST">
  Nuevo usuario: <input type="text" name="usuario"><br>
  Clave: <input type="password" name="clave"><br>
  <button type="submit">Registrarme</button>
</form>
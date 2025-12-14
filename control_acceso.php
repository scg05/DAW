<?php
session_start();
require "conexion.php";

$usuario = trim($_POST['usuario']);
$contrasena = trim($_POST['password']);

if ($usuario == '' || $contrasena == '') {
    header("Location: index.php?error=campos_vacios");
    exit;
}

$permitido = false;
$idUsuario = null;

$sql = "SELECT IdUsuario, Clave FROM Usuarios WHERE NomUsuario = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conn->error);
}

$stmt->bind_param("s", $usuario);
$stmt->execute();

$stmt->bind_result($dbId, $dbClave);

if ($stmt->fetch()) {

    // Compara la contraseña en texto plano ($contrasena) con el hash ($dbClave)
    if (password_verify($contrasena, $dbClave)) {
        $permitido = true;
        $idUsuario = $dbId;
    }
}

$stmt->close();
$conn->close();

if ($permitido) {
    $_SESSION['usuario'] = $usuario;
    $_SESSION['id_usuario'] = $idUsuario;

    if (isset($_POST['recordarme']) && $_POST['recordarme'] == 'si') {
        $hash_para_cookie = password_hash($contrasena, PASSWORD_DEFAULT);
        setcookie('recordar_usuario', $usuario, time() + (90 * 24 * 60 * 60));
        setcookie('recordar_password', $hash_para_cookie, time() + (90 * 24 * 60 * 60));
        setcookie('ultima_visita', date("c"), time() + (90 * 24 * 60 * 60));
    }

    header("Location: menuusu.php");
    exit;
} else {
    header("Location: index.php?error=acceso_denegado");
    exit;
}
?>
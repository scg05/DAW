<?php
require 'header.php';
require 'conexion.php';
if (!isset($_SESSION['usuario'])) { echo "<p class='error'>Debes iniciar sesión.</p>"; require 'footer.php'; exit; }

if (file_exists(__DIR__ . '/filtro_usuario.php')) {
    require __DIR__ . '/filtro_usuario.php';
} else {
    function validarUsuario($u){ return preg_match('/^[A-Za-z][A-Za-z0-9]{2,14}$/',$u); }
    function validarPass($p){ return preg_match('/^[A-Za-z0-9_-]{6,15}$/',$p)&&preg_match('/[A-Z]/',$p)&&preg_match('/[a-z]/',$p)&&preg_match('/[0-9]/',$p); }
    function validarEmail($e){ return filter_var($e,FILTER_VALIDATE_EMAIL); }
    function validarFecha($f){ $o=DateTime::createFromFormat('Y-m-d',$f); if(!$o||$o->format('Y-m-d')!==$f)return false; $h=new DateTime(); if($o>$h)return false; $o18=(clone $o)->add(new DateInterval('P18Y')); return $o18<=$h; }
}

$usuarioActual = $_SESSION['usuario'];
$sql = "SELECT * FROM Usuarios WHERE NomUsuario=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$usuarioActual);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$nUsuario = isset($_POST['username']) ? trim($_POST['username']) : $user['NomUsuario'];
$email = isset($_POST['email']) ? trim($_POST['email']) : $user['Email'];
$sexo = $_POST['sexo'] ?? $user['Sexo'];
$fecha = $_POST['fecha_nacimiento'] ?? $user['FNacimiento'];
$ciudad = isset($_POST['ciudad']) ? trim($_POST['ciudad']) : $user['Ciudad'];
$pais = $_POST['pais'] ?? $user['Pais'];
$passActual = $_POST['password'] ?? '';

//Aceptar varios nombres de campos para compatibilidad con formularios
$nuevaPass = $_POST['nueva_password'] ?? $_POST['confirm_password'] ?? '';
$repite = $_POST['repite_nueva_password'] ?? $_POST['confirm_password2'] ?? '';
$foto = '';

if(isset($_FILES['foto']) && $_FILES['foto']['error']==0){
    $foto = basename($_FILES['foto']['name']);
}

$errores = [];

if ($passActual === '') {
    $errores[] = "Debes introducir tu contraseña actual.";
} else {
    $stored = $user['Clave'];
    $passOk = false;
    // Detectar si la contraseña almacenada parece ser un hash
    if (is_string($stored) && preg_match('/^\$2y\$|^\$2a\$|^\$argon2/i', $stored)) {
        if (password_verify($passActual, $stored)) $passOk = true;
    } else {
        // valor en claro
        if ($passActual === $stored) $passOk = true;
    }
    if (!$passOk) $errores[] = "La contraseña actual no es correcta.";
}

if ($nUsuario !== $user['NomUsuario'] && !validarUsuario($nUsuario)) {
    $errores[] = "El nombre de usuario no es válido.";
}

if ($email !== $user['Email'] && !validarEmail($email)) {
    $errores[] = "El email no es válido.";
}

if ($fecha !== $user['FNacimiento'] && !validarFecha($fecha)) {
    $errores[] = "La fecha de nacimiento no es válida o no tienes 18 años.";
}

if ($nuevaPass !== '' || $repite !== '') {
    if (!validarPass($nuevaPass)) $errores[]="La nueva contraseña no es válida.";
    if ($nuevaPass !== $repite) $errores[]="Las nuevas contraseñas no coinciden.";
}

$pageStyles=["css/enviarmensaje.css"];
require 'header.php';

if (!empty($errores)) {
    echo "<main class='container'><h1>Errores al actualizar</h1><ul>";
    foreach($errores as $e) echo "<li>".htmlspecialchars($e)."</li>";
    echo "</ul><a href='mis_datos.php'>Volver</a></main>";
    require 'footer.php';
    exit;
}

$stored = $user['Clave'];
if ($nuevaPass !== '') {
    if (is_string($stored) && preg_match('/^\$2y\$|^\$2a\$|^\$argon2/i', $stored)) {
        $nuevaClaveGuardar = password_hash($nuevaPass, PASSWORD_DEFAULT);
    } else {
        $nuevaClaveGuardar = $nuevaPass; // saber si así estaba antes
    }
} else {
    $nuevaClaveGuardar = $user['Clave'];
}

$sexoInt = ($sexo==='M'?1:($sexo==='F'?2:3));
$paisInt = ($pais===''? NULL : (int)$pais);

$sqlU = "UPDATE Usuarios SET NomUsuario=?, Email=?, Sexo=?, FNacimiento=?, Ciudad=?, Pais=?, Foto=?, Clave=? WHERE IdUsuario=?";
$stmtU = $conn->prepare($sqlU);

$fotoGuardar = ($foto === '' ? $user['Foto'] : $foto);
$idUsuario = $user['IdUsuario'];

$paisParam = $paisInt;
$fnacParam = $fecha;
$ciudadParam = $ciudad;
$fotoParam = $fotoGuardar;
$claveParam = $nuevaClaveGuardar;
$usuarioParam = $nUsuario;
$emailParam = $email;
$sexoParam = $sexoInt;
$idParam = $idUsuario;

$stmtU->bind_param("ssississi",
    $usuarioParam, $emailParam, $sexoParam, $fnacParam, $ciudadParam, $paisParam,
    $fotoParam,
    $claveParam,
    $idParam
);
$stmtU->execute();

if (isset($_COOKIE['recuerdame'])) {
    if ($nUsuario !== $user['NomUsuario'] || $nuevaPass !== '') {
        // invalidar cookie
        setcookie('recuerdame','', time()-3600, '/');
    }
}

//Actualizar nombre en sesión
$_SESSION['usuario'] = $nUsuario;

echo "<main class='container'><h1>Datos actualizados correctamente</h1>
<p>Los cambios se han guardado.</p>
<a href='mis_datos.php'>Volver</a></main>";

require 'footer.php';
?>

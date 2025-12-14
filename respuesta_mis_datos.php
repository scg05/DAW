<?php
require 'header.php';
require 'conexion.php';

// Directorio de fotos de perfil
$DIR_FOTOS_PERFIL = "uploads/perfil/";
$ICONO_DEFECTO = "img/icon-user-placeholder.png";

if (file_exists(__DIR__ . '/filtro_usuario.php')) {
    require __DIR__ . '/filtro_usuario.php';
} else {
    function validarUsuario($u){ return preg_match('/^[A-Za-z][A-Za-z0-9]{2,14}$/',$u); }
    function validarPass($p){ return preg_match('/^[A-Za-z0-9_-]{6,15}$/',$p)&&preg_match('/[A-Z]/',$p)&&preg_match('/[a-z]/',$p)&&preg_match('/[0-9]/',$p); }
    function validarEmail($e){ return filter_var($e,FILTER_VALIDATE_EMAIL); }
    function validarFecha($f){ $o=DateTime::createFromFormat('Y-m-d',$f); if(!$o||$o->format('Y-m-d')!==$f)return false; $h=new DateTime(); if($o>$h)return false; $o18=(clone $o)->add(new DateInterval('P18Y')); return $o18<=$h; }
}

// 1. Comprobar sesión y obtener datos actuales
if (!isset($_SESSION['usuario'])) { header("Location: index.php?error=acceso_denegado"); exit; }

$usuarioActual = $_SESSION['usuario'];
$sql = "SELECT * FROM Usuarios WHERE NomUsuario=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$usuarioActual);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 2. Recoger datos del formulario
$nUsuario = isset($_POST['username']) ? trim($_POST['username']) : $user['NomUsuario'];
$email = isset($_POST['email']) ? trim($_POST['email']) : $user['Email'];
$sexo = $_POST['sexo'] ?? $user['Sexo'];
$fecha = $_POST['fecha_nacimiento'] ?? $user['FNacimiento'];
$ciudad = isset($_POST['ciudad']) ? trim($_POST['ciudad']) : $user['Ciudad'];
$pais = $_POST['pais'] ?? $user['Pais'];
$passActual = $_POST['password_actual'] ?? ''; // Campo de seguridad
$eliminarFoto = isset($_POST['eliminar_foto']) && $_POST['eliminar_foto'] === 'si';

//Aceptar varios nombres de campos para compatibilidad con formularios
$nuevaPass = $_POST['nueva_password'] ?? '';
$repite = $_POST['confirmar_nueva_password'] ?? '';

// Variables para la foto de perfil:
$fotoGuardar = $user['Foto']; // Valor por defecto: el que ya estaba en BD
$fotoSubidaExitosa = false;
$errores = [];

//comprobacion de seguridad-> contraseña actual
if ($passActual === '') {
    $errores[] = "Debes introducir tu contraseña actual.";
} else {
    $stored = $user['Clave'];
    $passOk = false;

    // Si la clave almacenada es un hash-> password_verify.
    if (is_string($stored) && (preg_match('/^\$2y\$|^\$2a\$|^\$argon2/i', $stored) || strlen($stored) > 20)) {
        if (password_verify($passActual, $stored)) {
            $passOk = true;
        }
    } else {
        // Fallback: Si no parece un hash, se compara directamente (solo para usuarios legacy/texto plano)
        if ($passActual === $stored) {
            $passOk = true;
        }
    }
    if (!$passOk) $errores[] = "La contraseña actual no es correcta.";
}

//validacion resto de campos si cambiaron
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

//manejo de ficheros SOLO si no hay errores previos
if (empty($errores)) {
    // A) Opción 1: Eliminar foto actual
    if ($eliminarFoto && !empty($user['Foto'])) {
        $rutaFisica = $DIR_FOTOS_PERFIL . $user['Foto'];
        if (file_exists($rutaFisica) && @unlink($rutaFisica)) {
            $fotoGuardar = ''; // Borrado exitoso, limpiamos el campo en BD
        } else {
            // Si no se puede borrar, no lo consideramos un error crítico que bloquee el UPDATE de datos.
            error_log("Error al borrar foto: " . $rutaFisica);
            $fotoGuardar = ''; // Limpiamos el campo de la BD de todas formas
        }
    }
    
    // B) Opción 2: Subir nueva foto (Sobrescribe la opción de eliminación y la foto anterior)
    if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] === UPLOAD_ERR_OK) {
        // 1. Borrar la foto anterior (si existe)
        if (!empty($user['Foto']) && file_exists($DIR_FOTOS_PERFIL . $user['Foto'])) {
             @unlink($DIR_FOTOS_PERFIL . $user['Foto']);
        }
        
        // 2. Generar nombre único y mover
        $extension = pathinfo($_FILES['nueva_foto']['name'], PATHINFO_EXTENSION);
        $nombreNuevoFichero = time() . '_' . uniqid() . '.' . $extension;
        $destinoFinal = $DIR_FOTOS_PERFIL . $nombreNuevoFichero;

        if (move_uploaded_file($_FILES['nueva_foto']['tmp_name'], $destinoFinal)) {
            $fotoGuardar = $nombreNuevoFichero; // Nombre único para guardar en BD
            $fotoSubidaExitosa = true;
        } else {
            $errores[] = "Error al mover el archivo de la nueva foto (revisar permisos del servidor).";
            $fotoGuardar = $user['Foto']; // Mantenemos el nombre antiguo si la subida falla
        }
    }
}

//preparacion y ejecucion UPDATE
$pageStyles=["css/enviarmensaje.css"];


if (!empty($errores)) {
    echo "<main class='container'><h1>Errores al actualizar</h1><ul>";
    foreach($errores as $e) echo "<li>".htmlspecialchars($e)."</li>";
    echo "</ul><a href='mis_datos.php'>Volver</a></main>";
    require 'footer.php';
    exit;
}

if ($nuevaPass !== '') {
    $nuevaClaveGuardar = password_hash($nuevaPass, PASSWORD_DEFAULT);
} else {
    $nuevaClaveGuardar = $user['Clave'];
}

$sexoInt = ($sexo==='M'?1:($sexo==='F'?2:3));
$paisInt = ($pais===''? NULL : (int)$pais);

$sqlU = "UPDATE Usuarios SET NomUsuario=?, Email=?, Sexo=?, FNacimiento=?, Ciudad=?, Pais=?, Foto=?, Clave=? WHERE IdUsuario=?";
$stmtU = $conn->prepare($sqlU);

$usuarioParam = $nUsuario;
$emailParam = $email;
$sexoParam = $sexoInt;
$fnacParam = $fecha;
$ciudadParam = $ciudad;
$paisParam = $paisInt;
$fotoParam = $fotoGuardar;
$claveParam = $nuevaClaveGuardar;
$idParam = $user['IdUsuario'];

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

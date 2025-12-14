<?php
require 'header.php';
require 'conexion.php';

// Directorios de archivos (CLAVE P11)
$DIR_FOTOS_PERFIL = "uploads/perfil/";
$DIR_FOTOS_ANUNCIOS = "uploads/anuncios/";

// 1. Verificar método POST y datos mínimos
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['confirmar'], $_POST['clave'])) {
    header("Location: menuusu.php?error=acceso_denegado");
    exit;
}

// 2. Obtener datos de sesión y POST
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== $_POST['nombre_usuario']) {
    header("Location: index.php?error=acceso_denegado");
    exit;
}

$idUsuario = intval($_POST['id_usuario']);
$nombreUsuario = $_POST['nombre_usuario'];
$passwordConfirmacion = $_POST['clave'];

// 3. Recuperar datos críticos (Clave y Foto) desde BD
$stmt = $conn->prepare("SELECT Clave, Foto FROM Usuarios WHERE IdUsuario = ? AND NomUsuario = ?");
$stmt->bind_param("is", $idUsuario, $nombreUsuario);
$stmt->execute();
$result = $stmt->get_result();
$userDB = $result->fetch_assoc();
$stmt->close();

if (!$userDB) {
    echo "<main class='container'><p class='error'>Error: Usuario no encontrado en la base de datos.</p></main>";
    require 'footer.php';
    exit;
}

$claveActualDB = $userDB['Clave'];
$fotoPerfilDB = $userDB['Foto'];

// 4. Verificación de Seguridad: Contraseña actual
if (!password_verify($passwordConfirmacion, $claveActualDB)) {
    echo "<main class='container'><p class='error'>Contraseña de confirmación incorrecta. Vuelve a <a href='darme_de_baja.php'>la página de baja</a>.</p></main>";
    require 'footer.php';
    exit;
}

//EJECUCION BORRADO ATOMICO
$conn->begin_transaction();

try {
    
    // PASO 1: RECOPILAR Y ELIMINAR ARCHIVOS FÍSICOS (P11)
    $archivosABorrar = [];
    
    // A) Foto de perfil
    if (!empty($fotoPerfilDB)) {
        $archivosABorrar[] = $DIR_FOTOS_PERFIL . $fotoPerfilDB;
    }

    // B) Fotos de Anuncios (FPrincipal y Fotos adicionales)
    $sqlArchivosAnuncios = "
        SELECT A.FPrincipal AS FotoNombre FROM Anuncios A WHERE A.Usuario = ?
        UNION ALL
        SELECT F.Foto AS FotoNombre FROM Fotos F JOIN Anuncios A ON F.Anuncio = A.IdAnuncio WHERE A.Usuario = ?
    ";
    $stmtArchivos = $conn->prepare($sqlArchivosAnuncios);
    $stmtArchivos->bind_param("ii", $idUsuario, $idUsuario);
    $stmtArchivos->execute();
    $resultArchivos = $stmtArchivos->get_result();
    
    while ($row = $resultArchivos->fetch_assoc()) {
        if (!empty($row['FotoNombre']) && $row['FotoNombre'] !== 'img/sin_foto.jpg') {
            $archivosABorrar[] = $DIR_FOTOS_ANUNCIOS . $row['FotoNombre'];
        }
    }
    $stmtArchivos->close();

    // Eliminar archivos físicos del servidor
    foreach ($archivosABorrar as $ruta) {
        if (file_exists($ruta)) {
            @unlink($ruta); // @unlink para evitar que el script falle
        }
    }

    // PASO 2: ELIMINAR REGISTROS DE LA BASE DE DATOS (En orden de dependencia)
    
    // 1. Mensajes y Solicitudes
    $conn->query("DELETE FROM Mensajes WHERE UsuOrigen = $idUsuario OR UsuDestino = $idUsuario");
    $conn->query("DELETE S FROM Solicitudes S JOIN Anuncios A ON S.Anuncio = A.IdAnuncio WHERE A.Usuario = $idUsuario");
    
    // 2. Fotos (asociadas a los anuncios del usuario)
    $conn->query("DELETE F FROM Fotos F JOIN Anuncios A ON F.Anuncio = A.IdAnuncio WHERE A.Usuario = $idUsuario");
    
    // 3. Anuncios del usuario
    $conn->query("DELETE FROM Anuncios WHERE Usuario = $idUsuario");

    // 4. Usuario final
    $conn->query("DELETE FROM Usuarios WHERE IdUsuario = $idUsuario");

    $conn->commit();
    
    // PASO 3: DESTRUIR SESIÓN Y COOKIES
    if (isset($_SESSION['usuario'])) {
        session_unset();
        session_destroy();
        setcookie('recordar_usuario','', time()-3600, "/");
        setcookie('recordar_password','', time()-3600, "/");
    }

    // PASO 4: RESULTADO DE ÉXITO
    echo "<main class='container'><h1>Cuenta eliminada</h1>
        <p>Tu cuenta, todos tus datos **y todos tus archivos** han sido eliminados correctamente.</p>
        <a href='index.php'>Volver a la página principal</a></main>";

} catch (Exception $e) {
    $conn->rollback();
    echo "<main class='container'><p class='error'>Error crítico al intentar eliminar la cuenta. Error de BD: " . htmlspecialchars($e->getMessage()) . "</p></main>";
}

$conn->close();
require 'footer.php';
?>
<?php
require 'header.php';
require 'conexion.php';

// Comprobar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=acceso_denegado");
    exit;
}

$nombreUsuario = $_SESSION['usuario'];

// Obtener IdUsuario
$stmt = $conn->prepare("SELECT IdUsuario FROM Usuarios WHERE NomUsuario = ?");
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$stmt->bind_result($idUsuario);
$stmt->fetch();
$stmt->close();

if (!$idUsuario) {
    echo "<p class='error'>Usuario no encontrado.</p>";
    require 'footer.php';
    exit;
}

// ID de la foto (GET o POST)
$idFoto = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idFoto = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $confirmar = isset($_POST['confirmar']) ? $_POST['confirmar'] : '';
} else {
    $idFoto = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $confirmar = '';
}

if ($idFoto <= 0) {
    echo "<p class='error'>Foto no válida.</p>";
    require 'footer.php';
    exit;
}

// Comprobar que la foto pertenece a un anuncio del usuario
$sql = "SELECT f.IdFoto, f.Titulo, f.Foto, f.Anuncio, a.Titulo AS TituloAnuncio
        FROM Fotos f
        INNER JOIN Anuncios a ON f.Anuncio = a.IdAnuncio
        WHERE f.IdFoto = ? AND a.Usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $idFoto, $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$foto = $result->fetch_assoc();
$result->free();
$stmt->close();

if (!$foto) {
    echo "<p class='error'>No se ha encontrado la foto o no tienes permiso para eliminarla.</p>";
    require 'footer.php';
    exit;
}

// Si se envió el formulario de confirmación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($confirmar === 'no') {
        // Usuario canceló
        header("Location: ver_anuncio.php?id=" . $foto['Anuncio']);
        exit;
    }

    if ($confirmar === 'si') {
        // Borrar la foto de la base de datos
        $sqlDel = "DELETE FROM Fotos WHERE IdFoto = ?";

        $stmtDel = $conn->prepare($sqlDel);
        $stmtDel->bind_param("i", $idFoto);
        $stmtDel->execute();
        $stmtDel->close();

        ?>
        <main class="container">
            <h1>Foto eliminada</h1>
            <p>La foto <strong><?= htmlspecialchars($foto['Titulo'], ENT_QUOTES, 'UTF-8'); ?></strong> ha sido eliminada de la base de datos.</p>
            <p><a href="ver_anuncio.php?id=<?= $foto['Anuncio']; ?>">Volver al anuncio</a></p>
        </main>
        <?php

        $conn->close();
        require 'footer.php';
        exit;
    }
}

// Si llegamos aquí por GET → mostrar página de confirmación
?>

<main class="container">
    <h1>Confirmar eliminación de foto</h1>

    <p>Estás a punto de eliminar la foto:</p>
    <p><strong><?= htmlspecialchars($foto['Titulo'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
    <p>Del anuncio: <strong><?= htmlspecialchars($foto['TituloAnuncio'], ENT_QUOTES, 'UTF-8'); ?></strong></p>

    <?php if (!empty($foto['Foto'])): ?>
        <p>
            <img src="<?= htmlspecialchars($foto['Foto'], ENT_QUOTES, 'UTF-8'); ?>" 
                 alt="<?= htmlspecialchars($foto['Titulo'], ENT_QUOTES, 'UTF-8'); ?>" 
                 width="200">
        </p>
    <?php endif; ?>

    <p>¿Seguro que quieres eliminarla de forma permanente?</p>

    <form action="respuesta_eliminar_foto.php" method="post">
        <input type="hidden" name="id" value="<?= $idFoto; ?>">
        <button type="submit" name="confirmar" value="si">Sí, eliminar</button>
        <button type="submit" name="confirmar" value="no">No, cancelar</button>
    </form>

    <p><a href="ver_anuncio.php?id=<?= $foto['Anuncio']; ?>">Volver al anuncio sin eliminar</a></p>
</main>

<?php
$conn->close();
require 'footer.php';
?>

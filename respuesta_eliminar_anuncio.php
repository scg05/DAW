<?php
require 'header.php';
require 'conexion.php';

// Comprobar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=acceso_denegado");
    exit;
}

// Obtener nombre de usuario desde sesión
$nombreUsuario = $_SESSION['usuario'];

// Recuperar IdUsuario desde la base de datos
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

// ID del anuncio (puede venir por GET en la primera carga o por POST al confirmar)
$idAnuncio = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAnuncio = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $confirmar = isset($_POST['confirmar']) ? $_POST['confirmar'] : '';
} else {
    $idAnuncio = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $confirmar = '';
}

if ($idAnuncio <= 0) {
    echo "<p class='error'>Anuncio no válido.</p>";
    require 'footer.php';
    exit;
}

// Comprobar que el anuncio pertenece al usuario logueado
$sql = "SELECT IdAnuncio, Titulo FROM Anuncios WHERE IdAnuncio = ? AND Usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $idAnuncio, $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$anuncio = $result->fetch_assoc();
$stmt->close();

if (!$anuncio) {
    echo "<p class='error'>No se ha encontrado el anuncio o no tienes permiso para eliminarlo.</p>";
    require 'footer.php';
    exit;
}

// Si se ha enviado el formulario de confirmación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($confirmar === 'no') {
        // Usuario canceló
        header("Location: mis_anuncios.php");
        exit;
    }

    if ($confirmar === 'si') {
        // Borrado: anuncio + datos asociados (por ahora, fotos)
        // Si tienes más tablas relacionadas (mensajes, etc.), aquí las borras también

        // Empezar transacción por seguridad
        $conn->begin_transaction();

        try {
            // Borrar fotos asociadas al anuncio
            $sqlFotos = "DELETE FROM Fotos WHERE Anuncio = ?";
            $stmtF = $conn->prepare($sqlFotos);
            $stmtF->bind_param("i", $idAnuncio);
            $stmtF->execute();
            $stmtF->close();

            // Borrar el anuncio
            $sqlDelAnuncio = "DELETE FROM Anuncios WHERE IdAnuncio = ? AND Usuario = ?";
            $stmtA = $conn->prepare($sqlDelAnuncio);
            $stmtA->bind_param("ii", $idAnuncio, $idUsuario);
            $stmtA->execute();
            $stmtA->close();

            $conn->commit();
            ?>

            <main class="container">
                <h1>Anuncio eliminado</h1>
                <p>El anuncio <strong><?= htmlspecialchars($anuncio['Titulo'], ENT_QUOTES, 'UTF-8'); ?></strong> y todos sus datos asociados han sido eliminados.</p>
                <p><a href="mis_anuncios.php">Volver a mis anuncios</a></p>
            </main>

            <?php
        } catch (Exception $e) {
            $conn->rollback();
            ?>
            <main class="container">
                <h1>Error</h1>
                <p>Ha ocurrido un error al intentar eliminar el anuncio.</p>
                <p><a href="mis_anuncios.php">Volver a mis anuncios</a></p>
            </main>
            <?php
        }

        $conn->close();
        require 'footer.php';
        exit;
    }
}

// Si llegamos aquí con GET (o sin confirmar todavía) → mostrar página de confirmación
?>

<main class="container">
    <h1>Confirmar eliminación</h1>
    <p>¿Seguro que quieres eliminar el anuncio <strong><?= htmlspecialchars($anuncio['Titulo'], ENT_QUOTES, 'UTF-8'); ?></strong> y todos los datos asociados (fotos, etc.)?</p>

    <form action="respuesta_eliminar_anuncio.php" method="post">
        <input type="hidden" name="id" value="<?= $idAnuncio; ?>">
        <button type="submit" name="confirmar" value="si">Sí, eliminar</button>
        <button type="submit" name="confirmar" value="no">No, cancelar</button>
    </form>

    <p><a href="mis_anuncios.php">Volver sin eliminar</a></p>
</main>

<?php
$conn->close();
require 'footer.php';
?>

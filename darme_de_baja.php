<?php
$pageStyles = ["css/usuario.css"];
require 'header.php';
require 'conexion.php';

// 1. Comprobar sesión iniciada
if (!isset($_SESSION['usuario'])) {
    echo "<p class='error'>Debes iniciar sesión.</p>";
    require 'footer.php';
    exit;
}

$nomUsuario = $_SESSION['usuario'];

// Obtener datos del usuario
$sql = "SELECT IdUsuario, Clave, Foto FROM Usuarios WHERE NomUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nomUsuario);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();
$stmt->close();

if (!$usuario) {
    echo "<p class='error'>No se pudo obtener la información del usuario.</p>";
    require 'footer.php';
    exit;
}

$idUsuario = $usuario['IdUsuario'];
$claveReal = $usuario['Clave'];

// 2. Obtener resumen de información para mostrar
$sqlAnuncios = "
    SELECT A.IdAnuncio, A.Titulo, 
           (SELECT COUNT(F.IdFoto) FROM Fotos F WHERE F.Anuncio = A.IdAnuncio) AS NumFotos
    FROM Anuncios A
    WHERE A.Usuario = ?
";
$stmtA = $conn->prepare($sqlAnuncios);
$stmtA->bind_param("i", $idUsuario);
$stmtA->execute();
$resAnuncios = $stmtA->get_result();

$anuncios = [];
$totalFotos = 0;

while ($row = $resAnuncios->fetch_assoc()) {
    $anuncios[] = $row;
    $totalFotos += $row["NumFotos"];
}

$totalAnuncios = count($anuncios);

// Sumar 1 foto si tienen foto de perfil
if (!empty($fotoPerfilDB)) {
    $totalFotos += 1;
}

$conn->close(); // Cerrar la conexión después de obtener todos los datos de resumen
?>

<main class="container">
    <h1 class="titulo-faculty">Darse de baja</h1>

    <p>Estás a punto de eliminar tu cuenta. Esta acción es <strong>irreversible</strong>.</p>

    <h2>Resumen de tus datos</h2>

    <p><strong>Total de anuncios:</strong> <?= $totalAnuncios ?></p>
    <p><strong>Total de fotos:</strong> <?= $totalFotos ?></p>

    <h3>Listado de anuncios</h3>

    <?php if ($totalAnuncios === 0): ?>
        <p>No tienes anuncios publicados.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($anuncios as $a): ?>
                <li><strong><?= htmlspecialchars($a['Titulo']) ?></strong> — <?= $a['NumFotos'] ?> fotos</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr>

    <h2>Confirmación</h2>
    <p>Para confirmar la eliminación de tu cuenta, introduce tu contraseña actual:</p>

    <form action="respuesta_darme_de_baja.php" method="post">
        <label for="clave">Contraseña:</label>
        <input type="password" name="clave" id="clave" required>

        <input type="hidden" name="id_usuario" value="<?= $idUsuario ?>">
        <input type="hidden" name="nombre_usuario" value="<?= htmlspecialchars($nomUsuario) ?>">
        <input type="hidden" name="confirmar" value="1">
        
        <button type="submit" style="background:red;color:white;">Eliminar mi cuenta</button>
    </form>

    <br>
    <a href="menuusu.php">Cancelar y volver</a>
</main>

<?php require 'footer.php'; ?>

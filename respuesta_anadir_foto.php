<?php
require 'header.php';
require 'conexion.php';

$titulo_foto = isset($_POST['titulo_foto']) ? trim($_POST['titulo_foto']) : '';
$alt         = isset($_POST['alt']) ? trim($_POST['alt']) : '';
$idAnuncio   = isset($_POST['anuncio']) ? intval($_POST['anuncio']) : 0;

// Guardamos SOLO el nombre del fichero, como pide el enunciado
$nombreFichero = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $nombreFichero = basename($_FILES['foto']['name']);
}

// INSERT — columnas reales de tu tabla: Titulo, Foto, Alternativo, Anuncio
$sql = "INSERT INTO Fotos (Titulo, Foto, Alternativo, Anuncio)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    ?>
    <h1>Error</h1>
    <p>No se ha podido preparar la consulta para insertar la foto.</p>
    <p><a href="anadir_foto.php<?= $idAnuncio > 0 ? '?id='.$idAnuncio : '' ?>">Volver al formulario</a></p>
    <?php
    require 'footer.php';
    exit;
}

$stmt->bind_param(
    "sssi",
    $titulo_foto,
    $nombreFichero,   // aquí va la columna Foto
    $alt,
    $idAnuncio
);

if ($stmt->execute()) {
    ?>
    <h1>Foto añadida correctamente</h1>
    <p>La foto se ha asociado al anuncio seleccionado.</p>

    <p><a href="anadir_foto.php?id=<?= $idAnuncio; ?>">Añadir otra foto al mismo anuncio</a></p>
    <p><a href="mis_anuncios.php">Volver a mis anuncios</a></p>
    <?php
} else {
    ?>
    <h1>Error</h1>
    <p>Ha ocurrido un error al guardar la foto en la base de datos.</p>
    <p><a href="anadir_foto.php<?= $idAnuncio > 0 ? '?id='.$idAnuncio : '' ?>">Volver al formulario</a></p>
    <?php
}

$stmt->close();
$conn->close();

require 'footer.php';
?>

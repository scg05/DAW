<?php
require 'header.php';
require 'conexion.php';

$titulo_foto = isset($_POST['titulo_foto']) ? trim($_POST['titulo_foto']) : '';
$alt         = isset($_POST['alt']) ? trim($_POST['alt']) : '';
$idAnuncio   = isset($_POST['anuncio']) ? intval($_POST['anuncio']) : 0;

// ******************* INICIO DE LA LÓGICA DE VALIDACIÓN *******************
$errores = [];

// 1. Validar Id Anuncio
if ($idAnuncio <= 0) {
    $errores[] = "Anuncio no válido. Debes seleccionar un anuncio.";
}

// 2. Validar Título de la foto (Obligatorio)
if (empty($titulo_foto)) {
    $errores[] = "El título de la foto es obligatorio.";
}

// 3. Validar Texto Alternativo (Obligatorio + Reglas)
if (empty($alt)) {
    $errores[] = "El texto alternativo es obligatorio.";
} else {
    // a. Longitud mínima 10 caracteres
    if (strlen($alt) < 10) {
        $errores[] = "El texto alternativo debe tener una longitud mínima de 10 caracteres.";
    }

    // b. No puede empezar por texto redundante ('foto', 'imagen', 'foto de', 'imagen de')
    if (preg_match('/^(foto\s*de|imagen\s*de|foto|imagen)/i', $alt)) {
        $errores[] = "El texto alternativo no debe empezar por palabras redundantes como 'foto' o 'imagen'.";
    }
}

// Manejo de errores - Si hay errores, mostrar y salir
if (!empty($errores)) {
    ?>
    <main class="container">
        <h1>Error al añadir foto</h1>
        <p>No se pudo añadir la foto debido a los siguientes errores:</p>
        <ul>
            <?php foreach ($errores as $e): ?>
                <li style="color:red;"><?= htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
        <p><a href="anadir_foto.php<?= $idAnuncio > 0 ? '?id='.$idAnuncio : '' ?>">Volver al formulario</a></p>
    </main>
    <?php
    require 'footer.php';
    exit;
}
// ******************* FIN DE LA LÓGICA DE VALIDACIÓN *******************


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
    <main class="container">
    <h1>Error de Sistema</h1>
    <p>No se ha podido preparar la consulta para insertar la foto.</p>
    <p>Error de BD: <?= htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8'); ?></p> 
    <p><a href="anadir_foto.php<?= $idAnuncio > 0 ? '?id='.$idAnuncio : '' ?>">Volver al formulario</a></p>
    </main>
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
    <main class="container">
    <h1>Foto añadida correctamente</h1>
    <p>La foto se ha asociado al anuncio seleccionado.</p>

    <p><a href="anadir_foto.php?id=<?= $idAnuncio; ?>">Añadir otra foto al mismo anuncio</a></p>
    <p><a href="mis_anuncios.php">Volver a mis anuncios</a></p>
    </main>
    <?php
} else {
    ?>
    <main class="container">
    <h1>Error</h1>
    <p>Ha ocurrido un error al guardar la foto en la base de datos.</p>
    <p style="color:red;">Error de BD: <?= htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8'); ?></p> 
    <p><a href="anadir_foto.php<?= $idAnuncio > 0 ? '?id='.$idAnuncio : '' ?>">Volver al formulario</a></p>
    </main>
    <?php
}

$stmt->close();
$conn->close();

require 'footer.php';
?>
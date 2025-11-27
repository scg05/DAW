<?php
require 'header.php';
require 'conexion.php';

$usuario = 1; // Cambiar cuando tengas login REAL
$mensaje_error = "";

// Id de anuncio preseleccionado (si vienes desde respuesta_crear_anuncio.php)
$idSeleccionado = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cargar anuncios del usuario
$anuncios = [];
$sqlA = "SELECT IdAnuncio, Titulo FROM Anuncios WHERE Usuario = ? ORDER BY FRegistro DESC";
$stmtA = $conn->prepare($sqlA);
$stmtA->bind_param("i", $usuario);
$stmtA->execute();
$resA = $stmtA->get_result();
while ($fila = $resA->fetch_assoc()) {
    $anuncios[] = $fila;
}
$stmtA->close();
$conn->close();

// Mensaje por GET si vienes de respuesta_anadir_foto con error simple
if (isset($_GET['error']) && $_GET['error'] == 'faltan_datos') {
    $mensaje_error = "Debes completar los datos obligatorios de la foto.";
}
?>

<h1>Añadir foto a anuncio</h1>

<?php if ($mensaje_error != ""): ?>
    <p style="color:red;"><?= $mensaje_error ?></p>
<?php endif; ?>

<form action="respuesta_anadir_foto.php" method="post" enctype="multipart/form-data" novalidate>
    <label>Título de la foto:</label>
    <input type="text" name="titulo_foto"><br>

    <label>Texto alternativo (mínimo 10 caracteres):</label>
    <input type="text" name="alt"><br>

    <label>Anuncio:</label><br>

    <?php if ($idSeleccionado > 0): ?>
        <?php
        // Buscar el título del anuncio seleccionado para mostrarlo
        $tituloSel = "";
        foreach ($anuncios as $a) {
            if ($a['IdAnuncio'] == $idSeleccionado) {
                $tituloSel = $a['Titulo'];
                break;
            }
        }
        ?>
        <p><strong><?= htmlspecialchars($tituloSel, ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <input type="hidden" name="anuncio" value="<?= $idSeleccionado; ?>">
    <?php else: ?>
        <select name="anuncio">
            <option value="">-- Selecciona anuncio --</option>
            <?php foreach ($anuncios as $a): ?>
                <option value="<?= $a['IdAnuncio']; ?>">
                    <?= htmlspecialchars($a['Titulo'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
    <br>

    <label>Archivo de imagen:</label>
    <input type="file" name="foto"><br>
    <!-- Recuerda: según el enunciado, la imagen real se sube “a mano” al servidor.
         Aquí por ahora solo vamos a guardar el nombre/ruta en la base de datos. -->

    <input type="submit" value="Añadir foto">
</form>

<?php require 'footer.php'; ?>

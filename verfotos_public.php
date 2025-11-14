<?php
require 'header.php';
require 'conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { echo "<p class='error'>Anuncio no especificado.</p>"; require 'footer.php'; exit; }

$sql = "SELECT Titulo, FPrincipal, Ciudad, Pais, Precio FROM Anuncios WHERE IdAnuncio=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$anuncio = $stmt->get_result()->fetch_assoc();

$sql_fotos = "SELECT Foto FROM Fotos WHERE Anuncio=?";
$stmt_f = $conn->prepare($sql_fotos);
$stmt_f->bind_param("i", $id);
$stmt_f->execute();
$fotos = $stmt_f->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<main class="container">
    <h1><?= htmlspecialchars($anuncio['Titulo']) ?></h1>
    <p><strong>Total de fotos:</strong> <?= 1 + count($fotos) ?></p>
    <p><strong>Ciudad:</strong> <?= htmlspecialchars($anuncio['Ciudad']) ?> — 
       <strong>País:</strong> <?= htmlspecialchars($anuncio['Pais']) ?> — 
       <strong>Precio:</strong> <?= htmlspecialchars($anuncio['Precio']) ?> €</p>

    <img src="<?= htmlspecialchars($anuncio['FPrincipal']) ?>" width="400" alt="Foto principal">

    <?php if (!empty($fotos)): ?>
        <?php foreach ($fotos as $f): ?>
            <img src="<?= htmlspecialchars($f['Foto']) ?>" width="150" alt="Foto adicional">
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php
$stmt->close();
$stmt_f->close();
$conn->close();
require 'footer.php';
?>

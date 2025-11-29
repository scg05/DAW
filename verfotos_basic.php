<?php
// Este archivo contiene la lógica común para verfotos_public.php y verfotos_private.php

// 1. Obtener detalles del anuncio
$sql = "SELECT Titulo, FPrincipal, Ciudad, Pais, Precio FROM Anuncios WHERE IdAnuncio=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$anuncio = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$anuncio) {
    echo "<p class='error'>El anuncio solicitado no existe.</p>";
    return;
}

// 2. Obtener fotos adicionales
$sql_fotos = "SELECT Foto FROM Fotos WHERE Anuncio=?";
$stmt_f = $conn->prepare($sql_fotos);
$stmt_f->bind_param("i", $id);
$stmt_f->execute();
$fotos = $stmt_f->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_f->close();

// Fallback para la foto principal
$anuncio['FPrincipal'] = $anuncio['FPrincipal'] ?? "img/sin_foto.jpg";

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
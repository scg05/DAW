<?php require 'header.php'; ?>

<?php
// Recoger datos del formulario o búsqueda rápida
$tipo_anuncio = $_GET['tipo_anuncio'] ?? null;
$tipo_vivienda = $_GET['tipo_vivienda'] ?? null;
$ciudad = $_GET['ciudad'] ?? ($_GET['q'] ?? null);
$pais = $_GET['pais'] ?? null;
$precio = $_GET['precio'] ?? null;
$fecha = $_GET['fecha'] ?? null;

// Simulación de anuncios disponibles
$anuncios = [
    1 => [
        "titulo" => "Piso en Madrid",
        "fecha" => "20/09/2025",
        "ciudad" => "Madrid",
        "pais" => "España",
        "precio" => "250.000 €",
        "img" => "img/piso1.jpg"
    ],
    2 => [
        "titulo" => "Piso céntrico en San Vicente",
        "fecha" => "17/08/2025",
        "ciudad" => "San Vicente del Raspeig",
        "pais" => "España",
        "precio" => "3.000 €",
        "img" => "img/piso3.jpg"
    ]
];

// Filtrado (por ciudad o país si se especifica)
$resultados = array_filter($anuncios, function($anuncio) use ($ciudad, $pais) {
    $coincide = true;
    if ($ciudad) {
        $coincide = stripos($anuncio['ciudad'], $ciudad) !== false;
    }
    if ($coincide && $pais) {
        $coincide = stripos($anuncio['pais'], $pais) !== false;
    }
    return $coincide;
});
?>

<main>
    <h2>Resultados de la búsqueda</h2>

    <?php if (!empty($_GET)) : ?>
        <div class="conBorde">
            <h3>Tu búsqueda:</h3>
            <ul>
                <?php if ($tipo_anuncio) echo "<li><strong>Tipo de anuncio:</strong> $tipo_anuncio</li>"; ?>
                <?php if ($tipo_vivienda) echo "<li><strong>Tipo de vivienda:</strong> $tipo_vivienda</li>"; ?>
                <?php if ($ciudad) echo "<li><strong>Ciudad:</strong> $ciudad</li>"; ?>
                <?php if ($pais) echo "<li><strong>País:</strong> $pais</li>"; ?>
                <?php if ($precio) echo "<li><strong>Precio máximo:</strong> $precio €</li>"; ?>
                <?php if ($fecha) echo "<li><strong>Fecha desde:</strong> $fecha</li>"; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="anuncios">
        <?php if (count($resultados) > 0): ?>
            <?php foreach ($resultados as $id => $a): ?>
                <div class="anuncio">
                    <img src="<?= $a['img'] ?>" alt="Foto vivienda <?= $id ?>" width="200">
                    <h3><a href="ver_anuncio.php?id=<?= $id ?>"><?= htmlspecialchars($a['titulo']) ?></a></h3>
                    <p><strong>Fecha:</strong> <?= $a['fecha'] ?></p>
                    <p><strong>Ciudad:</strong> <?= $a['ciudad'] ?></p>
                    <p><strong>País:</strong> <?= $a['pais'] ?></p>
                    <p><strong>Precio:</strong> <?= $a['precio'] ?></p>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron anuncios que coincidan con tu búsqueda.</p>
        <?php endif; ?>
    </div>
</main>

<?php require 'footer.php'; ?>

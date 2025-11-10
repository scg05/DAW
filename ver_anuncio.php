<?php
require 'header.php';
require 'anuncios.php';

// Obtener ID de anuncio enviado por GET
$id = $_GET['id'] ?? 1; // Si no llega nada mostramos el 1

// Seleccionar anuncio según si ID es par o impar
$anuncio = ($id % 2 == 0) ? $anuncio2 : $anuncio1;

// Array reducido con los datos que mostraremos en index.php
$anuncios_cookie = [
    1 => ["titulo" => "Piso en Madrid", "ciudad" => "Madrid", "pais" => "España", "precio" => "250.000 €", "img" => "img/piso1.jpg"],
    2 => ["titulo" => "Piso céntrico en San Vicente", "ciudad" => "San Vicente del Raspeig", "pais" => "España", "precio" => "3.000 €", "img" => "img/piso3.jpg"],
];

if (isset($anuncios_cookie[$id])) {
    $dato = $anuncios_cookie[$id];

    // Recuperamos cookie anterior si existe
    $ultimos = isset($_COOKIE['ultimos_anuncios'])
        ? json_decode($_COOKIE['ultimos_anuncios'], true)
        : [];

    // Eliminamos si ya estaba (para evitar duplicados)
    $ultimos = array_filter($ultimos, fn($a) => $a['id'] != $id);

    // Añadimos al principio
    array_unshift($ultimos, [
        'id' => $id,
        'titulo' => $dato['titulo'],
        'ciudad' => $dato['ciudad'],
        'pais' => $dato['pais'],
        'precio' => $dato['precio'],
        'img' => $dato['img']
    ]);

    // Limitamos a 4
    $ultimos = array_slice($ultimos, 0, 4);

    // Guardamos la cookie durante 7 días
    setcookie('ultimos_anuncios', json_encode($ultimos), time() + (7 * 24 * 60 * 60), "/");
}
?>

<main class="container">
  <h1 class="titulo-faculty">Detalle del anuncio</h1>
  <article>
    <h2><?= $anuncio["titulo"] ?></h2>
    <img src="<?= $anuncio["imagen"] ?>" alt="Foto principal" width="400">

    <p><strong>Tipo de anuncio:</strong> <?= $anuncio["tipo_anuncio"] ?></p>
    <p><strong>Tipo de vivienda:</strong> <?= $anuncio["tipo_vivienda"] ?></p>
    <p><strong>Fecha:</strong> <?= $anuncio["fecha"] ?></p>
    <p><strong>Ciudad:</strong> <?= $anuncio["ciudad"] ?></p>
    <p><strong>País:</strong> <?= $anuncio["pais"] ?></p>
    <p><strong>Precio:</strong> <?= $anuncio["precio"] ?></p>
    <p><strong>Características:</strong> <?= $anuncio["caracteristicas"] ?></p>
    <p><strong>Usuario propietario:</strong> <?= $anuncio["usuario"] ?></p>

    <section>
      <h3>Otras fotos</h3>
      <?php foreach ($anuncio["miniaturas"] as $mini) : ?>
        <img src="<?= $mini ?>" width="100">
      <?php endforeach; ?>
    </section>

    <p><a href="mensaje.php">Contactar con el anunciante</a></p>
  </article>
</main>

<a href="anadir_foto.php?titulo=<?php echo urlencode($anuncio['titulo']); ?>">Añadir foto a este anuncio</a>

<?php require 'footer.php'; ?>

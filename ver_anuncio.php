<?php
require 'header.php';
require 'conexion.php';

// Obtener ID de anuncio enviado por GET
$id = $_GET['id'] ?? 1;

// Consulta para recuperar el anuncio completo
$sql = "SELECT a.*, ta.NomTAnuncio, tv.NomTVivienda, u.NomUsuario 
        FROM Anuncios a
        LEFT JOIN TiposAnuncios ta ON a.TAnuncio = ta.IdTAnuncio
        LEFT JOIN TiposViviendas tv ON a.TVivienda = tv.IdTVivienda
        LEFT JOIN Usuarios u ON a.Usuario = u.IdUsuario
        WHERE a.IdAnuncio = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$anuncio = $result->fetch_assoc();

// Recuperar fotos adicionales
$fotos_sql = "SELECT Foto FROM Fotos WHERE Anuncio = ?";
$stmt_f = $conn->prepare($fotos_sql);
$stmt_f->bind_param("i", $id);
$stmt_f->execute();
$fotos_result = $stmt_f->get_result();
$miniaturas = [];
while($row = $fotos_result->fetch_assoc()) {
    $miniaturas[] = $row['Foto'];
}

// Para evitar warnings si no hay resultados
$anuncio['Texto'] = $anuncio['Texto'] ?? "Sin descripción disponible";
$anuncio['FPrincipal'] = $anuncio['FPrincipal'] ?? "img/sin_foto.jpg";
$anuncio['NomTAnuncio'] = $anuncio['NomTAnuncio'] ?? "Desconocido";
$anuncio['NomTVivienda'] = $anuncio['NomTVivienda'] ?? "Desconocido";
$anuncio['NomUsuario'] = $anuncio['NomUsuario'] ?? "Anónimo";
?>

<main class="container">
  <h1 class="titulo-faculty">Detalle del anuncio</h1>
  <article class="detalle-anuncio">
    <h2><?= htmlspecialchars($anuncio["Titulo"]) ?></h2>

    <!-- Imagen principal -->
    <img src="<?= htmlspecialchars($anuncio["FPrincipal"]) ?>" alt="Foto principal" width="500" class="foto-grande">

    <!-- Info principal -->
    <section class="info-principal">
      <p><strong>Tipo de anuncio:</strong> <?= htmlspecialchars($anuncio["NomTAnuncio"]) ?></p>
      <p><strong>Tipo de vivienda:</strong> <?= htmlspecialchars($anuncio["NomTVivienda"]) ?></p>
      <p><strong>Fecha:</strong> <?= htmlspecialchars($anuncio["FRegistro"]) ?></p>
      <p><strong>Ciudad:</strong> <?= htmlspecialchars($anuncio["Ciudad"]) ?></p>
      <p><strong>País:</strong> <?= htmlspecialchars($anuncio["Pais"]) ?></p>
      <p><strong>Precio:</strong> <?= htmlspecialchars($anuncio["Precio"]) ?> €</p>
      <p><strong>Superficie:</strong> <?= htmlspecialchars($anuncio["Superficie"]) ?> m²</p>
      <p><strong>Habitaciones:</strong> <?= htmlspecialchars($anuncio["NHabitaciones"]) ?></p>
      <p><strong>Baños:</strong> <?= htmlspecialchars($anuncio["NBanyos"]) ?></p>
      <p><strong>Planta:</strong> <?= htmlspecialchars($anuncio["Planta"]) ?></p>
      <p><strong>Usuario propietario:</strong> <?= htmlspecialchars($anuncio["NomUsuario"]) ?></p>
    </section>

    <!-- Descripción -->
    <section class="descripcion">
        <h3>Descripción del anuncio</h3>
        <p><?= nl2br(htmlspecialchars($anuncio["Texto"])) ?></p>
    </section>

    <!-- Miniaturas -->
    <section class="miniaturas">
      <h3>Otras fotos</h3>
      <?php if (!empty($miniaturas)): ?>
        <?php foreach ($miniaturas as $mini): ?>
          <img src="<?= htmlspecialchars($mini) ?>" width="100" alt="Foto adicional del anuncio">
        <?php endforeach; ?>
      <?php else: ?>
        <p>No hay fotos adicionales.</p>
      <?php endif; ?>
    </section>

    <!-- Contacto -->
    <section class="usuario">
      <p><a href="mensaje.php?anuncio=<?= $id ?>">Contactar con el anunciante</a></p>
    </section>

    <!-- Añadir foto -->
    <p>
      <a href="anadir_foto.php?titulo=<?= urlencode($anuncio['Titulo']); ?>">Añadir foto a este anuncio</a>
    </p>
  </article>
</main>

<?php
$stmt->close();
$stmt_f->close();
$conn->close();
require 'footer.php';
?>
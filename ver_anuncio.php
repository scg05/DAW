<?php
require 'header.php';
require 'conexion.php';

// Obtener ID de anuncio enviado por GET
$id = $_GET['id'] ?? 1;

// Consulta para recuperar el anuncio completo + país
$sql = "SELECT a.*, 
               ta.NomTAnuncio, 
               tv.NomTVivienda, 
               u.NomUsuario,
               p.Nombre AS NombrePais
        FROM Anuncios a
        LEFT JOIN TiposAnuncios ta ON a.TAnuncio = ta.IdTAnuncio
        LEFT JOIN TiposViviendas tv ON a.TVivienda = tv.IdTVivienda
        LEFT JOIN Usuarios u ON a.Usuario = u.IdUsuario
        LEFT JOIN Paises p ON a.Pais = p.IdPais
        WHERE a.IdAnuncio = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$anuncio = $result->fetch_assoc();

if (!$anuncio) {
    echo "<p class='error'>El anuncio solicitado no existe.</p>";
    require 'footer.php';
    exit;
}

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

// Evitar warnings
$anuncio['Texto'] = $anuncio['Texto'] ?? "Sin descripción disponible";
$anuncio['FPrincipal'] = $anuncio['FPrincipal'] ?? "img/sin_foto.jpg";
$anuncio['NomTAnuncio'] = $anuncio['NomTAnuncio'] ?? "Desconocido";
$anuncio['NomTVivienda'] = $anuncio['NomTVivienda'] ?? "Desconocido";
$anuncio['NomUsuario'] = $anuncio['NomUsuario'] ?? "Anónimo";
$anuncio['NombrePais'] = $anuncio['NombrePais'] ?? "Desconocido";
?>

<main class="container">
  <h1 class="titulo-faculty">Ver anuncio</h1>
  <article class="detalle-anuncio">
    <h2><?= htmlspecialchars($anuncio["Titulo"]) ?></h2>

    <!-- Imagen principal -->
    <img src="<?= htmlspecialchars($anuncio["FPrincipal"]) ?>" 
            alt="<?= htmlspecialchars($anuncio['Alternativo']) ?>" 
            width="500" class="foto-grande">

    <!-- Miniaturas -->
    <?php if (!empty($miniaturas)): ?>
      <div class="miniaturas">
        <?php foreach ($miniaturas as $f): ?>
          <img src="<?= htmlspecialchars($f) ?>" 
               alt="Miniatura" width="100" class="mini">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Info principal -->
    <section class="info-principal">
      <p><strong>Tipo de anuncio:</strong> <?= htmlspecialchars($anuncio["NomTAnuncio"]) ?></p>
      <p><strong>Tipo de vivienda:</strong> <?= htmlspecialchars($anuncio["NomTVivienda"]) ?></p>
      <p><strong>Fecha:</strong> <?= htmlspecialchars($anuncio["FRegistro"]) ?></p>
      <p><strong>Ciudad:</strong> <?= htmlspecialchars($anuncio["Ciudad"]) ?></p>
      <p><strong>País:</strong> <?= htmlspecialchars($anuncio["NombrePais"]) ?></p>
      <p><strong>Precio:</strong> <?= htmlspecialchars($anuncio["Precio"]) ?> €</p>
      <p><strong>Superficie:</strong> <?= htmlspecialchars($anuncio["Superficie"]) ?> m²</p>
      <p><strong>Habitaciones:</strong> <?= htmlspecialchars($anuncio["NHabitaciones"]) ?></p>
      <p><strong>Baños:</strong> <?= htmlspecialchars($anuncio["NBanyos"]) ?></p>
      <p><strong>Planta:</strong> <?= htmlspecialchars($anuncio["Planta"]) ?></p>
    </section>

    <!-- Descripción -->
    <section class="descripcion">
        <h3>Descripción del anuncio</h3>
        <p><?= nl2br(htmlspecialchars($anuncio["Texto"])) ?></p>
    </section>

    <p><a href="verfotos_private.php?id=<?= $anuncio['IdAnuncio'] ?>">Ver todas las fotos</a></p>

    <!-- Mensajes -->
    <section class="usuario">
      <p><a href="mensajes.php">Mensajes (<?= htmlspecialchars($anuncio['NomUsuario']) ?>)</a></p>
    </section>

    <!-- Añadir foto -->
    <p>
      <a href="anadir_foto.php?id=<?= $anuncio['IdAnuncio']; ?>">Añadir foto a este anuncio</a>
    </p>
  </article>
</main>

<?php
$stmt->close();
$stmt_f->close();
$conn->close();
require 'footer.php';
?>
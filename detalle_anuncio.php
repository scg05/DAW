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
    <img src="<?= htmlspecialchars($anuncio["FPrincipal"]) ?>" 
            alt="<?= htmlspecialchars($anuncio['Alternativo']) ?>" 
            width="500" class="foto-grande">

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

      <!-- Anunciante -->
      <p><strong>Usuario propietario:</strong> <a href="perfil_usuario.php?id=<?= $anuncio['Usuario'] ?>"><?= htmlspecialchars($anuncio["NomUsuario"]) ?></a></p>
    </section>

    <!-- Descripción -->
    <section class="descripcion">
        <h3>Descripción del anuncio</h3>
        <p><?= nl2br(htmlspecialchars($anuncio["Texto"])) ?></p>
    </section>

    <p><a href="verfotos_public.php?id=<?= $anuncio['IdAnuncio'] ?>">Ver todas las fotos</a></p>


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
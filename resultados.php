<?php 
require 'header.php';
require 'conexion.php'; // conexión a la BD

// Recoger parámetros de GET
$tipo_anuncio = $_GET['tipo_anuncio'] ?? null;
$tipo_vivienda = $_GET['tipo_vivienda'] ?? null;
$ciudad = $_GET['ciudad'] ?? null;
$pais = $_GET['pais'] ?? null;
$precio = $_GET['precio'] ?? null;
$fecha = $_GET['fecha'] ?? null;
$q = $_GET['q'] ?? null;

// --- Construir condiciones dinámicas ---
$condiciones = [];
$parametros = [];

if ($tipo_anuncio) {
    $condiciones[] = "A.TAnuncio = ?";
    $parametros[] = $tipo_anuncio;
}
if ($tipo_vivienda) {
    $condiciones[] = "A.TVivienda = ?";
    $parametros[] = $tipo_vivienda;
}
if ($ciudad) {
    $condiciones[] = "A.Ciudad LIKE ?";
    $parametros[] = "%" . $ciudad . "%";
}
if ($pais) {
    $condiciones[] = "A.Pais = ?";
    $parametros[] = $pais;
}
if ($precio) {
    $condiciones[] = "A.Precio <= ?";
    $parametros[] = $precio;
}
if ($fecha) {
    $condiciones[] = "A.FRegistro >= ?";
    $parametros[] = $fecha;
}

// --- Búsqueda rápida con texto libre ---
if ($q) {
    $q = strtolower(trim($q));
    $q = str_replace(['un', 'una', 'en', 'de'], '', $q);
    $palabras = explode(' ', $q);

    foreach ($palabras as $palabra) {
        if ($palabra != '') {
            $condiciones[] = "(A.Titulo LIKE ? OR A.Ciudad LIKE ? OR A.Texto LIKE ?)";
            $parametros[] = "%$palabra%";
            $parametros[] = "%$palabra%";
            $parametros[] = "%$palabra%";
        }
    }
}

// --- Construir la consulta ---
$sql = "SELECT A.IdAnuncio, A.Titulo, A.FPrincipal, A.FRegistro, A.Ciudad, P.Nombre AS Pais, A.Precio
        FROM Anuncios A
        LEFT JOIN Paises P ON A.Pais = P.IdPais";

if (!empty($condiciones)) {
    $sql .= " WHERE " . implode(" AND ", $condiciones);
}

$sql .= " ORDER BY A.FRegistro DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($parametros)) {
        // Crear tipos de parámetros para bind_param
        $tipos = str_repeat('s', count($parametros));
        $stmt->bind_param($tipos, ...$parametros);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Error en la consulta: " . $conn->error);
}
?>

<main>
    <h2>Resultados de la búsqueda</h2>

    <div class="anuncios">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($a = $result->fetch_assoc()): ?>
                <div class="anuncio">
                    <img src="<?= htmlspecialchars($a['FPrincipal']) ?>" width="200" alt="Foto vivienda">
                    <h3><a href="detalle_anuncio.php?id=<?= $a['IdAnuncio'] ?>"><?= htmlspecialchars($a['Titulo']) ?></a></h3>
                    <p><strong>Fecha:</strong> <?= date("d/m/Y", strtotime($a['FRegistro'])) ?></p>
                    <p><strong>Ciudad:</strong> <?= htmlspecialchars($a['Ciudad']) ?></p>
                    <p><strong>País:</strong> <?= htmlspecialchars($a['Pais']) ?></p>
                    <p><strong>Precio:</strong> <?= number_format($a['Precio'], 2, ',', '.') ?> €</p>
                </div>
                <hr>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No se encontraron anuncios que coincidan con tu búsqueda.</p>
        <?php endif; ?>
    </div>
</main>

<?php 
$stmt->close();
$conn->close();
require 'footer.php'; 
?>

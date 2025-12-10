<?php
require 'header.php';
require 'conexion.php';

// Comprobar sesión (igual que en mis_anuncios)
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=acceso_denegado");
    exit;
}

$nombreUsuario = $_SESSION['usuario'];

// Obtener IdUsuario
$stmt = $conn->prepare("SELECT IdUsuario FROM Usuarios WHERE NomUsuario = ?");
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$stmt->bind_result($idUsuario);
$stmt->fetch();
$stmt->close();

if (!$idUsuario) {
    echo "<p class='error'>Usuario no encontrado.</p>";
    require 'footer.php';
    exit;
}

// Id del anuncio a modificar
$idAnuncio = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idAnuncio <= 0) {
    echo "<p class='error'>Anuncio no válido.</p>";
    require 'footer.php';
    exit;
}

// Cargar datos del anuncio, asegurando que es del usuario
$sqlA = "SELECT * FROM Anuncios WHERE IdAnuncio = ? AND Usuario = ?";
$stmtA = $conn->prepare($sqlA);
$stmtA->bind_param("ii", $idAnuncio, $idUsuario);
$stmtA->execute();
$resA = $stmtA->get_result();
$anuncio = $resA->fetch_assoc();
$stmtA->close();

if (!$anuncio) {
    echo "<p class='error'>No se ha encontrado el anuncio o no tienes permisos para modificarlo.</p>";
    require 'footer.php';
    exit;
}

// Cargar tipos de anuncio
$tiposAnuncio = [];
$sqlTA = "SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios ORDER BY NomTAnuncio";
$resTA = $conn->query($sqlTA);
while ($fila = $resTA->fetch_assoc()) {
    $tiposAnuncio[] = $fila;
}

// Cargar tipos de vivienda
$tiposVivienda = [];
$sqlTV = "SELECT IdTVivienda, NomTVivienda FROM TiposViviendas ORDER BY NomTVivienda";
$resTV = $conn->query($sqlTV);
while ($fila = $resTV->fetch_assoc()) {
    $tiposVivienda[] = $fila;
}

// Cargar países
$paises = [];
$sqlP = "SELECT IdPais, Nombre FROM Paises ORDER BY Nombre";
$resP = $conn->query($sqlP);
while ($fila = $resP->fetch_assoc()) {
    $paises[] = $fila;
}

$conn->close();

// Valores actuales del anuncio
$titulo      = $anuncio['Titulo'] ?? '';
$ciudad      = $anuncio['Ciudad'] ?? '';
$pais        = $anuncio['Pais'] ?? '';
$precio      = $anuncio['Precio'] ?? '';
$tipoA       = $anuncio['TAnuncio'] ?? '';
$tipoV       = $anuncio['TVivienda'] ?? '';
$descripcion = $anuncio['Texto'] ?? '';

$mensaje_error = '';
$modo = 'modificar';
$accion = "respuesta_modificar_anuncio.php?id=" . $idAnuncio;
?>

<main class="container">
<?php require 'formulario_anuncio.php'; ?>
<p><a href="ver_anuncio.php?id=<?= $idAnuncio; ?>">Volver al anuncio</a></p>
</main>

<?php require 'footer.php'; ?>

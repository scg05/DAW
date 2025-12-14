<?php 
$pageStyles = ["css/enviarmensaje.css"]; 
require 'header.php'; 
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "<main class='container'><p class='error'>Acceso no válido.</p></main>";
    require 'footer.php';
    exit;
}

$tipo = $_POST['tipo'] ?? "";
$mensaje = trim($_POST['mensaje'] ?? "");
$anuncio = $_POST['anuncio'] ?? "";
$errores = [];

// Validaciones
if (!ctype_digit($tipo)) $errores[] = "Tipo de mensaje no válido.";
if ($mensaje === "") $errores[] = "El mensaje no puede estar vacío.";
if (!ctype_digit($anuncio)) $errores[] = "ID de anuncio no válido.";

if (!isset($_SESSION["usuario"])) {
    $errores[] = "Debes iniciar sesión para enviar un mensaje.";
}

if (!empty($errores)) {
    echo "<main class='container'><div class='error'><h3>Errores:</h3><ul>";
    foreach ($errores as $e) echo "<li>".htmlspecialchars($e)."</li>";
    echo "</ul></div></main>";
    require 'footer.php';
    exit;
}

// Obtener ID del usuario origen
$usuOrigen = $_SESSION["usuario_id"];  // Asegúrate que guardas esto en la sesión
// Obtener ID del usuario destino (propietario del anuncio)
$stmt = $conn->prepare("SELECT Usuario FROM Anuncios WHERE IdAnuncio = ?");
$stmt->bind_param("i", $anuncio);
$stmt->execute();
$result = $stmt->get_result();
$destino = $result->fetch_assoc()["Usuario"] ?? null;

if (!$destino) {
    echo "<main class='container'><p class='error'>No se encontró el anunciante.</p></main>";
    require 'footer.php';
    exit;
}

// Insertar mensaje
$sqlInsert = "INSERT INTO Mensajes (TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino, FRegistro)
              VALUES (?, ?, ?, ?, ?, NOW())";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("isi ii", $tipo, $mensaje, $anuncio, $usuOrigen, $destino);
$stmtInsert->execute();

// Obtener nombre del tipo
$stmt2 = $conn->prepare("SELECT NomTMensaje FROM TiposMensajes WHERE IdTMensaje = ?");
$stmt2->bind_param("i", $tipo);
$stmt2->execute();
$res2 = $stmt2->get_result();
$tipoNombre = $res2->fetch_assoc()["NomTMensaje"] ?? "Desconocido";
?>

<main class="container">
<h1>Mensaje enviado correctamente</h1>

<p>Su mensaje ha sido enviado al anunciante.</p>

<h2>Datos del mensaje</h2>
<ul>
  <li><strong>Tipo de mensaje:</strong> <?= htmlspecialchars($tipoNombre) ?></li>
  <li><strong>Contenido:</strong> <?= nl2br(htmlspecialchars($mensaje)) ?></li>
</ul>

<p>Gracias por contactar con el anunciante.</p>
</main>

<?php require 'footer.php'; ?>

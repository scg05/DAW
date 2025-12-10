<?php
$pageStyles = ["css/solicitarfolleto.css"];
require 'header.php';
require 'conexion.php';

// Si no llega por POST, salir
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "<p class='error'>Acceso no válido.</p>";
    require 'footer.php';
    exit;
}

$anuncio     = $_POST["anuncio"] ?? null;
$texto       = trim($_POST["texto"] ?? "");
$nombre      = trim($_POST["nombre"] ?? "");
$email       = trim($_POST["email"] ?? "");
$direccion   = trim($_POST["direccion"] ?? "");
$telefono    = trim($_POST["telefono"] ?? "");

$color       = trim($_POST["color"] ?? "");
$copias      = intval($_POST["copias"] ?? 1);
$resolucion  = intval($_POST["resolucion"] ?? 300);
$ifecha      = $_POST["fecha"] ?? date("Y-m-d");

$icolor      = isset($_POST["icolor"]) ? 1 : 0;
$iprecio     = isset($_POST["iprecio"]) ? 1 : 0;

// Validación básica
$errores = [];

if (!$anuncio || !ctype_digit($anuncio)) $errores[] = "ID de anuncio inválido.";
if ($nombre === "") $errores[] = "Debe indicar un nombre.";
if ($email === "") $errores[] = "Debe indicar un email.";
if ($direccion === "") $errores[] = "Debe indicar una dirección.";

if (!empty($errores)) {
    echo "<h1>❌ Error al enviar solicitud</h1><ul>";
    foreach ($errores as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
    echo "</ul>";
    require 'footer.php';
    exit;
}

// Insertar en Solicitudes
$sql = "INSERT INTO Solicitudes 
        (Anuncio, Texto, Nombre, Email, Direccion, Telefono, Color, Copias, Resolucion, Fecha, IColor, IPrecio, FRegistro, Coste)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NULL)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "issssssiiiii",
    $anuncio,
    $texto,
    $nombre,
    $email,
    $direccion,
    $telefono,
    $color,
    $copias,
    $resolucion,
    $ifecha,
    $icolor,
    $iprecio
);
$stmt->execute();

// Obtener ID insertado
$idSolicitud = $stmt->insert_id;

?>

<main class="container">
    <h1> Solicitud enviada correctamente</h1>

    <p>Su solicitud de folleto ha sido almacenada correctamente.</p>

    <h2>Datos enviados</h2>
    <ul>
        <li><strong>ID solicitud:</strong> <?= $idSolicitud ?></li>
        <li><strong>Anuncio:</strong> <?= htmlspecialchars($anuncio) ?></li>
        <li><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></li>
        <li><strong>Email:</strong> <?= htmlspecialchars($email) ?></li>
        <li><strong>Dirección:</strong> <?= htmlspecialchars($direccion) ?></li>
        <li><strong>Teléfono:</strong> <?= htmlspecialchars($telefono) ?></li>
        <li><strong>Color:</strong> <?= htmlspecialchars($color) ?></li>
        <li><strong>Copias:</strong> <?= htmlspecialchars($copias) ?></li>
        <li><strong>Resolución:</strong> <?= htmlspecialchars($resolucion) ?> dpi</li>
        <li><strong>Fecha deseada:</strong> <?= htmlspecialchars($ifecha) ?></li>
        <li><strong>Impresión a color:</strong> <?= $icolor ? "Sí" : "No" ?></li>
        <li><strong>Incluir precio:</strong> <?= $iprecio ? "Sí" : "No" ?></li>
        <li><strong>Mensaje adicional:</strong> <?= nl2br(htmlspecialchars($texto)) ?></li>
    </ul>

</main>

<?php require 'footer.php'; ?>

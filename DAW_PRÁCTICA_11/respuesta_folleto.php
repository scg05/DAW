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
$telefono    = trim($_POST["telefono"] ?? "");
$paginas     = intval($_POST["paginas"] ?? 1);
$fotos       = intval($_POST["fotos"] ?? 3);
$copias      = intval($_POST["copias"] ?? 1);
$resolucion  = intval($_POST["resolucion"] ?? 150);
$color_impresion = $_POST["color_impresion"] ?? "blanco_negro";
$mostrar_precio = $_POST["mostrar_precio"] ?? "si";
$color_portada = $_POST["color_portada"] ?? "#000000";

// Reconstruir dirección desde los campos separados
$calle       = trim($_POST["calle"] ?? "");
$numero      = trim($_POST["numero"] ?? "");
$piso        = trim($_POST["piso"] ?? "");
$cp          = trim($_POST["cp"] ?? "");
$localidad   = trim($_POST["localidad"] ?? "");
$provincia   = trim($_POST["provincia"] ?? "");
$pais        = trim($_POST["pais"] ?? "");

// Construir string de dirección completa
$direccion = $calle;
if (!empty($numero)) $direccion .= ", " . $numero;
if (!empty($piso)) $direccion .= " (" . $piso . ")";
if (!empty($cp)) $direccion .= ", " . $cp;
if (!empty($localidad)) $direccion .= " " . $localidad;
if (!empty($provincia)) $direccion .= ", " . $provincia;
if (!empty($pais)) $direccion .= ", " . $pais;

// Validación básica
$errores = [];

if (!$anuncio || !ctype_digit($anuncio)) $errores[] = "ID de anuncio inválido.";
if ($nombre === "") $errores[] = "Debe indicar un nombre.";
if ($email === "") $errores[] = "Debe indicar un email.";
if ($calle === "") $errores[] = "Debe indicar una dirección.";
if ($numero === "") $errores[] = "Debe indicar un número.";
if ($cp === "") $errores[] = "Debe indicar un código postal.";
if ($localidad === "") $errores[] = "Debe indicar una localidad.";
if ($provincia === "") $errores[] = "Debe indicar una provincia.";
if ($pais === "") $errores[] = "Debe indicar un país.";

if (!empty($errores)) {
    echo "<h1>❌ Error al enviar solicitud</h1><ul>";
    foreach ($errores as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
    echo "</ul>";
    require 'footer.php';
    exit;
}

// Calcular coste del folleto
// Precios base por página (varía según tipo de impresión y resolución)
$precios_base = [
    "blanco_negro_150" => 12.00,
    "blanco_negro_450" => 12.60,
    "color_150" => 13.50,
    "color_450" => 14.10
];

$incrementos = [
    "blanco_negro_150" => 2.00,
    "blanco_negro_450" => 2.60,
    "color_150" => 3.50,
    "color_450" => 4.10
];

$clave_precio = $color_impresion . "_" . $resolucion;
$precio_por_pagina = $precios_base[$clave_precio] ?? 12.00;
$incremento_por_pagina = $incrementos[$clave_precio] ?? 2.00;

$coste_base = $precio_por_pagina + ($paginas - 1) * $incremento_por_pagina;
$coste_total = ($coste_base * $copias) + 5; // 5€ de envío fijo

// Convertir valores booleanos a 1/0 para la BD
$icolor = ($color_impresion === "color") ? 1 : 0;
$iprecio = ($mostrar_precio === "si") ? 1 : 0;

// Campo "Color" almacena el tipo de impresión para compatibilidad con BD
$color = ($color_impresion === "color") ? "Color" : "Blanco y Negro";

// Fecha de registro (hoy)
$fecha_solicitud = date("Y-m-d");

// Insertar en Solicitudes usando solo las columnas que existen
$sql = "INSERT INTO Solicitudes 
        (Anuncio, Texto, Nombre, Email, Direccion, Telefono, Color, Copias, Resolucion, Fecha, IColor, IPrecio, Coste, FRegistro)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "issssssiiisid",
    $anuncio,
    $texto,
    $nombre,
    $email,
    $direccion,
    $telefono,
    $color,
    $copias,
    $resolucion,
    $fecha_solicitud,
    $icolor,
    $iprecio,
    $coste_total
);
$stmt->execute();

// Obtener ID insertado
$idSolicitud = $stmt->insert_id;
$stmt->close();
$conn->close();
?>

<main class="container">
    <h1>Solicitud enviada correctamente</h1>

    <p>Su solicitud de folleto ha sido almacenada correctamente.</p>

    <h2>Datos enviados</h2>
    <ul>
        <li><strong>ID solicitud:</strong> <?= $idSolicitud ?></li>
        <li><strong>Anuncio:</strong> <?= htmlspecialchars($anuncio) ?></li>
        <li><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></li>
        <li><strong>Email:</strong> <?= htmlspecialchars($email) ?></li>
        <li><strong>Dirección:</strong> <?= htmlspecialchars($direccion) ?></li>
        <li><strong>Teléfono:</strong> <?= htmlspecialchars($telefono) ?></li>
        <li><strong>Páginas:</strong> <?= $paginas ?></li>
        <li><strong>Fotos:</strong> <?= $fotos ?></li>
        <li><strong>Copias:</strong> <?= $copias ?></li>
        <li><strong>Resolución:</strong> <?= $resolucion ?> DPI</li>
        <li><strong>Tipo de impresión:</strong> <?= $color_impresion === "color" ? "Color" : "Blanco y Negro" ?></li>
        <li><strong>Mostrar precio en folleto:</strong> <?= $mostrar_precio === "si" ? "Sí" : "No" ?></li>
        <li><strong>Coste total:</strong> <strong><?= number_format($coste_total, 2, ',', '.') ?> €</strong></li>
        <li><strong>Mensaje adicional:</strong> <?= !empty($texto) ? nl2br(htmlspecialchars($texto)) : "(Sin mensaje)" ?></li>
    </ul>

    <p><a href="solicitar_folleto.php">Volver a solicitar otro folleto</a></p>

</main>

<?php require 'footer.php'; ?>
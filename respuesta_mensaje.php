<?php 
$pageStyles = ["css/enviarmensaje.css"]; 
require 'header.php'; 
require 'conexion.php';
?>

<main class="container">
<h1>Mensaje enviado correctamente</h1>

<?php
$tipo = $_GET['tipo'] ?? "";
$mensaje = $_GET['mensaje'] ?? "";

// Recuperar nombre del tipo
$sql = "SELECT NomTMensaje FROM TiposMensajes WHERE IdTMensaje = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tipo);
$stmt->execute();
$result = $stmt->get_result();
$tipoNombre = $result->fetch_assoc()["NomTMensaje"] ?? "Desconocido";
?>

<p>Su mensaje ha sido enviado al anunciante.</p>

<h2>Datos del mensaje</h2>
<ul>
  <li><strong>Tipo de mensaje:</strong> <?= htmlspecialchars($tipoNombre) ?></li>
  <li><strong>Contenido:</strong> <?= nl2br(htmlspecialchars($mensaje)) ?></li>
</ul>

<p>Gracias por contactar con el anunciante.</p>
</main>

<?php require 'footer.php'; ?>

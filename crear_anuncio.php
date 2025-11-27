<?php
require 'header.php';
require 'conexion.php';

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

// Variables vacías para el formulario
$titulo = "";
$ciudad = "";
$pais = "";
$precio = "";
$tipoA = "";
$tipoV = "";
$descripcion = "";

$modo = 'crear';
$mensaje_error = "";
$accion = "respuesta_crear_anuncio.php";
?>

<main class="container">
    <?php require 'formulario_anuncio.php'; ?>
</main>

<?php require 'footer.php'; ?>

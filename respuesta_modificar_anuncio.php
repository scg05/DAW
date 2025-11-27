<?php
require 'header.php';
require 'conexion.php';
require 'filtro_anuncio.php';

// Comprobar sesión
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

// Id anuncio a modificar
$idAnuncio = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($idAnuncio <= 0) {
    echo "<p class='error'>Anuncio no válido.</p>";
    require 'footer.php';
    exit;
}

// Aplicamos filtrado/validación común
list($datos, $errores) = filtrarDatosAnuncio();

$titulo      = $datos['titulo'];
$ciudad      = $datos['ciudad'];
$pais        = $datos['pais'];
$precio      = $datos['precio'];
$tipoA       = $datos['tipo_anuncio'];
$tipoV       = $datos['tipo_vivienda'];
$descripcion = $datos['descripcion'];

if (!empty($errores)) {

    // Si hay errores, volvemos a mostrar el formulario de modificación con los datos filtrados

    // Volvemos a cargar listas de tipos/paises para el form
    $tiposAnuncio = [];
    $sqlTA = "SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios ORDER BY NomTAnuncio";
    $resTA = $conn->query($sqlTA);
    while ($fila = $resTA->fetch_assoc()) {
        $tiposAnuncio[] = $fila;
    }

    $tiposVivienda = [];
    $sqlTV = "SELECT IdTVivienda, NomTVivienda FROM TiposViviendas ORDER BY NomTVivienda";
    $resTV = $conn->query($sqlTV);
    while ($fila = $resTV->fetch_assoc()) {
        $tiposVivienda[] = $fila;
    }

    $paises = [];
    $sqlP = "SELECT IdPais, Nombre FROM Paises ORDER BY Nombre";
    $resP = $conn->query($sqlP);
    while ($fila = $resP->fetch_assoc()) {
        $paises[] = $fila;
    }

    $mensaje_error = implode("<br>", $errores);
    $modo = 'modificar';
    $accion = "respuesta_modificar_anuncio.php?id=" . $idAnuncio;
    ?>

    <main class="container">
        <?php require 'formulario_anuncio.php'; ?>
        <p style="color:red;"><?= $mensaje_error; ?></p>
        <p><a href="ver_anuncio.php?id=<?= $idAnuncio; ?>">Cancelar y volver al anuncio</a></p>
    </main>

    <?php
    $conn->close();
    require 'footer.php';
    exit;
}

// Sin errores → actualizar en BD (y comprobar que el anuncio es suyo)
$sql = "UPDATE Anuncios
        SET Titulo = ?, Ciudad = ?, Pais = ?, Precio = ?, Texto = ?, TAnuncio = ?, TVivienda = ?
        WHERE IdAnuncio = ? AND Usuario = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo "<h1>Error</h1>";
    echo "<p>No se ha podido preparar la consulta de actualización.</p>";
    echo '<p><a href="modificar_anuncio.php?id=' . $idAnuncio . '">Volver al formulario</a></p>';
    $conn->close();
    require 'footer.php';
    exit;
}

$paisInt  = (int)$pais;
$tipoAInt = (int)$tipoA;
$tipoVInt = (int)$tipoV;

$stmt->bind_param(
    "ssidssiii",
    $titulo,
    $ciudad,
    $paisInt,
    $precio,
    $descripcion,
    $tipoAInt,
    $tipoVInt,
    $idAnuncio,
    $idUsuario
);

$stmt->execute();

if ($stmt->affected_rows >= 0) {
    ?>
    <main class="container">
        <h1>Anuncio modificado correctamente</h1>
        <p>Los datos del anuncio se han actualizado.</p>
        <p><a href="ver_anuncio.php?id=<?= $idAnuncio; ?>">Volver al anuncio</a></p>
        <p><a href="mis_anuncios.php">Ir a mis anuncios</a></p>
    </main>
    <?php
} else {
    ?>
    <main class="container">
        <h1>Error</h1>
        <p>Ha ocurrido un error al actualizar el anuncio.</p>
        <p><a href="modificar_anuncio.php?id=<?= $idAnuncio; ?>">Volver al formulario</a></p>
    </main>
    <?php
}

$stmt->close();
$conn->close();
require 'footer.php';
?>

<?php
require 'header.php';
require 'conexion.php';
require 'filtro_anuncio.php';

session_start();
$usuario = $_SESSION['idUsuario'] ?? 1; // O lo que uses para login

// 1. Validar todo con filtro_anuncio.php
list($datos, $errores) = filtrarDatosAnuncio();

$titulo      = $datos['titulo'];
$ciudad      = $datos['ciudad'];
$pais        = $datos['pais'];
$precio      = $datos['precio'];
$tipoA       = $datos['tipo_anuncio'];
$tipoV       = $datos['tipo_vivienda'];
$descripcion = $datos['descripcion'];

// 2. Si hay errores → volver a mostrar el formulario igual que en modificar
if (!empty($errores)) {

    $mensaje_error = implode("<br>", $errores);

    // Volvemos a cargar listas
    $tiposAnuncio = [];
    $resTA = $conn->query("SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios ORDER BY NomTAnuncio");
    while ($fila = $resTA->fetch_assoc()) $tiposAnuncio[] = $fila;

    $tiposVivienda = [];
    $resTV = $conn->query("SELECT IdTVivienda, NomTVivienda FROM TiposViviendas ORDER BY NomTVivienda");
    while ($fila = $resTV->fetch_assoc()) $tiposVivienda[] = $fila;

    $paises = [];
    $resP = $conn->query("SELECT IdPais, Nombre FROM Paises ORDER BY Nombre");
    while ($fila = $resP->fetch_assoc()) $paises[] = $fila;

    $modo = 'crear';
    $accion = "respuesta_crear_anuncio.php";

    ?>
    <main class="container">
        <?php require 'formulario_anuncio.php'; ?>
    </main>
    <?php
    require 'footer.php';
    exit;
}

// 3. Insertar el anuncio porque no hay errores

$sql = "INSERT INTO Anuncios 
        (Titulo, Ciudad, Pais, Precio, Texto, TAnuncio, TVivienda, Usuario, FRegistro, FPrincipal)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'img/sin_foto.jpg')";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssidssii",
    $titulo, $ciudad, $pais, $precio, $descripcion, $tipoA, $tipoV, $usuario
);

$stmt->execute();

$idNuevo = $conn->insert_id;

?>
<main class="container">
    <h1>Anuncio creado correctamente</h1>
    <p><a href="anadir_foto.php?id=<?= $idNuevo ?>">Añadir foto al anuncio</a></p>
</main>
<?php require 'footer.php'; ?>

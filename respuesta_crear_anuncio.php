<?php
// NOTA: Se ha eliminado el session_start() redundante, ya que ya se inicia en header.php.

require 'header.php';
require 'conexion.php';
require 'filtro_anuncio.php';

// Es crucial obtener el ID del usuario. Si no está logueado, se asume 1 (temporalmente).
// Es mejor forzar el login si la sesión no existe, pero mantendremos tu lógica existente de respaldo.
$usuario = $_SESSION['id_usuario'] ?? 1; // Usamos 'id_usuario' que es lo que establece control_acceso.php

// 1. Validar todo con filtro_anuncio.php
list($datos, $errores) = filtrarDatosAnuncio();

$titulo      = $datos['titulo'];
$ciudad      = $datos['ciudad'];
$pais        = $datos['pais'];      // Debe ser INT
$precio      = $datos['precio'];    // Debe ser DOUBLE/FLOAT
$tipoA       = $datos['tipo_anuncio']; // Debe ser INT
$tipoV       = $datos['tipo_vivienda'];// Debe ser INT
$descripcion = $datos['descripcion'];

// 2. Si hay errores → volver a mostrar el formulario
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

// CONVERTIMOS LAS VARIABLES A SUS TIPOS ESCALARES REQUERIDOS PARA bind_param
$paisInt  = (int)$pais;
$tipoAInt = (int)$tipoA;
$tipoVInt = (int)$tipoV;
$usuarioInt = (int)$usuario;

// CORRECCIÓN CRÍTICA: La cadena de tipos debe ser "ssidsiii"
// s: Titulo, s: Ciudad, i: Pais, d: Precio, s: Texto, i: TAnuncio, i: TVivienda, i: Usuario
$stmt->bind_param(
    "ssidsiii",
    $titulo, $ciudad, $paisInt, $precio, $descripcion, $tipoAInt, $tipoVInt, $usuarioInt
);

// CORRECCIÓN CRÍTICA: Comprobamos el resultado de la ejecución
if ($stmt->execute()) {
    
    $idNuevo = $conn->insert_id;
    
    // ÉXITO
    ?>
    <main class="container">
        <h1>Anuncio creado correctamente</h1>
        <p><a href="anadir_foto.php?id=<?= $idNuevo ?>">Añadir foto al anuncio</a></p>
    </main>
    <?php
} else {
    // FALLO DE INSERCIÓN
    ?>
    <main class="container">
        <h1>Error al crear anuncio</h1>
        <p>Ha ocurrido un error al intentar guardar el anuncio en la base de datos.</p>
        <p>Asegúrate de que estás logueado y que el usuario existe en la tabla `Usuarios`.</p>
        <p style="color:red;">Error de BD: <?= htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8'); ?></p> 
        <p><a href="crear_anuncio.php">Volver al formulario de creación</a></p>
    </main>
    <?php
}

$stmt->close();
$conn->close();

require 'footer.php'; 
?>
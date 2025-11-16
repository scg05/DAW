<?php
require 'header.php';
require 'conexion.php';

$mensaje_error = "";
$mensaje_exito = "";

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

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $titulo = trim($_POST['titulo']);
    $ciudad = trim($_POST['ciudad']);
    $pais = intval($_POST['pais']);
    $precio = floatval($_POST['precio']);
    $tipoA = intval($_POST['tipo_anuncio']);
    $tipoV = intval($_POST['tipo_vivienda']);
    $descripcion = trim($_POST['descripcion']);
    $usuario = 1; // Cambiar cuando tengas login REAL

    // Validación sencilla
    if ($titulo == "" || $ciudad == "" || $pais == 0 || $precio == 0 || $descripcion == "") {
        $mensaje_error = "Debes completar todos los campos.";
    } else {
        // Insertar en BD
        $sql = "INSERT INTO Anuncios 
                (Titulo, Ciudad, Pais, Precio, Texto, TAnuncio, TVivienda, Usuario, FRegistro, FPrincipal)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'img/sin_foto.jpg')";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidssii", 
            $titulo, 
            $ciudad, 
            $pais, 
            $precio, 
            $descripcion, 
            $tipoA, 
            $tipoV,
            $usuario
        );

        if ($stmt->execute()) {
            header("Location: mis_anuncios.php");
            exit;
        } else {
            $mensaje_error = "Error al guardar el anuncio.";
        }
    }
}

?>

<h1>Crear nuevo anuncio</h1>

<?php if ($mensaje_error != ""): ?>
    <p style='color:red'><?= $mensaje_error ?></p>
<?php endif; ?>

<form action="" method="post" novalidate>

    <label>Título:</label>
    <input type="text" name="titulo" required><br>

    <label>Ciudad:</label>
    <input type="text" name="ciudad" required><br>

    <label>País:</label>
    <select name="pais" required>
        <option value="">Seleccionar...</option>
        <?php foreach ($paises as $p): ?>
            <option value="<?= $p['IdPais'] ?>"><?= htmlspecialchars($p['Nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Precio (€):</label>
    <input type="number" step="0.01" name="precio" required><br>

    <label>Tipo de anuncio:</label>
    <select name="tipo_anuncio" required>
        <?php foreach ($tiposAnuncio as $t): ?>
            <option value="<?= $t['IdTAnuncio'] ?>"><?= htmlspecialchars($t['NomTAnuncio']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Tipo de vivienda:</label>
    <select name="tipo_vivienda" required>
        <?php foreach ($tiposVivienda as $tv): ?>
            <option value="<?= $tv['IdTVivienda'] ?>"><?= htmlspecialchars($tv['NomTVivienda']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion" required></textarea><br>

    <input type="submit" value="Crear anuncio">
</form>

<?php require 'footer.php'; ?>
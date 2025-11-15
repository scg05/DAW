<?php
require 'header.php';
require 'conexion.php';

// Comprobar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?error=acceso_denegado");
    exit;
}

// Obtener nombre de usuario desde sesión
$nombreUsuario = $_SESSION['usuario'];

// Recuperar IdUsuario desde la base de datos
$stmt = $conn->prepare("SELECT IdUsuario FROM Usuarios WHERE NomUsuario = ?");
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$stmt->bind_result($idUsuario);
$stmt->fetch();
$stmt->close();

// Comprobar que se encontró el usuario
if (!$idUsuario) {
    echo "<p class='error'>Usuario no encontrado.</p>";
    require 'footer.php';
    exit;
}

// Obtener anuncios del usuario
$sql = "SELECT IdAnuncio, Titulo FROM Anuncios WHERE Usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$stmt->bind_result($idAnuncio, $titulo);

$anuncios = [];
while ($stmt->fetch()) {
    $anuncios[] = ["id" => $idAnuncio, "titulo" => $titulo];
}

$stmt->close();
$conn->close();
?>

<h2>Solicitud de Folleto Publicitario Impreso</h2>

<p>
    En esta sección puede solicitar la impresión de un folleto publicitario personalizado basado en uno de sus anuncios. 
    El coste total dependerá del número de páginas, número de fotos, tipo de impresión y resolución de las imágenes. 
    El coste de procesamiento y envío es fijo y se añade al importe final.
</p>

<!-- Tabla de precios -->
<table border="1" cellpadding="6">
    <thead>
        <tr>
            <th>Número de páginas</th>
            <th>Número de fotos</th>
            <th>Blanco y negro (150-300 DPI)</th>
            <th>Blanco y negro (450-900 DPI)</th>
            <th>Color (150-300 DPI)</th>
            <th>Color (450-900 DPI)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $precios = [
            "bn_150" => 12.00,
            "bn_450" => 12.60,
            "col_150" => 13.50,
            "col_450" => 14.10
        ];

        $incrementos = [
            "bn_150" => 2.00,
            "bn_450" => 2.60,
            "col_150" => 3.50,
            "col_450" => 4.10
        ];

        for ($paginas = 1; $paginas <= 15; $paginas++) {
            $fotos = $paginas * 3;

            $precio_bn_150 = $precios["bn_150"] + ($paginas - 1) * $incrementos["bn_150"];
            $precio_bn_450 = $precios["bn_450"] + ($paginas - 1) * $incrementos["bn_450"];
            $precio_col_150 = $precios["col_150"] + ($paginas - 1) * $incrementos["col_150"];
            $precio_col_450 = $precios["col_450"] + ($paginas - 1) * $incrementos["col_450"];

            echo "<tr>
                <td>$paginas</td>
                <td>$fotos</td>
                <td>" . number_format($precio_bn_150, 2, ',', '.') . " €</td>
                <td>" . number_format($precio_bn_450, 2, ',', '.') . " €</td>
                <td>" . number_format($precio_col_150, 2, ',', '.') . " €</td>
                <td>" . number_format($precio_col_450, 2, ',', '.') . " €</td>
            </tr>";
        }
        ?>
    </tbody>
</table>

<p><strong>Coste fijo de procesamiento y envío:</strong> 5 €</p>
<hr>

<!-- Formulario -->
<form action="respuesta_folleto.php" method="post">
    <label for="paginas">Número de páginas:</label><br>
    <select name="paginas" id="paginas" required>
        <?php for($i=1; $i<=15; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select><br><br>

    <label for="fotos">Número de fotos:</label><br>
    <select name="fotos" id="fotos" required>
        <?php for($i=3; $i<=45; $i+=3): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select><br><br>

    <!-- Datos personales -->
    <label for="nombre">Nombre completo:</label><br>
    <input type="text" id="nombre" name="nombre" maxlength="200" required><br><br>

    <label for="email">Correo electrónico:</label><br>
    <input type="email" id="email" name="email" maxlength="200" required><br><br>

    <label for="texto">Texto adicional (opcional):</label><br>
    <textarea id="texto" name="texto" rows="5" cols="50" maxlength="4000" placeholder="Información complementaria..."></textarea><br><br>

    <!-- Dirección postal -->
    <fieldset>
        <legend>Dirección postal</legend>
        <label for="calle">Calle:</label><br>
        <input type="text" id="calle" name="calle" required><br><br>

        <label for="numero">Número:</label><br>
        <input type="text" id="numero" name="numero" required><br><br>

        <label for="piso">Piso / Puerta:</label><br>
        <input type="text" id="piso" name="piso"><br><br>

        <label for="cp">Código postal:</label><br>
        <input type="text" id="cp" name="cp" required><br><br>

        <label for="localidad">Localidad:</label><br>
        <input type="text" id="localidad" name="localidad" required><br><br>

        <label for="provincia">Provincia:</label><br>
        <input type="text" id="provincia" name="provincia" required><br><br>

        <label for="pais">País:</label><br>
        <select id="pais" name="pais" required>
            <option value="">Seleccione un país</option>
            <option value="es">España</option>
            <option value="mx">México</option>
            <option value="ar">Argentina</option>
            <option value="cl">Chile</option>
            <option value="co">Colombia</option>
            <option value="us">Estados Unidos</option>
        </select><br><br>
    </fieldset>

    <label for="telefono">Teléfono (opcional):</label><br>
    <input type="tel" id="telefono" name="telefono" pattern="[0-9+ ]*"><br><br>

    <label for="color_portada">Color de la portada:</label><br>
    <input type="color" id="color_portada" name="color_portada" value="#000000"><br><br>

    <label for="copias">Número de copias:</label><br>
    <input type="number" id="copias" name="copias" min="1" max="99" value="1"><br><br>

    <label for="resolucion">Resolución (DPI):</label><br>
    <input type="number" id="resolucion" name="resolucion" min="150" max="900" step="150" value="150"><br><br>

    <label for="anuncio">Anuncio base:</label><br>
    <select id="anuncio" name="anuncio" required>
        <option value="">Seleccione uno de sus anuncios</option>
        <?php foreach($anuncios as $an): ?>
            <option value="<?= $an['id'] ?>"><?= htmlspecialchars($an['titulo']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Tipo de impresión:</label><br>
    <input type="radio" id="bn" name="color_impresion" value="blanco_negro" required>
    <label for="bn">Blanco y negro</label>
    <input type="radio" id="color" name="color_impresion" value="color">
    <label for="color">A color</label><br><br>

    <label>¿Mostrar precio en el folleto?</label><br>
    <input type="radio" id="con_precio" name="mostrar_precio" value="si" required>
    <label for="con_precio">Sí</label>
    <input type="radio" id="sin_precio" name="mostrar_precio" value="no">
    <label for="sin_precio">No</label><br><br>

    <button type="submit">Enviar solicitud</button>
</form>

<?php require 'footer.php'; ?>

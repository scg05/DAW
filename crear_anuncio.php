<?php
    $mensaje_error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $titulo = trim($_POST['titulo']);
        $ciudad = trim($_POST['ciudad']);
        $pais = trim($_POST['pais']);
        $precio = trim($_POST['precio']);

        if ($titulo == "" || $ciudad == "" || $pais == "" || $precio == "") {
            $mensaje_error = "Debes completar todos los campos.";
        } else {
            // Aquí iría el "guardado" del anuncio
            header("Location: mis_anuncios.php");
            exit;
        }
    }
?>

<?php require 'header.php'; ?>

    <h1>Crear nuevo anuncio</h1>
     <?php if ($mensaje_error != "") {
        echo "<p style='color:red'>$mensaje_error</p>";
    } ?>

    <form action="mis_anuncios.php" method="post" novalidate>
        <label>Título:</label>
        <input type="text" name="titulo"><br>

        <label>Ciudad:</label>
        <input type="text" name="ciudad"><br>

        <label>País:</label>
        <input type="text" name="pais"><br>

        <label>Precio:</label>
        <input type="number" name="precio"><br>

        <label>Tipo de vivienda:</label>
        <select name="tipo">
            <option value="Piso">Piso</option>
            <option value="Casa">Casa</option>
            <option value="Chalet">Chalet</option>
        </select><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion"></textarea><br>

        <input type="submit" value="Crear anuncio">
    </form>
    
<?php require 'footer.php'; ?>

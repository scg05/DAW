<?php
    $titulo = isset($_GET['titulo']) ? $_GET['titulo'] : "";
    $mensaje_error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $titulo_foto = trim($_POST['titulo_foto']);
        $alt = trim($_POST['alt']);
        $anuncio = trim($_POST['anuncio']);

        if ($titulo_foto == "" || strlen($alt) < 10) {
            $mensaje_error = "Datos de la foto incompletos o texto alternativo muy corto.";
        } else {
            echo "<p>Foto añadida correctamente al anuncio '$anuncio'.</p>";
            header("Location: mis_anuncios.php");
            exit;
        }
    }
?>
<?php require 'header.php'; ?>
    
    <h1>Añadir foto a anuncio</h1>
    <?php
    if ($mensaje_error != "") {
        echo "<p style='color:red'>" . $mensaje_error . "</p>";
    }
    ?>

    <form action="" method="post" enctype="multipart/form-data" novalidate>
        <label>Título de la foto:</label>
        <input type="text" name="titulo_foto"><br>

        <label>Texto alternativo (mínimo 10 caracteres):</label>
        <input type="text" name="alt"><br>

        <label>Selecciona anuncio:</label>
        <select name="anuncio" <?php echo $titulo != "" ? "disabled" : "" ?>>
            <?php if ($titulo != "") {
                echo "<option selected>" . $titulo . "</option>";
            } else {
                echo "<option value=''>-- Selecciona anuncio --</option>";
                echo "<option>Apartamento en el centro</option>";
                echo "<option>Chalet con jardín</option>";
            } ?>
        </select><br>

        <label>Archivo de imagen:</label>
        <input type="file" name="foto"><br>

        <input type="submit" value="Añadir foto">
    </form>

<?php require 'footer.php'; ?>

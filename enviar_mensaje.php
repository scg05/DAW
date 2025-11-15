<?php $pageStyles = ["css/enviarmensaje.css"];
    require 'header.php'; 
    require 'conexion.php';
?>

<main class="container">
    <h1 class="titulo-faculty">Enviar mensaje al anunciante</h1>

    <?php
    
    $tipos=[];
    $consulta="SELECT IdTMensaje, NomTMensaje FROM TiposMensajes ORDER BY NomTMensaje ASC";
    $resultado=$conn->query($consulta);

    if($resultado && $resultado->num_rows>0){
        while($fila=$resultado->fetch_assoc()){
            $tipos[]=$fila;
        }
    } else{
        echo "<p class='error'>No se pudieron cargar los tipos de mensaje.</p>";
    }
    ?>

    <form action="mensaje.php" method="post">
        <label for="tipo">Tipo de mensaje:</label>
        <select id="tipo" name="tipo" required>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= htmlspecialchars($t['IdTMensaje']) ?>"><?= htmlspecialchars($t['NomTMensaje']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="mensaje">Mensaje:</label>
        <textarea id="mensaje" name="mensaje" rows="5" required></textarea>

        <button type="submit">Enviar</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $tipo = $_POST["tipo"] ?? "";
        $mensaje = trim($_POST["mensaje"] ?? "");

        $errores = [];

        if ($tipo === "" || !ctype_digit((string)$tipo)) {
            $errores[] = "Debes seleccionar un tipo de mensaje válido.";
        }

        if ($mensaje === "") {
            $errores[] = "El texto del mensaje no puede estar vacío.";
        }

        if (!empty($errores)) {
            echo "<div class='error'>";
            echo "<h3>❌ Se encontraron errores:</h3><ul>";
            foreach ($errores as $e) {
                echo "<li>" . htmlspecialchars($e) . "</li>";
            }
            echo "</ul></div>";
        } else {
            echo "<div class='exito'>";
            echo "<h3> Mensaje enviado correctamente</h3>";
            echo "<p><strong>Tipo:</strong> $tipo</p>";
            echo "<p><strong>Mensaje:</strong> $mensaje</p>";
            echo "</div>";
        }
    }
    $conn->close();
    ?>
</main>

<?php require 'footer.php'; ?>

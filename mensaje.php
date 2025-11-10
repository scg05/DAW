<?php $pageStyles = ["css/enviarmensaje.css"]; require 'header.php'; ?>

<main class="container">
    <h1 class="titulo-faculty">Enviar mensaje al anunciante</h1>

    <form action="mensaje.php" method="post">
        <label for="tipo">Tipo de mensaje:</label>
        <select id="tipo" name="tipo" required>
            <option value="informacion">Más información</option>
            <option value="cita">Solicitar una cita</option>
            <option value="oferta">Comunicar una oferta</option>
        </select>

        <label for="mensaje">Mensaje:</label>
        <textarea id="mensaje" name="mensaje" rows="5" required></textarea>

        <button type="submit">Enviar</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Valores válidos según tu formulario
        $tipo_valido = ["informacion", "cita", "oferta"];

        $tipo = $_POST["tipo"] ?? "";
        $mensaje = trim($_POST["mensaje"] ?? "");

        $errores = [];

        if (!in_array($tipo, $tipo_valido)) {
            $errores[] = "Tipo de mensaje no válido.";
        }

        if ($mensaje === "") {
            $errores[] = "El texto del mensaje no puede estar vacío.";
        }

        if (!empty($errores)) {
            echo "<div class='error'>";
            echo "<h3>❌ Se encontraron errores:</h3><ul>";
            foreach ($errores as $e) {
                echo "<li>$e</li>";
            }
            echo "</ul></div>";
        } else {
            echo "<div class='exito'>";
            echo "<h3>✅ Mensaje enviado correctamente</h3>";
            echo "<p><strong>Tipo:</strong> $tipo</p>";
            echo "<p><strong>Mensaje:</strong> $mensaje</p>";
            echo "</div>";
        }
    }
    ?>
</main>

<?php require 'footer.php'; ?>

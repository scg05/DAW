<?php 
$pageStyles = ["css/enviarmensaje.css"];
require 'header.php'; 
require 'conexion.php';

// Obtener IdAnuncio
$anuncioId = $_GET["id"] ?? null;

if (!$anuncioId || !ctype_digit($anuncioId)) {
    echo "<main class='container'><p class='error'>ID de anuncio no válido.</p></main>";
    require 'footer.php';
    exit;
}

// Cargar tipos de mensajes
$tipos = [];
$consulta = "SELECT IdTMensaje, NomTMensaje FROM TiposMensajes ORDER BY NomTMensaje ASC";
$resultado = $conn->query($consulta);

while ($fila = $resultado->fetch_assoc()) {
    $tipos[] = $fila;
}
?>

<main class="container">
    <h1 class="titulo-faculty">Enviar mensaje al anunciante</h1>

    <form action="respuesta_mensaje.php" method="post">
        
        <!-- ID del anuncio oculto -->
        <input type="hidden" name="anuncio" value="<?= htmlspecialchars($anuncioId) ?>">

        <label for="tipo">Tipo de mensaje:</label>
        <select id="tipo" name="tipo" required>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= htmlspecialchars($t['IdTMensaje']) ?>">
                    <?= htmlspecialchars($t['NomTMensaje']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="mensaje">Mensaje:</label>
        <textarea id="mensaje" name="mensaje" rows="5" required></textarea>

        <button type="submit">Enviar</button>
    </form>

</main>

<?php require 'footer.php'; ?>

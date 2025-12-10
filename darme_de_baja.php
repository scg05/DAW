<?php
$pageStyles = ["css/usuario.css"];
require 'header.php';
require 'conexion.php';

// 1. Comprobar sesión iniciada
if (!isset($_SESSION['usuario'])) {
    echo "<p class='error'>Debes iniciar sesión.</p>";
    require 'footer.php';
    exit;
}

$nomUsuario = $_SESSION['usuario'];

// Obtener datos del usuario
$sql = "SELECT IdUsuario, Clave FROM Usuarios WHERE NomUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nomUsuario);
$stmt->execute();
$res = $stmt->get_result();
$usuario = $res->fetch_assoc();

if (!$usuario) {
    echo "<p class='error'>No se pudo obtener la información del usuario.</p>";
    require 'footer.php';
    exit;
}

$idUsuario = $usuario['IdUsuario'];
$claveReal = $usuario['Clave'];

// 2. Si se ha enviado la confirmación
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmar"])) {

    $claveIntroducida = $_POST["clave"] ?? "";

    if ($claveIntroducida !== $claveReal) {
        echo "<p class='error'>La contraseña introducida no es correcta.</p>";
    } else {

        //Borrar fotos asociadas a sus anuncios
        $conn->query("
            DELETE Fotos FROM Fotos 
            INNER JOIN Anuncios ON Fotos.Anuncio = Anuncios.IdAnuncio
            WHERE Anuncios.Usuario = $idUsuario
        ");

        //Borrar anuncios del usuario
        $conn->query("DELETE FROM Anuncios WHERE Usuario = $idUsuario");

        //Borrar mensajes donde participa
        $conn->query("DELETE FROM Mensajes WHERE UsuOrigen = $idUsuario OR UsuDestino = $idUsuario");
        $conn->query("DELETE FROM Mensajes WHERE UsuOrigen = $idUsuario OR UsuDestino = $idUsuario");

        // 2.4 Borrar solicitudes asociadas a sus anuncios borrados
        $conn->query("
            DELETE Solicitudes FROM Solicitudes
            WHERE Anuncio IS NULL OR Anuncio IN (SELECT IdAnuncio FROM Anuncios WHERE Usuario = $idUsuario)
        ");
        //Borrar usuario
        $stmtDel = $conn->prepare("DELETE FROM Usuarios WHERE IdUsuario = ?");
        $stmtDel->bind_param("i", $idUsuario);
        $stmtDel->execute();

        session_destroy();

        echo "<main class='container'><h1>Cuenta eliminada</h1>
              <p>Tu cuenta y todos tus datos han sido eliminados correctamente.</p>
              <a href='index.php'>Volver a la página principal</a></main>";

        require 'footer.php';
        exit;
    }
}

// 3. Obtener resumen de información para mostrar

// Obtener anuncios del usuario
$sqlAnuncios = "
    SELECT A.IdAnuncio, A.Titulo, COUNT(F.IdFoto) AS NumFotos
    FROM Anuncios A
    LEFT JOIN Fotos F ON A.IdAnuncio = F.Anuncio
    WHERE A.Usuario = ?
    GROUP BY A.IdAnuncio
";
$stmtA = $conn->prepare($sqlAnuncios);
$stmtA->bind_param("i", $idUsuario);
$stmtA->execute();
$resAnuncios = $stmtA->get_result();

$anuncios = [];
$totalFotos = 0;

while ($row = $resAnuncios->fetch_assoc()) {
    $anuncios[] = $row;
    $totalFotos += $row["NumFotos"];
}

$totalAnuncios = count($anuncios);

?>

<main class="container">
    <h1 class="titulo-faculty">Darse de baja</h1>

    <p>Estás a punto de eliminar tu cuenta. Esta acción es <strong>irreversible</strong>.</p>

    <h2>Resumen de tus datos</h2>

    <p><strong>Total de anuncios:</strong> <?= $totalAnuncios ?></p>
    <p><strong>Total de fotos:</strong> <?= $totalFotos ?></p>

    <h3>Listado de anuncios</h3>

    <?php if ($totalAnuncios === 0): ?>
        <p>No tienes anuncios publicados.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($anuncios as $a): ?>
                <li><strong><?= htmlspecialchars($a['Titulo']) ?></strong> — <?= $a['NumFotos'] ?> fotos</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr>

    <h2>Confirmación</h2>
    <p>Para confirmar la eliminación de tu cuenta, introduce tu contraseña actual:</p>

    <form action="darme_de_baja.php" method="post">
        <label for="clave">Contraseña:</label>
        <input type="password" name="clave" id="clave" required>

        <input type="hidden" name="confirmar" value="1">

        <button type="submit" style="background:red;color:white;">Eliminar mi cuenta</button>
    </form>

    <br>
    <a href="menuusu.php">Cancelar y volver</a>
</main>

<?php require 'footer.php'; ?>

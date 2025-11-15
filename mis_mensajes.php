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

// Obtener mensajes enviados
$sqlEnviados = "
    SELECT M.IdMensaje, TM.NomTMensaje, M.Texto, M.FRegistro, A.Titulo AS TituloAnuncio
    FROM Mensajes M
    LEFT JOIN TiposMensajes TM ON TM.IdTMensaje = M.TMensaje
    LEFT JOIN Anuncios A ON A.IdAnuncio = M.Anuncio
    WHERE M.UsuOrigen = ?
    ORDER BY M.FRegistro DESC
";
$stmt = $conn->prepare($sqlEnviados);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$enviados = $stmt->get_result();
$totalEnviados = $enviados->num_rows;

// Obtener mensajes recibidos
$sqlRecibidos = "
    SELECT M.IdMensaje, TM.NomTMensaje, M.Texto, M.FRegistro, A.Titulo AS TituloAnuncio
    FROM Mensajes M
    LEFT JOIN TiposMensajes TM ON TM.IdTMensaje = M.TMensaje
    LEFT JOIN Anuncios A ON A.IdAnuncio = M.Anuncio
    WHERE M.UsuDestino = ?
    ORDER BY M.FRegistro DESC
";
$stmt = $conn->prepare($sqlRecibidos);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$recibidos = $stmt->get_result();
$totalRecibidos = $recibidos->num_rows;

?>
<main class="container">
    <h1 class="titulo-faculty">Mis mensajes</h1>

    <h2>Mensajes enviados (<?= $totalEnviados ?>)</h2>

    <?php if ($totalEnviados == 0): ?>
        <p>No has enviado mensajes.</p>
    <?php else: ?>
        <ul class="lista-mensajes">
            <?php while ($m = $enviados->fetch_assoc()): ?>
                <li>
                    <strong><?= htmlspecialchars($m["NomTMensaje"]) ?></strong><br>
                    <em><?= htmlspecialchars($m["FRegistro"]) ?></em><br>
                    <strong>Anuncio:</strong> <?= htmlspecialchars($m["TituloAnuncio"] ?? "Sin anuncio") ?><br>
                    <?= nl2br(htmlspecialchars($m["Texto"])) ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>

    <hr>

    <h2>Mensajes recibidos (<?= $totalRecibidos ?>)</h2>

    <?php if ($totalRecibidos == 0): ?>
        <p>No has recibido mensajes.</p>
    <?php else: ?>
        <ul class="lista-mensajes">
            <?php while ($m = $recibidos->fetch_assoc()): ?>
                <li>
                    <strong><?= htmlspecialchars($m["NomTMensaje"]) ?></strong><br>
                    <em><?= htmlspecialchars($m["FRegistro"]) ?></em><br>
                    <strong>Anuncio:</strong> <?= htmlspecialchars($m["TituloAnuncio"] ?? "Sin anuncio") ?><br>
                    <?= nl2br(htmlspecialchars($m["Texto"])) ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>

</main>

<?php require 'footer.php'; ?>

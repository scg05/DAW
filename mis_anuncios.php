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

// Consulta de anuncios del usuario
$sql = "
    SELECT 
        A.IdAnuncio,
        A.Titulo,
        A.Precio,
        A.Ciudad,
        P.Nombre AS Pais,
        A.FPrincipal AS FotoPrincipal
    FROM Anuncios A
    LEFT JOIN Paises P ON P.IdPais = A.Pais
    WHERE A.Usuario = ?
    ORDER BY A.FRegistro DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

$anuncios = [];
if ($result && $result->num_rows > 0) {
    while ($fila = $result->fetch_assoc()) {
        $anuncios[] = $fila;
    }
}

$conn->close();
?>

<main class="container">

<h1>Mis Anuncios</h1>

<a href="crear_anuncio.php" class="boton-crear">Crear nuevo anuncio</a>

<div class="anuncios">

    <?php if (empty($anuncios)): ?>
        <p>No has creado ningún anuncio todavía.</p>
    <?php else: ?>
        <?php foreach ($anuncios as $an): ?>
            <div class="anuncio">

                <img src="<?= htmlspecialchars($an['FotoPrincipal']) ?>" 
                     alt="<?= htmlspecialchars($an['Titulo']) ?>" width="150">

                <h3><?= htmlspecialchars($an['Titulo']) ?></h3>
                <p><?= htmlspecialchars($an['Ciudad']) ?>, <?= htmlspecialchars($an['Pais']) ?></p>
                <p><strong><?= number_format($an['Precio'], 0, ",", ".") ?> €</strong></p>

                <a href="ver_anuncio.php?id=<?= $an['IdAnuncio'] ?>">Ver anuncio</a>
                <a href="anadir_foto.php?id=<?= $an['IdAnuncio'] ?>">Añadir foto</a>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</main>

<?php require 'footer.php'; ?>
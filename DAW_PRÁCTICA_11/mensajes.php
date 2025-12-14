<?php
require 'header.php';
require 'conexion.php';

if(!isset($_SESSION['usuario'])){
    echo "<p class='error'>Debes iniciar sesión para ver tus mensajes.</p>";
    require 'footer.php';
    exit;
}

// Obtener ID usuario
$usuario = $_SESSION['usuario'];
$sql_user = "SELECT IdUsuario FROM Usuarios WHERE NomUsuario = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$res = $stmt->get_result();
$u = $res->fetch_assoc();
$idUsuario = $u['IdUsuario'];

// Obtener los anuncios del usuario
$sql_anuncios = "
    SELECT IdAnuncio, Titulo, FRegistro
    FROM Anuncios
    WHERE Usuario = ?
    ORDER BY FRegistro DESC";
$stmtA = $conn->prepare($sql_anuncios);
$stmtA->bind_param("i", $idUsuario);
$stmtA->execute();
$anuncios = $stmtA->get_result();
?>

<main class="container">
<h1 class="titulo-faculty">Mensajes recibidos</h1>

<?php
if ($anuncios->num_rows == 0){
    echo "<p>No tienes anuncios publicados.</p>";
} else {

    while($an = $anuncios->fetch_assoc()){
        $idA = $an['IdAnuncio'];

        echo "<h2>{$an['Titulo']}</h2>";

        // Contar mensajes recibidos
        $sql_count = "SELECT COUNT(*) AS total FROM Mensajes WHERE Anuncio = ? AND UsuDestino = ?";
        $stmtC = $conn->prepare($sql_count);
        $stmtC->bind_param("ii", $idA, $idUsuario);
        $stmtC->execute();
        $resCount = $stmtC->get_result()->fetch_assoc();
        $total = $resCount['total'];

        echo "<p>Total de mensajes recibidos: <strong>$total</strong></p>";

        if ($total > 0) {

            // Mensajes
            $sql_m = "
                SELECT M.Texto, M.FRegistro, U.NomUsuario AS Origen
                FROM Mensajes M
                LEFT JOIN Usuarios U ON M.UsuOrigen = U.IdUsuario
                WHERE M.Anuncio = ? AND M.UsuDestino = ?
                ORDER BY M.FRegistro DESC";
            $stmtM = $conn->prepare($sql_m);
            $stmtM->bind_param("ii", $idA, $idUsuario);
            $stmtM->execute();
            $mensajes = $stmtM->get_result();

            echo "<ul>";
            while($m = $mensajes->fetch_assoc()){
                $fecha = date("d/m/Y H:i", strtotime($m['FRegistro']));
                $origen = htmlspecialchars($m['Origen']);
                $texto = htmlspecialchars($m['Texto']);
                echo "<li><strong>$origen</strong> — $fecha<br>$texto</li><br>";
            }
            echo "</ul>";
        }

        echo "<hr>";
    }
}
?>
</main>

<?php require 'footer.php'; ?>
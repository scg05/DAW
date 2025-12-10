<?php
require 'conexion.php';

// Asegurar sesión iniciada
if (session_status() === PHP_SESSION_NONE) session_start();

// Debe existir un usuario logueado
if(!isset($_SESSION['usuario'])){
    $pageStyles = [];
    require 'header.php';
    echo "<p class='error'>Debes iniciar sesión para acceder.</p>";
    require 'footer.php';
    exit;
}

$usuario = $_SESSION['usuario'];

// Obtener datos del usuario
$sql_user = "SELECT IdUsuario, Estilo FROM Usuarios WHERE NomUsuario = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$res_user = $stmt->get_result();
$userData = $res_user->fetch_assoc();

$idUsuario = $userData['IdUsuario'];
$estiloActual = $userData['Estilo'];

// Si el usuario guarda un nuevo estilo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nuevoEstilo = intval($_POST['estilo']);

    // Guardar en BD
    $sql_upd = "UPDATE Usuarios SET Estilo = ? WHERE IdUsuario = ?";
    $stmt2 = $conn->prepare($sql_upd);
    $stmt2->bind_param("ii", $nuevoEstilo, $idUsuario);
    $stmt2->execute();

    // ► Actualizar la sesión para que header.php cargue YA el estilo nuevo
    $_SESSION['estilo_usuario'] = $nuevoEstilo;

    // ► Forzar recarga inmediata con el estilo aplicado
    header("Location: configurar.php?ok=1");
    exit;
}

// Obtener lista de estilos
$sql_estilos = "SELECT IdEstilo, Nombre, Descripcion FROM Estilos ORDER BY Nombre ASC";
$estilos = $conn->query($sql_estilos);

// (Muy importante) Establecer el estilo activo para header.php
$pageStyles = [];
$_SESSION['estilo_usuario'] = $estiloActual;

require 'header.php';
?>

<main class="container">
    <h1 class="titulo-faculty">Configurar estilo</h1>

    <?php if (isset($_GET['ok'])): ?>
        <p class="mensaje exito">Tu estilo se ha actualizado.</p>
    <?php endif; ?>

    <form action="configurar.php" method="post">
        <h3>Estilos disponibles:</h3>

        <?php
        if ($estilos && $estilos->num_rows > 0) {
            while ($row = $estilos->fetch_assoc()) {

                $id = $row['IdEstilo'];
                $nombre = htmlspecialchars($row['Nombre']);
                $descripcion = htmlspecialchars($row['Descripcion']);

                $checked = ($id == $estiloActual) ? "checked" : "";

                echo "
                    <div class='estilo-opcion'>
                        <input type='radio' name='estilo' value='$id' id='estilo$id' $checked>
                        <label for='estilo$id'><strong>$nombre</strong> — $descripcion</label>
                    </div><br>
                ";
            }
        }
        ?>
        <br>
        <button type="submit">Guardar cambios</button>
    </form>
</main>

<?php
$conn->close();
require 'footer.php';
?>

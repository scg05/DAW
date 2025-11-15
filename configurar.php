<?php
require 'header.php';
require 'conexion.php';

// Debe existir un usuario logueado
if(!isset($_SESSION['usuario'])){
    echo "<p class='error'>Debes iniciar sesión para acceder.</p>";
    require 'footer.php';
    exit;
}

// Obtener datos del usuario actual
$usuario = $_SESSION['usuario'];
$sql_user = "SELECT IdUsuario, Estilo FROM Usuarios WHERE NomUsuario = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$res_user = $stmt->get_result();
$userData = $res_user->fetch_assoc();

$idUsuario = $userData['IdUsuario'];
$estiloActual = $userData['Estilo'];

// Si el usuario ha enviado un nuevo estilo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoEstilo = intval($_POST['estilo']);

    $sql_upd = "UPDATE Usuarios SET Estilo = ? WHERE IdUsuario = ?";
    $stmt2 = $conn->prepare($sql_upd);
    $stmt2->bind_param("ii", $nuevoEstilo, $idUsuario);
    $stmt2->execute();

    echo "<p class='mensaje'>Tu estilo se ha actualizado correctamente.</p>";
    $estiloActual = $nuevoEstilo; // Actualiza visualmente
}

// Obtener lista de estilos
$sql_estilos = "SELECT IdEstilo, Nombre, Descripcion FROM Estilos ORDER BY Nombre ASC";
$estilos = $conn->query($sql_estilos);
?>

<main class="container">
    <h1 class="titulo-faculty">Configurar estilo</h1>

    <form action="configurar.php" method="post">
        <h3>Estilos disponibles:</h3>

        <?php
        if ($estilos && $estilos->num_rows > 0) {
            while ($row = $estilos->fetch_assoc()) {
                $id = $row['IdEstilo'];
                $nombre = htmlspecialchars($row['Nombre']);
                $desc = htmlspecialchars($row['Descripcion']);

                $checked = ($id == $estiloActual) ? "checked" : "";

                echo "
                    <div class='estilo-opcion'>
                        <input type='radio' name='estilo' value='$id' id='estilo$id' $checked>
                        <label for='estilo$id'><strong>$nombre</strong> — $desc</label>
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

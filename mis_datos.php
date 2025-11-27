<?php
require 'header.php';
require 'conexion.php';

if(!isset($_SESSION['usuario'])){
    echo "<p class='error'>Debes iniciar sesión para ver tus datos.</p>";
    require 'footer.php';
    exit;
}

$usuario = $_SESSION['usuario'];

// Obtener todos los datos del usuario
$sql = "SELECT * FROM Usuarios WHERE NomUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>

<main>
<h2>Mis datos</h2>

<form action="#" method="post" id="registroForm" enctype="multipart/form-data">

    <!-- Nombre de usuario -->
    <label for="username">Nombre de usuario:</label><br>
    <input type="text" id="username" name="username"
        value="<?= htmlspecialchars($user['NomUsuario']) ?>" readonly><br><br>

    <!-- Contraseña -->
    <label for="password">Contraseña:</label><br>
    <input type="password" id="password" name="password" value=""><br><br>

    <!-- Repetir contraseña -->
    <label for="confirm_password">Repetir contraseña:</label><br>
    <input type="password" id="confirm_password" name="confirm_password"><br><br>

    <!-- Email -->
    <label for="email">Dirección de email:</label><br>
    <input type="text" id="email" name="email"
        value="<?= htmlspecialchars($user['Email']) ?>"><br><br>

    <!-- Sexo -->
    <label>Sexo:</label><br>
    <input type="radio" name="sexo" value="M" <?= ($user['Sexo']==1?'checked':'') ?>> Masculino
    <input type="radio" name="sexo" value="F" <?= ($user['Sexo']==2?'checked':'') ?>> Femenino
    <input type="radio" name="sexo" value="O" <?= ($user['Sexo']==3?'checked':'') ?>> Otro<br><br>

    <!-- Fecha de nacimiento -->
    <label for="fecha_nacimiento">Fecha de nacimiento:</label><br>
    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
        value="<?= htmlspecialchars($user['FNacimiento']) ?>"><br><br>

    <!-- Ciudad -->
    <label for="ciudad">Ciudad de residencia:</label><br>
    <input type="text" id="ciudad" name="ciudad"
        value="<?= htmlspecialchars($user['Ciudad']) ?>"><br><br>

    <!-- País -->
    <label for="pais">País de residencia:</label><br>
    <select id="pais" name="pais">
        <?php
        $paises = $conn->query("SELECT IdPais, Nombre FROM Paises ORDER BY Nombre ASC");
        while($row = $paises->fetch_assoc()){
            $sel = ($row['IdPais']==$user['Pais']) ? "selected" : "";
            echo "<option value='{$row['IdPais']}' $sel>".htmlspecialchars($row['Nombre'])."</option>";
        }
        ?>
    </select><br><br>

    <!-- Foto -->
    <label for="foto">Foto de perfil:</label><br>
    <input type="file" id="foto" name="foto" accept="image/*"><br><br>

    <p>* Los cambios no se guardan todavía.</p>

</form>
</main>

<?php require 'footer.php'; ?>
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
$stmt->close();

// Definir el ícono por defecto (placeholder) y la ruta base de las fotos
$ICONO_DEFECTO = "img/icon-user-placeholder.png"; 
$DIR_FOTOS_PERFIL = "uploads/perfil/";

// Determinar la foto actual (BD o icono por defecto)
$fotoActual = empty($user['Foto']) ? $ICONO_DEFECTO : $DIR_FOTOS_PERFIL . htmlspecialchars($user['Foto']);
?>

<main>
<h2>Mis datos</h2>

<form action="respuesta_mis_datos.php" method="post" id="registroForm" enctype="multipart/form-data">

    <fieldset>
        <legend>Foto de perfil</legend>
        <p>
            <img src="<?= $fotoActual ?>" alt="Foto de perfil actual" style="max-width: 100px; border-radius: 50%;"><br>
        </p>
        <?php if (!empty($user['Foto'])): ?>
            <input type="checkbox" id="eliminar_foto" name="eliminar_foto" value="si">
            <label for="eliminar_foto">Eliminar foto de perfil actual </label><br><br>
        <?php endif; ?>

        <label for="nueva_foto">Subir nueva foto (sustituirá a la anterior):</label><br>
        <input type="file" id="nueva_foto" name="nueva_foto" accept="image/*"><br><br>
    </fieldset>

    <!-- Nombre de usuario -->
    <label for="username">Nombre de usuario:</label><br>
    <input type="text" id="username" name="username"
        value="<?= htmlspecialchars($user['NomUsuario']) ?>"><br><br>

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

    <fieldset>
        <legend>Cambiar Contraseña</legend>
        <p>Rellena solo si deseas cambiar tu contraseña.</p>
        <label for="nueva_password">Nueva Contraseña:</label><br>
        <input type="password" id="nueva_password" name="nueva_password" value=""><br><br>

        <label for="confirmar_nueva_password">Repetir Nueva Contraseña:</label><br>
        <input type="password" id="confirmar_nueva_password" name="confirmar_nueva_password"><br><br>
    </fieldset>

    <hr>
    <label for="password_actual">Para confirmar los cambios, introduce tu contraseña actual:</label><br>
    <input type="password" id="password_actual" name="password_actual" required><br><br>

    <input type="submit" value="Guardar cambios">
</form>
</main>

<?php 
$conn->close();
require 'footer.php'; ?>
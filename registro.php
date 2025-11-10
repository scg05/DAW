<?php $pageStyles = ["css/registro.css"]; require 'header.php'; ?>

        <main>
        <h2>Formulario de Registro</h2>
        <form action="respuesta_registro.php" method="post" id="registroForm" enctype="multipart/form-data">
            <!-- Nombre de usuario -->
            <label for="username">Nombre de usuario:</label><br>
            <input type="text" id="username" name="username"><br><br>
            <span id="username-error" class="error"></span><br><br>

            <!-- Contraseña -->
            <label for="password">Contraseña:</label><br>
            <input type="password" id="password" name="password"><br><br>
            <span id="password-error" class="error"></span><br><br>


            <!-- Repetir contraseña -->
            <label for="confirm_password">Repetir contraseña:</label><br>
            <input type="password" id="confirm_password" name="confirm_password"><br><br>
            <span id="confirm-error" class="error"></span><br><br>


            <!-- Email -->
            <label for="email">Dirección de email:</label><br>
            <input type="text" id="email" name="email"><br><br>
            <span id="email-error" class="error"></span><br><br>

            <!-- Sexo -->
            <label for="sexo">Sexo:</label><br>
            <input type="radio" id="sexo_m" name="sexo" value="M">
            <label for="sexo_m">Masculino</label>
            <input type="radio" id="sexo_f" name="sexo" value="F">
            <label for="sexo_f">Femenino</label>
            <input type="radio" id="sexo_o" name="sexo" value="O">
            <label for="sexo_o">Otro</label><br><br>
            <span id="sexo-error" class="error"></span><br><br>

            <!-- Fecha de nacimiento -->
            <label for="fecha_nacimiento">Fecha de nacimiento:</label><br>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"><br><br>
            <span id="fecha-error" class="error"></span><br><br>

            <!-- Ciudad -->
            <label for="ciudad">Ciudad de residencia:</label><br>
            <input type="text" id="ciudad" name="ciudad"><br><br>

            <!-- País -->
            <label for="pais">País de residencia:</label><br>
            <select id="pais" name="pais">
            <option value="">Seleccione un país</option>
            <option value="es">España</option>
            <option value="mx">México</option>
            <option value="ar">Argentina</option>
            <option value="cl">Chile</option>
            <option value="co">Colombia</option>
            <option value="us">Estados Unidos</option>
            <option value="fr">Francia</option>
            <option value="de">Alemania</option>
            <option value="it">Italia</option>
            <option value="uk">Reino Unido</option>
            <!-- Se pueden añadir más países -->
            </select><br><br>

            <!-- Foto -->
            <label for="foto">Foto de perfil:</label><br>
            <input type="file" id="foto" name="foto" accept="image/*"><br><br>

            <!-- Botón de envío -->
            <input type="submit" value="registrarse"/>
            <span id="error" class="error"></span><br><br>
        </form>
        </main>

<?php require 'footer.php'; ?>
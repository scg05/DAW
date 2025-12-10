<?php
// Recogemos los datos del formulario
$usuario = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$repite   = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$sexo     = isset($_POST['sexo']) ? $_POST['sexo'] : null;
$fecha    = isset($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : '';
$ciudad   = isset($_POST['ciudad']) ? trim($_POST['ciudad']) : '';
$pais     = isset($_POST['pais']) ? $_POST['pais'] : '';

//GESTION DE LA FOTO DE PERFIL
$DIR_FOTOS_PERFIL = "uploads/perfil/"; //donde se guarda
$nombreFicheroDB = ''; //nombre guardado en la base de datos (vacio si no hay foto)

$fotoSubidaExito  = false; 

// Comprobamos si el directorio existe y si no, lo creamos
if (!is_dir($DIR_FOTOS_PERFIL)) {
    // Intentamos crear la carpeta. 0777 es permisivo, true permite recursividad.
    mkdir($DIR_FOTOS_PERFIL, 0777, true); 
}

$errores = [];

//VALIDACION NOMBRE DE USUARIO
if ($usuario === '') {
    $errores[] = "El nombre de usuario es obligatorio.";
} else {
    if (!preg_match('/^[A-Za-z][A-Za-z0-9]{2,14}$/', $usuario)) {
        $errores[] = "El nombre de usuario debe tener entre 3 y 15 caracteres, empezar por una letra y solo puede contener letras y números.";
    }
}

//VALIDACION CONTRASEÑA

if ($password === '') {
    $errores[] = "La contraseña es obligatoria.";
} else {
    // caracteres permitidos y longitud
    if (!preg_match('/^[A-Za-z0-9_-]{6,15}$/', $password)) {
        $errores[] = "La contraseña debe tener entre 6 y 15 caracteres y solo puede contener letras, números, guion y guion bajo.";
    }

    // al menos una mayúscula, una minúscula y un número
    if (!preg_match('/[A-Z]/', $password)) {
        $errores[] = "La contraseña debe contener al menos una letra mayúscula.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errores[] = "La contraseña debe contener al menos una letra minúscula.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errores[] = "La contraseña debe contener al menos un número.";
    }
}

//VALIDACION REPETIR CONTRASEÑA
if ($repite === '') {
    $errores[] = "Debes repetir la contraseña.";
} elseif ($password !== $repite) {
    $errores[] = "Las contraseñas no coinciden.";
}

//4. VALIDACIÓN EMAIL 
if ($email === '') {
    $errores[] = "La dirección de correo es obligatoria.";
} else {
    if (strlen($email) > 254) {
        $errores[] = "La dirección de correo no puede tener más de 254 caracteres.";
    }

    // filtro básico de PHP
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "La dirección de correo no tiene un formato válido.";
    } else {
        // separación parte-local y dominio
        $partes = explode('@', $email, 2);
        if (count($partes) !== 2) {
            $errores[] = "La dirección de correo debe tener una única arroba (@).";
        } else {
            list($local, $domain) = $partes;

            // longitudes
            if (strlen($local) < 1 || strlen(utf8_decode($local)) > 64) {
                $errores[] = "La parte local del correo debe tener entre 1 y 64 caracteres.";
            }
            if (strlen($domain) < 1 || strlen(utf8_decode($domain)) > 255) {
                $errores[] = "El dominio del correo debe tener entre 1 y 255 caracteres.";
            }

            // caracteres permitidos en parte local
            if (!preg_match("/^[A-Za-z0-9!#$%&'*+\/=?^_`{|}~.]+$/", $local)) {
                $errores[] = "La parte local del correo contiene caracteres no permitidos.";
            } else {
                // el punto no al principio, ni al final, ni doble
                if ($local[0] === '.' || substr($local, -1) === '.' || strpos($local, '..') !== false) {
                    $errores[] = "En la parte local del correo el punto no puede estar al principio, al final ni aparecer dos veces seguidas.";
                }
            }

            // dominio: subdominios separados por punto
            $labels = explode('.', $domain);
            foreach ($labels as $label) {
                if ($label === '') {
                    $errores[] = "El dominio del correo no puede contener puntos consecutivos.";
                    break;
                }
                if (strlen(utf8_decode($label)) > 63) {
                    $errores[] = "Cada subdominio del dominio no puede tener más de 63 caracteres.";
                    break;
                }
                // letras, dígitos y guion, sin guion al principio ni al final
                if (!preg_match('/^[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?$/', $label)) {
                    $errores[] = "Cada subdominio del dominio solo puede contener letras, números y guiones, sin guion al principio o al final.";
                    break;
                }
            }
        }
    }
}

//5. SEXO: se debe elegir un valor
if ($sexo === null || !in_array($sexo, ['M', 'F', 'O'], true)) {
    $errores[] = "Debes seleccionar tu sexo.";
}

//6. FECHA DE NACIMIENTO
if ($fecha === '') {
    $errores[] = "La fecha de nacimiento es obligatoria.";
} else {
    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);

    // comprobar que la fecha es válida y coincide exactamente con el formato
    if ($fechaObj === false || $fechaObj->format('Y-m-d') !== $fecha) {
        $errores[] = "La fecha de nacimiento no es una fecha válida.";
    } else {
        $hoy = new DateTime();

        // no puede ser futura
        if ($fechaObj > $hoy) {
            $errores[] = "La fecha de nacimiento no puede ser futura.";
        }

        // comprobar 18 años
        $mayoria = clone $fechaObj;
        $mayoria->add(new DateInterval('P18Y'));
        if ($mayoria > $hoy) {
            $errores[] = "Debes tener al menos 18 años.";
        }
    }
}

//VALIDACION DE LA FOTO (solo si se subió)
if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Error al subir la foto de perfil (Código: " . $_FILES['foto']['error'] . ").";
    }
}

/* A partir de aquí mostramos resultado:
   - Si hay errores: los listamos
   - Si no hay errores: realizamos la inserción en BD
*/
$pageStyles = ["css/enviarmensaje.css"];
require 'header.php';

if (!empty($errores)): ?>
    <main class="container">
    <h1>Errores en el registro</h1>
    <ul>
        <?php foreach ($errores as $e): ?>
            <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
    <p><a href="registro.php">Volver al formulario de registro</a></p>
    </main>

<?php else: 

    require 'conexion.php'; 

    //SUBIR Y MOVER FICHERO
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombreOriginal = $_FILES['foto']['name'];
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        
        // Estrategia Anticolisión: time() + uniqid() + .extensión
        // Esto crea un nombre único basado en la hora, un identificador y la extensión original.
        $nombreFicheroDB = time() . '_' . uniqid() . '.' . $extension;
        $destinoFinal = $DIR_FOTOS_PERFIL . $nombreFicheroDB;
        
        // Mover el archivo temporal a su destino final
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destinoFinal)) {
            $fotoSubidaExito = true;
        } else {
            // Si falla el movimiento (por permisos o ruta), loggeamos el error y continuamos la inserción sin la foto.
            // En un sistema real, esto sería un error crítico. Aquí, por simplicidad, no bloquea el registro.
            error_log("Error al mover la foto de perfil del usuario: " . $usuario);
            $nombreFicheroDB = ''; // Asegurar que no se guarda el nombre si falló el movimiento
        }
    }

    //INSERCION EN BASE DE DATOS
    $sexoInt = ($sexo === 'M') ? 1 : (($sexo === 'F') ? 2 : 3);
    $paisInt = (empty($pais) || $pais === '') ? NULL : (int)$pais; 
    
    // Preparar la consulta de inserción con sentencias preparadas
    $sql = "INSERT INTO Usuarios 
            (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, FRegistro, Estilo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)"; 

    $stmt = $conn->prepare($sql);

    // Tipo de parámetros: NomUsuario(s), Clave(s), Email(s), Sexo(i), FNacimiento(s), Ciudad(s), Pais(i), Foto(s)
    $stmt->bind_param(
        "sssisiss",
        $usuario,
        $password,
        $email,
        $sexoInt,
        $fecha,
        $ciudad,
        $paisInt,
        $nombreFicheroDB // <-- Se pasa el nombre generado o cadena vacía/NULL
    );


    if ($stmt->execute()) {
        ?>
        <main class="container">
            <h1>Registro correcto</h1>
            <p><strong>Usuario:</strong> <?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Tu cuenta ha sido creada correctamente en la base de datos.</p>
            
            <?php if ($fotoSubidaExito): ?>
                <h3>Foto de perfil subida:</h3>
                <img src="<?php echo htmlspecialchars($destinoFinal); ?>" alt="Foto de perfil de <?php echo htmlspecialchars($usuario); ?>" style="max-width: 150px; border-radius: 50%;">
                <p>El nombre guardado en la BD es: <code><?php echo htmlspecialchars($nombreFicheroDB); ?></code></p>
            <?php else: ?>
                <p>No se subió foto de perfil, o la subida falló.</p>
            <?php endif; ?>

            <p><a href="index.php">Ir a la página principal (iniciar sesión)</a></p>
        </main>
        <?php
    } else {
        // En caso de error en la ejecución (ej. nombre de usuario duplicado, clave ajena, etc.)
        ?>
        <main class="container">
            <h1>Error al registrar usuario</h1>
            <p>Ha ocurrido un error al intentar registrar el usuario. Es posible que el nombre de usuario ya esté en uso (UNIQUE INDEX).</p>
            <p style="color:red;">Error de BD: <?= htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8'); ?></p> 
            <p><a href="registro.php">Volver al formulario de registro</a></p>
        </main>
        <?php
    }
    
    $stmt->close();
    $conn->close();


endif; ?>

<?php require 'footer.php'; ?>
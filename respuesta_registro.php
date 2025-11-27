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

$errores = [];

/* 1. VALIDACIÓN NOMBRE DE USUARIO
   - Solo letras inglesas y números
   - No puede comenzar por número
   - Longitud 3–15
*/
if ($usuario === '') {
    $errores[] = "El nombre de usuario es obligatorio.";
} else {
    if (!preg_match('/^[A-Za-z][A-Za-z0-9]{2,14}$/', $usuario)) {
        $errores[] = "El nombre de usuario debe tener entre 3 y 15 caracteres, empezar por una letra y solo puede contener letras y números.";
    }
}

/* 2. VALIDACIÓN CONTRASEÑA
   - Solo letras inglesas, números, guion y guion bajo
   - Al menos una mayúscula, una minúscula y un número
   - Longitud 6–15
*/
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

/* 3. REPETIR CONTRASEÑA: debe coincidir */
if ($repite === '') {
    $errores[] = "Debes repetir la contraseña.";
} elseif ($password !== $repite) {
    $errores[] = "Las contraseñas no coinciden.";
}

/* 4. VALIDACIÓN EMAIL con combinación de:
   - campo obligatorio
   - filter_var
   - comprobaciones manuales según el enunciado
*/
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
            if (strlen($local) < 1 || strlen($local) > 64) {
                $errores[] = "La parte local del correo debe tener entre 1 y 64 caracteres.";
            }
            if (strlen($domain) < 1 || strlen($domain) > 255) {
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
                if (strlen($label) > 63) {
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

/* 5. SEXO: se debe elegir un valor */
if ($sexo === null || !in_array($sexo, ['M', 'F', 'O'], true)) {
    $errores[] = "Debes seleccionar tu sexo.";
}

/* 6. FECHA DE NACIMIENTO:
   - fecha válida
   - al menos 18 años recién cumplidos
*/
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


// El resto de campos (ciudad, país, foto) pueden quedar vacíos según el enunciado.
// NO gestionamos todavía el almacenamiento de la foto.

/* A partir de aquí mostramos resultado:
   - Si hay errores: los listamos
   - Si no hay errores: mensaje de registro correcto
*/
$pageStyles = ["css/enviarmensaje.css"];
require 'header.php';

if (!empty($errores)): ?>
    <h1>Errores en el registro</h1>
    <ul>
        <?php foreach ($errores as $e): ?>
            <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
    <p><a href="registro.php">Volver al formulario de registro</a></p>

<?php else: ?>

    <h1>Registro correcto</h1>
    <p><strong>Usuario:</strong> <?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Tu cuenta ha sido creada correctamente.</p>

<?php endif; ?>

<?php require 'footer.php'; ?>

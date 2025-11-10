<?php
    $usuario = trim($_POST['username']);
    $password = trim($_POST['password']);
    $repite = trim($_POST['confirm_password']);

    if ($usuario == "" || $password == "" || $repite == "") {
        header("Location: registro.php?error=campos_vacios");
        exit;
    }

    if ($password != $repite) {
        header("Location: registro.php?error=no_coinciden");
        exit;
    }
?>
<?php $pageStyles = ["css/enviarmensaje.css"]; require 'header.php'; ?>

    <h1>Registro correcto</h1>
    <p><strong>Usuario:</strong> <?php echo $usuario; ?></p>
    <p>Tu cuenta ha sido creada correctamente.</p>

<?php require 'footer.php'; ?>

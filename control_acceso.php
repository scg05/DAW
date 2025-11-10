<?php
    session_start();

    $usuario=trim($_POST['usuario']);
    $contrasena=trim($_POST['password']);

    if($usuario==''||$contrasena==''){
        header("Location: index.php?error=campos_vacios");
        exit;
    }
    $permitido=false;
    $lineas=file("usuarios.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach($lineas as $linea){
        list($nombre, $clave) = explode(":", $linea);
        if ($usuario === $nombre && $contrasena === $clave) {
            $permitido = true;
            break;
        }
    }

    if($permitido){
        $_SESSION['usuario']=$usuario;
        //marco recuerdame
        if(isset($_POST['recordarme'])&& $_POST['recordarme']=='si'){
            setcookie('recordar_usuario', $usuario, time() + (90*24*60*60));
            setcookie('recordar_password', $contrasena, time() + (90*24*60*60)); //NO SE PUEDE MANDAR EN ABIERTO tiene que haber un hash
             setcookie('ultima_visita', date("c"), time() + (90*24*60*60)); //FALTA COOKIE ESTILO
        }
        header("Location: menuusu.php");
        exit;
    } else {
        header("Location: index.php?error=acceso_denegado");
        exit;
    }
?>
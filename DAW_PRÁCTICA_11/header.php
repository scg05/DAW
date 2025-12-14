<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

$estilosDisponibles = [
    'ninguno' => 'Sin estilo adicional',
    'oscuro.css' => 'Oscuro',
    'contraste.css' => 'Contraste',
    'grande.css' => 'Grande',
    'impresion.css' => 'Impresión'
];

$estiloActivo = null;

//   SI HAY USUARIO LOGUEADO → cargamos su estilo desde la BD
if (isset($_SESSION['usuario'])) {

    $sql = "SELECT Estilos.Fichero 
            FROM Usuarios 
            LEFT JOIN Estilos ON Usuarios.Estilo = Estilos.IdEstilo
            WHERE Usuarios.NomUsuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($fila = $res->fetch_assoc()) {
        if (!empty($fila['Fichero']) && array_key_exists($fila['Fichero'], $estilosDisponibles)) {
            $estiloActivo = $fila['Fichero']; // estilo desde la BD
        }
    }
}

//   SI HAY CAMBIO POR GET → lo aplicamos y lo guardamos
if (isset($_GET['estilo']) && array_key_exists($_GET['estilo'], $estilosDisponibles)) {

    $nuevo = $_GET['estilo'];

    if ($nuevo === 'ninguno') {
        setcookie('estilo_usuario', '', time() - 3600, "/");
        $estiloActivo = null;
    } else {
        setcookie('estilo_usuario', $nuevo, time() + 7 * 24 * 60 * 60, "/");
        $estiloActivo = $nuevo;
    }

    // Y si el usuario está logueado, guardarlo también en la BD
    if (isset($_SESSION['usuario'])) {
        $sql = "UPDATE Usuarios 
                SET Estilo = (SELECT IdEstilo FROM Estilos WHERE Fichero = ?)
                WHERE NomUsuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nuevo, $_SESSION['usuario']);
        $stmt->execute();
    }
}

//   SI NO HAY ESTILO EN BD, usar cookie
if (!$estiloActivo && isset($_COOKIE['estilo_usuario']) &&
    $_COOKIE['estilo_usuario'] !== 'ninguno' &&
    array_key_exists($_COOKIE['estilo_usuario'], $estilosDisponibles)) {

    $estiloActivo = $_COOKIE['estilo_usuario'];
}

//   Estilos específicos de cada página
$pageStyles = $pageStyles ?? [];

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PI - Pisos & Inmuebles</title>

    <!-- Fuentes Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Faculty+Glyphic&family=Montserrat:wght@100..900&display=swap" rel="stylesheet">

    <!-- Estilo base SIEMPRE -->
    <link rel="stylesheet" href="css/base.css">

    <!-- Estilo adicional activo -->
    <?php if ($estiloActivo): ?>
        <link rel="stylesheet" href="css/<?= htmlspecialchars($estiloActivo) ?>">
    <?php endif; ?>

    <!-- Estilos específicos de la página -->
    <?php foreach ($pageStyles as $style): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($style) ?>">
    <?php endforeach; ?>
</head>
<body>

<header>
    <div class="container header-inner">
        <a href="index.php" class="titulo-faculty">PI - PISOS & INMUEBLES</a>

        <nav aria-label="Menú principal">
            <button class="nav-toggle" aria-expanded="false" aria-controls="main-navigation">☰</button>
            <ul class="nav-lista" id="main-navigation">
                <li><a href="index.php">Página principal</a></li>
                <li><a href="registro.php">Registro</a></li>
                <li><a href="busqueda.php">Búsqueda</a></li>
                <li><a href="menuusu.php">Menú usuario</a></li>
                <li><a href="configurar.php">Configuración</a></li>
            </ul>
        </nav>
    </div>
</header>

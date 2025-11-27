<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Lista de estilos disponibles (excluyendo base.css, que se carga siempre)
$estilosDisponibles = [
    'ninguno' => 'Sin estilo adicional',
    'oscuro.css' => 'Oscuro',
    'contraste.css' => 'Contraste',
    'grande.css' => 'Grande',
    'impresion.css' => 'Impresión'
];


// Estilo por defecto
$estiloActivo = null;

if (isset($_COOKIE['estilo_usuario']) && 
    $_COOKIE['estilo_usuario'] !== 'ninguno' &&
    array_key_exists($_COOKIE['estilo_usuario'], $estilosDisponibles)) {

    $estiloActivo = $_COOKIE['estilo_usuario'];
}

if (isset($_GET['estilo']) && array_key_exists($_GET['estilo'], $estilosDisponibles)) {

    $estiloActivo = $_GET['estilo'];

    if ($estiloActivo === 'ninguno') {
        // Borrar cookie => volver al estilo normal
        setcookie('estilo_usuario', '', time() - 3600, "/");
        $estiloActivo = null; // no cargar ningún CSS extra
    } else {
        // Guardar nueva preferencia
        setcookie('estilo_usuario', $estiloActivo, time() + 7 * 24 * 60 * 60, "/");
    }
}

$pageStyles = $pageStyles ?? [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PI - Pisos & Inmuebles</title>

    <!-- Fuentes de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Faculty+Glyphic&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Estilo base siempre -->
    <link rel="stylesheet" href="css/base.css">

    <!-- Estilo adicional seleccionado -->
    <?php if ($estiloActivo): ?>
        <link rel="stylesheet" href="css/<?= htmlspecialchars($estiloActivo) ?>">
    <?php endif; ?>

    <!-- Estilos adicionales de la página -->
    <?php 
    if (!empty($pageStyles)) {
        foreach ($pageStyles as $style) {
            echo '<link rel="stylesheet" href="' . htmlspecialchars($style) . '">' . PHP_EOL;
        }
    } 
    ?>
</head>
<body>
<header>
    <div class="container header-inner">
        <a href="index.php" class="titulo-faculty">PI - PISOS & INMUEBLES</a>

        <nav aria-label="Menú principal">
            <button class="nav-toggle" aria-expanded="false" aria-controls="main-navigation">☰</button>
            <ul class="nav-lista" id="main-navigation">
                <li><a href="index.php"><i class="icon-home"></i>Página principal</a></li>
                <li><a href="registro.php"><i class="icon-user-plus"></i>Registro</a></li>
                <li><a href="busqueda.php"><i class="icon-search"></i>Búsqueda</a></li>
                <li><a href="menuusu.php"><i class="icon-mail-1"></i>Menu usuario</a></li>
                <li><a href="configurar.php"><i class="icon-mail-1"></i>Configuracion</a></li>

            </ul>
        </nav>
    </div>
</header>

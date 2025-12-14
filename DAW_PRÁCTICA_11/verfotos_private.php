<?php
require 'header.php';
require 'conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { 
    echo "<p class='error'>Anuncio no especificado.</p>"; 
    require 'footer.php'; 
    exit; 
}

// Incluir el contenido común que realiza la consulta y genera el HTML
require 'verfotos_basic.php';

$conn->close();
require 'footer.php';
?>
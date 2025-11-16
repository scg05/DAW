<?php 
$pageStyles = ["css/menuusu.css"]; 
require 'header.php';

    if(!isset($_SESSION['usuario'])){
        header("Location: index.php");
        exit;
    }   

    date_default_timezone_set('Europe/Madrid'); // Ajusta si hace falta
    $hora = date('H');
    $nombre = htmlspecialchars($_SESSION['usuario']);

    if ($hora >= 6 && $hora <= 11) {
        $saludo = "Buenos días $nombre";
    } elseif ($hora >= 12 && $hora <= 15) {
        $saludo = "Hola $nombre";
    } elseif ($hora >= 16 && $hora <= 19) {
        $saludo = "Buenas tardes $nombre";
    } else {
        $saludo = "Buenas noches $nombre";
    }
?>

    <!-- Contenido principal -->
    <main class="container">
        <h1 class="titulo-faculty">Menú de usuario registrado</h1>
        <p><?php echo $saludo; ?></p>
        <p><?php echo "Bienvenido a tu área privada. Selecciona una de las opciones disponibles:"; ?></p>
        <ul>
            <li><a href="mis_datos.php">Modificar mis datos</a></li>
            <li><a href="mis_anuncios.php">Visualizar mis anuncios</a></li>
            <li><a href="crear_anuncio.php">Crear un anuncio nuevo</a></li>
            <li><a href="mis_mensajes.php">Mis mensajes</a></li>
            <li><a href="solicitar_folleto.php">Solicitar folleto publicitario</a></li>
            <li><a href="index.php">Salir</a></li>
        </ul>
    </main>

<?php require 'footer.php'; ?>

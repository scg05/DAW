<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $tipo = $_POST["tipo"] ?? "";
    $mensaje = trim($_POST["mensaje"] ?? "");

    $errores = [];

    if ($tipo === "" || !ctype_digit((string)$tipo)) {
        $errores[] = "Debes seleccionar un tipo de mensaje válido.";
    }

    if ($mensaje === "") {
        $errores[] = "El mensaje no puede estar vacío.";
    }

    if (!empty($errores)) {
        // Volver a enviar a enviar_mensaje.php con un error
        header("Location: enviar_mensaje.php?error=1");
        exit;
    }

    // Insertar en la base de datos
    $sql = "INSERT INTO Mensajes (TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino, FRegistro) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiii", $tipo, $mensaje, $anuncio, $usuOrigen, $usuDestino);
    $stmt->execute();

    // Redirigir a la página de respuesta con los datos mostrados
    header("Location: respuesta_mensaje.php?tipo=$tipo&mensaje=" . urlencode($mensaje));
    exit;
}

?>

<?php require 'header.php'; ?>

<main class="container">
    <h2>Solicitud registrada correctamente</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Recogida de datos del formulario
        $paginas = intval($_POST["paginas"]);
        $fotos = intval($_POST["fotos"]);
        $copias = intval($_POST["copias"] ?? 1);
        $resolucion = intval($_POST["resolucion"] ?? 150);
        $impresion = $_POST["color_impresion"] ?? "blanco_negro";

        //Tabla de precios
        $precios_base = [
            "bn_150" => 12.00,
            "bn_450" => 12.60,
            "col_150" => 13.50,
            "col_450" => 14.10
        ];
        
        $incrementos = [
            "bn_150" => 2.00,
            "bn_450" => 2.60,
            "col_150" => 3.50,
            "col_450" => 4.10
        ];

        //Selección del tipo de impresión + resolución
        if ($impresion == "blanco_negro") {
            $key = ($resolucion <= 300) ? "bn_150" : "bn_450";
        } else {
            $key = ($resolucion <= 300) ? "col_150" : "col_450";
        }

        //Precio unitario
        $precio_unitario = $precios_base[$key] + ($paginas - 1) * $incrementos[$key];

        //Coste final
        $coste_fijo = 5;
        $coste_total = ($precio_unitario * $copias) + $coste_fijo;

        echo "
        <p><strong>Folleto solicitado:</strong></p>
        <ul>
            <li>Número de páginas: $paginas</li>
            <li>Número de fotos: $fotos</li>
            <li>Tipo impresión: ".(($impresion=="blanco_negro") ? "Blanco y negro" : "A color")."</li>
            <li>Resolución: $resolucion DPI</li>
            <li>Copias: $copias</li>
        </ul>

        <h3>Coste del folleto</h3>
        <ul>
            <li>Precio unitario: ".number_format($precio_unitario,2,',','.')." €</li>
            <li>Coste fijo: ".number_format($coste_fijo,2,',','.')." €</li>
            <li><strong>Total final:</strong> ".number_format($coste_total,2,',','.')." €</li>
        </ul>
        ";
    } else {
        echo "<p class='error'> Error: Acceso no permitido</p>";
    }
    ?>

    <p><a href="index.php">Volver al inicio</a></p>
</main>

<?php require 'footer.php'; ?>

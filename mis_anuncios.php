<?php
    $anuncios = [
        [
            "foto" => "../img/casa1.jpg",
            "titulo" => "Apartamento en el centro",
            "ciudad" => "Madrid",
            "pais" => "España",
            "precio" => "250.000 €"
        ],
        [
            "foto" => "../img/casa2.jpg",
            "titulo" => "Chalet con jardín",
            "ciudad" => "Valencia",
            "pais" => "España",
            "precio" => "420.000 €"
        ],
        [
            "foto" => "../img/casa3.jpg",
            "titulo" => "Estudio moderno",
            "ciudad" => "Barcelona",
            "pais" => "España",
            "precio" => "332.000 €"
        ]
    ];
?>

<?php $pageStyles = ["css/estilos_mis_anuncios.css"]; require 'header.php'; ?>

    <h1>Mis Anuncios</h1>

    <a href="crear_anuncio.php" class="boton-crear">Crear nuevo anuncio</a>

    <div class="anuncios">
        <?php foreach ($anuncios as $an):?>
            <div class="anuncio">
                <img src="<?= $an['foto'] ?>" alt="<?= $an['titulo'] ?>" width="150">
                <h3><?= $an['titulo'] ?></h3>
                <p><?= $an['ciudad'] ?>, <?= $an['pais'] ?></p>
                <p><strong><?= $an['precio'] ?></strong></p>
                <a href="ver_anuncio.php?titulo=<?= urlencode($an['titulo']) ?>">Ver anuncio</a>
                <a href="anadir_foto.php?titulo=<?= urlencode($an['titulo']) ?>">Añadir foto</a>
            </div>
        <?php endforeach; ?>
    </div>
    
<?php require 'footer.php'; ?>
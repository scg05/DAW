<?php
session_start(); //NO FUNCUIONA, SE INICIA 2 VECES

    //CERAR SESION
    if(isset($_GET['logout'])){
        session_unset();
        session_destroy();
        setcookie('recordar_usuario','',time()-3600);
        setcookie('recordar_password','',time()-3600);
        setcookie('ultima_visita','',time()-3600);
        header("Location: index.php");
        exit;
    }
    require 'header.php'; 
    $usuariorec = '';

    //COMPROBAR SI EXISTEN COOKIES DE RECORDAR USUARIO
    if(isset($_COOKIE['recordar_usuario']) && isset($_COOKIE['recordar_password'])){
        $usuariorec=$_COOKIE['recordar_usuario'];
        $passwordrec=$_COOKIE['recordar_password'];
        
        //MENSAJE BIENVENIDA ULTIMA VISITA
        if(isset($_COOKIE['ultima_visita'])){
            echo '<p class="mensaje">Bienvenido de nuevo, <strong>' . htmlspecialchars($usuariorec) . '</strong> ';
            echo 'tu última visita fue el ' . date("d/m/Y H:i", strtotime($_COOKIE['ultima_visita'])) . '.</p>';
        }else {
            echo '<p class="mensaje">Bienvenido de nuevo, <strong>' . htmlspecialchars($usuariorec) . '</strong>.</p>';
        }

        $visitaact=date("c");
        setcookie('ultima_visita',$visitaact,(time()+(90*24*60*60))); //Caduca en 90 dias

        echo '<p><a href="index.php?logout=1">Cerrar sesion</a></p>';
    } else{
        if (isset($_COOKIE['ultima_visita'])) {
            $ultimavisita = $_COOKIE['ultima_visita'];
        }
        $visitaact = date("c");
        setcookie('ultima_visita', $visitaact, (time() + (90*24*60*60))); //Caduca en 90 dias
?>

        <!-- Contenido principal -->
        <main class="container2">

            <!--FORMULARIO LOGIN-->
            <section class="conBorde">
                <h2 class="titulo-faculty">Acceso de usuario</h2>
                <form action="control_acceso.php" method="post" id="inicioses">
                    <label>Usuario:
                    <input type="text" id="usuario" name="usuario" >
                    </label>
                    <label>Contraseña:
                    <input type="password" id="password" name="password" >
                    </label>
                    <input type="checkbox" id="recordarme" name="recordarme" value="si">
                    <label for="recordarme">Recordarme en este equipo</label>
                    <button type="submit">Acceder</button>
                </form>
            </section>

            <?php
                if(isset($_GET['error'])){ //--isset->para ver si existe la variable
                    $error=$_GET['error'];
                    if($error=='campos_vacios'){
                        echo '<p class="error">Error: Debes rellenar todos los campos.</p>';
                    } elseif($error=='acceso_denegado'){
                        echo '<p class="error">Error: Usuario o contraseña incorrectos.</p>';
                    }
                }
            ?>
    <?php
    }  
?>

            <!-- BÚSQUEDA RÁPIDA -->
            <section class="conBorde">
            <h2 class="titulo-faculty">Búsqueda rápida</h2>
            <form action="resultados.php" method="get">
                <input type="text" name="q" placeholder="Ciudad, país o tipo de vivienda">
                <button type="submit">Buscar</button>
            </form>
            </section>

            <<!-- ÚLTIMOS ANUNCIOS VISITADOS -->
            <section class="fondoSuave">
            <h2 class="titulo-faculty">Últimos anuncios visitados</h2>
            <ul>
            <?php
            if (isset($_COOKIE['ultimos_anuncios'])) {
                $ultimos = json_decode($_COOKIE['ultimos_anuncios'], true);

                foreach ($ultimos as $a) {
                    echo "<li>
                            <a href='ver_anuncio.php?id={$a['id']}'>
                                <img src='{$a['img']}' alt='Foto {$a['titulo']}' width='150'>
                                <h3>{$a['titulo']}</h3>
                                <p>Ciudad: {$a['ciudad']} — País: {$a['pais']} — Precio: {$a['precio']}</p>
                            </a>
                        </li>";
                }
            } else {
                echo "<p>Aún no has visitado ningún anuncio.</p>";
            }
            ?>
            </ul>
            </section>
        </main>
    <?php require 'footer.php'; ?>
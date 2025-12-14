<?php
    session_start();
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
    if(isset($_SESSION['usuario'])){
        $usuariorec=$_SESSION['usuario'];
        $visitaact = date("c");
        //MENSAJE BIENVENIDA ULTIMA VISITA
        if(isset($_COOKIE['ultima_visita'])){
            echo '<p class="mensaje">Bienvenido de nuevo, <strong>' . htmlspecialchars($usuariorec) . '</strong> ';
            echo 'tu última visita fue el ' . date("d/m/Y H:i", strtotime($_COOKIE['ultima_visita'])) . '.</p>';
        }else {
            echo '<p class="mensaje">Bienvenido de nuevo, <strong>' . htmlspecialchars($usuariorec) . '</strong>.</p>';
        }

        setcookie('ultima_visita',$visitaact,(time()+(90*24*60*60))); //Caduca en 90 dias

        echo '<p><a href="index.php?logout=1">Cerrar sesion</a></p>';

    } elseif(isset($_COOKIE['recordar_usuario']) && isset($_COOKIE['recordar_password'])){

        $usuariorec = $_COOKIE['recordar_usuario'];
        $hashCookie = $_COOKIE['recordar_password'];
        require 'conexion.php'; 
    
        $sql = "SELECT IdUsuario, Clave FROM Usuarios WHERE NomUsuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuariorec);
        $stmt->execute();
        $stmt->bind_result($idUsuario, $dbClave);

        if ($stmt->fetch()) {
            $DB_HASH = $dbClave;

            // Compara el hash de la cookie con el hash de la base de datos
            // Ambos son hashes generados por password_hash().
            if ($hashCookie === $DB_HASH) {
                
                // ACCESO PERMITIDO POR COOKIE
                $_SESSION['usuario'] = $usuariorec;
                $_SESSION['id_usuario'] = $idUsuario; 
                
                header("Location: index.php");
                exit;
                
            } else {
                // Falla la verificación del hash: cookie posiblemente corrupta o manipulada
                // Destruimos las cookies inseguras y continuamos como si no hubiera sesión.
                setcookie('recordar_usuario', '', time() - 3600);
                setcookie('recordar_password', '', time() - 3600);
                setcookie('ultima_visita', '', time() - 3600, "/");
                // El flujo seguirá al bloque 'else' final.
            }
        }
        $stmt->close();
        $conn->close();
    
    }else {
        
        // --- USUARIO SIN SESIÓN NI COOKIES ---
        $visitaact = date("c");
        setcookie('ultima_visita', $visitaact, time() + (90 * 24 * 60 * 60), "/");
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

            <!-- BÚSQUEDA RÁPIDA (MODIFICADA CON SELECTS DESDE BD) -->
            <section class="conBorde">
            <h2 class="titulo-faculty">Búsqueda rápida</h2>
            <form action="resultados.php" method="get">
                <?php
                require 'conexion.php';

                // --- Cargar Tipos de Anuncio ---
                $tiposAnuncios = $conn->query("SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios ORDER BY NomTAnuncio ASC");
                echo '<label for="tipo_anuncio">Tipo de anuncio:</label><br>';
                echo '<select id="tipo_anuncio" name="tipo_anuncio">';
                echo '<option value="">-- Seleccione tipo --</option>';
                if ($tiposAnuncios && $tiposAnuncios->num_rows > 0) {
                    while ($row = $tiposAnuncios->fetch_assoc()) {
                        echo '<option value="' . $row['IdTAnuncio'] . '">' . htmlspecialchars($row['NomTAnuncio']) . '</option>';
                    }
                }
                echo '</select><br><br>';

                // --- Cargar Tipos de Vivienda ---
                $tiposViviendas = $conn->query("SELECT IdTVivienda, NomTVivienda FROM TiposViviendas ORDER BY NomTVivienda ASC");
                echo '<label for="tipo_vivienda">Tipo de vivienda:</label><br>';
                echo '<select id="tipo_vivienda" name="tipo_vivienda">';
                echo '<option value="">-- Seleccione tipo --</option>';
                if ($tiposViviendas && $tiposViviendas->num_rows > 0) {
                    while ($row = $tiposViviendas->fetch_assoc()) {
                        echo '<option value="' . $row['IdTVivienda'] . '">' . htmlspecialchars($row['NomTVivienda']) . '</option>';
                    }
                }
                echo '</select><br><br>';

                // --- Cargar Países ---
                $paises = $conn->query("SELECT IdPais, Nombre FROM Paises ORDER BY Nombre ASC");
                echo '<label for="pais">País:</label><br>';
                echo '<select id="pais" name="pais">';
                echo '<option value="">-- Seleccione país --</option>';
                if ($paises && $paises->num_rows > 0) {
                    while ($row = $paises->fetch_assoc()) {
                        echo '<option value="' . $row['IdPais'] . '">' . htmlspecialchars($row['Nombre']) . '</option>';
                    }
                }
                echo '</select><br><br>';

                $conn->close();
                ?>

                <label for="ciudad">Ciudad:</label><br>
                <input type="text" id="ciudad" name="ciudad" placeholder="Ej. Alicante"><br><br>

                <button type="submit">Buscar</button>
            </form>
            </section>

            <!-- ÚLTIMOS ANUNCIOS PUBLICADOS -->
            <?php
            require 'conexion.php';

            echo '<section class="conBorde">';
            echo '<h2 class="titulo-faculty">Últimos anuncios publicados</h2>';

            $sql = "SELECT A.IdAnuncio, A.FPrincipal, A.Titulo, A.FRegistro, 
                        A.Ciudad, P.Nombre AS Pais, A.Precio
                    FROM Anuncios A
                    LEFT JOIN Paises P ON A.Pais = P.IdPais
                    ORDER BY A.FRegistro DESC
                    LIMIT 5";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                echo '<ul class="ultimos-anuncios">';
                while ($row = $result->fetch_assoc()) {
                    $foto = htmlspecialchars($row['FPrincipal']);
                    $titulo = htmlspecialchars($row['Titulo']);
                    $fecha = date("d/m/Y H:i", strtotime($row['FRegistro']));
                    $ciudad = htmlspecialchars($row['Ciudad']);
                    $pais = htmlspecialchars($row['Pais']);
                    $precio = number_format($row['Precio'], 2, ',', '.');

                    echo "<li>
                            <a href='detalle_anuncio.php?id={$row['IdAnuncio']}'>
                                <img src='{$foto}' alt='Foto de {$titulo}' width='150'>
                                <h3>{$titulo}</h3>
                                <p>Publicado el {$fecha}</p>
                                <p>{$ciudad} — {$pais}</p>
                                <p><strong>{$precio} €</strong></p>
                            </a>
                        </li>";
                }
                echo '</ul>';
            } else {
                echo "<p>No hay anuncios publicados.</p>";
            }

            $conn->close();
            echo '</section>';
            ?>

        </main>
    <?php require 'footer.php'; ?>
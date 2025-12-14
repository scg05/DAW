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
    if(isset($_COOKIE['usuario'])){
        $usuariorec=$_SESSION['usuario'];
        
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
        $passwordrec = $_COOKIE['recordar_password'];
        $_SESSION['usuario']=$usuariorec;

        if (isset($_COOKIE['ultima_visita'])) {
            echo '<p class="mensaje">Bienvenido de nuevo, <strong>' . htmlspecialchars($usuariorec) . '</strong> ';
            echo 'tu última visita fue el ' . date("d/m/Y H:i", strtotime($_COOKIE['ultima_visita'])) . '.</p>';
        } else {
            echo '<p class="mensaje">Bienvenido de nuevo, <strong>' . htmlspecialchars($usuariorec) . '</strong>.</p>';
        }
        $visitaact = date("c");
        setcookie('ultima_visita', $visitaact, (time() + (90*24*60*60))); //Caduca en 90 dias
        echo '<p><a href="index.php?logout=1">Cerrar sesión</a></p>';
    
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

            <!-- ANUNCIO ESCOGIDO (SELECCIÓN ALEATORIA CON VALIDACIÓN DE BD) -->
            <?php
            require 'conexion.php';
            echo '<section class="conBorde">';
            echo '<h2 class="titulo-faculty">Anuncio Escogido</h2>';

            // Leer fichero de anuncios escogidos
            $ficheroEscogidos = 'anuncios_escogidos.txt';
            $anuncioMostrado = false;
            $anunciosEscogidos = [];

            if (file_exists($ficheroEscogidos)) {
                $fp = fopen($ficheroEscogidos, 'r');
                if ($fp) {
                    while (($linea = fgets($fp)) !== false) {
                        $linea = trim($linea);
                        if (!empty($linea)) {
                            $anunciosEscogidos[] = $linea;
                        }
                    }
                    fclose($fp);
                }
            }

            if (!empty($anunciosEscogidos)) {
                // Elegir anuncio escogido al azar
                $indiceAleatorio = array_rand($anunciosEscogidos);
                $lineaEscogida = $anunciosEscogidos[$indiceAleatorio];
                
                // Parsear formato: IdAnuncio|NombreExperto|Comentario
                $partes = explode('|', $lineaEscogida, 3);
                if (count($partes) === 3) {
                    $idAnuncioEscogido = (int)trim($partes[0]);
                    $nombreExperto = trim($partes[1]);
                    $comentario = trim($partes[2]);
                    
                    // VALIDAR que el anuncio existe en BD
                    $sqlValidar = "SELECT * FROM Anuncios WHERE IdAnuncio = ?";
                    $stmtValidar = $conn->prepare($sqlValidar);
                    $stmtValidar->bind_param("i", $idAnuncioEscogido);
                    $stmtValidar->execute();
                    $resultValidar = $stmtValidar->get_result();
                    
                    if ($resultValidar->num_rows > 0) {
                        // EXISTE: mostrar anuncio escogido
                        $anuncioEscogido = $resultValidar->fetch_assoc();
                        $foto = htmlspecialchars($anuncioEscogido['FPrincipal'] ?? 'img/sin_foto.jpg');
                        $titulo = htmlspecialchars($anuncioEscogido['Titulo']);
                        $precio = number_format($anuncioEscogido['Precio'], 2, ',', '.');
                        $ciudad = htmlspecialchars($anuncioEscogido['Ciudad']);
                        $pais = htmlspecialchars($anuncioEscogido['Pais']);
                        
                        echo "<div style='border: 2px solid #c9a961; padding: 15px; margin: 10px 0;'>";
                        echo "<img src='{$foto}' alt='Foto de {$titulo}' style='max-width: 200px; margin-bottom: 10px;'>";
                        echo "<h3>{$titulo}</h3>";
                        echo "<p><strong>Precio:</strong> {$precio} €</p>";
                        echo "<p><strong>Ubicación:</strong> {$ciudad}, {$pais}</p>";
                        echo "<p style='font-style: italic; color: #666;'><strong>{$nombreExperto}</strong> opina: \"{$comentario}\"</p>";
                        echo "<p><a href='detalle_anuncio.php?id={$idAnuncioEscogido}'>Ver detalles del anuncio</a></p>";
                        echo "</div>";
                        $anuncioMostrado = true;
                    }
                    $stmtValidar->close();
                }
            }

            // Si no se mostró anuncio escogido (NO EXISTE o fichero vacío), mostrar anuncio aleatorio de BD
            if (!$anuncioMostrado) {
                $sqlAleatorio = "SELECT * FROM Anuncios ORDER BY RAND() LIMIT 1";
                $resultAleatorio = $conn->query($sqlAleatorio);
                
                if ($resultAleatorio && $resultAleatorio->num_rows > 0) {
                    $anuncioAleatorio = $resultAleatorio->fetch_assoc();
                    $foto = htmlspecialchars($anuncioAleatorio['FPrincipal'] ?? 'img/sin_foto.jpg');
                    $titulo = htmlspecialchars($anuncioAleatorio['Titulo']);
                    $precio = number_format($anuncioAleatorio['Precio'], 2, ',', '.');
                    $ciudad = htmlspecialchars($anuncioAleatorio['Ciudad']);
                    $pais = htmlspecialchars($anuncioAleatorio['Pais']);
                    $idAnuncio = $anuncioAleatorio['IdAnuncio'];
                    
                    echo "<div style='border: 2px solid #c9a961; padding: 15px; margin: 10px 0;'>";
                    echo "<img src='{$foto}' alt='Foto de {$titulo}' style='max-width: 200px; margin-bottom: 10px;'>";
                    echo "<h3>{$titulo}</h3>";
                    echo "<p><strong>Precio:</strong> {$precio} €</p>";
                    echo "<p><strong>Ubicación:</strong> {$ciudad}, {$pais}</p>";
                    echo "<p style='font-style: italic; color: #666;'>Esta vivienda destaca por sus excelentes características y ubicación privilegiada.</p>";
                    echo "<p><a href='detalle_anuncio.php?id={$idAnuncio}'>Ver detalles del anuncio</a></p>";
                    echo "</div>";
                } else {
                    echo "<p>No hay anuncios disponibles en este momento.</p>";
                }
            }

            echo '</section>';
            ?>

            <!-- CONSEJO DE COMPRA/VENTA (SELECCIÓN ALEATORIA DESDE JSON) -->
            <?php
            echo '<section class="conBorde">';
            echo '<h2 class="titulo-faculty">Consejo de Compra/Venta</h2>';

            $ficheroConsejos = 'consejos.json';
            $consejoMostrado = false;

            if (file_exists($ficheroConsejos)) {
                $fp = fopen($ficheroConsejos, 'r');
                if ($fp) {
                    $contenidoJSON = fread($fp, filesize($ficheroConsejos));
                    fclose($fp);
                    
                    $consejos = json_decode($contenidoJSON, true);
                    
                    if (is_array($consejos) && !empty($consejos)) {
                        // Elegir consejo al azar
                        $indiceConsejoAleatorio = array_rand($consejos);
                        $consejo = $consejos[$indiceConsejoAleatorio];
                        
                        $categoria = htmlspecialchars($consejo['categoria'] ?? 'General');
                        $importancia = htmlspecialchars($consejo['importancia'] ?? 'Media');
                        $descripcion = htmlspecialchars($consejo['descripcion'] ?? '');
                        
                        echo "<div style='padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                        echo "<p><strong>Categoría:</strong> {$categoria}</p>";
                        echo "<p><strong>Importancia:</strong> {$importancia}</p>";
                        echo "<p><strong>Consejo:</strong> {$descripcion}</p>";
                        echo "</div>";
                        $consejoMostrado = true;
                    }
                }
            }

            if (!$consejoMostrado) {
                echo "<p>No hay consejos disponibles en este momento.</p>";
            }

            echo '</section>';
            ?>

        </main>
    <?php require 'footer.php'; ?>
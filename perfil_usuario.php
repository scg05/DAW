<?php 
    require 'header.php';
    require 'conexion.php';

    //validacion
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        echo "<main class='container'><p class='error'>Error: usuario no especificado.</p></main>";
        require 'footer.php';
        exit;
    }
    //obtener datos
    $sql_user="SELECT IdUsuario, NomUsuario, Foto, FRegistro FROM Usuarios WHERE IdUsuario=?";
    $stmtuser = $conn->prepare($sql_user);
    if(!$stmtuser){
        echo "<main class='container'><p class='error'>Error en la consulta: " . $conn->error . "</p></main>";
        require 'footer.php';
        exit;
    }

    $stmtuser->bind_param("i", $id);
    $stmtuser->execute();
    $result_user = $stmtuser->get_result();
    if($result_user->num_rows===0){
        echo "<main class='container'><p class='error'>El usuario solicitado no existe.</p></main>";
        $stmtuser->close();
        require 'footer.php';
        exit;
    }

    $user=$result_user->fetch_assoc();
    
    //obtener anuncios
    $sql_anuncios="SELECT IdAnuncio, Titulo, Ciudad, Precio FROM Anuncios WHERE Usuario=? ORDER BY FRegistro DESC";
    $stmtanuncios=$conn->prepare($sql_anuncios);
    if(!$stmtanuncios){
        echo "<main class='container'><p class='error'>Error en la consulta: " . $conn->error . "</p></main>";
        $stmtuser->close();
        require 'footer.php';
        exit;
    }

    $stmtanuncios->bind_param("i", $id);
    $stmtanuncios->execute();
    $resAn = $stmtanuncios->get_result();
?>

<main class="container">
    <h1 class="titulo-faculty">Perfil público de <?= htmlspecialchars($user['NomUsuario']) ?></h1>

    <section class="conBorde">
        <!-- Foto (si existe) -->
        <?php if (!empty($user['Foto'])): ?>
            <img src="<?= htmlspecialchars($user['Foto']) ?>" alt="Foto de <?= htmlspecialchars($user['NomUsuario']) ?>"
                >
        <?php else: ?>
            <div>
                <span>Sin foto</span>
            </div>
        <?php endif; ?>

        <div>
            <p><strong>Nombre de usuario:</strong> <?= htmlspecialchars($user['NomUsuario']) ?></p>
            <p><strong>Miembro desde:</strong> <?= (!empty($user['FRegistro'])) ? date("d/m/Y", strtotime($user['FRegistro'])) : "—" ?></p>
            <p><a href="verfotos_public.php?id=<?= $user['IdUsuario'] ?>">Ver todas sus fotos</a></p>
        </div>
    </section>

    <section class="fondoSuave">
        <h2>Anuncios publicados por <?= htmlspecialchars($user['NomUsuario']) ?></h2>

        <?php if ($resAn->num_rows > 0): ?>
            <ul class="ultimos-anuncios">
                <?php while ($a = $resAn->fetch_assoc()): ?>
                    <li>
                        <a href="detalle_anuncio.php?id=<?= (int)$a['IdAnuncio'] ?>">
                            <?php if (!empty($a['FPrincipal'])): ?>
                                <img src="<?= htmlspecialchars($a['FPrincipal']) ?>" alt="Foto anuncio" width="120">
                            <?php endif; ?>

                            <div>
                                <h3><?= htmlspecialchars($a['Titulo']) ?></h3>
                                <p><strong>Ciudad:</strong> <?= htmlspecialchars($a['Ciudad']) ?></p>
                                <p><strong>Publicado:</strong> <?= (!empty($a['FRegistro'])) ? date("d/m/Y", strtotime($a['FRegistro'])) : "—" ?></p>
                                <p><strong>Precio:</strong> <?= is_null($a['Precio']) ? '—' : number_format($a['Precio'], 2, ',', '.') . ' €' ?></p>
                                 <!-- Enlace a las fotos privadas de este anuncio -->
                                <p><a href="verfotos_public.php?id=<?= (int)$a['IdAnuncio'] ?>">Ver fotos de este anuncio</a></p>
                            </div>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Este usuario todavía no ha publicado anuncios.</p>
        <?php endif; ?>
    </section>
</main>
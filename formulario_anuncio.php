<?php
// Espera variables:
// $modo ("crear" | "modificar")
// $accion (URL del form action)
// $mensaje_error (string o vacío)
// $titulo, $ciudad, $pais, $precio, $tipoA, $tipoV, $descripcion
// $paises, $tiposAnuncio, $tiposVivienda (arrays de BD)
?>

<h1><?= ($modo === 'modificar') ? 'Modificar anuncio' : 'Crear nuevo anuncio'; ?></h1>

<?php if (!empty($mensaje_error)): ?>
    <p style="color:red;"><?= $mensaje_error; ?></p>
<?php endif; ?>

<form action="<?= htmlspecialchars($accion, ENT_QUOTES, 'UTF-8'); ?>" method="post" novalidate>

    <label>Título:</label>
    <input type="text" name="titulo" value="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>"><br>

    <label>Ciudad:</label>
    <input type="text" name="ciudad" value="<?= htmlspecialchars($ciudad, ENT_QUOTES, 'UTF-8'); ?>"><br>

    <label>País:</label>
    <select name="pais">
        <option value="">Seleccionar...</option>
        <?php foreach ($paises as $p): ?>
            <option value="<?= $p['IdPais']; ?>" <?= ($pais == $p['IdPais']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($p['Nombre'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Precio (€):</label>
    <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($precio, ENT_QUOTES, 'UTF-8'); ?>"><br>

    <label>Tipo de anuncio:</label>
    <select name="tipo_anuncio">
        <?php foreach ($tiposAnuncio as $t): ?>
            <option value="<?= $t['IdTAnuncio']; ?>" <?= ($tipoA == $t['IdTAnuncio']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($t['NomTAnuncio'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Tipo de vivienda:</label>
    <select name="tipo_vivienda">
        <?php foreach ($tiposVivienda as $tv): ?>
            <option value="<?= $tv['IdTVivienda']; ?>" <?= ($tipoV == $tv['IdTVivienda']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($tv['NomTVivienda'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion"><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8'); ?></textarea><br>

    <input type="submit" value="<?= ($modo === 'modificar') ? 'Guardar cambios' : 'Crear anuncio'; ?>">

</form>

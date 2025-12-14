<?php $pageStyles = ["css/busqueda.css"]; require 'header.php'; ?>

        <main>
        <h2>Buscar Anuncios</h2>
        <form action="resultados.php" method="get">
            <!-- Tipo de anuncio -->
            <label for="tipo_anuncio">Tipo de anuncio:</label><br>
            <select id="tipo_anuncio" name="tipo_anuncio">
            <option value="">Cualquiera</option>
            <option value="venta">Venta</option>
            <option value="alquiler">Alquiler</option>
            </select><br><br>

            <!-- Tipo de vivienda -->
            <label for="tipo_vivienda">Tipo de vivienda:</label><br>
            <select id="tipo_vivienda" name="tipo_vivienda">
            <option value="">Cualquiera</option>
            <option value="obra_nueva">Obra nueva</option>
            <option value="vivienda">Vivienda</option>
            <option value="oficina">Oficina</option>
            <option value="local">Local</option>
            <option value="garaje">Garaje</option>
            </select><br><br>

            <!-- Ciudad -->
            <label for="ciudad">Ciudad:</label><br>
            <input type="text" id="ciudad" name="ciudad"><br><br>

            <!-- País -->
            <label for="pais">País:</label><br>
            <select id="pais" name="pais">
            <option value="">Cualquiera</option>
            <option value="es">España</option>
            <option value="mx">México</option>
            <option value="ar">Argentina</option>
            <option value="cl">Chile</option>
            <option value="co">Colombia</option>
            <option value="us">Estados Unidos</option>
            <option value="fr">Francia</option>
            <option value="de">Alemania</option>
            <option value="it">Italia</option>
            <option value="uk">Reino Unido</option>
            </select><br><br>

            <!-- Precio -->
            <label for="precio">Precio máximo (€):</label><br>
            <input type="number" id="precio" name="precio" min="0" step="1000"><br><br>

            <!-- Fecha de publicación -->
            <label for="fecha">Fecha de publicación (desde):</label><br>
            <input type="date" id="fecha" name="fecha"><br><br>

            <!-- Botón de búsqueda -->
            <input type="submit" value="Buscar">
        </form>
        </main>
        
<?php require 'footer.php'; ?>
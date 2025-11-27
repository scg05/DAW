<?php
// filtro_anuncio.php
// Devuelve array [ $datos, $errores ]
function filtrarDatosAnuncio(): array {

    // Saneamos con filter_input_array
    $def = [
        'titulo'       => FILTER_SANITIZE_STRING,
        'ciudad'       => FILTER_SANITIZE_STRING,
        'pais'         => FILTER_SANITIZE_NUMBER_INT,
        'precio'       => [
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags'  => FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND
        ],
        'tipo_anuncio' => FILTER_SANITIZE_NUMBER_INT,
        'tipo_vivienda'=> FILTER_SANITIZE_NUMBER_INT,
        'descripcion'  => FILTER_SANITIZE_STRING,
    ];

    $input = filter_input_array(INPUT_POST, $def);

    // Normalizamos y hacemos trim
    $datos = [
        'titulo'       => isset($input['titulo']) ? trim($input['titulo']) : '',
        'ciudad'       => isset($input['ciudad']) ? trim($input['ciudad']) : '',
        'pais'         => isset($input['pais']) ? $input['pais'] : '',
        'precio'       => isset($input['precio']) ? str_replace(',', '.', $input['precio']) : '',
        'tipo_anuncio' => isset($input['tipo_anuncio']) ? $input['tipo_anuncio'] : '',
        'tipo_vivienda'=> isset($input['tipo_vivienda']) ? $input['tipo_vivienda'] : '',
        'descripcion'  => isset($input['descripcion']) ? trim($input['descripcion']) : '',
    ];

    $errores = [];

    // Enunciado: título y texto/descripcion obligatorios
    if ($datos['titulo'] === '') {
        $errores[] = "El título del anuncio es obligatorio.";
    }
    if ($datos['descripcion'] === '') {
        $errores[] = "El texto/descrición del anuncio es obligatorio.";
    }

    // Si quieres mantener resto como obligatorios (como en tu crear_anuncio)
    if ($datos['ciudad'] === '' || $datos['pais'] === '' || 
        $datos['precio'] === '' || $datos['tipo_anuncio'] === '' || 
        $datos['tipo_vivienda'] === '') {
        $errores[] = "Debes completar todos los campos del formulario.";
    }

    // Validación con filter_var para precio (float) y rangos si quieres
    if ($datos['precio'] !== '') {
        $precioOk = filter_var($datos['precio'], FILTER_VALIDATE_FLOAT);
        if ($precioOk === false) {
            $errores[] = "El precio debe ser un número real válido.";
        } elseif ($precioOk < 0) {
            $errores[] = "El precio no puede ser negativo.";
        } else {
            $datos['precio'] = $precioOk; // dejamos ya como float
        }
    }

    // Validaciones numéricas básicas para ids
    if ($datos['pais'] !== '' && !filter_var($datos['pais'], FILTER_VALIDATE_INT)) {
        $errores[] = "El país seleccionado no es válido.";
    }
    if ($datos['tipo_anuncio'] !== '' && !filter_var($datos['tipo_anuncio'], FILTER_VALIDATE_INT)) {
        $errores[] = "El tipo de anuncio seleccionado no es válido.";
    }
    if ($datos['tipo_vivienda'] !== '' && !filter_var($datos['tipo_vivienda'], FILTER_VALIDATE_INT)) {
        $errores[] = "El tipo de vivienda seleccionado no es válido.";
    }

    return [$datos, $errores];
}

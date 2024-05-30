<?php

function extraerExtension($cadena) {
    // Encuentra la última aparición del punto en la cadena
    $posicionPunto = strrpos($cadena, '.');

    if ($posicionPunto !== false) {
        // Extrae la subcadena después del punto
        $extension = substr($cadena, $posicionPunto + 1);
        return $extension;
    } else {
        // Si no se encuentra un punto, la extensión es vacía
        return '';
    }
}


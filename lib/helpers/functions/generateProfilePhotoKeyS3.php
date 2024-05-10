<?php

function generateProfilePhotoKeyS3($nombreUsuario, $dni, $extension) {
    // Concatenar el nombre de usuario y el DNI
    $key = "Fotos_de_Perfil/".$nombreUsuario . "_" . $dni;
    
    // Reemplazar espacios en blanco por guiones bajos
    $key = str_replace(" ", "_", $key);

    $key.='.'.$extension;
    
    // Devolver el nombre de la foto de perfil generado
    return $key;
}
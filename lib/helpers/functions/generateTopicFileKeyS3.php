<?php

function generateTopicFileKeyS3($Grado, $Seccion, $Nombre_Curso,$Id_Tema, $Nombre_Archivo, $extension) {
    // Concatenar el nombre de usuario y el DNI
    $key = "Aulas/".$Grado.$Seccion."/".$Nombre_Curso."/".$Id_Tema."/A/".$Nombre_Archivo.".".$extension;
        
    // Devolver el nombre de la foto de perfil generado
    return $key;
}
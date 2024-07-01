<?php

define("NOMBRE_CARPETA_DESCRIPCION_IMAGENES", [0=>"Imgs_Descripcion/Archivos", 1=>"Imgs_Descripcion/Foros", 2=>"Imgs_Descripcion/Tareas", 3=>"Imgs_Descripcion/URLs", 4=>"Imgs_Descripcion/Cuestionarios"]);

function generateResourceDescriptionImageKeyS3($Grado, $Seccion, $Nombre_Curso, $Id_Tema, $Nombre_Archivo, $extension, $tipo)
{
    // Concatenar el nombre de usuario y el DNI
    $key = "Aulas/" . $Grado . $Seccion . "/" . $Nombre_Curso . "/" . $Id_Tema . "/" . NOMBRE_CARPETA_DESCRIPCION_IMAGENES[$tipo] . "/" . $Nombre_Archivo . "." . $extension;

    // Devolver el nombre de la foto de perfil generado
    return $key;
}

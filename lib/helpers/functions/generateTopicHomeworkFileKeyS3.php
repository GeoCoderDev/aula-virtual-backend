<?php

define("NOMBRE_CARPETA_ARCHIVOS_TAREAS", "Tareas");
function generateTopicHomeworkFileKeyS3($Grado, $Seccion, $Nombre_Curso, $Id_Tema, $Id_Tarea, $Nombre_Archivo, $extension)
{
    // Concatenar el nombre de usuario y el DNI
    $key = "Aulas/" . $Grado . $Seccion . "/" . $Nombre_Curso . "/" . $Id_Tema . "/" . NOMBRE_CARPETA_ARCHIVOS_TAREAS . "/" . $Id_Tarea . "/" . $Nombre_Archivo . "." . $extension;

    // Devolver el nombre de la foto de perfil generado
    return $key;
}

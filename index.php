<?php

    require './vendor/autoload.php';

    //===========================================================
    //|                  IMPORTANDO RUTAS                       |
    //===========================================================    

    // Ruta al directorio
    $directory = __DIR__."./routes/";

    // Obtiene la lista de archivos PHP en el directorio
    $files = glob($directory . '*.php');

    // Requiere cada archivo
    foreach ($files as $file) {
        require_once $file;
    }
    
    // Finalmente, se inicia la API con el framework.
    Flight::start();
?>
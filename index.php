
<?php
require './vendor/autoload.php';

// Manejo del Preflight para poder hacer solicitudes HTTP desde Navegadores
Flight::route('OPTIONS *', function() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, DELETE, PUT, GET, OPTIONS");
    echo "";
});

//===========================================================
//|                   IMPORTANDO RUTAS                      |
//===========================================================
    // Ruta al directorio
    $directory = __DIR__ . "./routes/";

    // Obtiene la lista de archivos PHP en el directorio y sus subcarpetas
    $files = glob($directory . '**/*.php', GLOB_BRACE);

    // Requiere cada archivo
    foreach ($files as $file) {
        require_once $file;
    }

//=============================================================

//Ruta por defecto
Flight::route('*', function () {
    // Ruta al directorio raíz
    echo "404";
});

// Finalmente, se inicia la API con el framework.
Flight::start();
?>
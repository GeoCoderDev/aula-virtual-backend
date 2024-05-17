<?php
require './vendor/autoload.php';
use Dotenv\Dotenv;

// Carga las variables de entorno desde el archivo .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//constant
define("ALLOWED_ORIGINS", "*");

// Manejo del Preflight para poder hacer solicitudes HTTP desde Navegadores
Flight::route('OPTIONS *', function() {
    header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);
    header('Access-Control-Allow-Headers: Authorization, Content-Type');
    header("Access-Control-Allow-Methods: POST, DELETE, PUT, GET, OPTIONS");
    echo "";
});

//===========================================================
//|                    IMPORTANDO RUTAS                     |
//===========================================================
// Función recursiva para obtener la lista de archivos PHP
function getPhpFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

// Ruta al directorio
$directory = __DIR__ . "/routes/"; //endpoints

// Obtiene la lista de archivos PHP en el directorio y sus subcarpetas
$files = getPhpFiles($directory);

// Requiere cada archivo
foreach ($files as $file) {
    require_once $file;
}

//=============================================================


//Ruta por defecto
Flight::route('*', function () {
    // Ruta al directorio raíz
    Flight::json(["message"=>"Recurso no encontrado"], 404);
});

// Finalmente, se inicia la | con el framework.
Flight::start();

?>
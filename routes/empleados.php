<?php

// Incluye los archivos de controladores
require __DIR__."../../controllers/empleado_controller.php";


define("URL_BASE", "/api/empleados");

// Rutas para el módulo de empleados
Flight::route('GET '.URL_BASE , function(){
    $controller = new EmpleadoController();
    echo $controller->getAll();

});

Flight::route('GET '.URL_BASE.'/@id', function($id){
    $controller = new EmpleadoController();
    echo $controller->getById($id);
});



Flight::route('DELETE '.URL_BASE.'/@id', function($id){
    $controller = new EmpleadoController();
    $controller->delete($id);
});


?>

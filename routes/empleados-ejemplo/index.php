<?php

// Incluye los archivos de controladores
require __DIR__."../../../controllers/empleado_controller.php";

// Rutas para el módulo de empleados
Flight::group("/api/empleados", function(){
    
    Flight::route('GET ', function(){
        $controller = new EmpleadoController();
        echo $controller->getAll();
    });

    Flight::route('GET /@id', function($id){
        $controller = new EmpleadoController();
        echo $controller->getById($id);
    });


    Flight::route('GET /name/@nombre', function($nombre){
        $controller = new EmpleadoController();
        echo $controller->getEmpleadoByName($nombre);
    });

    Flight::route('GET /@id/@nombre', function($id,$nombre){
        $controller = new EmpleadoController();
        echo $controller->getEmpleadoByIdAndName($id, $nombre);
    });

    


    Flight::route('POST ', function() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type"); //no necesario a mi parecer
        $controller = new EmpleadoController();
        $data = Flight::request()->data->getData();
        echo $controller->create($data);    
    });

    Flight::route('DELETE /@id', function($id) {
        header("Access-Control-Allow-Origin: *");
        $controller = new EmpleadoController();
        echo $controller->delete($id);    
    });

    Flight::route('PUT /@id', function($id) {
        header("Access-Control-Allow-Origin: *");
        $controller = new EmpleadoController();
        $data = Flight::request()->data->getData();
        echo $controller->update($id, $data);
    });
    
});


<?php

require_once __DIR__ . "/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";
require_once __DIR__ . "/../../controllers/Estudiante.php";

Flight::group("/api/students",  function(){

    Flight::route("GET ", function() {
        // Obtener los parámetros de consulta de la URL
        $startFrom = Flight::request()->query['startFrom'] ?? 0;
        $limit = Flight::request()->query['limit'] ?? 200;
        $dni = Flight::request()->query['DNI'] ?? null;
        $nombre = Flight::request()->query['Nombre'] ?? null;
        $apellidos = Flight::request()->query['Apellidos'] ?? null;
        $grado = Flight::request()->query['Grado'] ?? null;
        $seccion = Flight::request()->query['Seccion'] ?? null;
        $estado = Flight::request()->query['Estado'] ?? null; // Nuevo parámetro de consulta

        // Convertir a entero si es una cadena
        $startFrom = intval($startFrom);
        $limit = intval($limit);

        $controller = new EstudianteController();

        $results = $controller->getAll(false, $limit, $startFrom, $dni, $nombre, $apellidos, $grado, $seccion, $estado); // Pasa el nuevo parámetro de consulta

        if($startFrom==0){

            $count = $controller->getStudentCount($dni, $nombre, $apellidos, $grado, $seccion, $estado); // Pasa el nuevo parámetro de consulta

            Flight::json(["results" => $results, "count"=>$count], 200);

        } else {
            Flight::json(["results" => $results], 200);                                    
        }        
        
    });

    Flight::route("GET /@DNI", function($DNI){

        $controller = new EstudianteController();
        $controller->getByDNI($DNI);

    });

    Flight::route("POST ", function(){
        $data = Flight::request()->data->getData();        
        $controller = new EstudianteController();
        $controller->create($data);

    });

    Flight::route("POST /multiple", function(){

        $data = Flight::request()->data->getData();
        $controller = new EstudianteController();
        $controller->multipleCreate($data);

    });

    Flight::route("POST /@DNI", function($DNI){
        
        $data = Flight::request()->data->getData();
        $controller = new EstudianteController();
        $controller->update($DNI,$data);

    });

    Flight::route("PUT /@DNI/toggleState", function($DNI) {
        $controller = new EstudianteController();
        $controller->toggleState($DNI);
    });



    Flight::route("DELETE /@DNI", function($DNI){

        $controller = new EstudianteController();
        $controller->delete($DNI);

    });


}, [ new NotSQLInjection(), new AdminAuthenticated(true), new SuperadminAuthenticated()]);

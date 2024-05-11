<?php

require_once __DIR__ . "/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ . "/../../controllers/Estudiante.php";

Flight::group("/api/students",  function(){

    Flight::route("GET ", function() {
        // Obtener los parámetros de consulta de la URL
        $startFrom = Flight::request()->query['startFrom'] ?? 0;
        $limit = Flight::request()->query['limit'] ?? 200;
        $dni = Flight::request()->query['dni'] ?? null;
        $nombre = Flight::request()->query['nombre'] ?? null;
        $apellidos = Flight::request()->query['apellidos'] ?? null;
        $grado = Flight::request()->query['grado'] ?? null;
        $seccion = Flight::request()->query['seccion'] ?? null;

        // Convertir a entero si es una cadena
        $startFrom = intval($startFrom);
        $limit = intval($limit);

        $controller = new EstudianteController();

        $results = $controller->getAll(false, $limit, $startFrom, $dni, $nombre, $apellidos, $grado, $seccion);

        if($startFrom==0){

            $count = $controller->getStudentCount($dni, $nombre, $apellidos, $grado, $seccion);

            Flight::json(["results" => $results, "count"=>$count], 200);

        }else{

            Flight::json(["results" => $results], 200);         
                           
        }        

    });


    Flight::route("POST ", function(){

        $data = Flight::request()->data->getData();
        $controller = new EstudianteController();
        $controller->create($data);

    });

    Flight::route("PUT /@DNI", function($DNI){

        $data = Flight::request()->data->getData();
        $controller = new EstudianteController();
        $controller->update($DNI,$data);

    });

    Flight::route("DELETE /@DNI", function($DNI){

        $data = Flight::request()->data->getData();
        $controller = new EstudianteController();
        $controller->delete($DNI);

    });


}, [ new AdminAuthenticated(true), new SuperadminAuthenticated()]);

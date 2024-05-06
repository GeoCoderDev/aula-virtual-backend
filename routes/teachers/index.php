<?php

require_once __DIR__ . "/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ . "/../../controllers/Profesor.php";

Flight::group("/api/teachers",  function(){

    Flight::route("GET ", function() {
        // Obtener los parámetros de consulta de la URL
        $startFrom = Flight::request()->query['startFrom'] ?? 0;
        $limit = Flight::request()->query['limit'] ?? 200;
        $dni = Flight::request()->query['dni'] ?? null;
        $nombre = Flight::request()->query['nombre'] ?? null;
        $apellidos = Flight::request()->query['apellidos'] ?? null;

        // Convertir a entero si es una cadena
        $startFrom = intval($startFrom);
        $limit = intval($limit);

        $controller = new ProfesorController();

        $results = $controller->getAll(false, $limit, $startFrom, $dni, $nombre, $apellidos);

        if($startFrom==0){            
            Flight::json(["results"=>$results, "count"=>$controller->getProfessorCount($dni,$nombre,$apellidos)], 200);
        }else{
            Flight::json(["results"=>$results], 200);
        }

    });

    Flight::route("POST ", function(){
        $data = Flight::request()->data->getData();
        $controller = new ProfesorController();
        Flight::json($controller->create($data), 200); // Agregué un retorno aquí
    });

}, [new AdminAuthenticated(true), new SuperadminAuthenticated()]);

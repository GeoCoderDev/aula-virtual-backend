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
        $estado = Flight::request()->query['estado'] ?? null; // Nuevo: agregar estado

        // Convertir a entero si es una cadena
        $startFrom = intval($startFrom);
        $limit = intval($limit);

        $controller = new ProfesorController();

        $results = $controller->getAll(false, $limit, $startFrom, $dni, $nombre, $apellidos, $estado); // Pasar el estado a la función getAll

        if($startFrom==0){            
            Flight::json(["results"=>$results, "count"=>$controller->getProfessorCount($dni,$nombre,$apellidos, $estado)], 200);
        }else{
            Flight::json(["results"=>$results], 200);
        }

    });

    Flight::route("POST ", function(){
        $data = Flight::request()->data->getData();
        $controller = new ProfesorController();
        $controller->create($data);
    });

    Flight::route("PUT /@DNI", function($DNI){
        $data = Flight::request()->data->getData();
        $controller = new ProfesorController();
        $controller->update($DNI, $data);
    });

    Flight::route("DELETE /@DNI", function($DNI){
        $controller = new ProfesorController();
        $controller->delete($DNI);
    });

}, [new AdminAuthenticated(true), new SuperadminAuthenticated()]);

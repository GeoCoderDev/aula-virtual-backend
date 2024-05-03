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
        // Pasar los parámetros a tu método getAll
        Flight::json($controller->getAll(false, $limit, $startFrom, $dni, $nombre, $apellidos, $grado, $seccion), 200);
    });



    Flight::route("GET /count", function(){
        $controller = new EstudianteController();
        Flight::json(["count"=>$controller->getStudentCount()], 200);
    });



    Flight::route("POST ", function(){

        $data = Flight::request()->data->getData();
        $controller = new EstudianteController();
        Flight::json($controller->create($data), 200);

    });

}, [ new AdminAuthenticated(true), new SuperadminAuthenticated()]);

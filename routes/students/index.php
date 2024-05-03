<?php

require_once __DIR__ . "/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ . "/../../controllers/Estudiante.php";

Flight::group("/api/students",  function(){

    Flight::route("GET ", function() {

        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
    // Obtener los parámetros de consulta de la URL
        $startFrom = Flight::request()->query['startFrom'] ?? 0;
        $limit = Flight::request()->query['limit'] ?? 200;

        // Convertir a entero si es una cadena
        $startFrom = intval($startFrom);
        $limit = intval($limit);

        $controller = new EstudianteController();
        // Pasar los parámetros a tu método getAll
        Flight::json($controller->getAll(false, $limit, $startFrom), 200);
    });



    Flight::route("POST ", function(){

        $data = Flight::request()->data->getData();
        $controller = new EstudianteController();
        Flight::json($controller->create($data), 200);

    });

}, [ new AdminAuthenticated(true), new SuperadminAuthenticated()]);

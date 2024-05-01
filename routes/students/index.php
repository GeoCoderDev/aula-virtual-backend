<?php

require_once __DIR__ . "/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ . "/../../controllers/Estudiante.php";

Flight::group("/api/students",  function(){

    Flight::route("GET ", function(){

        $controller = new EstudianteController();
        Flight::json($controller->getAll(), 200);

    });

    Flight::route("POST ", function(){

        $data = Flight::request()->data->getData();
        $controller = new EstudianteController();
        Flight::json($controller->create($data), 200);

    });

}, [ new AdminAuthenticated(true), new SuperadminAuthenticated()]);

<?php

require_once __DIR__ ."/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ ."/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ ."/../../controllers/Profesor.php";

Flight::group("/api/teachers",  function(){

    Flight::route("GET ", function(){

        $controller = new ProfesorController();
        Flight::json($controller->getAll(),200);

    });


    Flight::route("POST ",function(){

        $data = Flight::request()->data->getData();
        $controller = new ProfesorController();
        $controller->create($data);

    });


//Para usar cualquiera de estas rutas el token proporcionado debera ser el de un superadministrador o un administrador
},[new SuperadminAuthenticated(true), new AdminAuthenticated()]);



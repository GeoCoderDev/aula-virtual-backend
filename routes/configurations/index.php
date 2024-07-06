<?php
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";
require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ . "/../../controllers/Configuracion.php";

Flight::group("/api/interfazColor", function () {

    Flight::route("GET ", function () {
        $controller = new ConfiguracionController();

        $controller->getValueByName("Color_Interfaz");
    });
}, [new NotSQLInjection()]);

Flight::group("/api/configurations", function () {


    Flight::route("GET /@name", function ($name) {

        $controller = new ConfiguracionController();

        $controller->getByName($name);
    });

    Flight::route("POST /interfazColor", function () {

        $controller = new ConfiguracionController();

        $data = Flight::request()->data->getData();

        $controller->update("Color_Interfaz", $data);
    });
    
}, [new NotSQLInjection(), new SuperadminAuthenticated()]);

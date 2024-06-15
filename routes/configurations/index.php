<?php
require_once __DIR__."/../../middlewares/isNotSQLInjection.php";
require_once __DIR__."/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__."/../../controllers/Configuracion.php";

Flight::group("/api/configurations", function(){

    Flight::route("GET /@name", function($name){

        $controller = new ConfiguracionController();

        $controller->getByName($name);

    });


},[new NotSQLInjection(), new SuperadminAuthenticated()]);
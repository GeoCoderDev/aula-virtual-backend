<?php


require_once __DIR__ . "/../../controllers/Asignacion.php";
require_once __DIR__ . "/../../middlewares/isTeacherAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";

Flight::group("/api/asignations",  function () {

    Flight::route("GET ", function () {



        $grado = Flight::request()->query['Grado'] ?? null;
        $seccion = Flight::request()->query['Seccion'] ?? null;

        $controller = new AsignacionController();
        $controller->getAsignationsByAula($grado, $seccion);
    });


    /*Flight::route("POST ", function(){


    });*/
}, [new NotSQLInjection(), new TeacherAuthenticated()]);

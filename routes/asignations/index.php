<?php


require_once __DIR__ . "/../../controllers/Asignacion.php";

require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";

Flight::group("/api/asignations",  function () {

    Flight::route("GET /byClassroom", function () {

        $grado = Flight::request()->query['Grado'] ?? null;
        $seccion = Flight::request()->query['Seccion'] ?? null;

        $controller = new AsignacionController();
        $controller->getAsignationsByAula($grado, $seccion);
    });

    Flight::route("GET /byTeacher", function () {

        $DNI_Profesor = Flight::request()->query['DNI'] ?? null;


        $controller = new AsignacionController();
        $controller->getAsignationsByTeacher($DNI_Profesor);
    });
}, [new NotSQLInjection(), new AdminAuthenticated(true), new SuperadminAuthenticated()]);

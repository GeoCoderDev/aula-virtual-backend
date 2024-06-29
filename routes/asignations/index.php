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

        $grado = Flight::request()->query['Grado'] ?? null;
        $seccion = Flight::request()->query['Seccion'] ?? null;

        $controller = new AsignacionController();
        $controller->getAsignationsByAula($grado, $seccion);
    });


}, [new NotSQLInjection(), new AdminAuthenticated(true), new SuperadminAuthenticated()]);

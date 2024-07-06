<?php

require_once __DIR__ . "/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";
require_once __DIR__ . "/../../controllers/Aula.php";

Flight::group("/api/classrooms",  function () {

    Flight::route("GET ", function () {

        $controller = new AulaController();
        $controller->getAllSectionsByGrades();
    });

    Flight::route("GET /grade/@grade/sections", function ($grade) {

        $controller = new AulaController();
        $controller->getSectionsByGrade($grade);
    });


    Flight::route("GET /additionalData", function () {

        $grado = Flight::request()->query['Grado'];
        $seccion = Flight::request()->query['Seccion'];
        

        $controller = new AulaController();

        $controller->getCursosAulaByGradoSeccion($grado, $seccion);
    });

    Flight::route("POST /grade/@grade/sections", function ($grade) {

        $controller = new AulaController();
        $controller->agreeSection($grade);
    });

    Flight::route("DELETE /grade/@grade/sections", function ($grade) {

        $controller = new AulaController();
        $controller->deleteLastSection($grade);
    });
}, [new NotSQLInjection(), new AdminAuthenticated(true), new SuperadminAuthenticated()]);

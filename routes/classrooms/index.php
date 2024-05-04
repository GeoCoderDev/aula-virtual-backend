<?php

require_once __DIR__ . "/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ . "/../../controllers/Aula.php";

Flight::group("/api/classrooms",  function(){


    Flight::route("GET /grade/@grade/sections", function($grade){

        $controller = new AulaController();
        $sections = $controller->getSectionsByGrade($grade);
        Flight::json($sections,200);

    });




}, [new AdminAuthenticated(true), new SuperadminAuthenticated()]);
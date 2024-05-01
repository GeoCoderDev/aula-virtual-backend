<?php

require_once __DIR__ . "/../../middlewares/isStudentAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isTeacherAuthenticated.php";

Flight::group("/api/myAsignations",  function(){

    Flight::route("GET ", function(){

        $data = Flight::request()->data->getData();


        $controller = new ProfesorController();

        $DNI_Profesor = $data["DNI_Profesor"];
        Flight::json($controller->getAsignacionesByDNI($DNI_Profesor) ,200);
        

    });


    /*Flight::route("POST ", function(){


    });*/

}, [new TeacherAuthenticated()]);

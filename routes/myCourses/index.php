<?php

require_once __DIR__ . "/../../middlewares/isStudentAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isTeacherAuthenticated.php";

Flight::group("/api/myCourses",  function(){

    Flight::route("GET ", function(){

        $data = Flight::request()->data->getData();

        if(key_exists("DNI_Estudiante", $data)){
            $controller = new EstudianteController();

            $DNI_Estudiante = $data["DNI_Estudiante"];
            Flight::json($controller->getCursosByDNI($DNI_Estudiante) ,200);

        }else{

            $controller = new ProfesorController();

            $DNI_Profesor = $data["DNI_Profesor"];
            Flight::json($controller->getCursosByDNI($DNI_Profesor) ,200);
        }

    });



}, [new StudentAuthenticated(true), new TeacherAuthenticated()]);

<?php

require_once __DIR__ . "/../../middlewares/isStudentAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isTeacherAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";

Flight::group("/api/myCourses",  function(){

    Flight::route("GET ", function(){

        $data = Flight::request()->data->getData();

        if(key_exists("DNI_Estudiante", $data)){
            $controller = new EstudianteController();

            $DNI_Estudiante = $data["DNI_Estudiante"];
            $controller->getCursosByDNI($DNI_Estudiante);
            

        }else{

            $controller = new ProfesorController();

            $DNI_Profesor = $data["DNI_Profesor"];
            $controller->getCursosByDNI($DNI_Profesor);
            
        }

    });

    Flight::route("GET /@id/data",function ($id){

        $data = Flight::request()->data->getData();

        if(key_exists("DNI_Estudiante", $data)){

            $controller = new EstudianteController();
        
            $controller->getCourseData($id);

        }else{
            $controller = new ProfesorController();
        
            $controller->getCourseData($id);
        }
    });

    Flight::route("POST /@id/access", function($id){

        $data = Flight::request()->data->getData();

        if(key_exists("DNI_Estudiante", $data)){
            $controller = new EstudianteController();
            
            $DNI_Estudiante = $data["DNI_Estudiante"];
            
            $controller->hasAccessToCourse($DNI_Estudiante,$id);

        }else{

            $controller = new ProfesorController();

            $DNI_Profesor = $data["DNI_Profesor"];

            $controller->hasAccessToCourse($DNI_Profesor,$id);
        }

    });


}, [new NotSQLInjection(), new StudentAuthenticated(true), new TeacherAuthenticated()]);

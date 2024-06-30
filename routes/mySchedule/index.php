<?php

require_once __DIR__ . "/../../middlewares/isStudentAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isTeacherAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";

Flight::group("/api/mySchedule",  function () {

    Flight::route("GET ", function () {

        $data = Flight::request()->data->getData();

        if (key_exists("DNI_Estudiante", $data)) {
            $controller = new EstudianteController();

            $DNI_Estudiante = $data["DNI_Estudiante"];
            $controller->getSchedule($DNI_Estudiante);
        } else {

            $controller = new ProfesorController();

            $DNI_Profesor = $data["DNI_Profesor"];
            $controller->getSchedule($DNI_Profesor);
        }
    });
}, [new NotSQLInjection(), new StudentAuthenticated(true), new TeacherAuthenticated()]);

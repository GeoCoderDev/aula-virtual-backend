<?php

require_once __DIR__ . "/../../middlewares/isStudentAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isTeacherAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";
require_once __DIR__ . "/../../controllers/Foro.php";

Flight::group("/api/forum",  function () {

    Flight::route("GET /@id/data", function ($id) {

        $data = Flight::request()->data->getData();

        $DNI_Profesor = $data["DNI_Profesor"] ?? null;
        $DNI_Estudiante = $data["DNI_Estudiante"] ?? null;

        $controller = new ForoController();


        $controller->getForumData($id, $DNI_Profesor, $DNI_Estudiante);
    });

    Flight::route("POST /@id/addResponse", function ($id) {

        $data = Flight::request()->data->getData();

        $controller = new ForoController();

        $controller->addResponse($id, $data);
    });
}, [new NotSQLInjection(), new StudentAuthenticated(true), new TeacherAuthenticated()]);

<?php

require_once __DIR__ . "/../../middlewares/isStudentAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isTeacherAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";
require_once __DIR__ . "/../../controllers/Tema.php";

Flight::group("/api/topics", function () {

    Flight::route("POST ", function () {

        
        $data = Flight::request()->data->getData();

        if(!key_exists("DNI_Profesor", $data)) return Flight::json(["message" => "No estás autorizado para usar este recurso"],401);

        

        $controller = new TemaController();
        $controller->create();
    });

    Flight::route("GET /@id/resources", function ($id) {
        $controller = new TemaController();
        $controller->getResourcesByTopicId($id);
    });

}, [new NotSQLInjection(), new StudentAuthenticated(true), new TeacherAuthenticated()]);

?>

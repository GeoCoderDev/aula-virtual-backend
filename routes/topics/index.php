<?php

require_once __DIR__ . "/../../middlewares/isStudentAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isTeacherAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";
require_once __DIR__ . "/../../controllers/Tema.php";

Flight::group('/api/topics', function () {

    Flight::route('POST ', function () {
        $data = Flight::request()->data->getData();
        $temaController = new TemaController();
        $temaController->create($data);
    });


    Flight::route('PUT /@id', function ($id) {
        $data = Flight::request()->data->getData();
        $temaController = new TemaController();
        $temaController->updateName($id, $data);
    });

}, [new NotSQLInjection(), new StudentAuthenticated(true), new TeacherAuthenticated()]);

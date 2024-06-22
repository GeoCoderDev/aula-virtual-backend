<?php

require_once __DIR__ . '/../../middlewares/isStudentAuthenticated.php';
require_once __DIR__ . '/../../middlewares/isTeacherAuthenticated.php';
require_once __DIR__ . '/../../middlewares/isNotSQLInjection.php';
require_once __DIR__ . '/../../controllers/RecursoTema.php';

Flight::group('/api/topicResources', function () {

    Flight::route("POST /@topicId/addFile", function($topicId){
        $data = Flight::request()->data->getData();
        $controller = new RecursoTemaController();
        $controller->addFileToTopic($topicId, $data);
    });

}, [new NotSQLInjection(), new StudentAuthenticated(true), new TeacherAuthenticated()]);

?>

<?php

require_once __DIR__ . '/../../middlewares/isStudentAuthenticated.php';
require_once __DIR__ . '/../../middlewares/isTeacherAuthenticated.php';
require_once __DIR__ . '/../../middlewares/isNotSQLInjection.php';
require_once __DIR__ . '/../../controllers/RecursoTema.php';

Flight::group('/api/topicResources', function () {

    Flight::route("GET /@topicId", function ($topicId) {
        $controller = new RecursoTemaController();
        $controller->getResourcesByTopicId($topicId);
    });

    Flight::route("POST /@topicId/addFile", function ($topicId) {
        $data = Flight::request()->data->getData();
        $controller = new RecursoTemaController();
        $controller->addFileToTopic($topicId, $data);
    });

    Flight::route("POST /@topicId/addForum", function ($topicId) {
        $data = Flight::request()->data->getData();
        $controller = new RecursoTemaController();
        $controller->addForumToTopic($topicId, $data);
    });

    Flight::route("POST /@topicId/addHomework", function ($topicId) {
        $data = Flight::request()->data->getData();
        $controller = new RecursoTemaController();
        $controller->addHomeworkToTopic($topicId, $data);
    });

    Flight::route("POST /@topicId/addURL", function ($topicId) {
        $data = Flight::request()->data->getData();
        $controller = new RecursoTemaController();
        $controller->addURLToTopic($topicId, $data);
    });
}, [new NotSQLInjection(), new StudentAuthenticated(true), new TeacherAuthenticated()]);

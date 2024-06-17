<?php

require_once __DIR__ . "/../../middlewares/isTeacherAuthenticated.php.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";
require_once __DIR__ . "/../../controllers/Profesor.php";

Flight::group("/api/topics",  function(){

    Flight::route("GET ", function() {
        

    });

 

}, [new NotSQLInjection(), new TeacherAuthenticated()]);

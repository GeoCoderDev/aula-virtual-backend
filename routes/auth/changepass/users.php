<?php

require_once __DIR__."/../../../controllers/Usuario.php";
require_once __DIR__."/../../../lib/helpers/JWT/JWT_Student.php";
require_once __DIR__."/../../../lib/helpers/JWT/JWT_Teacher.php";
require_once __DIR__."/../../../middlewares/isTeacherAuthenticated.php";
require_once __DIR__."/../../../middlewares/isStudentAuthenticated.php";
require_once __DIR__."/../../../middlewares/isNotSQLInjection.php";

Flight::group("/api/auth/me/password", function(){

    Flight::route("PUT ", function(){

    $controller = new UsuarioController();    

    // Obtener los datos de la solicitud
    $data = Flight::request()->data->getData();
        
    // Llamar al método updatePassword() del controlador AdminController con los datos de la nueva contraseña
    $controller->updatePasswordByMe($data); 

    });

}, [new NotSQLInjection(), new StudentAuthenticated(true), new TeacherAuthenticated() ]);
?>

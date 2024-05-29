<?php

require_once __DIR__."/../../../controllers/SuperAdmin.php";
require_once __DIR__ ."/../../../lib/helpers/JWT/JWT_Superadmin.php";
require_once __DIR__."/../../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__."/../../../middlewares/isNotSQLInjection.php";

Flight::group("/api/auth/superadmin/me/password", function(){

    Flight::route("PUT ", function(){

        $controller = new SuperadminController();    

        // Obtener los datos de la solicitud
        $data = Flight::request()->data->getData();
    
        // Llamar al método updatePassword() del controlador SuperadminController con los datos de la nueva contraseña
        $controller->updatePasswordByMe($data);    
        
    });

}, [new NotSQLInjection(), new SuperadminAuthenticated()]);
?>
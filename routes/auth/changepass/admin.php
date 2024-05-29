<?php

require_once __DIR__."/../../../controllers/Admin.php";
require_once __DIR__ ."/../../../lib/helpers/JWT/JWT_Admin.php";
require_once __DIR__."/../../../middlewares/isAdminAuthenticated.php";
require_once __DIR__."/../../../middlewares/isNotSQLInjection.php";

Flight::group("/api/auth/admin/me/password", function() {


    Flight::route("PUT ", function(){

        $controller = new AdminController();

        // Obtener los datos de la solicitud
        $data = Flight::request()->data->getData();
    
        // Llamar al método updatePassword() del controlador AdminController con los datos de la nueva contraseña
        $controller->updatePasswordByMe($data);    
        
    });


}, [new NotSQLInjection(),new AdminAuthenticated()]);

?>

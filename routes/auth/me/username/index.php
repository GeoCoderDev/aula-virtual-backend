<?php 
    require_once __DIR__."/../../../../middlewares/isNotSQLInjection.php";
    require_once __DIR__."/../../../../middlewares/isAdminAuthenticated.php";
    require_once __DIR__."/../../../../controllers/Admin.php";
    require_once __DIR__."/../../../../middlewares/isSuperadminAuthenticated.php";
    require_once __DIR__."/../../../../controllers/SuperAdmin.php";

    Flight::group("api/auth/me/username", function(){

        Flight::route("PUT ", function(){

            $data = Flight::request()->data->getData();

            if(key_exists("Id_Admin", $data)){
                $controller = new AdminController();
                $controller->updatePasswordByMe($data);
            }else{
                $controller = new SuperadminController();
                $controller->updatePasswordByMe($data);
            }

        });


    }, [new NotSQLInjection() , new AdminAuthenticated(true), new SuperadminAuthenticated()]);
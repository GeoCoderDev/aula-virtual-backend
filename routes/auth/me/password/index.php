<?php 
    require_once __DIR__."/../../../../middlewares/isNotSQLInjection.php";
    require_once __DIR__."/../../../../middlewares/isStudentAuthenticated.php";
    require_once __DIR__."/../../../../controllers/Usuario.php";
    require_once __DIR__."/../../../../middlewares/isTeacherAuthenticated.php";
    require_once __DIR__."/../../../../middlewares/isAdminAuthenticated.php";
    require_once __DIR__."/../../../../controllers/Admin.php";
    require_once __DIR__."/../../../../middlewares/isSuperadminAuthenticated.php";
    require_once __DIR__."/../../../../controllers/SuperAdmin.php";
    require_once __DIR__."/../../../../middlewares/isNotSQLInjection.php";

    Flight::group("api/auth/me/password", function(){

        Flight::route("PUT ", function(){

            $data = Flight::request()->data->getData();

            if(key_exists("DNI_Estudiante", $data) || key_exists("DNI_Profesor", $data)){
                $controller = new UsuarioController();
                $controller->updatePasswordByMe($data);

            }else if(key_exists("Id_Admin", $data)){
                $controller = new AdminController();
                $controller->updatePasswordByMe($data);
            }else{
                $controller = new SuperadminController();
                $controller->updatePasswordByMe($data);
            }

        });



    }, [new NotSQLInjection() , new StudentAuthenticated(true), new TeacherAuthenticated(true) ,new AdminAuthenticated(true), new SuperadminAuthenticated()]);
<?php 
    require_once __DIR__."/../../../middlewares/isNotSQLInjection.php";
    require_once __DIR__."/../../../middlewares/isStudentAuthenticated.php";
    require_once __DIR__."/../../../controllers/Estudiante.php";
    require_once __DIR__."/../../../middlewares/isTeacherAuthenticated.php";
    require_once __DIR__."/../../../controllers/Profesor.php";
    require_once __DIR__."/../../../middlewares/isAdminAuthenticated.php";
    require_once __DIR__."/../../../controllers/Admin.php";
    require_once __DIR__."/../../../middlewares/isSuperadminAuthenticated.php";
    require_once __DIR__."/../../../controllers/SuperAdmin.php";

    Flight::group("/api/auth/me",function(){
     
        Flight::route("GET ", function(){            

            $data = Flight::request()->data->getData();

            if(key_exists("DNI_Estudiante", $data)){
                $controller = new EstudianteController();

                $DNI_Estudiante = $data["DNI_Estudiante"];
                $controller->getByDNI($DNI_Estudiante);

            }else if(key_exists("DNI_Profesor", $data)){

                $controller = new ProfesorController();
                $DNI_Profesor = $data["DNI_Profesor"];
                $controller->getByDNI($DNI_Profesor);

            }else if(key_exists("Id_Admin", $data)){
                $controller = new AdminController();
                $Id_Admin = $data["Id_Admin"];
                $controller->getById($Id_Admin);
            }else{
                $controller = new SuperadminController();
                $Id_Superadmin = $data["Id_Superadmin"];
                $controller->getById($Id_Superadmin);
            }

        });

        //Actualizacion
        Flight::route("POST ", function(){

            $data = Flight::request()->data->getData();

            if(key_exists("DNI_Estudiante", $data)){
                $controller = new EstudianteController();
                $DNI_Estudiante = $data["DNI_Estudiante"];
                $controller->updateByMe($DNI_Estudiante, $data);

            }else{

                $controller = new ProfesorController();
                $DNI_Profesor = $data["DNI_Profesor"];
                $controller->updateByMe($DNI_Profesor, $data);
            }

        });




    }, [new NotSQLInjection() , new StudentAuthenticated(true), new TeacherAuthenticated(true), new AdminAuthenticated(true), new SuperadminAuthenticated()]);
<?php 
    include_once __DIR__."/../../../middlewares/isNotSQLInjection.php";
    include_once __DIR__."/../../../middlewares/isStudentAuthenticated.php";
    include_once __DIR__."/../../../controllers/Estudiante.php";
    include_once __DIR__."/../../../middlewares/isTeacherAuthenticated.php";
    include_once __DIR__."/../../../controllers/Profesor.php";
    include_once __DIR__."/../../../middlewares/isAdminAuthenticated.php";
    include_once __DIR__."/../../../controllers/Admin.php";
    include_once __DIR__."/../../../middlewares/isSuperadminAuthenticated.php";
    include_once __DIR__."/../../../controllers/SuperAdmin.php";
    include_once __DIR__."/../../../middlewares/isNotSQLInjection.php";

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
                Flight::json($controller->getById($Id_Admin) ,200);
            }else{
                $controller = new SuperadminController();
                $Id_Superadmin = $data["Id_Superadmin"];
                Flight::json($controller->getById($Id_Superadmin) ,200);
            }

        });

        //Actualizacion
        Flight::route("POST ", function(){

            $data = Flight::request()->data->getData();

            if(key_exists("DNI_Estudiante", $data)){
                $controller = new EstudianteController();
                $DNI_Estudiante = $data["DNI_Estudiante"];


            }else if(key_exists("DNI_Profesor", $data)){

                $controller = new ProfesorController();
                $DNI_Profesor = $data["DNI_Profesor"];


            }else if(key_exists("Id_Admin", $data)){
                $controller = new AdminController();
                $Id_Admin = $data["Id_Admin"];

            }else{
                $controller = new SuperadminController();
                $Id_Superadmin = $data["Id_Superadmin"];

            }

        });

    }, [new NotSQLInjection() , new StudentAuthenticated(true), new TeacherAuthenticated(true) ,new AdminAuthenticated(true), new SuperadminAuthenticated()]);
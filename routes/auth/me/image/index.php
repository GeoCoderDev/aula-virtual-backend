<?php 
    require_once __DIR__."/../../../../middlewares/isNotSQLInjection.php";
    require_once __DIR__."/../../../../middlewares/isStudentAuthenticated.php";
    require_once __DIR__."/../../../../controllers/Estudiante.php";
    require_once __DIR__."/../../../../middlewares/isTeacherAuthenticated.php";
    require_once __DIR__."/../../../../controllers/Profesor.php";
    require_once __DIR__."/../../../../controllers/Usuario.php";

    Flight::group("/api/auth/me/image",function(){
     
        Flight::route("GET ", function(){            

            $data = Flight::request()->data->getData();

            
            if(key_exists("DNI_Estudiante", $data)){
                $controller = new EstudianteController();
                $controller->getProfilePhotoUrl($data["DNI_Estudiante"]);
                
            }else{
                $controller = new ProfesorController();
                $controller->getProfilePhotoUrl($data["DNI_Profesor"]);
            }
        });

    }, [new NotSQLInjection() , new StudentAuthenticated(true), new TeacherAuthenticated()]);

<?php 

    include_once __DIR__."/../../../middlewares/isStudentAuthenticated.php";
    include_once __DIR__."/../../../middlewares/isNotSQLInjection.php";
    include_once __DIR__."/../../../controllers/Estudiante.php";

    Flight::group("/api/auth/me",function(){
     
      /*  Flight::route("GET ", function(){

            $data = Flight::request()->data->getData();
            $DNI_Estudiante = $data["DNI_Estudiante"];

            $controller = new EstudianteController();

            $controller->getByDNI($DNI_Estudiante);                    

        });

        Flight::route("PUT ", function(){

            $data = Flight::request()->data->getData();
            $DNI_Estudiante = $data["DNI_Estudiante"];
            $controller = new EstudianteController();
            $controller->update($DNI_Estudiante, $data);                    

        });
*/
    }, [new NotSQLInjection() ,new StudentAuthenticated()]);
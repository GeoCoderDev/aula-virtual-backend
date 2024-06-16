<?php
require_once __DIR__ . "/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isNotSQLInjection.php";
require_once __DIR__.'/../../controllers/Curso.php';

Flight::group("/api/courses", function (){

    Flight::route('GET ', function(){
        
        $nombre = Flight::request()->query['nombre'] ?? null;
        $grado = Flight::request()->query['grado'] ?? null;
        $startFrom = Flight::request()->query['startFrom'] ?? 0;
        $limit = Flight::request()->query['limit'] ?? 200;

        $cursoController = new CursoController();
        $results = $cursoController->getAll( $startFrom, $limit, $nombre, $grado);

        if($startFrom==0){

            $count = $cursoController->getCursosCount($nombre, $grado);

            Flight::json(["results" => $results, "count"=>$count], 200);

        }else{
            Flight::json(["results" => $results], 200);                                    
        }        


    });

    Flight::route("POST ", function(){
        $data = Flight::request()->data->getData();
        $cursoController = new CursoController();
        $cursoController->createWithAulas($data);
    });

    
    
    Flight::route("PUT /@id", function($id){
        $data = Flight::request()->data->getData();


        // Instanciar el controlador de Curso
        $controller = new CursoController();

        // Actualizar el curso
        $controller->updateWithAulas($id, $data);

    });
    
    Flight::route("DELETE /@id", function($id){
        $controller = new CursoController();
        $controller->delete($id);
    
    });


}, [new NotSQLInjection(),new AdminAuthenticated(true), new SuperadminAuthenticated()]);

<?php
require_once __DIR__ . "/../../middlewares/isAdminAuthenticated.php";
require_once __DIR__ . "/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__.'/../../controllers/Curso.php';

Flight::group("/api/courses", function (){

    Flight::route('GET ', function(){
        $nombre = Flight::request()->query['nombre'] ?? null;
        $grados = Flight::request()->query['grados'] ?? null;
        $startFrom = Flight::request()->query['startFrom'] ?? 0;
        $limit = Flight::request()->query['limit'] ?? 200;

        $cursoController = new CursoController();
        $results = $cursoController->getAll( $startFrom, $limit, $nombre, $grados);

        if($startFrom==0){

            $count = $cursoController->getCursosCount($nombre, $grados);

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
        $result = $controller->delete($id);

        if (isset($result['error'])) {
            Flight::json($result, 404);
        } elseif (isset($result['message'])) {
            Flight::json($result, 200);
        } else {
            Flight::json(["error" => "Error desconocido al eliminar el curso"], 500);
        }
    });


}, [new AdminAuthenticated(true), new SuperadminAuthenticated()]);

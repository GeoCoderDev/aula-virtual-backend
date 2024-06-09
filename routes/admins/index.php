<?php

require_once __DIR__."/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__."/../../middlewares/isNotSQLInjection.php";
require_once __DIR__."/../../controllers/Admin.php";


Flight::group("/api/admins",  function(){

    Flight::route("GET ", function(){
        // Obtener los parámetros de consulta de la URL
        $startFrom = Flight::request()->query['startFrom'] ?? 0;
        $limit = Flight::request()->query['limit'] ?? 200;
        $username = Flight::request()->query['username'] ?? '';

        // Convertir a entero si es una cadena
        $startFrom = intval($startFrom);
        $limit = intval($limit);

        $controller = new AdminController();

        if($startFrom==0){
            Flight::json(["count" => $controller->getAdminCount(), "results"=>$controller->getAll($limit, $startFrom, $username)], 200);
        }else{
    
            // Pasar los parámetros a tu método getAll
            Flight::json(["results"=>$controller->getAll($limit, $startFrom, $username)], 200);
        }
    });    

    //Enviar en el cuerpo, username y password
    Flight::route("POST ", function(){                
        $data = Flight::request()->data->getData();        
        $controller = new AdminController();
        $controller->create($data);        
    });

    Flight::route("POST /multiple", function(){
        $data = Flight::request()->data->getData();        
        $controller = new AdminController();
        $controller->multipleCreate($data);  
    });

    //Enviar newUsername en el cuerpo
    Flight::route("PUT /updateUsername/@id",function ($id){
        $data = Flight::request()->data->getData();        
        $controller = new AdminController();
        $controller->updateUsername($id,$data);
    });

    //Enviar newPassword en el cuerpo
    Flight::route("PUT /updatePassword/@id",function ($id){
        $data = Flight::request()->data->getData();        
        $controller = new AdminController();
        $controller->updatePassword($id,$data);
    });

    Flight::route("DELETE /@id",function ($id){
        $controller = new AdminController();      
        $controller->delete($id);
    });

//Para usar cualquiera de estas rutas el token proporcionado debera ser el de un superadministrador
},[new NotSQLInjection() ,new SuperadminAuthenticated()]);



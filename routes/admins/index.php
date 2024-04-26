<?php

require_once __DIR__."/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__."/../../controllers/Admin.php";


Flight::group("/api/admins",  function(){

    Flight::route("GET ", function(){

        $controller = new AdminController();
        Flight::json($controller->getAll(),200);

    });

    //Enviar en el cuerpo, username y password
    Flight::route("POST ", function(){
                
        $data = Flight::request()->data->getData();        
        $controller = new AdminController();
        $controller->create($data);
        
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
},[new SuperadminAuthenticated()]);



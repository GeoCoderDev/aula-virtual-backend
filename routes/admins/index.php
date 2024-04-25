<?php

require_once __DIR__."/../../middlewares/isSuperadminAuthenticated.php";
require_once __DIR__."/../../controllers/Admin.php";


Flight::group("/api/admins",  function(){


    Flight::route("POST ", function(){
                
        $data = Flight::request()->data->getData();        
        $controller = new AdminController();
        $controller->create($data);
        
    });

    Flight::route("GET ", function(){

        $controller = new AdminController();
        Flight::json($controller->getAll(),200);

    });

},[new SuperadminAuthenticated()]);



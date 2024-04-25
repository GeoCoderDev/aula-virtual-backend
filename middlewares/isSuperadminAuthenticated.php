<?php

require_once __DIR__."/../controllers/SuperAdmin.php";
require_once __DIR__."/../lib/helpers/JWT/JWT_Superadmin.php";

class SuperadminAuthenticated {

    public function before($params) {        
        
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $token = getallheaders()["Authorization"] ?? null;

        
        if(!$token){ 
            return Flight::halt(401, json_encode(["message" => "No estas autorizado para usar esta ruta"])); 
        } 

        $jwtData = decodeSuperAdminJWT($token); 
        
        $controller = new SuperadminController(); 
        $validateResponse = $controller->validateIdAndUsername($jwtData->data); 
                
        if(is_array($validateResponse)){ // Obtener el superadminID y el nombre de usuario 
            $superadminID = $validateResponse["Id_Superadmin"]; 
            $username = $validateResponse['Nombre_Usuario']; 
            
            Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["Id_Superadmin"=>$superadminID, "Nombre_Usuario"=>$username]));

         } else { 
            return Flight::halt(401, json_encode(["message" => "No estas autorizado para usar esta ruta"]));            
        }
    }
    

    /*public function after($params) {
        Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["password"=>23464644545]));
        Flight::json(Flight::request()->data->getData(), 200);
    }*/
}

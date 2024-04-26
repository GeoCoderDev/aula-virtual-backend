<?php

require_once __DIR__."/../controllers/Admin.php";
require_once __DIR__."/../lib/helpers/JWT/JWT_Admin.php";

class AdminAuthenticated {

    public function before($params) {   
        
        if (array_key_exists("Id_Superadmin", Flight::request()->data->getData())) return;
        
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        
        $token = getallheaders()["Authorization"] ?? null;

        if(!$token){ 
            return Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"])); 
        } 

        $jwtData = decodeAdminJWT($token); 
        
        $controller = new AdminController(); 
        $validateResponse = $controller->validateIdAndUsername($jwtData->data); 
                
        if(is_array($validateResponse)){ // Obtener el ID del administrador y el nombre de usuario 
            $adminID = $validateResponse["Id_Admin"]; 
            $username = $validateResponse['Nombre_Usuario']; 
            
            Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["Id_Admin"=>$adminID, "Nombre_Usuario"=>$username]));

         } else { 
            return Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"]));            
        }
    }

}

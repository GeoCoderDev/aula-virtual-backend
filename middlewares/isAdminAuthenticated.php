<?php

require_once __DIR__."/../controllers/Admin.php";
require_once __DIR__."/../lib/helpers/JWT/JWT_Admin.php";

class AdminAuthenticated {

    private $nextMiddleware;

    public function __construct($nextMiddleware = false) {
        $this->nextMiddleware = $nextMiddleware;
    }

    public function before($params) {         
        
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        
        $data = Flight::request()->data->getData();
        
        if (array_key_exists("DNI_Estudiante", $data)) return;
        if (array_key_exists("DNI_Profesor", $data)) return;
        if (array_key_exists("Id_Superadmin", $data)) return;
        
        
        $token = getallheaders()["Authorization"] ?? $data["Authorization"]?? null;

        if(!$token){ 
            

            Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"])); 
            
            return;
        } 

        $jwtData = decodeAdminJWT($token, $this->nextMiddleware); 

        if(is_null($jwtData)) return;  

        $controller = new AdminController(); 
        $validateResponse = $controller->validateIdAndUsername($jwtData->data); 
                
        if(is_array($validateResponse)){ // Obtener el ID del administrador y el nombre de usuario 
            $adminID = $validateResponse["Id_Admin"]; 
            $username = $validateResponse['Nombre_Usuario']; 
            
            Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["Id_Admin"=>$adminID, "Nombre_Usuario_Admin"=>$username]));

         } else { 
            return Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"]));            
        }
    }

}

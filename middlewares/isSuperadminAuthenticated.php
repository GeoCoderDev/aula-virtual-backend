<?php

require_once __DIR__."/../controllers/SuperAdmin.php";
require_once __DIR__."/../lib/helpers/JWT/JWT_Superadmin.php";

class SuperadminAuthenticated {

    private $nextMiddleware;

    public function __construct($nextMiddleware = false) {
        $this->nextMiddleware = $nextMiddleware;
    }

    public function before($params) {        

        $data = Flight::request()->data->getData();

        if (array_key_exists("DNI_Estudiante", $data)) return;
        if (array_key_exists("Id_Admin", $data)) return;
        if (array_key_exists("DNI_Profesor", $data)) return;

        
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $token = getallheaders()["Authorization"] ?? null;

        
        if(!$token){                        
            return Flight::halt(401, json_encode(["message" => "No estas autorizado para usar esta ruta"])); 
        } 

        $jwtData = decodeSuperAdminJWT($token, $this->nextMiddleware); 

        if(is_null($jwtData)) return;                  
        
        $controller = new SuperadminController(); 
        $validateResponse = $controller->validateIdAndUsername($jwtData->data); 
                
        if(is_array($validateResponse)){ // Obtener el superadminID y el nombre de usuario 
            $superadminID = $validateResponse["Id_Superadmin"]; 
            $username = $validateResponse['Nombre_Usuario']; 
            Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["Id_Superadmin"=>$superadminID, "Nombre_Usuario_Superadmin"=>$username]));

         } else { 

            if(!$this->nextMiddleware){
                Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"]));
            }
            
            return ;  
        
        }
    }

}

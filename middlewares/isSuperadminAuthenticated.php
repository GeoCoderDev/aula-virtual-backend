<?php

require_once __DIR__."/../controllers/SuperAdmin.php";
require_once __DIR__."/../lib/helpers/JWT/JWT_Superadmin.php";

class SuperadminAuthenticated {

    private $nextIsAdminMiddleware;

    public function __construct($nextIsAdminMiddleware = false) {
        $this->nextIsAdminMiddleware = $nextIsAdminMiddleware;
    }

    public function before($params) {        
        
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $token = getallheaders()["Authorization"] ?? null;

        
        if(!$token){            
            
            if(!$this->nextIsAdminMiddleware){
                Flight::halt(401, json_encode(["message" => "No estas autorizado para usar esta ruta"])); 
            }

            return;
        } 

        $jwtData = decodeSuperAdminJWT($token, $this->nextIsAdminMiddleware); 

        if(is_null($jwtData)) return;                  
        
        $controller = new SuperadminController(); 
        $validateResponse = $controller->validateIdAndUsername($jwtData->data); 
                
        if(is_array($validateResponse)){ // Obtener el superadminID y el nombre de usuario 
            $superadminID = $validateResponse["Id_Superadmin"]; 
            $username = $validateResponse['Nombre_Usuario']; 
            
            Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["Id_Superadmin"=>$superadminID, "Nombre_Usuario"=>$username]));

         } else { 

            if(!$this->nextIsAdminMiddleware){
                Flight::halt(401, json_encode(["message" => "No estas autorizado para usar esta ruta"]));
            }
            
            return ;            
        }
    }

}

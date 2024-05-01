<?php

require_once __DIR__."/../controllers/Profesor.php";
require_once __DIR__."/../lib/helpers/JWT/JWT_Teacher.php";

class TeacherAuthenticated {

    private $nextIsAdminMiddleware;

    public function __construct($nextIsAdminMiddleware = false) {
        $this->nextIsAdminMiddleware = $nextIsAdminMiddleware;
    }

    public function before($params) {        
        if (array_key_exists("DNI_Estudiante", Flight::request()->data->getData())) return;
        
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $token = getallheaders()["Authorization"] ?? null;

        
        if(!$token){            
            
            if(!$this->nextIsAdminMiddleware){
                Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"])); 
            }

            return;
        } 

        $jwtData = decodeTeacherJWT($token); 

        if(is_null($jwtData)) return;                  
        
        $controller = new ProfesorController(); 
        $validateResponse = $controller->validateDNIAndUsername($jwtData->data); 
                
        if(is_array($validateResponse)){ // Obtener el ID del profesor y el nombre de usuario 
            $profesorID = $validateResponse["DNI_Profesor"]; 
            $username = $validateResponse['Nombre_Usuario']; 
            
            Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["DNI_Profesor"=>$profesorID, "Nombre_Usuario"=>$username]));

         } else { 
            return Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"]));          
        }
    }

}

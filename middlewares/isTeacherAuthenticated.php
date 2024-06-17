<?php

require_once __DIR__."/../controllers/Profesor.php";
require_once __DIR__."/../lib/helpers/JWT/JWT_Teacher.php";

class TeacherAuthenticated {

    private $nextMiddleware;

    public function __construct($nextMiddleware = false) {
        $this->nextMiddleware = $nextMiddleware;
    }

    public function before($params) {
        
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        
        $data = Flight::request()->data->getData();
        
        if (array_key_exists("DNI_Estudiante", $data)) return;
        if (array_key_exists("Id_Admin", $data)) return;
        if (array_key_exists("Id_Superadmin", $data)) return;
        
        $token = getallheaders()["Authorization"] ?? $data["Authorization"]?? null;

        if(!$token){            
            

                Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"])); 
            

            return;
        } 

        $jwtData = decodeTeacherJWT($token, $this->nextMiddleware); 

        if(is_null($jwtData)) return;                  
        
        $controller = new ProfesorController(); 
        $validateResponse = $controller->validateDNIAndUsername($jwtData->data); 
                
        if(is_array($validateResponse)){ // Obtener el ID del profesor y el nombre de usuario 
            $profesorID = $validateResponse["DNI_Profesor"]; 
            $username = $validateResponse['Nombre_Usuario']; 
            
            Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["DNI_Profesor"=>$profesorID, "Nombre_Usuario_Profesor"=>$username]));

         } else { 

            if(!$this->nextMiddleware){
                Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"]));
            }            
            return ;       
        }
    }

}

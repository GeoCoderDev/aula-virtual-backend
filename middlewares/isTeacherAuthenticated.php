<?php

require_once __DIR__."/../controllers/Profesor.php";
require_once __DIR__."/../lib/helpers/JWT/JWT_Teacher.php";

class TeacherAuthenticated {



    public function before($params) {        
        if (array_key_exists("DNI_Estudiante", Flight::request()->data->getData())) return;
        
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $token = getallheaders()["Authorization"] ?? null;

        
        if(!$token){                        
                Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"]));             
        } 

        $jwtData = decodeTeacherJWT($token); 

        if(is_null($jwtData)) return;                  
        
        $controller = new ProfesorController(); 
        $validateResponse = $controller->validateDNIAndUsername($jwtData->data); 
                
        if(is_array($validateResponse)){ // Obtener el ID del profesor y el nombre de usuario 
            $profesorID = $validateResponse["DNI_Profesor"]; 
            $username = $validateResponse['Nombre_Usuario']; 
            
            Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["DNI_Profesor"=>$profesorID, "Nombre_Usuario_Profesor"=>$username]));

         } else { 
            return Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"]));          
        }
    }

}

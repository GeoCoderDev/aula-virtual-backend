<?php

require_once __DIR__."/../controllers/Estudiante.php";
require_once __DIR__."/../lib/helpers/JWT/JWT_Student.php";

class StudentAuthenticated {

    private $nextIsTeacherMiddleware;

    public function __construct($nextIsTeacherMiddleware = false) {
        $this->nextIsTeacherMiddleware = $nextIsTeacherMiddleware;
    }

    public function before($params) {        
        
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $token = getallheaders()["Authorization"] ?? null;

        
        if(!$token){            
            
            if(!$this->nextIsTeacherMiddleware){
                Flight::halt(401, json_encode(["message" => "No estás autorizado para usar este recurso"])); 
            }

            return;
        } 

        $jwtData = decodeStudentJWT($token, $this->nextIsTeacherMiddleware); 

        if(is_null($jwtData)) return;                  
        
        $controller = new EstudianteController(); 
        $validateResponse = $controller->validateDNIAndUsername($jwtData->data); 
                
        if(is_array($validateResponse)){ // Obtener el estudianteID y el nombre de usuario 
            $estudianteID = $validateResponse["DNI_Estudiante"]; 
            $username = $validateResponse['Nombre_Usuario']; 
            
            Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["DNI_Estudiante"=>$estudianteID, "Nombre_Usuario_Estudiante"=>$username]));

         } else { 

            if(!$this->nextIsTeacherMiddleware){
                Flight::halt(401, json_encode(["message" => "No estás autorizado para usar esta ruta"]));
            }
            
            return ;            
        }
    }

}


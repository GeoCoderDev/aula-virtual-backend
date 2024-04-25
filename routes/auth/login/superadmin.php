<?php

require __DIR__."/../../../controllers/SuperAdmin.php";
require __DIR__ ."/../../../lib/helpers/JWT/JWT_Superadmin.php";

Flight::route("POST /api/auth/superadmin/login", function(){

    header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);  

    $controller = new SuperadminController();
    $data = Flight::request()->data->getData(); 
    
    $validateResponse = $controller->validateSuperadmin($data);

    if(is_array($validateResponse)){
        
        // Obtener el superadminID y el nombre de usuario
        $superadminID = $validateResponse["Id_Superadmin"];
        $username = $validateResponse['Nombre_Usuario'];
        
        // Generar el token JWT
        $token = generateSuperadminJWT($superadminID, $username);

        // Devolver el token JWT en la respuesta
        Flight::json(["message" => "Superadministrador logeado", "token" => $token],200);
    } else {
        if($validateResponse==1){
            Flight::json(["message" => "username y password son obligatorios"],400);
        }else{
            Flight::json(['message' => 'Credenciales inválidas'], 401);
        }
    }

});
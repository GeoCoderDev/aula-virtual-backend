<?php

require __DIR__."/../../../controllers/SuperAdmin.php";
require __DIR__ ."/../../../lib/helpers/JWT/JWT_Superadmin.php";

Flight::route("POST /api/auth/superadmin/login", function(){

    header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);    

    $controller = new SuperadminController();
    $data = Flight::request()->data->getData(); 
    
    $dataSuperadminValidateResponse = $controller->validateSuperadmin($data);

    if(is_array($dataSuperadminValidateResponse)){
        
        // Obtener el superadminID y el nombre de usuario
        $superadminID = $dataSuperadminValidateResponse["Id_Superadmin"];
        $username = $dataSuperadminValidateResponse['Nombre_Usuario'];
        
        // Generar el token JWT
        $token = generateSuperadminJWT($superadminID, $username);

        // Devolver el token JWT en la respuesta
        echo json_encode(["message" => "Superadministrador logeado", "token" => $token]);
    } else {
        echo $dataSuperadminValidateResponse;
    }

});
<?php

require_once __DIR__."/../../../controllers/Admin.php";
require_once __DIR__ ."/../../../lib/helpers/JWT/JWT_Admin.php";

Flight::route("POST /api/auth/admin/login", function(){

    header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);  

    $controller = new AdminController();
    $data = Flight::request()->data->getData(); 
    
    $validateResponse = $controller->validateAdmin($data);

    if(is_array($validateResponse)){
        
        // Obtener el adminID y el nombre de usuario
        $adminID = $validateResponse["Id_Admin"];
        $username = $validateResponse['Nombre_Usuario'];
        
        // Generar el token JWT
        $token = generateAdminJWT($adminID, $username);

        // Devolver el token JWT en la respuesta
        Flight::json(["message" => "Administrador autenticado", "token" => $token, "rol" => "admin"], 200);
    } else {
        if($validateResponse==1){
            Flight::json(["message" => "Nombre de usuario y contraseña son obligatorios"], 400);
        } else {
            Flight::json(['message' => 'Credenciales inválidas'], 401);
        }
    }

});
?>

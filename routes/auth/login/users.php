<?php

require_once __DIR__."/../../../controllers/Usuario.php";

Flight::route("POST /api/auth/login", function(){

    header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);  

    $controller = new UsuarioController();
    $data = Flight::request()->data->getData(); 
    
    // Llama a una función para validar el usuario
    $validateResponse = $controller->validateUser($data);

    if(is_string($validateResponse)){    
        // Devuelve el token JWT en la respuesta
        Flight::json(["message" => "Usuario autenticado", "token" => $validateResponse], 200);
    } else {
        if($validateResponse == 1){
            Flight::json(["message" => "Nombre de usuario y contraseña son obligatorios"], 400);
        } else {
            Flight::json(['message' => 'Credenciales inválidas'], 401);
        }
    }

});

?>

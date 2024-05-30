<?php

require_once __DIR__."/../../../controllers/Usuario.php";
require_once __DIR__."/../../../middlewares/isNotSQLInjection.php";

Flight::group("/api/auth/login",function(){

    Flight::route("POST ", function(){
        header("Access-Control-Allow-Origin: ".ALLOWED_ORIGINS);  
        $controller = new UsuarioController();
        $data = Flight::request()->data->getData(); 
        
        // Llama a una función para validar el usuario
        $validateResponse = $controller->validateUser($data);
    
        if (is_array($validateResponse) && isset($validateResponse['token'])) {    
            // Devuelve el token JWT en la respuesta junto con el rol
            Flight::json(["message" => "Usuario autenticado", "token" => $validateResponse['token'], "role" => $validateResponse['role']], 200);
        } else {
            if ($validateResponse === 1) {
                Flight::json(["message" => "Nombre de usuario y contraseña son obligatorios"], 400);
            } else if($validateResponse === 2) {
                Flight::json(['message' => 'Nombre de Usuario y/o Contraseña incorrectos'], 401);
            }
        }
    });

},[new NotSQLInjection()]);

?>

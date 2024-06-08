<?php

require_once __DIR__."/../functions/totalTimeInSeconds.php";

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

 // El token expirará en 1 dia
define("EXPIRATION_TIME_TOKEN_SUPERADMIN", totalTimeInSeconds(1,0,0,0));

$JWT_KEY_SUPERADMIN = $_ENV["JWT_KEY_SUPERADMINS"];

function generateSuperadminJWT($superadminID, $username) {

    global $JWT_KEY_SUPERADMIN;

    $issuedAt = time();
    $expirationTime = $issuedAt + EXPIRATION_TIME_TOKEN_SUPERADMIN;

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'data' => [
            'Id_Superadmin' => $superadminID,
            'Username_Superadmin' => $username
        ]
    ];

    $jwt = JWT::encode($payload, $JWT_KEY_SUPERADMIN, 'HS256');

    return $jwt;
}

function decodeSuperAdminJWT($token, $nextMiddleware){
    global $JWT_KEY_SUPERADMIN;

    try {
        $decoded = JWT::decode($token, new Key($JWT_KEY_SUPERADMIN, "HS256"));
        return $decoded;
    } catch (ExpiredException $e) {
        if(!$nextMiddleware)
            Flight::halt(401, json_encode(['message' => 'El token ha expirado']));
        else return null;  
    } catch (Exception $e) {
        if(!$nextMiddleware)
            // También puedes enviar una respuesta de error al cliente
            Flight::halt(401, json_encode(['message' => 'Token inválido', 'content' => 'Error al decodificar el token: ' . $e->getMessage()]));        
        else return null;
    }
}

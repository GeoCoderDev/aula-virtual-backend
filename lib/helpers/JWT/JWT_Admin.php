<?php

require_once __DIR__."/../functions/totalTimeInSeconds.php";

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

// El token expirará en 1 día
define("EXPIRATION_TIME_TOKEN_ADMIN", totalTimeInSeconds(1, 0, 0, 0));

$JWT_KEY_ADMIN = $_ENV["JWT_KEY_ADMINS"];

function generateAdminJWT($adminID, $username) {
    global $JWT_KEY_ADMIN;

    $issuedAt = time();
    $expirationTime = $issuedAt + EXPIRATION_TIME_TOKEN_ADMIN;

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'data' => [
            'Id_Admin' => $adminID,
            'Username_Admin' => $username
        ]
    ];

    $jwt = JWT::encode($payload, $JWT_KEY_ADMIN, 'HS256');

    return $jwt;
}

function decodeAdminJWT($token, $nextIsSuperadminMiddleware) {
    global $JWT_KEY_ADMIN;

    try {
        $decoded = JWT::decode($token, new Key($JWT_KEY_ADMIN, "HS256"));
        return $decoded;
    } catch (ExpiredException $e) {
        if(!$nextIsSuperadminMiddleware)
            Flight::halt(401, json_encode(['message' => 'El token ha expirado']));
        else return null;        
    } catch (Exception $e) {
        if(!$nextIsSuperadminMiddleware)
            // También puedes enviar una respuesta de error al cliente
            Flight::halt(401, json_encode(['message' => 'Token inválido', 'content' => 'Error al decodificar el token: ' . $e->getMessage()]));        
        else return null;
    }
}
?>

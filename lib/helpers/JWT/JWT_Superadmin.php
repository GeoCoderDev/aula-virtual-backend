<?php

require __DIR__."/../functions/totalTimeInSeconds.php";

use Firebase\JWT\JWT;

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
            'superadminID' => $superadminID,
            'username' => $username
        ]
    ];

    $jwt = JWT::encode($payload, $JWT_KEY_SUPERADMIN, 'HS256');

    return $jwt;
}

function  decodeSuperAdminJWT($token){
    global $JWT_KEY_SUPERADMIN;

    $jwt = JWT::decode($token, $JWT_KEY_SUPERADMIN);

    return $jwt;

}
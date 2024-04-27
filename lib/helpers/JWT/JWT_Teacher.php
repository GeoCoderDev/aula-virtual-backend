<?php

require_once __DIR__."/../functions/totalTimeInSeconds.php";

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;

// El token expirará en 1 día
define("EXPIRATION_TIME_TOKEN_TEACHER", totalTimeInSeconds(1, 0, 0, 0));

$JWT_KEY_TEACHER = $_ENV["JWT_KEY_TEACHERS"];

function generateTeacherJWT($teacherID, $username) {
    global $JWT_KEY_TEACHER;

    $issuedAt = time();
    $expirationTime = $issuedAt + EXPIRATION_TIME_TOKEN_TEACHER;

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'data' => [
            'teacherID' => $teacherID,
            'username' => $username
        ]
    ];

    $jwt = JWT::encode($payload, $JWT_KEY_TEACHER, 'HS256');

    return $jwt;
}

function decodeTeacherJWT($token) {
    global $JWT_KEY_TEACHER;

    try {
        $decoded = JWT::decode($token, new Key($JWT_KEY_TEACHER, "HS256"));
        return $decoded;
    } catch (ExpiredException $e) {
        Flight::halt(401, json_encode(['message' => 'El token ha expirado']));
    } catch (Exception $e) {
        // También puedes enviar una respuesta de error al cliente
        Flight::halt(401, json_encode(['message' => 'Token inválido', 'content' => 'Error al decodificar el token: ' . $e->getMessage()]));
    }
}

?>

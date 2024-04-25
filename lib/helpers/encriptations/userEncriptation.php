<?php


$Encript_key_passwords_users = $_ENV["ENCRIPT_KEY_PASSWORD_USERS"];

// Función para encriptar una contraseña
function encryptUserPassword($password) {
    global $Encript_key_passwords_users;
    $encrypted = openssl_encrypt($password, 'aes-256-cbc', $Encript_key_passwords_users, 0, substr($Encript_key_passwords_users, 0, 16));
    return base64_encode($encrypted);
}

// Función para desencriptar una contraseña
function decryptUserPassword($encryptedPassword) {
    global $Encript_key_passwords_users;
    $decoded = base64_decode($encryptedPassword);
    $decrypted = openssl_decrypt($decoded, 'aes-256-cbc', $Encript_key_passwords_users, 0, substr($Encript_key_passwords_users, 0, 16));
    return $decrypted;
}

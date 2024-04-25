<?php


$Encript_key_username_superadmin = $_ENV["ENCRIPT_KEY_USERNAME_SUPERADMIN"];
$Encript_key_password_superadmin = $_ENV["ENCRIPT_KEY_PASSWORD_SUPERADMIN"];

// Función para encriptar el nombre de usuario del superadministrador
function encryptSuperadminUsername($username) {
    global $Encript_key_username_superadmin;
    $encrypted = openssl_encrypt($username, 'aes-256-cbc', $Encript_key_username_superadmin, 0, substr($Encript_key_username_superadmin, 0, 16));
    return base64_encode($encrypted);
}

// Función para desencriptar el nombre de usuario del superadministrador
function decryptSuperadminUsername($encryptedUsername) {
    global $Encript_key_username_superadmin;
    $decoded = base64_decode($encryptedUsername);
    $decrypted = openssl_decrypt($decoded, 'aes-256-cbc', $Encript_key_username_superadmin, 0, substr($Encript_key_username_superadmin, 0, 16));
    return $decrypted;
}

// Función para encriptar la contraseña del superadministrador
function encryptSuperadminPassword($password) {
    global $Encript_key_password_superadmin;
    $encrypted = openssl_encrypt($password, 'aes-256-cbc', $Encript_key_password_superadmin, 0, substr($Encript_key_password_superadmin, 0, 16));
    return base64_encode($encrypted);
}

// Función para desencriptar la contraseña del superadministrador
function decryptSuperadminPassword($encryptedPassword) {
    global $Encript_key_password_superadmin;
    $decoded = base64_decode($encryptedPassword);
    $decrypted = openssl_decrypt($decoded, 'aes-256-cbc', $Encript_key_password_superadmin, 0, substr($Encript_key_password_superadmin, 0, 16));
    return $decrypted;
}
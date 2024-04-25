<?php


$Encript_key_username_admin = $_ENV["ENCRIPT_KEY_USERNAME_ADMIN"];
$Encript_key_password_superadmin = $_ENV["ENCRIPT_KEY_PASSWORD_ADMIN"];

// Función para encriptar el nombre de usuario del superadministrador
function encryptAdminUsername($username) {
    global $Encript_key_username_admin;
    $encrypted = openssl_encrypt($username, 'aes-256-cbc', $Encript_key_username_admin, 0, substr($Encript_key_username_admin, 0, 16));
    return base64_encode($encrypted);
}

// Función para desencriptar el nombre de usuario del superadministrador
function decryptAdminUsername($encryptedUsername) {
    global $Encript_key_username_admin;
    $decoded = base64_decode($encryptedUsername);
    $decrypted = openssl_decrypt($decoded, 'aes-256-cbc', $Encript_key_username_admin, 0, substr($Encript_key_username_admin, 0, 16));
    return $decrypted;
}

// Función para encriptar la contraseña del superadministrador
function encryptAdminPassword($password) {
    global $Encript_key_password_admin;
    $encrypted = openssl_encrypt($password, 'aes-256-cbc', $Encript_key_password_admin, 0, substr($Encript_key_password_admin, 0, 16));
    return base64_encode($encrypted);
}

// Función para desencriptar la contraseña del superadministrador
function decryptAdminPassword($encryptedPassword) {
    global $Encript_key_password_admin;
    $decoded = base64_decode($encryptedPassword);
    $decrypted = openssl_decrypt($decoded, 'aes-256-cbc', $Encript_key_password_admin, 0, substr($Encript_key_password_admin, 0, 16));
    return $decrypted;
}
<?php

namespace Config;
use PDO;
use PDOException;

class Database {
    public static function getConnection() {
        $host = 'localhost';
        $dbname = 'tienda';
        $username = 'root';
        $password = '';

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            // Configura PDO para que lance excepciones en caso de errores
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}

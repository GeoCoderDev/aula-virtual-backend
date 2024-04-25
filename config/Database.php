<?php

namespace Config;
use PDO;
use PDOException;

class Database {
    public static function getConnection() {
        $host = $_ENV["MYSQL_DB_HOST"];
        $port = $_ENV["MYSQL_DB_PORT"];
        $dbname = $_ENV["MYSQL_DB_NAME"];
        $username = $_ENV["MYSQL_DB_USER"];
        $password = $_ENV["MYSQL_DB_PASSWORD"];

        try {
            $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
            // Configura PDO para que lance excepciones en caso de errores
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}

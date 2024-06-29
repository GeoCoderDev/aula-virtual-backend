<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $host = $_ENV["MYSQL_DB_HOST"];
        $port = $_ENV["MYSQL_DB_PORT"];
        $dbname = $_ENV["MYSQL_DB_NAME"];
        $username = $_ENV["MYSQL_DB_USER"];
        $password = $_ENV["MYSQL_DB_PASSWORD"];

        try {
            $this->conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
            // Configura PDO para que lance excepciones en caso de errores
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getConnection()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->conn;
    }
}

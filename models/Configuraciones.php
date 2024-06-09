<?php

require_once __DIR__ . '/../config/Database.php';
use Config\Database;

class Configuracion {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll($limit = 200, $startFrom = 0) {
        $query = "SELECT * FROM T_Configuraciones LIMIT :startFrom, :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':startFrom', $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByName($nombreConf) {
        $stmt = $this->conn->prepare("SELECT * FROM T_Configuraciones WHERE Nombre_Conf = :nombreConf");
        $stmt->execute(['nombreConf' => $nombreConf]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nombreConf, $valor, $descripcion = null) {
        $stmt = $this->conn->prepare("INSERT INTO T_Configuraciones (Nombre_Conf, Valor, Descripcion) VALUES (:nombreConf, :valor, :descripcion)");
        $stmt->execute(['nombreConf' => $nombreConf, 'valor' => $valor, 'descripcion' => $descripcion]);
        return $this->conn->lastInsertId();
    }

    public function update($nombreConf, $valor, $descripcion = null) {
        $stmt = $this->conn->prepare("UPDATE T_Configuraciones SET Valor = :valor, Descripcion = :descripcion, Ultima_Actualizacion = CURRENT_TIMESTAMP WHERE Nombre_Conf = :nombreConf");
        $stmt->execute(['nombreConf' => $nombreConf, 'valor' => $valor, 'descripcion' => $descripcion]);
        return $stmt->rowCount();
    }

    public function delete($nombreConf) {
        $stmt = $this->conn->prepare("DELETE FROM T_Configuraciones WHERE Nombre_Conf = :nombreConf");
        $stmt->execute(['nombreConf' => $nombreConf]);
        return $stmt->rowCount();
    }
}
?>

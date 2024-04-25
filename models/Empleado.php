<?php

require_once __DIR__ . '../../Config/Database.php';
use Config\Database;

class Empleado {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM empleado");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM empleado WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByIdAndName($id, $nombre){
        $stmt = $this->conn->prepare('SELECT * FROM empleado WHERE id = :yu AND nombre = :hjk');
        $stmt->execute(['yu'=> $id,'hjk'=> $nombre]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nombre, $sueldo) {
        $stmt = $this->conn->prepare("INSERT INTO empleado (nombre, sueldo) VALUES (:nombre, :sueldo)");
        $stmt->execute(['nombre' => $nombre, 'sueldo' => $sueldo]);
        return $this->conn->lastInsertId();
    }

    public function update($id, $nombre, $sueldo) {
        $stmt = $this->conn->prepare("UPDATE empleado SET nombre = :nombre, sueldo = :sueldo WHERE id = :id");
        $stmt->execute(['id' => $id, 'nombre' => $nombre, 'sueldo' => $sueldo]);
        return $stmt->rowCount();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM empleado WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}


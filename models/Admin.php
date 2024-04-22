<?php

require __DIR__ . '../../Config/Database.php';
use Config\Database;

class Admin {
    private $conn;
    private $nombre_tabla_BD = "T_Admins";

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM ".$this->nombre_tabla_BD."");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->nombre_tabla_BD." WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nombre, $sueldo) {
        $stmt = $this->conn->prepare("INSERT INTO ".$this->nombre_tabla_BD." (nombre, sueldo) VALUES (:nombre, :sueldo)");
        $stmt->execute(['nombre' => $nombre, 'sueldo' => $sueldo]);
        return $this->conn->lastInsertId();
    }

    public function update($id, $nombre, $sueldo) {
        $stmt = $this->conn->prepare("UPDATE ".$this->nombre_tabla_BD." SET nombre = :nombre, sueldo = :sueldo WHERE id = :id");
        $stmt->execute(['id' => $id, 'nombre' => $nombre, 'sueldo' => $sueldo]);
        return $stmt->rowCount();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM ".$this->nombre_tabla_BD." WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}

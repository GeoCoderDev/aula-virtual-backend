<?php
require_once __DIR__."/./Usuario.php";

use Config\Database;

class Profesor{
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM T_Profesores");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByDNI($dni) {
        $stmt = $this->conn->prepare("SELECT * FROM T_Profesores WHERE DNI_Profesor = :dni");
        $stmt->execute(['dni' => $dni]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM T_Profesores WHERE Id_Usuario = :userId");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($dni, $userId) {
        $stmt = $this->conn->prepare("INSERT INTO T_Profesores (DNI_Profesor, Id_Usuario) VALUES (:dni, :userId)");
        $stmt->execute(['dni' => $dni, 'userId' => $userId]);
        return $this->conn->lastInsertId();
    }

    public function update($dni, $userId) {
        $stmt = $this->conn->prepare("UPDATE T_Profesores SET Id_Usuario = :userId WHERE DNI_Profesor = :dni");
        $stmt->execute(['dni' => $dni, 'userId' => $userId]);
        return $stmt->rowCount();
    }

    public function delete($dni) {
        $stmt = $this->conn->prepare("DELETE FROM T_Profesores WHERE DNI_Profesor = :dni");
        $stmt->execute(['dni' => $dni]);
        return $stmt->rowCount();
    }
}

?>

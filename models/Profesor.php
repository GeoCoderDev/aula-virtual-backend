<?php
require_once __DIR__."/./Usuario.php";

use Config\Database;

class Profesor{
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT P.*, U.* FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDNI($dni) {
        $stmt = $this->conn->prepare("SELECT P.*, U.* FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.DNI_Profesor = :dni");
        $stmt->execute(['dni' => $dni]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($userId) {
        $stmt = $this->conn->prepare("SELECT P.*, U.* FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.Id_Usuario = :userId");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($dni, $userId) {
        $stmt = $this->conn->prepare("INSERT INTO T_Profesores (DNI_Profesor, Id_Usuario) VALUES (:dni, :userId)");
        $success = $stmt->execute(['dni' => $dni, 'userId' => $userId]);
        return $success;
    }

    public function getByUsername($username) {
        $stmt = $this->conn->prepare("SELECT P.*, U.* FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE U.Nombre_Usuario = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

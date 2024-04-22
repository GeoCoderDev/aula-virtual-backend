<?php

require_once __DIR__ . '../../Config/Database.php';
use Config\Database;

class Admin {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM T_Admins");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM T_Admins WHERE Id_Admin = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nombreUsuario, $contrasena) {
        $stmt = $this->conn->prepare("INSERT INTO T_Admins (Nombre_Usuario, Contraseña) VALUES (:nombreUsuario, :contrasena)");
        $stmt->execute(['nombreUsuario' => $nombreUsuario, 'contrasena' => $contrasena]);
        return $this->conn->lastInsertId();
    }

    public function updateUsername($id, $newUsername) {
        $stmt = $this->conn->prepare("UPDATE T_Admins SET Nombre_Usuario = :newUsername WHERE Id_Admin = :id");
        $stmt->execute(['id' => $id, 'newUsername' => $newUsername]);
        return $stmt->rowCount();
    }

    public function updatePassword($id, $newPassword) {
        $stmt = $this->conn->prepare("UPDATE T_Admins SET Contraseña = :newPassword WHERE Id_Admin = :id");
        $stmt->execute(['id' => $id, 'newPassword' => $newPassword]);
        return $stmt->rowCount();
    }
}

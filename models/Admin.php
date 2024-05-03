<?php

require_once __DIR__ . '../../Config/Database.php';
use Config\Database;

class Admin {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll($limit = 200, $startFrom = 0) {
        $query = "SELECT Id_Admin, Nombre_Usuario FROM T_Administradores";

        if ($limit !== null) {
            $query .= " LIMIT :startFrom,:limit";
        }

        $stmt = $this->conn->prepare($query);

        if ($limit !== null) {
            $stmt->bindValue(':startFrom', $startFrom, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($admins as $key => $admin) {
            $admin["Nombre_Usuario"] = decryptAdminUsername($admin["Nombre_Usuario"]);
            $admins[$key] = $admin;
        }

        return $admins;
    }

    public function getAdminCount() {
        $stmt = $this->conn->query("SELECT COUNT(*) AS count FROM T_Administradores");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }


    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM T_Administradores WHERE Id_Admin = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM T_Administradores WHERE Nombre_Usuario = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nombreUsuario, $contrasena) {
        $stmt = $this->conn->prepare("INSERT INTO T_Administradores (Nombre_Usuario, Contraseña) VALUES (:nombreUsuario, :contrasena)");
        $stmt->execute(['nombreUsuario' => $nombreUsuario, 'contrasena' => $contrasena]);
        return $this->conn->lastInsertId();
    }

    public function updateUsername($id, $newUsername) {
        $stmt = $this->conn->prepare("UPDATE T_Administradores SET Nombre_Usuario = :newUsername WHERE Id_Admin = :id");
        $stmt->execute(['id' => $id, 'newUsername' => $newUsername]);
        return $stmt->rowCount();
    }

    public function updatePassword($id, $newPassword) {
        $stmt = $this->conn->prepare("UPDATE T_Administradores SET Contraseña = :newPassword WHERE Id_Admin = :id");
        $stmt->execute(['id' => $id, 'newPassword' => $newPassword]);
        return $stmt->rowCount();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM T_Administradores WHERE Id_Admin = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }

}

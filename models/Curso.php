<?php
require_once __DIR__ . '../../Config/Database.php';
use Config\Database;

class Curso
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM T_Cursos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($Id_Curso)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Cursos WHERE Id_Curso = :Id_Curso");
        $stmt->execute(['Id_Curso' => $Id_Curso]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByNombre($Nombre)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Cursos WHERE Nombre = :Nombre");
        $stmt->execute(['Nombre' => $Nombre]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($Nombre)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Cursos (Nombre) VALUES (:Nombre)");
        $stmt->execute(['Nombre' => $Nombre]);
        return $this->conn->lastInsertId();
    }

    public function update($Id_Curso, $Nombre)
    {
        $stmt = $this->conn->prepare("UPDATE T_Cursos SET Nombre = :Nombre WHERE Id_Curso = :Id_Curso");
        $stmt->execute(['Id_Curso' => $Id_Curso, 'Nombre' => $Nombre]);
        return $stmt->rowCount();
    }

    public function delete($Id_Curso)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Cursos WHERE Id_Curso = :Id_Curso");
        $stmt->execute(['Id_Curso' => $Id_Curso]);
        return $stmt->rowCount();
    }
}
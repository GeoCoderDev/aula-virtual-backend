<?php
require_once __DIR__ . '/../config/Database.php';
use Config\Database;

class Curso_Aula
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM T_Cursos_Aula");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($Id_Curso_Aula)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Cursos_Aula WHERE Id_Curso_Aula = :Id_Curso_Aula");
        $stmt->execute(['Id_Curso_Aula' => $Id_Curso_Aula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByCursoAula($Id_Curso, $Id_Aula)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Cursos_Aula WHERE Id_Curso = :Id_Curso AND Id_Aula = :Id_Aula");
        $stmt->execute(['Id_Curso' => $Id_Curso, 'Id_Aula' => $Id_Aula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($Id_Curso, $Id_Aula)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Cursos_Aula (Id_Curso, Id_Aula) VALUES (:Id_Curso, :Id_Aula)");
        $stmt->execute(['Id_Curso' => $Id_Curso, 'Id_Aula' => $Id_Aula]);
        return $this->conn->lastInsertId();
    }

    public function update($Id_Curso_Aula, $Id_Curso, $Id_Aula)
    {
        $stmt = $this->conn->prepare("UPDATE T_Cursos_Aula SET Id_Curso = :Id_Curso, Id_Aula = :Id_Aula WHERE Id_Curso_Aula = :Id_Curso_Aula");
        $stmt->execute(['Id_Curso_Aula' => $Id_Curso_Aula, 'Id_Curso' => $Id_Curso, 'Id_Aula' => $Id_Aula]);
        return $stmt->rowCount();
    }

    public function delete($Id_Curso_Aula)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Cursos_Aula WHERE Id_Curso_Aula = :Id_Curso_Aula");
        $stmt->execute(['Id_Curso_Aula' => $Id_Curso_Aula]);
        return $stmt->rowCount();
    }
}
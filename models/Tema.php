<?php
require_once __DIR__ . '/../config/Database.php';
use Config\Database;

class Tema
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM T_Temas");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($Id_Tema)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Temas WHERE Id_Tema = :Id_Tema");
        $stmt->execute(['Id_Tema' => $Id_Tema]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByNombre($Nombre_Tema)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Temas WHERE Nombre_Tema = :Nombre_Tema");
        $stmt->execute(['Nombre_Tema' => $Nombre_Tema]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByIDCursoAula($Id_Curso_Aula)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Temas WHERE Id_Curso_Aula = :Id_Curso_Aula");
        $stmt->execute(['Id_Curso_Aula' => $Id_Curso_Aula]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCursoAula($Id_Curso, $Id_Aula) {
        $stmt = $this->conn->prepare("SELECT * FROM T_Temas WHERE Id_Curso_Aula = (SELECT Id_Curso_Aula FROM T_Cursos_Aula WHERE Id_Curso = :Id_Curso AND Id_Aula = :Id_Aula)");
        $stmt->execute(['Id_Curso' => $Id_Curso, 'Id_Aula' => $Id_Aula]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($Nombre_Tema, $Id_Curso_Aula, $Num_Orden)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Temas (Nombre_Tema, Id_Curso_Aula, Num_Orden) VALUES (:Nombre_Tema, :Id_Curso_Aula, :Num_Orden)");
        $stmt->execute(['Nombre_Tema' => $Nombre_Tema, 'Id_Curso_Aula' => $Id_Curso_Aula, 'Num_Orden' => $Num_Orden]);
        return $this->conn->lastInsertId();
    }

    public function update($Id_Tema, $Nombre_Tema, $Id_Curso_Aula, $Num_Orden)
    {
        $stmt = $this->conn->prepare("UPDATE T_Temas SET Nombre_Tema = :Nombre_Tema, Id_Curso_Aula = :Id_Curso_Aula, Num_Orden = :Num_Orden WHERE Id_Tema = :Id_Tema");
        $stmt->execute(['Id_Tema' => $Id_Tema, 'Nombre_Tema' => $Nombre_Tema, 'Id_Curso_Aula' => $Id_Curso_Aula, 'Num_Orden' => $Num_Orden]);
        return $stmt->rowCount();
    }

    public function delete($Id_Tema)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Temas WHERE Id_Tema = :Id_Tema");
        $stmt->execute(['Id_Tema' => $Id_Tema]);
        return $stmt->rowCount();
    }
}
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
        $query = "SELECT * FROM T_Temas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT * FROM T_Temas WHERE Id_Tema = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAdditionalDataById($id)
    {
        $query = "SELECT 
                T_Temas.Nombre_Tema, 
                T_Aulas.Grado, 
                T_Aulas.Seccion, 
                T_Cursos.Nombre AS Nombre_Curso
              FROM T_Temas
              JOIN T_Cursos_Aula ON T_Temas.Id_Curso_Aula = T_Cursos_Aula.Id_Curso_Aula
              JOIN T_Aulas ON T_Cursos_Aula.Id_Aula = T_Aulas.Id_Aula
              JOIN T_Cursos ON T_Cursos_Aula.Id_Curso = T_Cursos.Id_Curso
              WHERE T_Temas.Id_Tema = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function create($nombre, $cursoAulaId)
    {
        $query = "INSERT INTO T_Temas (Nombre_Tema, Id_Curso_Aula) VALUES (:nombre, :cursoAulaId)";
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute(['nombre' => $nombre, 'cursoAulaId' => $cursoAulaId])) {
            return $this->conn->lastInsertId();
        } else {
            return false;
        }
    }


    public function update($id, $nombre, $descripcion)
    {
        $query = "UPDATE T_Temas SET Nombre_Tema = :nombre WHERE Id_Tema = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'nombre' => $nombre
        ]);
    }

    public function delete($id)
    {
        $query = "DELETE FROM T_Temas WHERE Id_Tema = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateName($id, $newName)
    {
        $query = "UPDATE T_Temas SET Nombre_Tema = :newName WHERE Id_Tema = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id, 'newName' => $newName]);
    }

    public function existsByNombreAndCursoAula($nombre, $cursoAulaId)
    {
        $query = "SELECT COUNT(*) FROM T_Temas WHERE Nombre_Tema = :nombre AND Id_Curso_Aula = :cursoAulaId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['nombre' => $nombre, 'cursoAulaId' => $cursoAulaId]);
        return $stmt->fetchColumn() > 0;
    }
}

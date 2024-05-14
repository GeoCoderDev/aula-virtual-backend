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
    public function getAll($startFrom = 0, $limit = 200, $nombre = null, $grados = null)
    {
        $query = "SELECT C.Id_Curso, C.Nombre AS Nombre_Curso, (SELECT GROUP_CONCAT(DISTINCT A.Grado) FROM T_Cursos_Aula AS CA_Inner INNER JOIN T_Aulas AS A ON CA_Inner.Id_Aula = A.Id_Aula WHERE CA_Inner.Id_Curso = C.Id_Curso) AS Grados  FROM T_Cursos AS C ";

        // Agregar condiciones según los parámetros de búsqueda
        if ($grados !== null) {
            $query .= "WHERE EXISTS ( SELECT 1 FROM T_Cursos_Aula AS CA_Inner INNER JOIN T_Aulas AS A ON CA_Inner.Id_Aula = A.Id_Aula  WHERE CA_Inner.Id_Curso = C.Id_Curso AND A.Grado = :grado) ";
        }

        if ($nombre !== null) {
            $query .= ($grados !== null ? "AND" : "WHERE") . " C.Nombre LIKE :nombre ";
        }

        $query .= "LIMIT :startFrom, :limit";

        $stmt = $this->conn->prepare($query);

        // Vincular los parámetros
        if ($grados !== null) {
            $stmt->bindValue(':grado', $grados, PDO::PARAM_INT);
        }
        if ($nombre !== null) {
            $stmt->bindValue(':nombre', '%' . $nombre . '%', PDO::PARAM_STR);
        }
        $stmt->bindValue(':startFrom', $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $cursos;
    }

public function getCursosCount($nombre = null, $grados = null)
{
    $query = "SELECT COUNT(*) AS count FROM T_Cursos AS C ";

    // Agregar condiciones según los parámetros de búsqueda
    if ($grados !== null) {
        $query .= "WHERE EXISTS ( SELECT 1 FROM T_Cursos_Aula AS CA_Inner INNER JOIN T_Aulas AS A ON CA_Inner.Id_Aula = A.Id_Aula  WHERE CA_Inner.Id_Curso = C.Id_Curso AND A.Grado = :grado) ";
    }

    if ($nombre !== null) {
        $query .= ($grados !== null ? "AND" : "WHERE") . " C.Nombre LIKE :nombre ";
    }

    $stmt = $this->conn->prepare($query);

    // Vincular los parámetros
    if ($grados !== null) {
        $stmt->bindValue(':grado', $grados, PDO::PARAM_INT);
    }
    if ($nombre !== null) {
        $stmt->bindValue(':nombre', '%' . $nombre . '%', PDO::PARAM_STR);
    }

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['count'];
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

    public function getCursosConGrados()
    {
        $stmt = $this->conn->prepare("SELECT DISTINCT C.Nombre AS Nombre_Curso, GROUP_CONCAT(DISTINCT A.Grado) AS Grados FROM T_Cursos AS C INNER JOIN T_Cursos_Aula AS CA ON C.Id_Curso = CA.Id_Curso INNER JOIN T_Aulas AS A ON CA.Id_Aula = A.Id_Aula GROUP BY C.Nombre");
        $stmt->execute();
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $cursos;
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
        return $stmt->execute(['Id_Curso' => $Id_Curso, 'Nombre' => $Nombre]);
    }

    public function delete($Id_Curso)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Cursos WHERE Id_Curso = :Id_Curso");
        $stmt->execute(['Id_Curso' => $Id_Curso]);
        return $stmt->rowCount();
    }
}
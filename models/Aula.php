<?php
require_once __DIR__ . '/../config/Database.php';
use Config\Database;

class Aula
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM T_Aulas");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByGrados($grados)
{
    $gradosPlaceholders = rtrim(str_repeat('?, ', count($grados)), ', ');
    $query = "SELECT * FROM T_Aulas WHERE Grado IN ($gradosPlaceholders)";
    $stmt = $this->conn->prepare($query);
    $stmt->execute($grados);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function addCursoToAula($idAula, $idCurso)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Cursos_Aula (Id_Curso, Id_Aula) VALUES (:idCurso, :idAula)");
        $stmt->execute(['idCurso' => $idCurso, 'idAula' => $idAula]);
        return $stmt->rowCount() > 0;
    }


    public function getSectionsByGrade($Grado)
    {
        $stmt = $this->conn->prepare("SELECT DISTINCT Seccion FROM T_Aulas WHERE Grado = :Grado");
        $stmt->execute(['Grado' => $Grado]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

        public function removeCursoFromAula($idCurso)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Cursos_Aula WHERE Id_Curso = :idCurso");
        $stmt->execute(['idCurso' => $idCurso]);
        return $stmt->rowCount() > 0;
    }


    public function removeAllCursoAssociations()
    {

        $stmt = $this->conn->prepare("DELETE FROM T_Cursos_Aula");
        return $stmt->execute();
    }

    public function removeCursoFromSpecificAulas($idCurso, $aulas)
    {
        $aulasIds = array_column($aulas, 'Id_Aula');
        $aulasPlaceholders = rtrim(str_repeat('?, ', count($aulasIds)), ', ');
        $query = "DELETE FROM T_Cursos_Aula WHERE Id_Curso = ? AND Id_Aula IN ($aulasPlaceholders)";
        $params = array_merge([$idCurso], $aulasIds);
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }



    public function getById($Id_Aula)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Aulas WHERE Id_Aula = :Id_Aula");
        $stmt->execute(['Id_Aula' => $Id_Aula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByGradoSeccion($Grado, $Seccion)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Aulas WHERE Grado = :Grado AND Seccion = :Seccion");
        $stmt->execute(['Grado' => $Grado, 'Seccion' => $Seccion]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastSection($Grado)
    {
        $stmt = $this->conn->prepare("SELECT Seccion FROM T_Aulas WHERE Grado = :Grado ORDER BY Id_Aula DESC LIMIT 1");
        $stmt->execute(['Grado' => $Grado]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['Seccion'] : ''; // Devuelve la última sección o cadena vacía si no hay secciones
    }


    public function getStudentsCountByGradoSeccion($Grado, $Seccion)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS Total FROM T_Estudiantes WHERE Id_Aula IN (SELECT Id_Aula FROM T_Aulas WHERE Grado = :Grado AND Seccion = :Seccion)");
        $stmt->execute(['Grado' => $Grado, 'Seccion' => $Seccion]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? intval($result['Total']) : 0; // Devuelve el número de estudiantes relacionados con la sección
    }


    public function addSection($Grado, $Seccion)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Aulas (Grado, Seccion) VALUES (:Grado, :Seccion)");
        $stmt->execute(['Grado' => $Grado, 'Seccion' => $Seccion]);
    }

    public function deleteLastSection($Grado)
    {
        $lastSection = $this->getLastSection($Grado);
        $stmt = $this->conn->prepare("DELETE FROM T_Aulas WHERE Grado = :Grado AND Seccion = :Seccion");
        $stmt->execute(['Grado' => $Grado, 'Seccion' => $lastSection]);
    }



}
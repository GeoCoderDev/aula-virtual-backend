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
    
    public function getGradosByCurso($Id_Curso) {
        $stmt = $this->conn->prepare("SELECT DISTINCT A.Grado FROM T_Cursos_Aula AS CA INNER JOIN T_Aulas AS A ON CA.Id_Aula = A.Id_Aula WHERE CA.Id_Curso = :Id_Curso");
        $stmt->execute(['Id_Curso' => $Id_Curso]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    public function removeCursoFromAulas($idCurso)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Cursos_Aula WHERE Id_Curso = :idCurso");
        return $stmt->execute(['idCurso' => $idCurso]);
    }


    public function getAllSectionsByGrades()
    {
        // Consulta SQL para obtener los grados y secciones
        $query = "SELECT Grado, GROUP_CONCAT(Seccion ORDER BY Seccion) AS Secciones FROM T_Aulas GROUP BY Grado";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los resultados
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Array para almacenar los grados y secciones
        $gradosSecciones = [];

        // Iterar sobre los resultados
        foreach ($results as $result) {
            // Obtener el grado y las secciones
            $grado = $result['Grado'];
            $secciones = explode(',', $result['Secciones']);

            // Agregar el grado y las secciones al array
            $gradosSecciones[$grado] = $secciones;
        }

        // Devolver el array de grados y secciones
        return $gradosSecciones;
    }


    public function getSectionsByGrade($Grado)
    {
        $stmt = $this->conn->prepare("SELECT DISTINCT Seccion FROM T_Aulas WHERE Grado = :Grado");
        $stmt->execute(['Grado' => $Grado]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
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


    public function getAulasByCurso($Id_Curso)
    {
        $stmt = $this->conn->prepare("SELECT A.* FROM T_Aulas AS A INNER JOIN T_Cursos_Aula AS CA ON A.Id_Aula = CA.Id_Aula WHERE CA.Id_Curso = :Id_Curso");
        $stmt->execute(['Id_Curso' => $Id_Curso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByGrado($grado)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Aulas WHERE Grado = :grado");
        $stmt->execute(['grado' => $grado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function getCursosByGrado($Grado)
    {
        $stmt = $this->conn->prepare("SELECT c.Id_Curso  FROM T_Cursos c JOIN T_Cursos_Aula ca ON c.Id_Curso = ca.Id_Curso JOIN T_Aulas a ON ca.Id_Aula = a.Id_Aula WHERE a.Grado = :Grado GROUP BY c.Id_Curso");
        $stmt->execute(['Grado' => $Grado]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }



    public function addSection($Grado, $Seccion)
    {
        $this->conn->beginTransaction(); // Start transaction

        try {
            // Insertar nueva sección
            $stmt = $this->conn->prepare("INSERT INTO T_Aulas (Grado, Seccion) VALUES (:Grado, :Seccion)");
            $stmt->execute(['Grado' => $Grado, 'Seccion' => $Seccion]);

            // Obtener el Id_Aula de la nueva sección
            $idAula = $this->conn->lastInsertId();

            // Obtener cursos existentes para el grado
            $cursos = $this->getCursosByGrado($Grado);

            // Insertar cursos para la nueva sección
            foreach ($cursos as $idCurso) {
                $stmt = $this->conn->prepare("INSERT INTO T_Cursos_Aula (Id_Curso, Id_Aula) VALUES (:idCurso, :idAula)");
                $stmt->execute(['idCurso' => $idCurso, 'idAula' => $idAula]);
            }

            $this->conn->commit(); // Commit transaction
        } catch (Exception $e) {
            $this->conn->rollBack(); // Rollback transaction if something fails
            throw $e; // Rethrow exception
        }
    }


    public function deleteLastSection($Grado)
    {
        $lastSection = $this->getLastSection($Grado);
        $stmt = $this->conn->prepare("DELETE FROM T_Aulas WHERE Grado = :Grado AND Seccion = :Seccion");
        $stmt->execute(['Grado' => $Grado, 'Seccion' => $lastSection]);
    }



}
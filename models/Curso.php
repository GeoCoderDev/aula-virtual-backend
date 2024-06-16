<?php
require_once __DIR__ . '/../config/Database.php';
use Config\Database;

class Curso
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }
    public function getAll($startFrom = 0, $limit = 200, $nombre = null, $grado = null)
    {
        $query = "SELECT C.Id_Curso, C.Nombre AS Nombre_Curso, (SELECT GROUP_CONCAT(DISTINCT A.Grado) FROM T_Cursos_Aula AS CA_Inner INNER JOIN T_Aulas AS A ON CA_Inner.Id_Aula = A.Id_Aula WHERE CA_Inner.Id_Curso = C.Id_Curso) AS Grados  FROM T_Cursos AS C ";

        // Agregar condiciones según los parámetros de búsqueda
        if ($grado !== null) {
            $query .= "WHERE EXISTS ( SELECT 1 FROM T_Cursos_Aula AS CA_Inner INNER JOIN T_Aulas AS A ON CA_Inner.Id_Aula = A.Id_Aula  WHERE CA_Inner.Id_Curso = C.Id_Curso AND A.Grado = :grado) ";
        }

        if ($nombre !== null) {
            $query .= ($grado !== null ? "AND" : "WHERE") . " C.Nombre LIKE :nombre ";
        }

        $query .= "LIMIT :startFrom, :limit";

        $stmt = $this->conn->prepare($query);

        // Vincular los parámetros
        if ($grado !== null) {
            $stmt->bindValue(':grado', $grado, PDO::PARAM_INT);
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

    public function checkDependencies($Id_Curso, $Id_Aula)
    {
        // Verificar archivos_tema
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) 
            FROM T_Archivos_Tema 
            WHERE Id_Tema IN (
                SELECT Id_Tema 
                FROM T_Temas 
                WHERE Id_Curso_Aula IN (
                    SELECT Id_Curso_Aula 
                    FROM T_Cursos_Aula 
                    WHERE Id_Curso = :Id_Curso AND Id_Aula = :Id_Aula
                )
            )
        ");
        $stmt->execute(['Id_Curso' => $Id_Curso, 'Id_Aula' => $Id_Aula]);
        $count = $stmt->fetchColumn();
        if ($count > 0) return true;

        // Verificar urls
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) 
            FROM T_URLs 
            WHERE Id_Tema IN (
                SELECT Id_Tema 
                FROM T_Temas 
                WHERE Id_Curso_Aula IN (
                    SELECT Id_Curso_Aula 
                    FROM T_Cursos_Aula 
                    WHERE Id_Curso = :Id_Curso AND Id_Aula = :Id_Aula
                )
            )
        ");
        $stmt->execute(['Id_Curso' => $Id_Curso, 'Id_Aula' => $Id_Aula]);
        $count = $stmt->fetchColumn();
        if ($count > 0) return true;

        // Verificar tareas
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) 
            FROM T_Tarea 
            WHERE Id_Tema IN (
                SELECT Id_Tema 
                FROM T_Temas 
                WHERE Id_Curso_Aula IN (
                    SELECT Id_Curso_Aula 
                    FROM T_Cursos_Aula 
                    WHERE Id_Curso = :Id_Curso AND Id_Aula = :Id_Aula
                )
            )
        ");
        $stmt->execute(['Id_Curso' => $Id_Curso, 'Id_Aula' => $Id_Aula]);
        $count = $stmt->fetchColumn();
        if ($count > 0) return true;

        // Verificar cuestionarios
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) 
            FROM T_Cuestionario 
            WHERE Id_Tema IN (
                SELECT Id_Tema 
                FROM T_Temas 
                WHERE Id_Curso_Aula IN (
                    SELECT Id_Curso_Aula 
                    FROM T_Cursos_Aula 
                    WHERE Id_Curso = :Id_Curso AND Id_Aula = :Id_Aula
                )
            )
        ");
        $stmt->execute(['Id_Curso' => $Id_Curso, 'Id_Aula' => $Id_Aula]);
        $count = $stmt->fetchColumn();
        if ($count > 0) return true;

        // Verificar foros
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) 
            FROM T_Foro 
            WHERE Id_Tema IN (
                SELECT Id_Tema 
                FROM T_Temas 
                WHERE Id_Curso_Aula IN (
                    SELECT Id_Curso_Aula 
                    FROM T_Cursos_Aula 
                    WHERE Id_Curso = :Id_Curso AND Id_Aula = :Id_Aula
                )
            )
        ");
        $stmt->execute(['Id_Curso' => $Id_Curso, 'Id_Aula' => $Id_Aula]);
        $count = $stmt->fetchColumn();
        if ($count > 0) return true;

        return false;
    }


    public function addCursoToAula($Id_Aula, $Id_Curso)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Cursos_Aula (Id_Aula, Id_Curso) VALUES (:Id_Aula, :Id_Curso)");
        return $stmt->execute(['Id_Aula' => $Id_Aula, 'Id_Curso' => $Id_Curso]);
    }


    public function addCursoToGrado($idCurso, $grado) {
        try {
            // Preparar la consulta SQL
            $sql = "INSERT INTO T_Cursos_Aula (Id_Aula, Id_Curso)
                    SELECT a.Id_Aula, :idCurso
                    FROM T_Aulas a
                    WHERE a.Grado = :grado
                    AND NOT EXISTS (
                        SELECT 1
                        FROM T_Cursos_Aula ca
                        WHERE ca.Id_Aula = a.Id_Aula
                        AND ca.Id_Curso = :idCurso
                    )";

            // Preparar la declaración SQL
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':idCurso', $idCurso, PDO::PARAM_INT);
            $stmt->bindParam(':grado', $grado, PDO::PARAM_INT);

            // Ejecutar la consulta
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    public function removeCursoFromAula($Id_Aula, $Id_Curso)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Cursos_Aula WHERE Id_Aula = :Id_Aula AND Id_Curso = :Id_Curso");
        return $stmt->execute(['Id_Aula' => $Id_Aula, 'Id_Curso' => $Id_Curso]);
    }


    public function deleteCursosByGradoSeccion($Grado, $Seccion)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM T_Cursos_Aula WHERE Id_Aula IN (SELECT Id_Aula FROM T_Aulas WHERE Grado = :Grado AND Seccion = :Seccion)"
        );
        $stmt->execute(['Grado' => $Grado, 'Seccion' => $Seccion]);
    }
    
    public function deleteSection($Grado, $Seccion)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Aulas WHERE Grado = :Grado AND Seccion = :Seccion");
        $stmt->execute(['Grado' => $Grado, 'Seccion' => $Seccion]);
    }


    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollback() {
        return $this->conn->rollBack();
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
<?php

require_once __DIR__ . "/./Usuario.php";
require_once __DIR__ . "/../config/S3Manager.php"; // Agrega la ruta correcta al archivo S3Manager.php
require_once __DIR__ . '/../lib/helpers/functions/totalTimeInSeconds.php';

use Config\Database;
use Config\S3Manager; // Asegúrate de agregar el uso de S3Manager aquí

define("DURATION_PERFIL_PHOTO_TEACHER", totalTimeInSeconds(1, 0, 0, 0));

class Profesor
{
    private $conn;
    private $s3Manager; // Agrega una propiedad para el S3Manager

    public function __construct()
    {
        $this->conn = Database::getConnection();
        $this->s3Manager = new S3Manager(); // Inicializa el S3Manager en el constructor
    }

    /*public function getAll($includePassword = false, $limit = 200, $startFrom = 0, $dni = null, $nombre = null, $apellidos = null, $estado = null)
    {
        if ($includePassword) {
            $query = "SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Telefono, U.Foto_Perfil_Key_S3, U.Estado FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE 1=1";
        } else {
            $query = "SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Telefono, U.Foto_Perfil_Key_S3, U.Estado FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE 1=1";
        }

        // Agregar condiciones según los parámetros de búsqueda
        if ($dni !== null) {
            $query .= " AND P.DNI_Profesor LIKE :dni";
        }
        if ($nombre !== null) {
            $query .= " AND U.Nombres LIKE :nombre";
        }
        if ($apellidos !== null) {
            $query .= " AND U.Apellidos LIKE :apellidos";
        }
        if ($estado !== null) {
            $query .= " AND U.Estado = :estado";
        }

        // Agregar límite y offset
        $query .= " LIMIT :startFrom, :limit";

        $stmt = $this->conn->prepare($query);

        // Vincular los parámetros
        if ($dni !== null) {
            $stmt->bindValue(':dni', $dni . '%', PDO::PARAM_STR);
        }
        if ($nombre !== null) {
            $stmt->bindValue(':nombre', '%' . $nombre . '%', PDO::PARAM_STR);
        }
        if ($apellidos !== null) {
            $stmt->bindValue(':apellidos', '%' . $apellidos . '%', PDO::PARAM_STR);
        }
        if ($estado !== null) {
            $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        }

        $stmt->bindValue(':startFrom', $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar la URL del objeto S3 al resultado
        foreach ($professors as &$professor) {
            // Verificar si Foto_Perfil_Key_S3 no es null antes de agregar la URL
            if ($professor['Foto_Perfil_Key_S3'] !== null) {
                $professor['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($professor['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_TEACHER);
            }
        }

        return $professors;
    }*/

    public function getAll($includePassword = false, $limit = 200, $startFrom = 0, $dni = null, $nombre = null, $apellidos = null, $estado = null)
    {
        $query = "SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Estado, U.Foto_Perfil_Key_S3 
              FROM T_Profesores AS P 
              INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario 
              WHERE 1=1";

        // Agregar condiciones según los parámetros de búsqueda
        if ($dni !== null) {
            $query .= " AND P.DNI_Profesor LIKE :dni";
        }
        if ($nombre !== null) {
            $query .= " AND U.Nombres LIKE :nombre";
        }
        if ($apellidos !== null) {
            $query .= " AND U.Apellidos LIKE :apellidos";
        }
        if ($estado !== null) {
            $query .= " AND U.Estado = :estado";
        }

        // Agregar límite y offset
        $query .= " LIMIT :startFrom, :limit";

        $stmt = $this->conn->prepare($query);

        // Vincular los parámetros
        if ($dni !== null) {
            $stmt->bindValue(':dni', $dni . '%', PDO::PARAM_STR);
        }
        if ($nombre !== null) {
            $stmt->bindValue(':nombre', '%' . $nombre . '%', PDO::PARAM_STR);
        }
        if ($apellidos !== null) {
            $stmt->bindValue(':apellidos', '%' . $apellidos . '%', PDO::PARAM_STR);
        }
        if ($estado !== null) {
            $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        }

        $stmt->bindValue(':startFrom', $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar la URL del objeto S3 al resultado
        foreach ($professors as &$professor) {
            // Verificar si Foto_Perfil_Key_S3 no es null antes de agregar la URL
            if ($professor['Foto_Perfil_Key_S3'] !== null) {
                $professor['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($professor['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_TEACHER);
            }
            // Eliminar el campo Foto_Perfil_Key_S3 ya que no es necesario en el resultado final
            unset($professor['Foto_Perfil_Key_S3']);
        }

        return $professors;
    }



    public function getProfessorCount($dni = null, $nombre = null, $apellidos = null, $estado = null)
    {
        $query = "SELECT COUNT(*) AS count FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE 1=1";

        // Agregar condiciones según los parámetros de consulta
        if ($dni !== null) {
            $query .= " AND P.DNI_Profesor LIKE :dni";
        }
        if ($nombre !== null) {
            $query .= " AND U.Nombres LIKE :nombre";
        }
        if ($apellidos !== null) {
            $query .= " AND U.Apellidos LIKE :apellidos";
        }
        if ($estado !== null) {
            $query .= " AND U.Estado = :estado";
        }

        $stmt = $this->conn->prepare($query);

        // Vincular los parámetros
        if ($dni !== null) {
            $stmt->bindValue(':dni', $dni . '%', PDO::PARAM_STR);
        }
        if ($nombre !== null) {
            $stmt->bindValue(':nombre', '%' . $nombre . '%', PDO::PARAM_STR);
        }
        if ($apellidos !== null) {
            $stmt->bindValue(':apellidos', '%' . $apellidos . '%', PDO::PARAM_STR);
        }
        if ($estado !== null) {
            $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'];
    }


    public function getByUserId($userId, $includePassword = false)
    {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Telefono, U.Foto_Perfil_Key_S3, U.Estado FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.Id_Usuario = :userId");
        } else {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Telefono, U.Foto_Perfil_Key_S3, U.Estado FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.Id_Usuario = :userId");
        }
        $stmt->execute(['userId' => $userId]);
        $professor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró algún profesor antes de intentar acceder a la URL de la foto de perfil
        if ($professor && $professor['Foto_Perfil_Key_S3'] !== null) {
            $professor['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($professor['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_TEACHER);
        }

        return $professor;
    }

    public function getByUsername($username, $includePassword = false)
    {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Telefono, U.Foto_Perfil_Key_S3, U.Estado FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE U.Nombre_Usuario = :username");
        } else {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Telefono, U.Foto_Perfil_Key_S3, U.Estado FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE U.Nombre_Usuario = :username");
        }
        $stmt->execute(['username' => $username]);
        $professor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Agrega la URL del objeto S3 al resultado si Foto_Perfil_Key_S3 no es null
        if ($professor && $professor['Foto_Perfil_Key_S3'] !== null) {
            $professor['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($professor['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_TEACHER);
        }

        return $professor;
    }

    public function getNameAndSurnameByDNI($DNI_Profesor)
    {
        $sql = "SELECT u.Nombres AS Nombre_Profesor, u.Apellidos AS Apellido_Profesor 
            FROM T_Profesores p
            JOIN T_Usuarios u ON p.Id_Usuario = u.Id_Usuario
            WHERE p.DNI_Profesor = :dni";
        $query = $this->conn->prepare($sql);
        $query->bindParam(':dni', $DNI_Profesor, PDO::PARAM_STR);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }


    public function getByDNI($dni, $includePassword = false)
    {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Telefono, U.Foto_Perfil_Key_S3, U.Estado FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.DNI_Profesor = :dni");
        } else {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Telefono, U.Foto_Perfil_Key_S3, U.Estado FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.DNI_Profesor = :dni");
        }
        $stmt->execute(['dni' => $dni]);
        $professor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró algún profesor antes de intentar acceder a la URL de la foto de perfil
        if ($professor && $professor['Foto_Perfil_Key_S3'] !== null) {
            // Agrega la URL del objeto S3 al resultado
            $professor['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($professor['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_TEACHER);
        }

        return $professor;
    }


    public function getUserIdByDNI($DNI_Profesor)
    {
        // Consulta SQL para obtener el ID de usuario por el DNI del profesor
        $query = "SELECT Id_Usuario FROM T_Profesores WHERE DNI_Profesor = :DNI_Profesor";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);

        // Bind de parámetros
        $stmt->bindParam(":DNI_Profesor", $DNI_Profesor);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró el profesor
        if ($result) {
            return $result['Id_Usuario'];
        } else {
            return false;
        }
    }

    public function getProfilePhotoUrl($DNI_Profesor)
    {
        // Consulta SQL para obtener el 'Foto_Perfil_Key_S3' por el DNI del estudiante
        $query = "SELECT U.Foto_Perfil_Key_S3 FROM T_Profesores AS E  INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario  WHERE E.DNI_Profesor = :dni";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);

        // Vincular el parámetro
        $stmt->bindValue(':dni', $DNI_Profesor, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró el estudiante y si tiene una foto de perfil
        if ($result && $result['Foto_Perfil_Key_S3'] !== null) {
            // Obtener la URL del objeto S3
            $photoUrl = $this->s3Manager->getObjectUrl($result['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_TEACHER);
            return $photoUrl;
        } else {
            return null; // Si no se encontró el estudiante o no tiene foto de perfil
        }
    }


    public function fetchCourseData($idCursoAula)
    {
        $query = "
            SELECT 
                ca.Id_Curso_Aula, 
                a.Grado, 
                a.Seccion,
                c.Nombre AS Nombre_Curso
            FROM 
                T_Cursos_Aula ca
                INNER JOIN T_Aulas a ON ca.Id_Aula = a.Id_Aula
                INNER JOIN T_Cursos c ON ca.Id_Curso = c.Id_Curso
            WHERE 
                ca.Id_Curso_Aula = :idCursoAula
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idCursoAula', $idCursoAula);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchCourseTopics($idCursoAula)
    {
        $query = "
            SELECT 
                t.Id_Tema, 
                t.Nombre_Tema
            FROM 
                T_Temas t
                INNER JOIN T_Cursos_Aula ca ON t.Id_Curso_Aula = ca.Id_Curso_Aula
            WHERE 
                ca.Id_Curso_Aula = :idCursoAula
            ORDER BY 
                t.Id_Tema ASC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idCursoAula', $idCursoAula);
        $stmt->execute();

        $topics = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $topics[] = [
                'Id_Tema' => $row['Id_Tema'],
                'Nombre_Tema' => $row['Nombre_Tema']
            ];
        }

        return $topics;
    }


    public function create($dni, $userId)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Profesores (DNI_Profesor, Id_Usuario) VALUES (:dni, :userId)");
        $success = $stmt->execute(['dni' => $dni, 'userId' => $userId]);
        return $success;
    }

    public function hasAccessToCourse($DNI_Profesor, $cursoAulaID)
    {
        // Consulta para verificar si el profesor tiene acceso al curso aula a través de sus asignaciones
        $query = "
        SELECT COUNT(*) as count 
        FROM T_Asignaciones a
        JOIN T_Horario_Curso_Aula hca ON a.Id_Horario_Curso_Aula = hca.Id_Horario_Curso_Aula
        JOIN T_Cursos_Aula ca ON hca.Id_Curso_Aula = ca.Id_Curso_Aula
        WHERE a.DNI_Profesor = ? AND ca.Id_Curso_Aula = ?
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$DNI_Profesor, $cursoAulaID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }



    public function update($dni, $userId)
    {
        $stmt = $this->conn->prepare("UPDATE T_Profesores SET Id_Usuario = :userId WHERE DNI_Profesor = :dni");
        return $stmt->execute(['dni' => $dni, 'userId' => $userId]);
    }

    public function delete($dni)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Profesores WHERE DNI_Profesor = :dni");
        return $stmt->execute(['dni' => $dni]);
    }

    public function getCursosByDNI($DNI_Profesor)
    {
        $stmt = $this->conn->prepare("
        SELECT DISTINCT 
            CA.Id_Curso_Aula, 
            C.Nombre AS Nombre_Curso, 
            A.Grado, 
            A.Seccion 
        FROM 
            T_Cursos AS C   
            INNER JOIN T_Cursos_Aula AS CA ON C.Id_Curso = CA.Id_Curso 
            INNER JOIN T_Horario_Curso_Aula AS HCA ON CA.Id_Curso_Aula = HCA.Id_Curso_Aula 
            INNER JOIN T_Asignaciones AS ASIG ON HCA.Id_Horario_Curso_Aula = ASIG.Id_Horario_Curso_Aula  
            INNER JOIN T_Profesores AS P ON ASIG.DNI_Profesor = P.DNI_Profesor  
            INNER JOIN T_Aulas AS A ON CA.Id_Aula = A.Id_Aula 
        WHERE 
            P.DNI_Profesor = :DNI_Profesor
    ");
        $stmt->execute(['DNI_Profesor' => $DNI_Profesor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php

require_once __DIR__."/./Usuario.php";
require_once __DIR__."/../Config/S3Manager.php"; // Agrega la ruta correcta al archivo S3Manager.php
require_once __DIR__.'/../lib/helpers/functions/totalTimeInSeconds.php';

use Config\Database;
use Config\S3Manager; // Asegúrate de agregar el uso de S3Manager aquí

define("DURATION_PERFIL_PHOTO_TEACHER", totalTimeInSeconds(1,0,0,0));

class Profesor{
    private $conn;
    private $s3Manager; // Agrega una propiedad para el S3Manager

    public function __construct() {
        $this->conn = Database::getConnection();
        $this->s3Manager = new S3Manager(); // Inicializa el S3Manager en el constructor
    }

    public function getAll($includePassword = false, $limit = 200, $startFrom = 0, $dni = null, $nombre = null, $apellidos = null)
    {
        if ($includePassword) {
            $query = "SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE 1=1";
        } else {
            $query = "SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE 1=1";
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

        $stmt->bindValue(':startFrom', $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar la URL del objeto S3 al resultado
        foreach ($professors as &$professor) {
            $professor['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($professor['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_TEACHER);
        }

        return $professors;
    }


    public function getProfessorCount($dni = null, $nombre = null, $apellidos = null) {
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

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'];
    }

    public function getByUserId($userId, $includePassword = false)
{
    if ($includePassword) {
        $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.Id_Usuario = :userId");
    } else {
        $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.Id_Usuario = :userId");
    }
    $stmt->execute(['userId' => $userId]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró algún profesor antes de intentar acceder a la URL de la foto de perfil
    if ($professor) {
        // Agrega la URL del objeto S3 al resultado
        $professor['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($professor['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_TEACHER);
    }

    return $professor;
}


    public function getByUsername($username, $includePassword = false) {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE U.Nombre_Usuario = :username");
        } else {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE U.Nombre_Usuario = :username");
        }
        $stmt->execute(['username' => $username]);
        $professor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Agrega la URL del objeto S3 al resultado
        $professor['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($professor['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_TEACHER);

        return $professor;
    }

    public function getByDNI($dni, $includePassword = false)
{
    if ($includePassword) {
        $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.DNI_Profesor = :dni");
    } else {
        $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.DNI_Profesor = :dni");
    }
    $stmt->execute(['dni' => $dni]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró algún profesor antes de intentar acceder a la URL de la foto de perfil
    if ($professor) {
        // Agrega la URL del objeto S3 al resultado
        $professor['Foto_Perfil_URL'] = $this->s3Manager->getObjectUrl($professor['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_TEACHER);
    }

    return $professor;
}


    public function create($dni, $userId) {
        $stmt = $this->conn->prepare("INSERT INTO T_Profesores (DNI_Profesor, Id_Usuario) VALUES (:dni, :userId)");
        $success = $stmt->execute(['dni' => $dni, 'userId' => $userId]);
        return $success;
    }

    public function update($dni, $userId) {
        $stmt = $this->conn->prepare("UPDATE T_Profesores SET Id_Usuario = :userId WHERE DNI_Profesor = :dni");
        return $stmt->execute(['dni' => $dni, 'userId' => $userId]);        
    }

    public function delete($dni) {
        $stmt = $this->conn->prepare("DELETE FROM T_Profesores WHERE DNI_Profesor = :dni");
        return $stmt->execute(['dni' => $dni]);
        
    }


    public function getCursosByDNI($DNI_Profesor)
    {
        $stmt = $this->conn->prepare("SELECT DISTINCT C.* FROM T_Cursos AS C  INNER JOIN T_Cursos_Aula AS CA ON C.Id_Curso = CA.Id_Curso  INNER JOIN T_Horario_Curso_Aula AS HCA ON CA.Id_Curso_Aula = HCA.Id_Curso_Aula INNER JOIN T_Asignaciones AS ASIG ON HCA.Id_Horario_Curso_Aula = ASIG.Id_Horario_Curso_Aula INNER JOIN T_Profesores AS P ON ASIG.DNI_Profesor = P.DNI_Profesor WHERE P.DNI_Profesor = :DNI_Profesor");
        $stmt->execute(['DNI_Profesor' => $DNI_Profesor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAsignacionesByDNI($DNI_Profesor)
    {
        $stmt = $this->conn->prepare("SELECT A.*, C.Nombre AS Nombre_Curso, CA.Id_Curso_Aula, AU.Grado, AU.Seccion, HCA.Id_Horario_Curso_Aula, HCA.Dia_Semana, HCA.Hora_Inicio, HCA.Cant_Horas_Academicas   FROM T_Asignaciones AS A INNER JOIN T_Horario_Curso_Aula AS HCA ON A.Id_Horario_Curso_Aula = HCA.Id_Horario_Curso_Aula INNER JOIN T_Cursos_Aula AS CA ON HCA.Id_Curso_Aula = CA.Id_Curso_Aula INNER JOIN T_Cursos AS C ON CA.Id_Curso = C.Id_Curso INNER JOIN T_Aulas AS AU ON CA.Id_Aula = AU.Id_Aula INNER JOIN T_Profesores AS P ON A.DNI_Profesor = P.DNI_Profesor WHERE P.DNI_Profesor = :DNI_Profesor");
        
        $stmt->execute(['DNI_Profesor' => $DNI_Profesor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>

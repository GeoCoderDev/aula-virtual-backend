<?php
require_once __DIR__."/./Usuario.php";

use Config\Database;

class Profesor{
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll($includePassword = false)
    {
        if ($includePassword) {
            $stmt = $this->conn->query("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario");
        } else {
            $stmt = $this->conn->query("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUserId($userId, $includePassword = false)
    {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.Id_Usuario = :userId");
        } else {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.Id_Usuario = :userId");
        }
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername($username, $includePassword = false) {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE U.Nombre_Usuario = :username");
        } else {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE U.Nombre_Usuario = :username");
        }
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByDNI($dni, $includePassword = false)
    {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.DNI_Profesor = :dni");
        } else {
            $stmt = $this->conn->prepare("SELECT P.DNI_Profesor, P.Id_Usuario, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Profesores AS P INNER JOIN T_Usuarios AS U ON P.Id_Usuario = U.Id_Usuario WHERE P.DNI_Profesor = :dni");
        }
        $stmt->execute(['dni' => $dni]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($dni, $userId) {
        $stmt = $this->conn->prepare("INSERT INTO T_Profesores (DNI_Profesor, Id_Usuario) VALUES (:dni, :userId)");
        $success = $stmt->execute(['dni' => $dni, 'userId' => $userId]);
        return $success;
    }

    public function update($dni, $userId) {
        $stmt = $this->conn->prepare("UPDATE T_Profesores SET Id_Usuario = :userId WHERE DNI_Profesor = :dni");
        $stmt->execute(['dni' => $dni, 'userId' => $userId]);
        return $stmt->rowCount();
    }

    public function delete($dni) {
        $stmt = $this->conn->prepare("DELETE FROM T_Profesores WHERE DNI_Profesor = :dni");
        $stmt->execute(['dni' => $dni]);
        return $stmt->rowCount();
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

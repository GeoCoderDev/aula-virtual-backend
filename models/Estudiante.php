<?php
require_once __DIR__ . '../../Config/Database.php';
use Config\Database;

class Estudiante
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getAll($includePassword = false)
    {
        if ($includePassword) {
            $stmt = $this->conn->query("SELECT E.DNI_Estudiante, E.Id_Aula, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario");
        } else {
            $stmt = $this->conn->query("SELECT E.DNI_Estudiante, E.Id_Aula, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDNI($DNI_Estudiante, $includePassword = false)
    {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT E.DNI_Estudiante, E.Id_Aula, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario WHERE E.DNI_Estudiante = :DNI_Estudiante");
        } else {
            $stmt = $this->conn->prepare("SELECT E.DNI_Estudiante, E.Id_Aula, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario WHERE E.DNI_Estudiante = :DNI_Estudiante");
        }
        $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($Id_Usuario, $includePassword = false)
    {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT E.DNI_Estudiante, E.Id_Aula, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario WHERE E.Id_Usuario = :Id_Usuario");
        } else {
            $stmt = $this->conn->prepare("SELECT E.DNI_Estudiante, E.Id_Aula, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario WHERE E.Id_Usuario = :Id_Usuario");
        }
        $stmt->execute(['Id_Usuario' => $Id_Usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername($username, $includePassword = false) {
        if ($includePassword) {
            $stmt = $this->conn->prepare("SELECT E.DNI_Estudiante, E.Id_Aula, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Contraseña_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario WHERE U.Nombre_Usuario = :username");
        } else {
            $stmt = $this->conn->prepare("SELECT E.DNI_Estudiante, E.Id_Aula, U.Nombres, U.Apellidos, U.Fecha_Nacimiento, U.Nombre_Usuario, U.Direccion_Domicilio, U.Nombre_Contacto_Emergencia, U.Parentezco_Contacto_Emergencia, U.Telefono_Contacto_Emergencia, U.Foto_Perfil_Key_S3 FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario WHERE U.Nombre_Usuario = :username");
        }
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($DNI_Estudiante, $Id_Usuario, $Id_Aula)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Estudiantes (DNI_Estudiante, Id_Usuario, Id_Aula) VALUES (:DNI_Estudiante, :Id_Usuario, :Id_Aula)");
        $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante, 'Id_Usuario' => $Id_Usuario, 'Id_Aula' => $Id_Aula]);
        return $stmt->rowCount();
    }

    public function getCursosByDNI($DNI_Estudiante)
    {
        $stmt = $this->conn->prepare("SELECT C.* FROM T_Cursos AS C INNER JOIN T_Cursos_Aula AS CA ON C.Id_Curso = CA.Id_Curso INNER JOIN T_Aulas AS A ON CA.Id_Aula = A.Id_Aula INNER JOIN T_Estudiantes AS E ON A.Id_Aula = E.Id_Aula WHERE E.DNI_Estudiante = :DNI_Estudiante");
        $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function update($DNI_Estudiante, $Id_Usuario, $Id_Aula)
    {
        $stmt = $this->conn->prepare("UPDATE T_Estudiantes SET Id_Usuario = :Id_Usuario, Id_Aula = :Id_Aula WHERE DNI_Estudiante = :DNI_Estudiante");
        $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante, 'Id_Usuario' => $Id_Usuario, 'Id_Aula' => $Id_Aula]);
        return $stmt->rowCount();
    }

    public function delete($DNI_Estudiante)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Estudiantes WHERE DNI_Estudiante = :DNI_Estudiante");
        $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante]);
        return $stmt->rowCount();
    }
}
?>

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

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT E.*, U.* FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDNI($DNI_Estudiante)
    {
        $stmt = $this->conn->prepare("SELECT E.*, U.* FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario WHERE E.DNI_Estudiante = :DNI_Estudiante");
        $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($Id_Usuario)
    {
        $stmt = $this->conn->prepare("SELECT E.*, U.* FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario WHERE E.Id_Usuario = :Id_Usuario");
        $stmt->execute(['Id_Usuario' => $Id_Usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername($username) {
        $stmt = $this->conn->prepare("SELECT E.*, U.* FROM T_Estudiantes AS E INNER JOIN T_Usuarios AS U ON E.Id_Usuario = U.Id_Usuario WHERE U.Nombre_Usuario = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($DNI_Estudiante, $Id_Usuario, $Id_Aula)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Estudiantes (DNI_Estudiante, Id_Usuario, Id_Aula) VALUES (:DNI_Estudiante, :Id_Usuario, :Id_Aula)");
        $stmt->execute(['DNI_Estudiante' => $DNI_Estudiante, 'Id_Usuario' => $Id_Usuario, 'Id_Aula' => $Id_Aula]);
        return $stmt->rowCount();
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

<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Estudiante.php';
require_once __DIR__ . '/../config/S3Manager.php';

use Config\Database;
use Config\S3Manager;

class Foro
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function addResponse($Id_Foro, $DNI_Estudiante, $Contenido_Respuesta)
    {
        $query = "INSERT INTO T_Respuestas_Foro (Id_Foro, DNI_Estudiante, Contenido_Respuesta) 
              VALUES (:Id_Foro, :DNI_Estudiante, :Contenido_Respuesta)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':Id_Foro', $Id_Foro, PDO::PARAM_INT);
        $stmt->bindValue(':DNI_Estudiante', $DNI_Estudiante, PDO::PARAM_STR);
        $stmt->bindValue(':Contenido_Respuesta', $Contenido_Respuesta, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        } else {
            return false;
        }
    }

    public function create($Id_Recurso_Tema)
    {
        $query = "INSERT INTO T_Foro (Id_Recurso_Tema) VALUES (:Id_Recurso_Tema)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':Id_Recurso_Tema', $Id_Recurso_Tema, PDO::PARAM_INT);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function getAll($startFrom = 0, $limit = 200)
    {
        $query = "SELECT * FROM T_Foro LIMIT :startFrom, :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':startFrom', $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getById($Id_Foro)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Foro WHERE Id_Foro = :Id_Foro");
        $stmt->execute(['Id_Foro' => $Id_Foro]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getForumDetails($Id_Foro)
    {
        $sql = "SELECT f.Id_Foro, rt.Titulo, rt.Descripcion_Recurso, rt.Imagen_Key_S3, t.Nombre_Tema AS Nombre_Tema, a.Grado, a.Seccion, c.Nombre AS Nombre_Curso 
            FROM T_Foro f 
            JOIN T_Recursos_Tema rt ON f.Id_Recurso_Tema = rt.Id_Recurso_Tema 
            JOIN T_Temas t ON rt.Id_Tema = t.Id_Tema 
            JOIN T_Cursos_Aula ca ON t.Id_Curso_Aula = ca.Id_Curso_Aula 
            JOIN T_Aulas a ON ca.Id_Aula = a.Id_Aula 
            JOIN T_Cursos c ON ca.Id_Curso = c.Id_Curso 
            WHERE f.Id_Foro = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$Id_Foro]);
        $forumDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($forumDetails && $forumDetails['Imagen_Key_S3'] !== null) {
            $s3Manager = new S3Manager();
            $forumDetails['Descripcion_Imagen_URL'] = $s3Manager->getObjectUrl($forumDetails['Imagen_Key_S3'], DURATION_TOPIC_RESOURCE_PHOTO_DESCRIPTION);
            unset($forumDetails['Imagen_Key_S3']);
        }

        return $forumDetails;
    }


    public function getStudentsWhoRespondedByForumId($Id_Foro)
    {
        $sql = "SELECT rf.Id_Respuesta_Foro, rf.Contenido_Respuesta, e.DNI_Estudiante, e.Id_Usuario, u.Nombres, u.Apellidos, u.Estado, u.Foto_Perfil_Key_S3
                FROM T_Respuestas_Foro rf
                JOIN T_Estudiantes e ON rf.DNI_Estudiante = e.DNI_Estudiante
                JOIN T_Usuarios u ON e.Id_Usuario = u.Id_Usuario
                WHERE rf.Id_Foro = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$Id_Foro]);
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $s3Manager = new S3Manager();

        foreach ($responses as &$response) {
            if ($response['Foto_Perfil_Key_S3'] !== null) {
                $response['Foto_Perfil_URL'] = $s3Manager->getObjectUrl($response['Foto_Perfil_Key_S3'], DURATION_PERFIL_PHOTO_STUDENT);
                unset($response['Foto_Perfil_Key_S3']);
            }
        }

        return $responses;
    }

    public function update($Id_Foro, $Id_Recurso_Tema)
    {
        $query = "UPDATE T_Foro SET Id_Recurso_Tema = :Id_Recurso_Tema WHERE Id_Foro = :Id_Foro";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':Id_Foro', $Id_Foro, PDO::PARAM_INT);
        $stmt->bindValue(':Id_Recurso_Tema', $Id_Recurso_Tema, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($Id_Foro)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Foro WHERE Id_Foro = :Id_Foro");
        $stmt->execute(['Id_Foro' => $Id_Foro]);
        return $stmt->rowCount();
    }

    public function getResponsesByForumId($Id_Foro)
    {
        $query = "
        SELECT rf.*, e.DNI_Estudiante, u.Id_Usuario, u.Nombres, u.Apellidos, u.Estado, u.Foto_Perfil
        FROM T_Respuestas_Foro rf
        INNER JOIN T_Estudiantes e ON rf.Id_Estudiante = e.Id_Estudiante
        INNER JOIN T_Usuarios u ON e.DNI_Estudiante = u.DNI
        WHERE rf.Id_Foro = :Id_Foro
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':Id_Foro', $Id_Foro, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function beginTransaction()
    {
        return $this->conn->beginTransaction();
    }

    public function commit()
    {
        return $this->conn->commit();
    }

    public function rollback()
    {
        return $this->conn->rollBack();
    }
}

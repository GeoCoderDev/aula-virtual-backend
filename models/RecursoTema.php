<?php

use Config\Database;

require_once __DIR__ . '/../config/Database.php';

class RecursoTema
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getByTopicId($idTema)
    {
        $query = "SELECT Id_Recurso_Tema, Titulo, Descripcion_Recurso, Imagen_Key_S3, Tipo FROM T_Recursos_Tema WHERE Id_Tema = :idTema";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTema', $idTema, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($idRecursoTema)
    {
        $query = "SELECT Id_Recurso_Tema, Titulo, Descripcion_Recurso, Imagen_Key_S3, Tipo FROM T_Recursos_Tema WHERE Id_Recurso_Tema = :idRecursoTema";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idRecursoTema', $idRecursoTema, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function create($idTema, $titulo, $descripcion, $imagenKeyS3 = null, $tipo)
    {
        $queryRecurso = "INSERT INTO T_Recursos_Tema (Id_Tema, Titulo, Descripcion_Recurso, Imagen_Key_S3, Tipo) VALUES (:idTema, :titulo, :descripcion, :imagenKeyS3, :tipo)";
        $stmtRecurso = $this->conn->prepare($queryRecurso);
        $stmtRecurso->bindParam(':idTema', $idTema, PDO::PARAM_INT);
        $stmtRecurso->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmtRecurso->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmtRecurso->bindParam(':imagenKeyS3', $imagenKeyS3, PDO::PARAM_STR);
        $stmtRecurso->bindParam(':tipo', $tipo, PDO::PARAM_INT);
        $stmtRecurso->execute();

        $idRecursoTema = $this->conn->lastInsertId();

        return $idRecursoTema;
    }

    public function update($idRecursoTema, $titulo, $descripcion, $imagenKeyS3 = null, $tipo)
    {
        $query = "UPDATE T_Recursos_Tema SET Titulo = :titulo, Descripcion_Recurso = :descripcion, Imagen_Key_S3 = :imagenKeyS3, Tipo = :tipo WHERE Id_Recurso_Tema = :idRecursoTema";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idRecursoTema', $idRecursoTema, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':imagenKeyS3', $imagenKeyS3, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete($idRecursoTema)
    {
        $query = "DELETE FROM T_Recursos_Tema WHERE Id_Recurso_Tema = :idRecursoTema";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idRecursoTema', $idRecursoTema, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function addFileToTopic($idTema, $titulo, $descripcion, $imagenKeyS3 = null, $tipo, $idArchivo)
    {
        try {


            $queryRecurso = "INSERT INTO T_Recursos_Tema (Id_Tema, Titulo, Descripcion_Recurso, Imagen_Key_S3, Tipo) VALUES (:idTema, :titulo, :descripcion, :imagenKeyS3, :tipo)";
            $stmtRecurso = $this->conn->prepare($queryRecurso);
            $stmtRecurso->bindParam(':idTema', $idTema, PDO::PARAM_INT);
            $stmtRecurso->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmtRecurso->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmtRecurso->bindParam(':imagenKeyS3', $imagenKeyS3, PDO::PARAM_STR);
            $stmtRecurso->bindParam(':tipo', $tipo, PDO::PARAM_INT);
            $stmtRecurso->execute();

            $idRecursoTema = $this->conn->lastInsertId();

            $queryArchivoTema = "INSERT INTO T_Archivos_Tema (Id_Archivo, Id_Recurso_Tema) VALUES (:idArchivo, :idRecursoTema)";
            $stmtArchivoTema = $this->conn->prepare($queryArchivoTema);
            $stmtArchivoTema->bindParam(':idArchivo', $idArchivo, PDO::PARAM_INT);
            $stmtArchivoTema->bindParam(':idRecursoTema', $idRecursoTema, PDO::PARAM_INT);
            $stmtArchivoTema->execute();


            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addHomeworkToTopic($idTema, $titulo, $descripcion, $imagenKeyS3 = null, $tipo, $fechaApertura, $fechaLimite, $puntajeMax)
    {
        try {


            $queryRecurso = "INSERT INTO T_Recursos_Tema (Id_Tema, Titulo, Descripcion_Recurso, Imagen_Key_S3, Tipo) VALUES (:idTema, :titulo, :descripcion, :imagenKeyS3, :tipo)";
            $stmtRecurso = $this->conn->prepare($queryRecurso);
            $stmtRecurso->bindParam(':idTema', $idTema, PDO::PARAM_INT);
            $stmtRecurso->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmtRecurso->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmtRecurso->bindParam(':imagenKeyS3', $imagenKeyS3, PDO::PARAM_STR);
            $stmtRecurso->bindParam(':tipo', $tipo, PDO::PARAM_INT);
            $stmtRecurso->execute();

            $idRecursoTema = $this->conn->lastInsertId();

            $queryTarea = "INSERT INTO T_Tarea (Id_Recurso_Tema, Fecha_hora_apertura, Fecha_hora_limite, Puntaje_Max) 
                  VALUES (:idRecursoTema, :fechaApertura, :fechaLimite, :puntajeMax)";
            $stmtTarea = $this->conn->prepare($queryTarea);
            $stmtTarea->bindParam(':idRecursoTema', $idRecursoTema, PDO::PARAM_INT);
            $stmtTarea->bindParam(':fechaApertura', $fechaApertura, PDO::PARAM_STR);
            $stmtTarea->bindParam(':fechaLimite', $fechaLimite, PDO::PARAM_STR);
            $stmtTarea->bindParam(':puntajeMax', $puntajeMax, PDO::PARAM_STR);

            $idTarea = $this->conn->lastInsertId();

            return $idTarea;
        } catch (Exception $e) {
            return false;
        }
    }


    public function existsWithTitleAndType($idTema, $titulo, $tipo)
    {
        $query = "SELECT COUNT(*) FROM T_Recursos_Tema WHERE Id_Tema = :idTema AND Titulo = :titulo AND Tipo = :tipo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTema', $idTema, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    public function commit()
    {
        $this->conn->commit();
    }

    public function rollBack()
    {
        $this->conn->rollBack();
    }
}

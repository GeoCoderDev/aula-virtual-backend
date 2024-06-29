<?php


require_once __DIR__ . '/../config/Database.php';

use Config\Database;

class ArchivosTarea
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getByTaskId($idTarea)
    {
        $query = "SELECT Id_Archivos_Tarea, Id_Archivo, Id_Tarea FROM T_Archivos_Tarea WHERE Id_Tarea = :idTarea";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTarea', $idTarea, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($idArchivosTarea)
    {
        $query = "SELECT Id_Archivos_Tarea, Id_Archivo, Id_Tarea FROM T_Archivos_Tarea WHERE Id_Archivos_Tarea = :idArchivosTarea";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idArchivosTarea', $idArchivosTarea, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function create($nombreArchivo, $extension, $archivoKeyS3, $idTarea)
    {
        $queryArchivo = "INSERT INTO T_Archivos (Nombre_Archivo, Extension, Key_S3) VALUES (:nombreArchivo, :extension, :archivoKeyS3)";
        $stmtArchivo = $this->conn->prepare($queryArchivo);
        $stmtArchivo->bindParam(':nombreArchivo', $nombreArchivo, PDO::PARAM_STR);
        $stmtArchivo->bindParam(':extension', $extension, PDO::PARAM_STR);
        $stmtArchivo->bindParam(':archivoKeyS3', $archivoKeyS3, PDO::PARAM_STR);

        $stmtArchivo->execute();
        $idArchivo = $this->conn->lastInsertId();

        $queryArchivoTarea = "INSERT INTO T_Archivos_Tarea (Id_Archivo, Id_Tarea) VALUES (:idArchivo, :idTarea)";
        $stmtArchivoTarea = $this->conn->prepare($queryArchivoTarea);
        $stmtArchivoTarea->bindParam(':idArchivo', $idArchivo, PDO::PARAM_INT);
        $stmtArchivoTarea->bindParam(':idTarea', $idTarea, PDO::PARAM_INT);
        return $stmtArchivoTarea->execute();
    }

    public function update($idArchivosTarea, $idArchivo, $idTarea)
    {
        $query = "UPDATE T_Archivos_Tarea SET Id_Archivo = :idArchivo, Id_Tarea = :idTarea WHERE Id_Archivos_Tarea = :idArchivosTarea";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idArchivosTarea', $idArchivosTarea, PDO::PARAM_INT);
        $stmt->bindParam(':idArchivo', $idArchivo, PDO::PARAM_INT);
        $stmt->bindParam(':idTarea', $idTarea, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete($idArchivosTarea)
    {
        $query = "DELETE FROM T_Archivos_Tarea WHERE Id_Archivos_Tarea = :idArchivosTarea";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idArchivosTarea', $idArchivosTarea, PDO::PARAM_INT);

        return $stmt->execute();
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

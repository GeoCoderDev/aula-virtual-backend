<?php

use Config\Database;

require_once __DIR__ . '/../config/Database.php';

class Tarea
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // Obtener tareas por recurso tema
    public function getByRecursoTemaId($idRecursoTema)
    {
        $query = "SELECT Id_Tarea, Id_Recurso_Tema, Fecha_hora_apertura, Fecha_hora_limite, Puntaje_Max 
                  FROM T_Tarea 
                  WHERE Id_Recurso_Tema = :idRecursoTema";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idRecursoTema', $idRecursoTema, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener tarea por ID
    public function getById($idTarea)
    {
        $query = "SELECT Id_Tarea, Id_Recurso_Tema, Fecha_hora_apertura, Fecha_hora_limite, Puntaje_Max 
                  FROM T_Tarea 
                  WHERE Id_Tarea = :idTarea";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTarea', $idTarea, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    // Crear nueva tarea
    public function create($idRecursoTema, $fechaApertura, $fechaLimite, $puntajeMax)
    {
        $query = "INSERT INTO T_Tarea (Id_Recurso_Tema, Fecha_hora_apertura, Fecha_hora_limite, Puntaje_Max) 
                  VALUES (:idRecursoTema, :fechaApertura, :fechaLimite, :puntajeMax)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idRecursoTema', $idRecursoTema, PDO::PARAM_INT);
        $stmt->bindParam(':fechaApertura', $fechaApertura, PDO::PARAM_STR);
        $stmt->bindParam(':fechaLimite', $fechaLimite, PDO::PARAM_STR);
        $stmt->bindParam(':puntajeMax', $puntajeMax, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Actualizar tarea
    public function update($idTarea, $fechaApertura, $fechaLimite, $puntajeMax)
    {
        $query = "UPDATE T_Tarea 
                  SET Fecha_hora_apertura = :fechaApertura, Fecha_hora_limite = :fechaLimite, Puntaje_Max = :puntajeMax 
                  WHERE Id_Tarea = :idTarea";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTarea', $idTarea, PDO::PARAM_INT);
        $stmt->bindParam(':fechaApertura', $fechaApertura, PDO::PARAM_STR);
        $stmt->bindParam(':fechaLimite', $fechaLimite, PDO::PARAM_STR);
        $stmt->bindParam(':puntajeMax', $puntajeMax, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Eliminar tarea
    public function delete($idTarea)
    {
        $query = "DELETE FROM T_Tarea WHERE Id_Tarea = :idTarea";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTarea', $idTarea, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Agregar archivo adjunto a la tarea
    public function addFileToTask($idTarea, $idArchivo)
    {
        $query = "INSERT INTO T_Archivos_Tarea (Id_Archivo, Id_Tarea) VALUES (:idArchivo, :idTarea)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idArchivo', $idArchivo, PDO::PARAM_INT);
        $stmt->bindParam(':idTarea', $idTarea, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Obtener archivos adjuntos de una tarea
    public function getFilesByTaskId($idTarea)
    {
        $query = "SELECT a.Id_Archivo, a.Nombre_Archivo, a.Extension, a.Key_S3 
                  FROM T_Archivos a
                  JOIN T_Archivos_Tarea at ON a.Id_Archivo = at.Id_Archivo 
                  WHERE at.Id_Tarea = :idTarea";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTarea', $idTarea, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener respuestas de una tarea
    public function getResponsesByTaskId($idTarea)
    {
        $query = "SELECT rt.Id_Respuesta_Tarea, rt.Id_Archivo, rt.DNI_Estudiante, a.Nombre_Archivo, a.Extension, a.Key_S3 
                  FROM T_Respuestas_Tarea rt
                  JOIN T_Archivos a ON rt.Id_Archivo = a.Id_Archivo 
                  WHERE rt.Id_Tarea = :idTarea";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTarea', $idTarea, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Iniciar transacción
    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    // Confirmar transacción
    public function commit()
    {
        $this->conn->commit();
    }

    // Revertir transacción
    public function rollBack()
    {
        $this->conn->rollBack();
    }
}

?>

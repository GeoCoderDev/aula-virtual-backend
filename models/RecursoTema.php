<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/S3Manager.php';

use Config\Database;
use Config\S3Manager;

define("DURATION_TOPIC_RESOURCE_PHOTO_DESCRIPTION", totalTimeInSeconds(1, 0, 0, 0));
define("DURATION_TOPIC_RESOURCE_FILE", totalTimeInSeconds(1, 0, 0, 0));

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

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertir Imagen_Key_S3 a URL utilizando S3Manager
        $s3Manager = new S3Manager();

        foreach ($result as &$resource) {
            if ($resource['Imagen_Key_S3'] !== null) {
                $resource['Descripcion_Imagen_URL'] = $s3Manager->getObjectUrl($resource['Imagen_Key_S3'], DURATION_TOPIC_RESOURCE_PHOTO_DESCRIPTION);
            }

            unset($resource['Imagen_Key_S3']);

            // Obtener URL adicional para tipos 0 (Archivo_tema) y 1 (URL)
            if ($resource['Tipo'] == 0) {
                // Obtener URL del archivo
                $queryArchivo = "SELECT a.Key_S3 FROM T_Archivos a 
                             INNER JOIN T_Archivos_Tema at ON a.Id_Archivo = at.Id_Archivo
                             WHERE at.Id_Recurso_Tema = :idRecursoTema";
                $stmtArchivo = $this->conn->prepare($queryArchivo);
                $stmtArchivo->bindParam(':idRecursoTema', $resource['Id_Recurso_Tema'], PDO::PARAM_INT);
                $stmtArchivo->execute();
                $archivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC);

                if ($archivo) {
                    $resource['Recurso_URL'] = $s3Manager->getObjectUrl($archivo['Key_S3'], DURATION_TOPIC_RESOURCE_FILE);
                }
            } elseif ($resource['Tipo'] == 3) {
                // Obtener URL
                $queryURL = "SELECT URL FROM T_URLs WHERE Id_Recurso_Tema = :idRecursoTema";
                $stmtURL = $this->conn->prepare($queryURL);
                $stmtURL->bindParam(':idRecursoTema', $resource['Id_Recurso_Tema'], PDO::PARAM_INT);
                $stmtURL->execute();
                $url = $stmtURL->fetch(PDO::FETCH_ASSOC);

                if ($url) {
                    $resource['Recurso_URL'] = $url['URL'];
                }
            }
        }

        return $result;
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

    public function addForumToTopic($topicId, $titulo, $descripcionRecurso, $imagenDescripcionKeyS3, $tipo)
    {
        $sql = "INSERT INTO T_Recursos_Tema (Id_Tema, Titulo, Descripcion_Recurso, Imagen_Key_S3, Tipo) 
            VALUES (:topicId, :titulo, :descripcionRecurso, :imagenDescripcionKeyS3, :tipo)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':topicId', $topicId, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':descripcionRecurso', $descripcionRecurso, $descripcionRecurso ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':imagenDescripcionKeyS3', $imagenDescripcionKeyS3, $imagenDescripcionKeyS3 ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT);
        $stmt->execute();
        return $this->conn->lastInsertId();
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

            $idRecursoTema = $this->conn->query("SELECT MAX(Id_Recurso_Tema) FROM T_Recursos_Tema")->fetchColumn();

            $queryTarea = "INSERT INTO T_Tarea (Id_Recurso_Tema, Fecha_hora_apertura, Fecha_hora_limite, Puntaje_Max) 
                  VALUES (:idRecursoTema, :fechaApertura, :fechaLimite, :puntajeMax)";
            $stmtTarea = $this->conn->prepare($queryTarea);
            $stmtTarea->bindParam(':idRecursoTema', $idRecursoTema, PDO::PARAM_INT);
            $stmtTarea->bindParam(':fechaApertura', $fechaApertura, PDO::PARAM_STR);
            $stmtTarea->bindParam(':fechaLimite', $fechaLimite, PDO::PARAM_STR);
            $stmtTarea->bindParam(':puntajeMax', $puntajeMax, PDO::PARAM_STR);

            $stmtTarea->execute();

            $idTarea = $this->conn->query("SELECT MAX(Id_Tarea) FROM T_Tarea;")->fetchColumn();

            return $idTarea;
        } catch (Exception $e) {
            return false;
        }
    }



    public function addURLToTopic($topicId, $titulo, $url, $tipo)
    {
        try {
            $stmt = $this->conn->prepare("
            INSERT INTO T_Recursos_Tema (Id_Tema, Titulo, Tipo)
            VALUES (:Id_Tema, :Titulo, :Tipo)
        ");

            $stmt->bindParam(':Id_Tema', $topicId);
            $stmt->bindParam(':Titulo', $titulo);
            $stmt->bindParam(':Tipo', $tipo);

            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log('Error en addURLToTopic: ' . $e->getMessage());
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

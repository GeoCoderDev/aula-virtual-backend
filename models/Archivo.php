<?php

use Config\Database;

require_once __DIR__ . '/../config/Database.php';

class Archivo
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /**
     * Crea un nuevo archivo en la tabla T_Archivos.
     *
     * @param string $nombreArchivo El nombre del archivo.
     * @param string $extension La extensión del archivo.
     * @param string $archivoKeyS3 La clave del archivo en S3.
     * @return int|false Retorna el ID del archivo creado o false en caso de error.
     */
    public function create($nombreArchivo, $extension, $archivoKeyS3)
    {
        $query = "INSERT INTO T_Archivos (Nombre_Archivo, Extension, Key_S3) VALUES (:nombreArchivo, :extension, :archivoKeyS3)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombreArchivo', $nombreArchivo, PDO::PARAM_STR);
        $stmt->bindParam(':extension', $extension, PDO::PARAM_STR);
        $stmt->bindParam(':archivoKeyS3', $archivoKeyS3, PDO::PARAM_STR);

        $stmt->execute();
        $idArchivo = $this->conn->query("SELECT MAX(Id_Archivo) FROM T_Archivos")->fetchColumn();
        error_log("HOLAAAAAAAAAAAAAAAAAAAAAAAAAAAA" . $idArchivo);
        return $idArchivo;
    }


    public function existsInTopic($idTema, $nombreArchivo, $extension)
    {
        $query = "SELECT COUNT(*)
                  FROM T_Archivos a
                  INNER JOIN T_Archivos_Tema at ON a.Id_Archivo = at.Id_Archivo
                  INNER JOIN T_Recursos_Tema rt ON at.Id_Recurso_Tema = rt.Id_Recurso_Tema
                  WHERE rt.Id_Tema = :idTema AND a.Nombre_Archivo = :nombreArchivo AND a.Extension = :extension";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTema', $idTema, PDO::PARAM_INT);
        $stmt->bindParam(':nombreArchivo', $nombreArchivo, PDO::PARAM_STR);
        $stmt->bindParam(':extension', $extension, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }
}

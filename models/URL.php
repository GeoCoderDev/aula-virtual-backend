<?php
require_once __DIR__ . '/../config/Database.php';

use Config\Database;

class URL
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function create($Id_Recurso_Tema, $URL)
    {
        $query = "INSERT INTO T_URLs (Id_Recurso_Tema, URL) VALUES (:Id_Recurso_Tema, :URL)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':Id_Recurso_Tema', $Id_Recurso_Tema, PDO::PARAM_INT);
        $stmt->bindValue(':URL', $URL, PDO::PARAM_STR);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function getAll($startFrom = 0, $limit = 200)
    {
        $query = "SELECT * FROM T_URLs LIMIT :startFrom, :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':startFrom', $startFrom, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($Id_URL)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_URLs WHERE Id_URL = :Id_URL");
        $stmt->execute(['Id_URL' => $Id_URL]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($Id_URL, $Id_Recurso_Tema, $URL)
    {
        $query = "UPDATE T_URLs SET Id_Recurso_Tema = :Id_Recurso_Tema, URL = :URL WHERE Id_URL = :Id_URL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':Id_URL', $Id_URL, PDO::PARAM_INT);
        $stmt->bindValue(':Id_Recurso_Tema', $Id_Recurso_Tema, PDO::PARAM_INT);
        $stmt->bindValue(':URL', $URL, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete($Id_URL)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_URLs WHERE Id_URL = :Id_URL");
        $stmt->execute(['Id_URL' => $Id_URL]);
        return $stmt->rowCount();
    }

    public function getByRecursoTema($Id_Recurso_Tema)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_URLs WHERE Id_Recurso_Tema = :Id_Recurso_Tema");
        $stmt->execute(['Id_Recurso_Tema' => $Id_Recurso_Tema]);
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

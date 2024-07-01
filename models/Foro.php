<?php
require_once __DIR__ . '/../config/Database.php';

use Config\Database;

class Foro
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
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
?>

<?php

require_once __DIR__ . '../../Config/Database.php';
use Config\Database;

class Usuario {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM T_Usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM T_Usuarios WHERE Id_Usuario = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM T_Usuarios WHERE Nombre_Usuario = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByNombreUsuario($Nombre_Usuario)
    {
        $stmt = $this->conn->prepare("SELECT * FROM T_Usuarios WHERE Nombre_Usuario = :Nombre_Usuario");
        $stmt->execute(['Nombre_Usuario' => $Nombre_Usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($Nombres, $Apellidos, $Fecha_Nacimiento, $Nombre_Usuario, $Contraseña_Usuario, $Dirección_Domicilio, $Nombre_Contacto_Emergencia, $Parentezco_Contacto_Emergencia, $Telefono_Contacto_Emergencia, $Foto_Perfil_Key_S3)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Usuarios (Nombres, Apellidos, Fecha_Nacimiento, Nombre_Usuario, Contraseña_Usuario, Dirección_Domicilio, Nombre_Contacto_Emergencia, Parentezco_Contacto_Emergencia, Telefono_Contacto_Emergencia, Foto_Perfil_Key_S3) VALUES (:Nombres, :Apellidos, :Fecha_Nacimiento, :Nombre_Usuario, :Contraseña_Usuario, :Dirección_Domicilio, :Nombre_Contacto_Emergencia, :Parentezco_Contacto_Emergencia, :Telefono_Contacto_Emergencia, :Foto_Perfil_Key_S3)");
        $stmt->execute([
            'Nombres' => $Nombres,
            'Apellidos' => $Apellidos,
            'Fecha_Nacimiento' => $Fecha_Nacimiento,
            'Nombre_Usuario' => $Nombre_Usuario,
            'Contraseña_Usuario' => $Contraseña_Usuario,
            'Dirección_Domicilio' => $Dirección_Domicilio,
            'Nombre_Contacto_Emergencia' => $Nombre_Contacto_Emergencia,
            'Parentezco_Contacto_Emergencia' => $Parentezco_Contacto_Emergencia,
            'Telefono_Contacto_Emergencia' => $Telefono_Contacto_Emergencia,
            'Foto_Perfil_Key_S3' => $Foto_Perfil_Key_S3
        ]);
        return $this->conn->lastInsertId();
    }

    public function update($Id_Usuario, $data)
    {
        $query = "UPDATE T_Usuarios SET ";
        $params = [];
        foreach ($data as $key => $value) {
            $query .= "$key = :$key, ";
            $params[":$key"] = $value;
        }
        $query = rtrim($query, ', ');
        $query .= " WHERE Id_Usuario = :Id_Usuario";
        $params[':Id_Usuario'] = $Id_Usuario;

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete($Id_Usuario)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Usuarios WHERE Id_Usuario = :Id_Usuario");
        $stmt->execute(['Id_Usuario' => $Id_Usuario]);
        return $stmt->rowCount();
    }
}

?>

<?php

require_once __DIR__ . '/../Config/Database.php'; // Corregí la ruta del archivo de configuración de la base de datos
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

    public function create($Nombres, $Apellidos, $Fecha_Nacimiento, $Nombre_Usuario, $passwordEncripted, $Direccion_Domicilio, $Nombre_Contacto_Emergencia, $Parentezco_Contacto_Emergencia, $Telefono_Contacto_Emergencia, $Foto_Perfil_Key_S3)
    {
        $stmt = $this->conn->prepare("INSERT INTO T_Usuarios (Nombres, Apellidos, Fecha_Nacimiento, Nombre_Usuario, Contraseña_Usuario, Direccion_Domicilio, Nombre_Contacto_Emergencia, Parentezco_Contacto_Emergencia, Telefono_Contacto_Emergencia, Foto_Perfil_Key_S3) VALUES (:Nombres, :Apellidos, :Fecha_Nacimiento, :Nombre_Usuario, :passwordEncripted, :Direccion_Domicilio, :Nombre_Contacto_Emergencia, :Parentezco_Contacto_Emergencia, :Telefono_Contacto_Emergencia, :Foto_Perfil_Key_S3)");

        $success = $stmt->execute([
            'Nombres' => $Nombres,
            'Apellidos' => $Apellidos,
            'Fecha_Nacimiento' => $Fecha_Nacimiento,
            'Nombre_Usuario' => $Nombre_Usuario,
            'passwordEncripted' => $passwordEncripted,
            'Direccion_Domicilio' => $Direccion_Domicilio,
            'Nombre_Contacto_Emergencia' => $Nombre_Contacto_Emergencia,
            'Parentezco_Contacto_Emergencia' => $Parentezco_Contacto_Emergencia,
            'Telefono_Contacto_Emergencia' => $Telefono_Contacto_Emergencia,
            'Foto_Perfil_Key_S3' => $Foto_Perfil_Key_S3
        ]);

        if ($success) {
            return $this->conn->lastInsertId();
        } else {
            return false;
        }
    }

    public function update(
    $Id_Usuario,
    $Nombres,
    $Apellidos,
    $Fecha_Nacimiento,
    $Nombre_Usuario,
    $Direccion_Domicilio,
    $Nombre_Contacto_Emergencia,
    $Parentezco_Contacto_Emergencia,
    $Telefono_Contacto_Emergencia,
    $Foto_Perfil_Key_S3 = null
    ) {
        // Construir la consulta SQL para la actualización
        $query = "UPDATE T_Usuarios SET ";
        $query .= "Nombres = :nombres, ";
        $query .= "Apellidos = :apellidos, ";
        $query .= "Fecha_Nacimiento = :fecha_nacimiento, ";
        $query .= "Nombre_Usuario = :nombre_usuario, ";
        $query .= "Direccion_Domicilio = :direccion_domicilio, ";
        $query .= "Nombre_Contacto_Emergencia = :nombre_contacto_emergencia, ";
        $query .= "Parentezco_Contacto_Emergencia = :parentezco_contacto_emergencia, ";
        $query .= "Telefono_Contacto_Emergencia = :telefono_contacto_emergencia, ";
        $query .= "Foto_Perfil_Key_S3 = :foto_perfil_key_s3 ";
        $query .= "WHERE Id_Usuario = :id_usuario";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombres', $Nombres);
        $stmt->bindParam(':apellidos', $Apellidos);
        $stmt->bindParam(':fecha_nacimiento', $Fecha_Nacimiento);
        $stmt->bindParam(':nombre_usuario', $Nombre_Usuario);
        $stmt->bindParam(':direccion_domicilio', $Direccion_Domicilio);
        $stmt->bindParam(':nombre_contacto_emergencia', $Nombre_Contacto_Emergencia);
        $stmt->bindParam(':parentezco_contacto_emergencia', $Parentezco_Contacto_Emergencia);
        $stmt->bindParam(':telefono_contacto_emergencia', $Telefono_Contacto_Emergencia);
        $stmt->bindParam(':id_usuario', $Id_Usuario);

        // Asignar el valor de Foto_Perfil_Key_S3 teniendo en cuenta la posibilidad de que sea null
        if ($Foto_Perfil_Key_S3 === null) {
            $stmt->bindValue(':foto_perfil_key_s3', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':foto_perfil_key_s3', $Foto_Perfil_Key_S3);
        }

        // Ejecutar la consulta
        return $stmt->execute();
    }


    public function delete($Id_Usuario)
    {
        $stmt = $this->conn->prepare("DELETE FROM T_Usuarios WHERE Id_Usuario = :Id_Usuario");
        return $stmt->execute(['Id_Usuario' => $Id_Usuario]);
        
    }

    public function updatePassword($id, $newPassword) {
        $stmt = $this->conn->prepare("UPDATE T_Usuarios SET Contraseña_Usuario = :newPassword WHERE Id_Usuario = :id");
        return $stmt->execute(['id' => $id, 'newPassword' => $newPassword]);

    }


}

?>

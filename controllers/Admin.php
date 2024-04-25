<?php

require_once __DIR__ . '../../Models/Admin.php';
require_once __DIR__."/../lib/helpers/encriptations/adminEncriptation.php";

class AdminController {

    public function getById($id) {
        $adminModel = new Admin();
        $admin = $adminModel->getById($id);
        return $admin;
    }

    public function getAll() {
        $adminModel = new Admin();
        $admins = $adminModel->getAll();
        return $admins;
    }


    public function create($data) {
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return Flight::json(["message" => "username y password son obligatorios"], 400);
        }

        $encriptedUsername = encryptAdminUsername($username);
        $encriptedPassword = encryptAdminUsername($password);

        $adminModel = new Admin();
        $adminId = $adminModel->create($encriptedUsername, $encriptedPassword);
        
        return Flight::json(["message" => "Admin creado", "id"=>$adminId], 201);
    }

    public function updateUsername($id, $data) {
        $newUsername = $data['newUsername'] ?? null;

        if (!$newUsername) {
            return json_encode(["message" => "Nuevo nombre de usuario es obligatorio"]);
        }

        $adminModel = new Admin();
        $rowCount = $adminModel->updateUsername($id, $newUsername);
        
        if ($rowCount > 0) {
            return json_encode(["message" => "Nombre de usuario actualizado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún admin con el ID proporcionado"]);
        }
    }

    public function updatePassword($id, $data) {
        $newPassword = $data['newPassword'] ?? null;

        if (!$newPassword) {
            return json_encode(["message" => "Nueva contraseña es obligatoria"]);
        }

        $adminModel = new Admin();
        $rowCount = $adminModel->updatePassword($id, $newPassword);
        
        if ($rowCount > 0) {
            return json_encode(["message" => "Contraseña actualizada"]);
        } else {
            return json_encode(["message" => "No se encontró ningún admin con el ID proporcionado"]);
        }
    }


}
?>

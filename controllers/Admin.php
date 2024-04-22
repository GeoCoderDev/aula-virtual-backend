<?php

require __DIR__ . '../../Models/Admin.php';

class AdminController {

    public function getById($id) {
        $adminModel = new Admin();
        $admin = $adminModel->getById($id);
        return json_encode($admin);
    }

    public function getAll() {
        $adminModel = new Admin();
        $admins = $adminModel->getAll();
        return json_encode($admins);
    }

    public function create($data) {
        $nombreUsuario = $data['nombreUsuario'] ?? null;
        $contrasena = $data['contrasena'] ?? null;
        
        if (!$nombreUsuario || !$contrasena) {
            return json_encode(["message" => "Nombre de usuario y contraseña son obligatorios"]);
        }

        $adminModel = new Admin();
        $adminId = $adminModel->create($nombreUsuario, $contrasena);
        
        return json_encode(["message" => "Admin creado con ID: $adminId"]);
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

<?php

require __DIR__ . '/../Models/Superadmin.php';
require __DIR__ .'/../lib/helpers/encriptations/superadminEncriptation.php';

class SuperadminController {

    public function getById($id) {
        $superadminModel = new Superadmin();
        $superadmin = $superadminModel->getById($id);
        return json_encode($superadmin);
    }

    public function validateSuperadmin($data) {

        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        
        if (!$username || !$password) {
            return json_encode(["message" => "username y password son obligatorios"]);
        }

        $username_encripted = encryptSuperadminUsername($username);

        $superadmin = new Superadmin();
        $superadminFinded = $superadmin->getByUsername($username_encripted);        

        if ($superadminFinded) {
            $superadmin_password_decripted = decryptSuperadminPassword($superadminFinded["Contraseña"]);
            if($superadmin_password_decripted==$password) return $superadminFinded;
        }

        return json_encode(["message" => "Superadministrador no logeado"]);
    }

    public function getAll() {
        $superadminModel = new Superadmin();
        $superadmins = $superadminModel->getAll();
        return json_encode($superadmins);
    }

    public function create($data) {
        $nombreUsuario = $data['nombreUsuario'] ?? null;
        $contrasena = $data['contrasena'] ?? null;
        
        if (!$nombreUsuario || !$contrasena) {
            return json_encode(["message" => "Nombre de usuario y contraseña son obligatorios"]);
        }

        $superadminModel = new Superadmin();
        $superadminId = $superadminModel->create($nombreUsuario, $contrasena);
        
        return json_encode(["message" => "Superadmin creado con ID: $superadminId"]);
    }

    public function updateUsername($id, $data) {
        $newUsername = $data['newUsername'] ?? null;

        if (!$newUsername) {
            return json_encode(["message" => "Nuevo nombre de usuario es obligatorio"]);
        }

        $superadminModel = new Superadmin();
        $rowCount = $superadminModel->updateUsername($id, $newUsername);
        
        if ($rowCount > 0) {
            return json_encode(["message" => "Nombre de usuario actualizado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún superadmin con el ID proporcionado"]);
        }
    }

    public function updatePassword($id, $data) {
        $newPassword = $data['newPassword'] ?? null;

        if (!$newPassword) {
            return json_encode(["message" => "Nueva contraseña es obligatoria"]);
        }

        $superadminModel = new Superadmin();
        $rowCount = $superadminModel->updatePassword($id, $newPassword);
        
        if ($rowCount > 0) {
            return json_encode(["message" => "Contraseña actualizada"]);
        } else {
            return json_encode(["message" => "No se encontró ningún superadmin con el ID proporcionado"]);
        }
    }

    // Puedes agregar más métodos según sea necesario
}
?>

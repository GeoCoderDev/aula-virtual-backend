<?php

require_once __DIR__ . '../../Models/Admin.php';
require_once __DIR__."/../lib/helpers/encriptations/adminEncriptation.php";

class AdminController {
    
    public function getAll() {
        $adminModel = new Admin();
        $admins = $adminModel->getAll();

        foreach ($admins as $key=>$admin) {
            $admin["Nombre_Usuario"] = decryptAdminUsername($admin["Nombre_Usuario"]);
            $admins[$key] = $admin;
        }

        return $admins;
    }
    public function getById($id) {
        $adminModel = new Admin();
        $admin = $adminModel->getById($id);
        return $admin;
    }


    /**
     * Esta función devuelve un array o false
     * array si se enviaron las credenciales correctas, este array contendra los datos del  superadmin logueado
     * false si las credenciales son incorrectas
     * @param [type] $data
     * @return bool|array
     */
    public function validateIdAndUsername($data){

        $id_admin = $data->adminID;
        $username = $data->username;


        $admin = new Admin();
        $adminFinded = $admin->getById($id_admin);

        if(!$adminFinded){
            if($adminFinded["Nombre_Usuario"]==$username) return $adminFinded;
        }

        return $adminFinded;

    }

    /**
     * Esta funcion devuelve 1 o 2 o un array
     * 1 si se enviaron datos incompletos
     * 2 si se enviaron credenciales incorrectas
     * array si se enviaron las credenciales correctas, este array contendra los datos del  superadmin logueado
     * @param array $data
     * @return int|array
     */
    public function validateAdmin($data) {

        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) return 1;

        $username_encripted = encryptAdminUsername($username);

        $admin = new Admin();
        $adminFinded = $admin->getByUsername($username_encripted);        

        if ($adminFinded) {
            $admin_password_decripted = decryptAdminPassword($adminFinded["Contraseña"]);
            if($admin_password_decripted==$password) return $adminFinded;
        }

        return 2;// Credenciales inválidas

    }

    public function create($data) {
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return Flight::json(["message" => "username y password son obligatorios"], 400);
        }

        $adminModel = new Admin();        
        $encriptedUsername = encryptAdminUsername($username);
        $adminsSameUsername = $adminModel->getByUsername($encriptedUsername);

        if (!$adminsSameUsername) {
            $encriptedUsername = encryptAdminUsername($username);
            $encriptedPassword = encryptAdminPassword($password);


            $adminId = $adminModel->create($encriptedUsername, $encriptedPassword);
            
            return Flight::json(["message" => "Admin creado", "id"=>$adminId], 201);
        }

        return  Flight::json(['message' => 'Ya existe un administrador con este username'],  409);
    }

    public function updateUsername($id, $data) {
        $newUsername = $data['newUsername'] ?? null;

        if (!$newUsername) {
            return Flight::json(["message" => "Nuevo nombre de usuario es obligatorio"] , 400);
        }

        $adminModel = new Admin();

        $encriptedNewUsername = encryptAdminUsername($newUsername);
        $rowCount = $adminModel->updateUsername($id, $encriptedNewUsername);
        
        if ($rowCount > 0) {
            return Flight::json(["message" => "Nombre de usuario actualizado"], 200);
        } else {
            return Flight::json(["message" => "No se encontró ningún admin con el ID proporcionado"] , 404);
        }
    }

    public function updatePassword($id, $data) {
        $newPassword = $data['newPassword'] ?? null;

        if (!$newPassword) {
            return Flight::json(["message" => "Nueva contraseña es obligatoria"] , 400);
        }

        $adminModel = new Admin();
        $encriptedNewPassword = encryptAdminPassword($newPassword);
        $rowCount = $adminModel->updatePassword($id, $encriptedNewPassword);
        
        if ($rowCount > 0) {
            return Flight::json(["message" => "Contraseña actualizada"], 200);
        } else {
            return Flight::json(["message" => "No se encontró ningún admin con el ID proporcionado"] , 404);
        }
    }

    public function delete($id) {
        $adminModel = new Admin();
        $rowCount = $adminModel->delete($id);

        if ($rowCount > 0) {
            return Flight::json(["message" => "Administrador eliminado correctamente"], 200);
        } else {
            return Flight::json(["message" => "No se encontró ningún administrador con el ID proporcionado"], 404);
        }
    }

}
?>

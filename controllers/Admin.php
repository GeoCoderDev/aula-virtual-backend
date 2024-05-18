<?php

require_once __DIR__ . '../../Models/Admin.php';
require_once __DIR__."/../lib/helpers/encriptations/adminEncriptation.php";

class AdminController {
    
    public function getAll($limit = 200, $startFrom = 0, $username) {
        $adminModel = new Admin();

        $admins = $adminModel->getAll($limit, $startFrom, $username);
        return $admins;
    }

    public function getById($id) {
        $adminModel = new Admin();
        $admin = $adminModel->getById($id);
        return $admin;
    }

    public function getAdminCount() {
        $adminModel = new Admin();
        $count = $adminModel->getAdminCount();
        return $count;
    }


    /**
     * Esta función devuelve un array o false
     * array si se enviaron las credenciales correctas, este array contendra los datos del  superadmin logueado
     * false si las credenciales son incorrectas
     * @param [type] $data
     * @return bool|array
     */
    public function validateIdAndUsername($data){
        
        $admin = new Admin();
        $adminFinded = $admin->getById($data->Id_Admin);

        if($adminFinded && $adminFinded["Nombre_Usuario"]==$data->Username_Admin){

            return $adminFinded;
        }

        return false;

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



        $admin = new Admin();
        $adminFinded = $admin->getByUsername($username);        

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

        $adminsSameUsername = $adminModel->getByUsername($username);

        if (!$adminsSameUsername) {

            $encriptedPassword = encryptAdminPassword($password);

            $adminId = $adminModel->create($username, $encriptedPassword);
            
            return Flight::json(["message" => "Admin creado", "id"=>$adminId], 201);
        }

        return  Flight::json(['message' => 'Ya existe un administrador con ese username'],  409);
    }

    public function updateUsername($id, $data) {
        $newUsername = $data['newUsername'] ?? null;

        if (!$newUsername) {
            return Flight::json(["message" => "Nuevo nombre de usuario es obligatorio"] , 400);
        }

        $adminModel = new Admin();


        // Verificar si el nuevo nombre de usuario ya está en uso
        $existingAdmin = $adminModel->getByUsername($newUsername);
        if ($existingAdmin && $existingAdmin['Id_Admin'] !== $id) {
            return Flight::json(["message" => "El nuevo nombre de usuario ya está en uso"], 400);
        }

        $updateSuccess = $adminModel->updateUsername($id, $newUsername);
        
        if ($updateSuccess) {
            return Flight::json(["message" => "Nombre de usuario actualizado"], 200);
        } else {
            return Flight::json(["message" => "No se encontró ningún admin con el ID proporcionado"] , 404);
        }
    }

    // public function updatePassword($id, $data) {
    //     $newPassword = $data['password'] ?? null;

    //     if (!$newPassword) {
    //         return Flight::json(["message" => "Nueva contraseña es obligatoria"] , 400);
    //     }

    //     $adminModel = new Admin();
    //     $encriptedNewPassword = encryptAdminPassword($newPassword);
    //     $rowCount = $adminModel->updatePassword($id, $encriptedNewPassword);
        
    //     if ($rowCount > 0) {
    //         return Flight::json(["message" => "Contraseña actualizada"], 200);
    //     } else {
    //         return Flight::json(["message" => "No se encontró ningún admin con el ID proporcionado"] , 404);
    //     }
    // }

    // public function updatePassword($id, $data) {
    //     $newPassword = $data['Contraseña'] ?? null;
    
    //     if (!$newPassword) {
    //         return Flight::json(["message" => "Nueva contraseña es obligatoria"] , 400);
    //     }
    
    //     $adminModel = new Admin();
    //     $encriptedNewPassword = encryptAdminPassword($newPassword);
    //     $rowCount = $adminModel->updatePassword($id, $encriptedNewPassword);
        
    //     if ($rowCount > 0) {
    //         return Flight::json(["message" => "Contraseña actualizada"], 200);
    //     } else {
    //         return Flight::json(["message" => "No se encontró ningún admin con el ID proporcionado"] , 404);
    //     }
    // }
    
    // public function updatePassword($data) {
    //     $newPassword = $data['Contraseña'] ?? null;
        
    //     if (!$newPassword) {
    //         return Flight::json(["message" => "Nueva contraseña es obligatoria"], 400);
    //     }
        
    //     // El ID del administrador debería estar disponible a través del middleware de autenticación
    //     $adminID = Flight::request()->data->getData()['Id_Admin'] ?? null;
    
    //     if (!$adminID) {
    //         return Flight::json(["message" => "ID de administrador no encontrado en la solicitud"], 400);
    //     }
    
    //     $adminModel = new Admin();
    //     $encriptedNewPassword = encryptAdminPassword($newPassword);
    //     $rowCount = $adminModel->updatePassword($adminID, $encriptedNewPassword);
        
    //     if ($rowCount > 0) {
    //         return Flight::json(["message" => "Contraseña actualizada"], 200);
    //     } else {
    //         return Flight::json(["message" => "No se encontró ningún admin con el ID proporcionado"], 404);
    //     }
    // }

    public function updatePasswordByMe($data) {
        $newPassword = $data['Contraseña'] ?? null;
        
        if (!$newPassword) {
            return Flight::json(["message" => "Nueva contraseña es obligatoria"], 400);
        }
        
        // El ID del administrador debería estar disponible a través del middleware de autenticación
        $adminID = Flight::request()->data->getData()['Id_Admin'] ?? null;
    
        if (!$adminID) {
            return Flight::json(["message" => "ID de administrador no encontrado en la solicitud"], 400);
        }
    
        $adminModel = new Admin();
        $encriptedNewPassword = encryptAdminPassword($newPassword);
        $updateSuccess = $adminModel->updatePassword($adminID, $encriptedNewPassword);
        
        if ($updateSuccess) {
            return Flight::json(["message" => "Contraseña actualizada"], 200);
        } else {
            return Flight::json(["message" => "No se encontró ningún admin con el ID proporcionado"], 404);
        }
    }
    
    public function updatePassword($id, $data) {
        
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

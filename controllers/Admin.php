<?php

require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__.'/../lib/helpers/encriptations/adminEncriptation.php';

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
        $username = $data['Nombre_Usuario'] ?? null;
        $password = $data['Contraseña'] ?? null;

        if (!$username || !$password) {
            return Flight::json(["message" => "username y password son obligatorios"], 400);
        }

        $adminModel = new Admin();        

        $adminsSameUsername = $adminModel->getByUsername($username);

        if (!$adminsSameUsername) {

            $encriptedPassword = encryptAdminPassword($password);

            $adminId = $adminModel->create($username, $encriptedPassword);
            
            return Flight::json(["message" => "Administrador creado", "id"=>$adminId], 201);
        }

        return  Flight::json(['message' => 'Ya existe un administrador con ese username'],  409);
    }

    public function multipleCreate($data) {
    $alerts = [];

    if (!isset($data['adminValues']) || !is_array($data['adminValues'])) {
        return Flight::json(["message" => "No se encontraron datos de administradores para crear"], 400);
    }

    $adminModel = new Admin();

    foreach ($data['adminValues'] as $index => $adminData) {
        $username = $adminData[0] ?? null;
        $password = $adminData[1] ?? null;

        if (!$username || !$password) {
            $alerts[] = [
                'type' => 'critical',
                'content' => "Fila " . ($index + 1) . ": Nombre de usuario y contraseña son obligatorios"
            ];
            continue;
        }

        $existingAdmin = $adminModel->getByUsername($username);
        if ($existingAdmin) {
            $alerts[] = [
                'type' => 'critical',
                'content' => "Fila " . ($index + 1) . ": Ya existe un administrador con el nombre de usuario '$username'"
            ];
            continue;
        }

        // Aquí puedes agregar más validaciones según tus necesidades

        // Crear el administrador si pasa todas las validaciones
        $encriptedPassword = encryptAdminPassword($password);
        $adminId = $adminModel->create($username, $encriptedPassword);
        
        if ($adminId) {
            $alerts[] = [
                'type' => 'success',
                'content' => "Fila " . ($index + 1) . ": Administrador creado exitosamente"
            ];
        } else {
            $alerts[] = [
                'type' => 'critical',
                'content' => "Fila " . ($index + 1) . ": No se pudo crear el administrador. Por favor, inténtalo de nuevo"
            ];
        }
    }

    return Flight::json(["message" => "Creación de administradores completada", "alerts" => $alerts], 200);
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

    public function updatePasswordByMe($data) {
        $oldPassword = $data['Contraseña_Actual'] ?? null;
        $newPassword = $data['Contraseña_Nueva'] ?? null;

        if (!$oldPassword || !$newPassword) {
            return Flight::json(["message" => "La contraseña antigua y la nueva son obligatorias"], 400);
        }

        // El ID del administrador debería estar disponible a través del middleware de autenticación
        $adminID = Flight::request()->data->getData()['Id_Admin'] ?? null;

        if (!$adminID) {
            return Flight::json(["message" => "ID de administrador no encontrado en la solicitud"], 400);
        }

        $adminModel = new Admin();
        $admin = $adminModel->getById($adminID);

        if (!$admin) {
            return Flight::json(["message" => "No se encontró ningún administrador con el ID proporcionado"], 404);
        }

        $adminPasswordDecrypted = decryptAdminPassword($admin['Contraseña']);

        if ($adminPasswordDecrypted !== $oldPassword) {
            return Flight::json(["message" => "La contraseña actual es incorrecta"], 400);
        }

        $encriptedNewPassword = encryptAdminPassword($newPassword);
        $updateSuccess = $adminModel->updatePassword($adminID, $encriptedNewPassword);
        
        if ($updateSuccess) {
            return Flight::json(["message" => "Contraseña actualizada"], 200);
        } else {
            return Flight::json(["message" => "Error al actualizar la contraseña"], 500);
        }
    }


     public function updatePassword($id, $data) {
         $newPassword = $data['Contraseña'] ?? null;
         if (!$newPassword) {
             return Flight::json(["message" => "Nueva contraseña es obligatoria"], 400);
         }
         $adminModel = new Admin();
         $encriptedNewPassword = encryptAdminPassword($newPassword);
         $rowCount = $adminModel->updatePassword($id, $encriptedNewPassword);
      
         if ($rowCount > 0) {
             return Flight::json(["message" => "Contraseña actualizada"], 200);
         } else {
             return Flight::json(["message" => "No se encontró ningún admin con el ID proporcionado"], 404);
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

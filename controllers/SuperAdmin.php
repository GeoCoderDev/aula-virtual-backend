<?php

require __DIR__ . '/../models/SuperAdmin.php';
require __DIR__ . '/../lib/helpers/encriptations/superadminEncriptation.php';

class SuperadminController {

    public function getById($id, $includePassword = false)
    {
        $superadminModel = new Superadmin();
        $superadmin = $superadminModel->getById($id, $includePassword);

        if (!$superadmin) {
            Flight::json(["message" => "No existe este superadministrador"], 404);
        } else {
            // Desencriptar el nombre de usuario
            $superadmin['Nombre_Usuario'] = decryptSuperadminUsername($superadmin['Nombre_Usuario']);

            // Si includePassword es verdadero, desencriptar la contraseña
            if ($includePassword) {
                $superadmin['Contraseña'] = decryptSuperadminPassword($superadmin['Contraseña']);
            }

            Flight::json($superadmin, 200);
        }
    }


    /**
     * Esta función devuelve un array o false
     * array si se enviaron las credenciales correctas, este array contendra los datos del  superadmin logueado
     * false si las credenciales son incorrectas
     * @param [type] $data
     * @return bool|array
     */
    public function validateIdAndUsername($data){

        $superadmin = new Superadmin();
        $superadminFinded = $superadmin->getById($data->Id_Superadmin);

        if($superadminFinded && $superadminFinded["Nombre_Usuario"]==$data->Username_Superadmin){
            return $superadminFinded;
 
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
    public function validateSuperadmin($data) {

        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        
        if (!$username || !$password) return 1;

        $username_encripted = encryptSuperadminUsername($username);

        $superadmin = new Superadmin();
        $superadminFinded = $superadmin->getByUsername($username_encripted);        

        if ($superadminFinded) {
            $superadmin_password_decripted = decryptSuperadminPassword($superadminFinded["Contraseña"]);
            if($superadmin_password_decripted==$password) return $superadminFinded;
        }

        return 2;// Credenciales inválidas
        
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
            return ["message" => "Nombre de usuario actualizado"];
        } else {
            return ["message" => "No se encontró ningún superadmin con el ID proporcionado"];
        }
    }

    public function updatePasswordByMe($data) {
        $oldPassword = $data['Contraseña_Actual'] ?? null;
        $newPassword = $data['Contraseña_Nueva'] ?? null;

        if (!$oldPassword || !$newPassword) {
            return Flight::json(["message" => "La antigua y la nueva contraseña son obligatorias"]);
        }

        // El ID del superadministrador debería estar disponible a través del middleware de autenticación
        $superadminID = Flight::request()->data->getData()['Id_Superadmin'] ?? null;

        if (!$superadminID) {
            return Flight::json(["message" => "No estas autenticado como Superadministrador"]);
        }

        $superadminModel = new Superadmin();
        $superadmin = $superadminModel->getById($superadminID);

        if (!$superadmin) {
            return Flight::json(["message" => "No se encontró ningún superadministrador con el ID proporcionado"]);
        }

        $superadminPasswordDecrypted = decryptSuperadminPassword($superadmin['Contraseña']);

        if ($superadminPasswordDecrypted !== $oldPassword) {
            return Flight::json(["message" => "La contraseña actual es incorrecta"]);
        }

        $encriptedNewPassword = encryptSuperadminPassword($newPassword);
        $updateSuccess = $superadminModel->updatePassword($superadminID, $encriptedNewPassword);
        
        if ($updateSuccess) {
            return Flight::json(["message" => "Contraseña actualizada"]);
        } else {
            return Flight::json(["message" => "Error al actualizar la contraseña"]);
        }
    }


    // Puedes agregar más métodos según sea necesario
}
?>

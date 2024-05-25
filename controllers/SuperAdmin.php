<?php

require __DIR__ . '/../Models/Superadmin.php';
require __DIR__ .'/../lib/helpers/encriptations/superadminEncriptation.php';

class SuperadminController {

    public function getById($id) {
        $superadminModel = new Superadmin();
        $superadmin = $superadminModel->getById($id);
        return json_encode($superadmin);
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

    public function updatePassword($data) {
        $newPassword = $data['Contraseña'] ?? null;

        if (!$newPassword) {
            return Flight::json(["message" => "Nueva contraseña es obligatoria"], 400);
        }

        // El ID del supeadministrador debería estar disponible a través del middleware de autenticación
        $superadminID = Flight::request()->data->getData()['Id_Superadmin'] ?? null;

        if (!$superadminID) {
            return Flight::json(["message" => "ID de superadministrador no encontrado en la solicitud"], 400);
        }

        $superadminModel = new Superadmin();
        $encriptedNewPassword = encryptSuperadminPassword($newPassword);
        $updateSuccess = $superadminModel->updatePassword($superadminID, $encriptedNewPassword);
        
        if ($updateSuccess) {
            return Flight::json(["message" => "Contraseña actualizada"], 200);
        } else {
            return Flight::json(["message" => "No se encontró ningún superadmin con el ID proporcionado"], 404);
        }
    }

    

    // Puedes agregar más métodos según sea necesario
}
?>

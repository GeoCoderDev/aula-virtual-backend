<?php

use Config\S3Manager;

require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ .'/../lib/helpers/JWT/JWT_Teacher.php';
require_once __DIR__ .'/../lib/helpers/JWT/JWT_Student.php';
require_once __DIR__ .'/../lib/helpers/encriptations/userEncriptation.php';
require_once __DIR__ .'/../lib/helpers/functions/areFieldsComplete.php';
require_once __DIR__ .'/../config/S3Manager.php';
require_once __DIR__.'/../lib/helpers/functions/generateProfilePhotoKeyS3.php';

class UsuarioController {

    public function getById($id) {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getById($id);
        return json_encode($usuario);
    }

    public function getAll() {
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->getAll();
        return json_encode($usuarios);
    }

    public function getByUsername($username) {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getByUsername($username);
        return json_encode($usuario);
    }

    public function create($data, $DNI,  $returnAlerts=false,$rowIndex=null) {

        $otherAlerts = [];
        // Verificar si todos los campos requeridos están presentes en $data
        if (!$returnAlerts) {
            if (!areFieldsComplete($data,  ['Nombres', 'Apellidos', 'Fecha_Nacimiento', 'Nombre_Usuario', 'Contraseña_Usuario', 'Direccion_Domicilio', 'Nombre_Contacto_Emergencia', 'Parentezco_Contacto_Emergencia', 'Telefono_Contacto_Emergencia'])) {
                return;
            }
        } else {
            // Verificar si el array tiene exactamente 9 elementos
            if (count($data) !== 9) {
                global $otherAlerts;
                $otherAlerts[] = [
                    'type' => 'critical',
                    'content' => "Fila ".($rowIndex+1).": Faltan datos..."
                ];
                return $otherAlerts;
            }

            // Verificar que cada elemento del array no sea nulo o indefinido
            $requiredFields = ['Nombres', 'Apellidos', 'Fecha_Nacimiento', 'Nombre_Usuario', 'Contraseña_Usuario', 'Direccion_Domicilio', 'Nombre_Contacto_Emergencia', 'Parentezco_Contacto_Emergencia', 'Telefono_Contacto_Emergencia'];

            foreach ($data as $index => $value) {
                if (!isset($value) || $value === null || $value === '') {
                    $requiredFields = ['Nombres', 'Apellidos', 'Fecha_Nacimiento', 'Nombre_Usuario', 'Contraseña_Usuario', 'Direccion_Domicilio', 'Nombre_Contacto_Emergencia', 'Parentezco_Contacto_Emergencia', 'Telefono_Contacto_Emergencia'];
                    $field = $requiredFields[$index];
                    global $otherAlerts;
                    $otherAlerts[] = [
                        'type' => 'critical',
                        'content' => "Fila " . ($rowIndex + 1) . ": Falta el campo obligatorio: $field"
                    ];
                    return $otherAlerts;
                }
            }            
        }
    
        $Nombres = $data[$returnAlerts?0:'Nombres'] ?? null;
        $Apellidos = $data[$returnAlerts?1:'Apellidos'] ?? null;
        $Fecha_Nacimiento = $data[$returnAlerts?2:'Fecha_Nacimiento']?? null;
        $Nombre_Usuario = $data[$returnAlerts?3:'Nombre_Usuario'];
        $Contraseña_Usuario = $data[$returnAlerts?4:'Contraseña_Usuario']?? null;
        $Direccion_Domicilio = $data[$returnAlerts?5:'Direccion_Domicilio']?? null;
        $Nombre_Contacto_Emergencia = $data[$returnAlerts?6:'Nombre_Contacto_Emergencia']?? null;
        $Parentezco_Contacto_Emergencia = $data[$returnAlerts?7:'Parentezco_Contacto_Emergencia']?? null;
        $Telefono_Contacto_Emergencia = $data[$returnAlerts?8:'Telefono_Contacto_Emergencia']?? null;
        $Foto_Perfil_Key_S3 = null;

        // Verificar si se ha enviado la foto de perfil(Esto solo se podra desde un formulario)
        if(isset($_FILES['Foto_Perfil']) && $_FILES['Foto_Perfil']['error'] === UPLOAD_ERR_OK) {
            // Obtener la información de la foto de perfil
            $fotoPerfil = $_FILES['Foto_Perfil'];
            $extension = pathinfo($fotoPerfil['name'], PATHINFO_EXTENSION); // Obtener la extensión del archivo

            // Validar la extensión del archivo
            $allowedExtensions = ['jpg', 'jpeg', 'png']; // Extensiones permitidas
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                Flight::json(["message" => "La extensión del archivo de la foto de perfil no es válida. Solo se permiten archivos jpg, jpeg y png."], 400);
                return;
            }

            $Foto_Perfil_Key_S3 = generateProfilePhotoKeyS3($Nombre_Usuario, $DNI, $extension);
            
            // Creando un cliente de S3
            $s3Manager = new S3Manager();

            // Ruta temporal del archivo
            $tempFilePath = $fotoPerfil['tmp_name'];

            // Subir el archivo al bucket de S3
            $uploadResult = $s3Manager->uploadFile($tempFilePath, $Foto_Perfil_Key_S3);

            if(!$uploadResult) {
                Flight::json(["message" => "Error al subir la foto de perfil"], 500);
                return;
            }       
        }

        $usuarioModelo = new Usuario();
        $existingUsuario = $usuarioModelo->getByUsername($Nombre_Usuario);

        if ($existingUsuario) {

            if($returnAlerts){
                global $otherAlerts;
                $otherAlerts[] = [
                        'type' => 'critical',
                        'content' => "Fila ".($rowIndex+1).": Ya existe un usuario con ese nombre de usuario"
                    ];
                return $otherAlerts;
            }else{
                
                Flight::json(["message" => "Ya existe un usuario con ese nombre de usuario"], 409);
            }

            return;
        }

        $Id_Usuario = $usuarioModelo->create(
            $Nombres,
            $Apellidos,
            $Fecha_Nacimiento,
            $Nombre_Usuario,
            encryptUserPassword($Contraseña_Usuario),
            $Direccion_Domicilio,
            $Nombre_Contacto_Emergencia,
            $Parentezco_Contacto_Emergencia,
            $Telefono_Contacto_Emergencia,
            $Foto_Perfil_Key_S3
        );

        if (!$Id_Usuario) {

            if($returnAlerts){
                global $otherAlerts;
                $otherAlerts[] = [
                    'type' => 'critical',
                    'content' => "Fila ".($rowIndex+1).": No se pudo crear el Usuario"
                ];
                return $otherAlerts;
            }else{
                
                Flight::json(["message" => "No se pudo crear el usuario"], 409);
            }

            return;
        }

        return $Id_Usuario;
    }


    public function createFromArray($data, $DNI) {

        // Verificar si todos los campos requeridos están presentes en $data
        $Nombres = $data['Nombres'];
        $Apellidos = $data['Apellidos'];
        $Fecha_Nacimiento = $data['Fecha_Nacimiento'];
        $Nombre_Usuario = $data['Nombre_Usuario'];
        $Contraseña_Usuario = $data['Contraseña_Usuario'];
        $Direccion_Domicilio = $data['Direccion_Domicilio'];
        $Nombre_Contacto_Emergencia = $data['Nombre_Contacto_Emergencia'];
        $Parentezco_Contacto_Emergencia = $data['Parentezco_Contacto_Emergencia'];
        $Telefono_Contacto_Emergencia = $data['Telefono_Contacto_Emergencia'];
        $Foto_Perfil_Key_S3 = null;

        // Verificar si se ha enviado la foto de perfil
        if(isset($_FILES['Foto_Perfil']) && $_FILES['Foto_Perfil']['error'] === UPLOAD_ERR_OK) {
            // Obtener la información de la foto de perfil
            $fotoPerfil = $_FILES['Foto_Perfil'];
            $extension = pathinfo($fotoPerfil['name'], PATHINFO_EXTENSION); // Obtener la extensión del archivo

            // Validar la extensión del archivo
            $allowedExtensions = ['jpg', 'jpeg', 'png']; // Extensiones permitidas
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                Flight::json(["message" => "La extensión del archivo de la foto de perfil no es válida. Solo se permiten archivos jpg, jpeg y png."], 400);
                return;
            }

            $Foto_Perfil_Key_S3 = generateProfilePhotoKeyS3($Nombre_Usuario, $DNI, $extension);
            
            // Creando un cliente de S3
            $s3Manager = new S3Manager();

            // Ruta temporal del archivo
            $tempFilePath = $fotoPerfil['tmp_name'];

            // Subir el archivo al bucket de S3
            $uploadResult = $s3Manager->uploadFile($tempFilePath, $Foto_Perfil_Key_S3);

            if(!$uploadResult) {
                Flight::json(["message" => "Error al subir la foto de perfil"], 500);
                return;
            }       
        }


        $usuarioModelo = new Usuario();
        $existingUsuario = $usuarioModelo->getByUsername($Nombre_Usuario);

        if ($existingUsuario) {
            Flight::json(["message" => "Ya existe un usuario con ese nombre de usuario"], 409);
            return;
        }

        $Id_Usuario = $usuarioModelo->create(
            $Nombres,
            $Apellidos,
            $Fecha_Nacimiento,
            $Nombre_Usuario,
            encryptUserPassword($Contraseña_Usuario),
            $Direccion_Domicilio,
            $Nombre_Contacto_Emergencia,
            $Parentezco_Contacto_Emergencia,
            $Telefono_Contacto_Emergencia,
            $Foto_Perfil_Key_S3
        );


        return $Id_Usuario;
        }


    public function update($id, $data, $DNI_Estudiante) {

        // Verificar si todos los campos requeridos están presentes en $data
        if(!areFieldsComplete($data,  ['Nombres', 'Apellidos', 'Fecha_Nacimiento', 'Nombre_Usuario', 'Contraseña_Usuario', 'Direccion_Domicilio', 'Nombre_Contacto_Emergencia', 'Parentezco_Contacto_Emergencia', 'Telefono_Contacto_Emergencia'])) return;

        $Nombres = $data['Nombres'];
        $Apellidos = $data['Apellidos'];
        $Fecha_Nacimiento = $data['Fecha_Nacimiento'];
        $Nombre_Usuario = $data['Nombre_Usuario'];
        $Contraseña_Usuario = $data['Contraseña_Usuario'];
        $Direccion_Domicilio = $data['Direccion_Domicilio'];
        $Nombre_Contacto_Emergencia = $data['Nombre_Contacto_Emergencia'];
        $Parentezco_Contacto_Emergencia = $data['Parentezco_Contacto_Emergencia'];
        $Telefono_Contacto_Emergencia = $data['Telefono_Contacto_Emergencia'];
        $Foto_Perfil_Key_S3 = $data['Foto_Perfil_Key_S3'] ?? null;

        // Verificar si se ha enviado la foto de perfil
        if(isset($_FILES['Foto_Perfil']) && $_FILES['Foto_Perfil']['error'] === UPLOAD_ERR_OK) {            

            // Obtener la información de la foto de perfil
            $fotoPerfil = $_FILES['Foto_Perfil'];
            $extension = pathinfo($fotoPerfil['name'], PATHINFO_EXTENSION); // Obtener la extensión del archivo

            // Validar la extensión del archivo
            $allowedExtensions = ['jpg', 'jpeg', 'png']; // Extensiones permitidas
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                Flight::json(["message" => "La extensión del archivo de la foto de perfil no es válida. Solo se permiten archivos jpg, jpeg y png."], 400);
                return;
            }

            // Creando un cliente de S3
            $s3Manager = new S3Manager();

            // Eliminar la anterior foto de perfil si existe
            if($Foto_Perfil_Key_S3){
                // Intentar eliminar la foto de perfil anterior
                $deleteResult = $s3Manager->deleteObject($Foto_Perfil_Key_S3);
                // Verificar si la eliminación fue exitosa
                if(!$deleteResult){
                    // Si la eliminación falla, devolver un error
                    Flight::json(["message" => "Error al eliminar la foto de perfil anterior"], 500);
                    return;
                }
            }

            $Foto_Perfil_Key_S3 = generateProfilePhotoKeyS3($Nombre_Usuario, $DNI_Estudiante, $extension);            

            // Ruta temporal del archivo
            $tempFilePath = $fotoPerfil['tmp_name'];

            // Subir el archivo al bucket de S3
            $uploadResult = $s3Manager->uploadFile($tempFilePath, $Foto_Perfil_Key_S3);

            if(!$uploadResult) {
                Flight::json(["message" => "Error al subir la foto de perfil"], 500);
                return;
            }       
        }

        $usuarioModelo = new Usuario();
        $successUpdate = $usuarioModelo->update(
            $id,
            $Nombres,
            $Apellidos,
            $Fecha_Nacimiento,
            $Nombre_Usuario,
            $Contraseña_Usuario,
            $Direccion_Domicilio,
            $Nombre_Contacto_Emergencia,
            $Parentezco_Contacto_Emergencia,
            $Telefono_Contacto_Emergencia,
            $Foto_Perfil_Key_S3
        );

        // Verificar si se actualizó al menos una fila
        return $successUpdate;


    }

    
    public function delete($id)
{
    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getById($id);

    if (!$usuario) {
        Flight::json(["message" => "No se encontró ningún usuario con el ID proporcionado"], 404);
        return;
    }

    $fotoPerfilKeyS3 = $usuario['Foto_Perfil_Key_S3'] ?? null;

    // Eliminar el registro del usuario
    $succesDeletedUser = $usuarioModel->delete($id);

    if ($succesDeletedUser) {
        // Eliminar la foto de perfil del servicio de almacenamiento (S3) si existe
        if ($fotoPerfilKeyS3 !== null) {
            
            $s3Manager = new S3Manager();
            $s3Manager->deleteObject($fotoPerfilKeyS3);
        }

        return true;
    } else {
        return false;
    }
}


    public function getUserRoleByUserId($userId) {
        $profesorModel = new Profesor();
        
        // Verificar si el usuario es un profesor
        $profesor = $profesorModel->getByUserId($userId);
        if ($profesor) {
            return "teacher";
        }
        
        $estudianteModel = new Estudiante();
        // Verificar si el usuario es un estudiante
        $estudiante = $estudianteModel->getByUserId($userId);
        if ($estudiante) {
            return "student";
        }

        // Si no es profesor ni estudiante, no tiene un rol específico
        return null;
    }

        // Método para obtener el rol de usuario por nombre de usuario
    public function getUserRoleByUsername($username) {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getByUsername($username);

        if (!$usuario) {
            return "Usuario no encontrado";
        }

        return $this->getUserRoleByUserId($usuario['Id_Usuario']);
    }



    public function validateUser($data) {
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return 1; // Datos incompletos
        }

        $userRole = $this->getUserRoleByUsername($username);

        if ($userRole === "teacher") {
            $profesorModel = new Profesor();
            $profesor = $profesorModel->getByUsername($username,true);
            if ($profesor) {
                $decriptedPassword = decryptUserPassword($profesor['Contraseña_Usuario']);
                if ($decriptedPassword === $password) {
                    // Credenciales correctas, generar JWT para profesor
                    return ["token" => generateTeacherJWT($profesor['DNI_Profesor'], $username), "role" => "teacher"];
                } else {
                    return 2; // Credenciales incorrectas
                }
            }
        } else if ($userRole === "student") {
            $estudianteModel = new Estudiante();
            $estudiante = $estudianteModel->getByUsername($username, true);
            if ($estudiante) {
                $decriptedPassword = decryptUserPassword($estudiante['Contraseña_Usuario']);
                if ($decriptedPassword === $password) {
                    // Credenciales correctas, generar JWT para estudiante
                    return ["token" => generateStudentJWT($estudiante['DNI_Estudiante'], $username), "role" => "student"];
                } else {
                    return 2; // Credenciales incorrectas
                }
            }
        }

        return 2; // Credenciales incorrectas
    }


}
?>

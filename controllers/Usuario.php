<?php

require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ .'/../lib/helpers/JWT/JWT_Teacher.php';
require_once __DIR__ .'/../lib/helpers/JWT/JWT_Student.php';
require_once __DIR__ .'/../lib/helpers/encriptations/userEncriptation.php';
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

    public function create($data) {
        // Verificar si se proporcionan todos los campos necesarios
        $requiredFields = ['Nombres', 'Apellidos', 'Fecha_Nacimiento', 'Nombre_Usuario', 'Contraseña_Usuario'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return json_encode(["message" => "Falta el campo obligatorio: $field"]);
            }
        }

        $usuarioModel = new Usuario();
        $idUsuario = $usuarioModel->create(
            $data['Nombres'],
            $data['Apellidos'],
            $data['Fecha_Nacimiento'],
            $data['Nombre_Usuario'],
            $data['Contraseña_Usuario'],
            $data['Direccion_Domicilio'] ?? null,
            $data['Nombre_Contacto_Emergencia'] ?? null,
            $data['Parentezco_Contacto_Emergencia'] ?? null,
            $data['Telefono_Contacto_Emergencia'] ?? null,
            $data['Foto_Perfil_Key_S3'] ?? null
        );
        
        return json_encode(["message" => "Usuario creado", "id" => $idUsuario]);
    }

    public function update($id, $data) {
        // Verificar si al menos un campo para actualizar ha sido proporcionado
        if (!isset($data['Nombres']) && !isset($data['Apellidos']) && !isset($data['Fecha_Nacimiento']) && !isset($data['Nombre_Usuario']) && !isset($data['Contraseña_Usuario']) && !isset($data['Direccion_Domicilio']) && !isset($data['Nombre_Contacto_Emergencia']) && !isset($data['Parentezco_Contacto_Emergencia']) && !isset($data['Telefono_Contacto_Emergencia']) && !isset($data['Foto_Perfil_Key_S3'])) {
            return json_encode(["message" => "Ningún campo para actualizar proporcionado"]);
        }

        $usuarioModel = new Usuario();
        $rowCount = $usuarioModel->update($id, $data);
        
        if ($rowCount > 0) {
            return json_encode(["message" => "Usuario actualizado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún usuario con el ID proporcionado"]);
        }
    }

    public function delete($id) {
        $usuarioModel = new Usuario();
        $rowCount = $usuarioModel->delete($id);
        
        if ($rowCount > 0) {
            return json_encode(["message" => "Usuario eliminado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún usuario con el ID proporcionado"]);
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

<?php

require_once __DIR__ . '/../Models/Usuario.php';

class UsuarioController
{
    public function getAll()
    {
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->getAll();
        return json_encode($usuarios);
    }

    public function getById($id)
    {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getById($id);
        return json_encode($usuario);
    }

    public function getByUsername($username)
    {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getByUsername($username);
        return json_encode($usuario);
    }

    public function getByNombreUsuario($Nombre_Usuario)
    {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getByNombreUsuario($Nombre_Usuario);
        return json_encode($usuario);
    }

    public function create(
            $Nombres,
            $Apellidos,
            $Fecha_Nacimiento,
            $Nombre_Usuario,
            $Contraseña_Usuario,
            $Dirección_Domicilio,
            $Nombre_Contacto_Emergencia,
            $Parentezco_Contacto_Emergencia,
            $Telefono_Contacto_Emergencia,
            $Foto_Perfil_Key_S3)
    {
        // Validar que se proporcionen todos los campos necesarios
        $requiredFields = ['Nombres', 'Apellidos', 'Fecha_Nacimiento', 'Nombre_Usuario', 'Contraseña_Usuario', "Dirección_Domicilio", "Nombre_Contacto_Emergencia","Parentezco_Contacto_Emergencia", "Telefono_Contacto_Emergencia", "Foto_Perfil_Key_S3"];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return json_encode(["message" => "Falta el campo obligatorio: $field"]);
            }
        }

        // Crear el usuario
        $usuarioModel = new Usuario();
        $userId = $usuarioModel->create(
            $Nombres,
            $Apellidos,
            $Fecha_Nacimiento,
            $Nombre_Usuario,
            $Contraseña_Usuario,
            $Dirección_Domicilio,
            $Nombre_Contacto_Emergencia,
            $Parentezco_Contacto_Emergencia,
            $Telefono_Contacto_Emergencia,
            $Foto_Perfil_Key_S3
        );

        if ($userId) {
            return json_encode(["message" => "Usuario creado", "user_id" => $userId]);
        } else {
            return json_encode(["message" => "Error al crear el usuario"]);
        }
    }

    public function update($Id_Usuario, $data)
    {
        $usuarioModel = new Usuario();
        $rowCount = $usuarioModel->update($Id_Usuario, $data);

        if ($rowCount > 0) {
            return json_encode(["message" => "Usuario actualizado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún usuario con el ID proporcionado"]);
        }
    }

    public function delete($Id_Usuario)
    {
        $usuarioModel = new Usuario();
        $rowCount = $usuarioModel->delete($Id_Usuario);

        if ($rowCount > 0) {
            return json_encode(["message" => "Usuario eliminado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún usuario con el ID proporcionado"]);
        }
    }
}

?>

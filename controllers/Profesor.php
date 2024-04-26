<?php
require_once __DIR__ . '../../Models/Profesor.php';
require_once __DIR__.'/Usuario.php';

class ProfesorController
{
    public function getAll()
    {
        $profesorModel = new Profesor();
        $profesores = $profesorModel->getAll();
        return $profesores;
    }

    public function getByDNI($DNI_Profesor)
    {
        $profesorModel = new Profesor();
        $profesor = $profesorModel->getByDNI($DNI_Profesor);
        return json_encode($profesor);
    }

    public function create($data)
    {
        $DNI_Profesor = $data['DNI_Profesor'] ?? null;
        $Nombres = $data['Nombres'] ?? null;
        $Apellidos = $data['Apellidos'] ?? null;
        $Fecha_Nacimiento = $data['Fecha_Nacimiento'] ?? null;
        $Nombre_Usuario = $data['Nombre_Usuario'] ?? null;
        $Contraseña_Usuario = $data['Contraseña_Usuario'] ?? null;
        $Dirección_Domicilio = $data['Dirección_Domicilio'] ?? null;
        $Nombre_Contacto_Emergencia = $data['Nombre_Contacto_Emergencia'] ?? null;
        $Parentezco_Contacto_Emergencia = $data['Parentezco_Contacto_Emergencia'] ?? null;
        $Telefono_Contacto_Emergencia = $data['Telefono_Contacto_Emergencia'] ?? null;
        $Foto_Perfil_Key_S3 = $data['Foto_Perfil_Key_S3'] ?? null;

        if (!$DNI_Profesor || !$Nombres || !$Apellidos || !$Fecha_Nacimiento || !$Nombre_Usuario || !$Contraseña_Usuario || !$Dirección_Domicilio || !$Nombre_Contacto_Emergencia || !$Parentezco_Contacto_Emergencia || !$Telefono_Contacto_Emergencia || !$Foto_Perfil_Key_S3) {
            return Flight::json(["message" => "Faltan campos obligatorios"], 401);
        }

        $profesorModel = new Profesor();
        $existingProfesor = $profesorModel->getByDNI($DNI_Profesor);

        if ($existingProfesor) {
            return Flight::json(["message" => "Ya existe un profesor con ese DNI"], 409);
        }

        $usuarioController = new UsuarioController();
        $existingUsuario = $usuarioController->getByUsername($Nombre_Usuario);

        if ($existingUsuario) {
            return Flight::json(["message" => "Ya existe un usuario con ese nombre de usuario"], 409);
        }

        $Id_Usuario = $usuarioController->create(
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

        if ($Id_Usuario) {
            $rowCount = $profesorModel->create($DNI_Profesor, $Id_Usuario);
            if ($rowCount > 0) {
                return Flight::json(["message" => "Profesor creado"]);
            } else {
                return Flight::json(["message" => "Error al crear el profesor"], 500);
            }
        } else {
            return Flight::json(["message" => "Error al crear el usuario"], 500);
        }
    }

    public function update($DNI_Profesor, $data)
    {
        $Id_Usuario = $data['Id_Usuario'] ?? null;
        if (!$Id_Usuario) {
            return json_encode(["message" => "Id_Usuario debe ser proporcionado para actualizar"]);
        }

        $profesorModel = new Profesor();
        $rowCount = $profesorModel->update($DNI_Profesor, $Id_Usuario);

        if ($rowCount > 0) {
            return json_encode(["message" => "Profesor actualizado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún profesor con el DNI proporcionado"]);
        }
    }

    public function delete($DNI_Profesor)
    {
        $profesorModel = new Profesor();
        $rowCount = $profesorModel->delete($DNI_Profesor);

        if ($rowCount > 0) {
            return json_encode(["message" => "Profesor eliminado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún profesor con el DNI proporcionado"]);
        }
    }
}
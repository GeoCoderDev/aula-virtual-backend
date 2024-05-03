<?php
require_once __DIR__ . '/../models/Estudiante.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../lib/helpers/encriptations/userEncriptation.php';

class EstudianteController
{
    
    public function getAll($includePassword = false, $limit = 200, $startFrom = 0)
    {
        $estudianteModel = new Estudiante();
        $estudiantes = $estudianteModel->getAll($includePassword, $limit , $startFrom);
        return $estudiantes;
    }

    public function getByDNI($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $estudiante = $estudianteModel->getByDNI($DNI_Estudiante);
        return json_encode($estudiante);
    }

    public function validateDNIAndUsername($data) {
    $estudianteModel = new Estudiante();
    $estudianteFinded = $estudianteModel->getByDNI($data->DNI_Estudiante);

    if ($estudianteFinded && $estudianteFinded["Nombre_Usuario"]==$data->Username_Estudiante) {
        return $estudianteFinded;      
    }

    return false;
    }

    public function create($data)
    {
        // Definir los campos requeridos
        $requiredFields = ['DNI_Estudiante', 'Nombres', 'Apellidos', 'Fecha_Nacimiento', 'Nombre_Usuario', 'Contraseña_Usuario', 'Direccion_Domicilio', 'Nombre_Contacto_Emergencia', 'Parentezco_Contacto_Emergencia', 'Telefono_Contacto_Emergencia', 'Foto_Perfil_Key_S3', 'Id_Aula'];
        
        // Verificar si todos los campos requeridos están presentes en $data
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                // Devolver una respuesta JSON indicando el campo que falta
                return json_encode(["message" => "Falta el campo obligatorio: $field"]);
            }
        }

        // Si todos los campos requeridos están presentes, continuar con la lógica para insertar en la base de datos
        $DNI_Estudiante = $data['DNI_Estudiante'];
        $Nombres = $data['Nombres'];
        $Apellidos = $data['Apellidos'];
        $Fecha_Nacimiento = $data['Fecha_Nacimiento'];
        $Nombre_Usuario = $data['Nombre_Usuario'];
        $Contraseña_Usuario = $data['Contraseña_Usuario'];
        $Direccion_Domicilio = $data['Direccion_Domicilio'];
        $Nombre_Contacto_Emergencia = $data['Nombre_Contacto_Emergencia'];
        $Parentezco_Contacto_Emergencia = $data['Parentezco_Contacto_Emergencia'];
        $Telefono_Contacto_Emergencia = $data['Telefono_Contacto_Emergencia'];
        $Foto_Perfil_Key_S3 = $data['Foto_Perfil_Key_S3'];
        $Id_Aula = $data['Id_Aula'];

        $estudianteModel = new Estudiante();
        $existingEstudiante = $estudianteModel->getByDNI($DNI_Estudiante);

        if ($existingEstudiante) {
            return json_encode(["message" => "Ya existe un estudiante con ese DNI"], 409);
        }

        $usuarioModelo = new Usuario();
        $existingUsuario = $usuarioModelo->getByUsername($Nombre_Usuario);

        if ($existingUsuario) {
            return json_encode(["message" => "Ya existe un usuario con ese nombre de usuario"], 409);
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

        if ($Id_Usuario) {
            $success = $estudianteModel->create($DNI_Estudiante, $Id_Usuario, $Id_Aula);
            if ($success) {
                return json_encode(["message" => "Estudiante creado"]);
            } else {
                return json_encode(["message" => "Error al crear el estudiante"], 500);
            }
        } else {
            return json_encode(["message" => "Error al crear el usuario"], 500);
        }
    }

    public function getCursosByDNI($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $cursos = $estudianteModel->getCursosByDNI($DNI_Estudiante);
        return $cursos;
    }


    public function update($DNI_Estudiante, $data)
    {
        $Id_Usuario = $data['Id_Usuario'] ?? null;
        if (!$Id_Usuario) {
            return json_encode(["message" => "Id_Usuario debe ser proporcionado para actualizar"]);
        }
        $Id_Aula = $data['Id_Aula'];

        $estudianteModel = new Estudiante();
        $rowCount = $estudianteModel->update($DNI_Estudiante, $Id_Usuario, $Id_Aula);

        if ($rowCount > 0) {
            return json_encode(["message" => "Estudiante actualizado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún estudiante con el DNI proporcionado"]);
        }
    }

    public function delete($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $rowCount = $estudianteModel->delete($DNI_Estudiante);

        if ($rowCount > 0) {
            return json_encode(["message" => "Estudiante eliminado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún estudiante con el DNI proporcionado"]);
        }
    }
}

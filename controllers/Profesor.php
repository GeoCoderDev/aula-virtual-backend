<?php
require_once __DIR__ . '/../models/Profesor.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../lib/helpers/encriptations/userEncriptation.php';

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

    public function validateDNIAndUsername($data) {
        $profesorModel = new Profesor();
        $profesorFinded = $profesorModel->getByDNI($data->DNI_Profesor);

        if ($profesorFinded && $profesorFinded["Nombre_Usuario"]==$data->Username_Profesor) {
            return $profesorFinded;                    
        }

        return false; // No se encontró el profesor o no coincide el ID y el nombre de usuario
        
    }

    public function create($data)
    {
        // Definir los campos requeridos
        $requiredFields = ['DNI_Profesor', 'Nombres', 'Apellidos', 'Fecha_Nacimiento', 'Nombre_Usuario', 'Contraseña_Usuario', 'Direccion_Domicilio', 'Nombre_Contacto_Emergencia', 'Parentezco_Contacto_Emergencia', 'Telefono_Contacto_Emergencia', 'Foto_Perfil_Key_S3'];
        
        // Verificar si todos los campos requeridos están presentes en $data
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                // Devolver una respuesta JSON indicando el campo que falta
                return json_encode(["message" => "Falta el campo obligatorio: $field"]);
            }
        }

        // Si todos los campos requeridos están presentes, continuar con la lógica para insertar en la base de datos
        $DNI_Profesor = $data['DNI_Profesor'];
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

        $profesorModel = new Profesor();
        $existingProfesor = $profesorModel->getByDNI($DNI_Profesor);

        if ($existingProfesor) {
            return json_encode(["message" => "Ya existe un profesor con ese DNI"], 409);
        }

        $usuarioModel = new Usuario();
        $existingUsuario = $usuarioModel->getByUsername($Nombre_Usuario);

        if ($existingUsuario) {
            return json_encode(["message" => "Ya existe un usuario con ese nombre de usuario"], 409);
        }

        $Id_Usuario = $usuarioModel->create(
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
            $success = $profesorModel->create($DNI_Profesor, $Id_Usuario);
            if ($success) {
                return json_encode(["message" => "Profesor creado"]);
            } else {
                return json_encode(["message" => "Error al crear el profesor"], 500);
            }
        } else {
            return json_encode(["message" => "Error al crear el usuario"], 500);
        }
    }

    /**
     * Esta funcion devuelve la lista de curso que enseña un profesor sin considerar el grado o seccion, y sin repetir 
     *
     * @param [type] $DNI_Profesor
     * @return array
    */
    public function getCursosByDNI($DNI_Profesor)
    {
        $profesorModel = new Profesor();
        $cursos = $profesorModel->getCursosByDNI($DNI_Profesor);
        return $cursos;
    }

    public function getAsignacionesByDNI($DNI_Profesor){
        $profesorModel = new Profesor();
        $asignations = $profesorModel->getAsignacionesByDNI($DNI_Profesor);
        return $asignations;
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

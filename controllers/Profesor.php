<?php
require_once __DIR__ . '/../models/Profesor.php';
require_once __DIR__ . '/Usuario.php';
require_once __DIR__ . '/../lib/helpers/encriptations/userEncriptation.php';

class ProfesorController
{

    public function getAll($includePassword = false, $limit = 200, $startFrom = 0, $dni = null, $nombre = null, $apellidos = null, $estado = null)
    {
        $profesorModel = new Profesor();
        $profesores = $profesorModel->getAll($includePassword, $limit, $startFrom, $dni, $nombre, $apellidos, $estado);
        return $profesores;
    }

    public function getProfessorCount($dni = null, $nombre = null, $apellidos = null, $estado = null) {
        $profesorModel = new Profesor();
        // Pasar los parámetros de consulta al modelo para obtener el conteo de profesores
        $count = $profesorModel->getProfessorCount($dni, $nombre, $apellidos, $estado);
        return $count;
    }


    public function getByDNI($DNI_Profesor)
    {
        $profesorModel = new Profesor();
        $profesor = $profesorModel->getByDNI($DNI_Profesor);
        return $profesor;
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
       // Verificar si todos los campos requeridos están presentes en $data
       if(!areFieldsComplete($data,  ['DNI_Profesor'])) return;   

        // Si todos los campos requeridos están presentes, continuar con la lógica para insertar en la base de datos
        $DNI_Profesor = $data['DNI_Profesor'];
       

        $profesorModel = new Profesor();
        $existingProfesor = $profesorModel->getByDNI($DNI_Profesor);

        if ($existingProfesor) {
            Flight::json(["message" => "Ya existe un profesor con ese DNI"], 409);
            return;
        }

        $userController = new UsuarioController();
        $Id_Usuario = $userController->create($data, $DNI_Profesor);

        if ($Id_Usuario) {
            $success = $profesorModel->create($DNI_Profesor, $Id_Usuario);
            if ($success) {
                Flight::json(["message" => "Profesor creado"], 201);
            } else {
                Flight::json(["message" => "Error al crear el profesor"], 500);
            }
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
        // Verificar si el estudiante existe
        $profesorModel = new Profesor();
        $existingProfesor = $profesorModel->getByDNI($DNI_Profesor);

        if (!$existingProfesor) {
            Flight::json(["message" => "No se encontró ningún estudiante con el DNI proporcionado"], 404);
            return;
        }

        $userController = new UsuarioController();

        $data['Foto_Perfil_Key_S3'] = $existingProfesor['Foto_Perfil_Key_S3'];

        $successUpdateUser = $userController->update($existingProfesor['Id_Usuario'], $data, $DNI_Profesor);

        if ($successUpdateUser) {        
            Flight::json(["message" => "Usuario actualizado correctamente"], 200);
        }else{
            Flight::json(["message" => "Error al actualizar el usuario"], 500);
        }
    }

    public function delete($DNI_Profesor)
    {
        $profesorModel = new Profesor();

        $profesor = $profesorModel->getByDNI($DNI_Profesor);

        if (!$profesor) {
            Flight::json(["message" => "No se encontró ningún estudiante con el DNI proporcionado"], 404);
            return;
        }

        // Eliminar el registro del estudiante
        $successDeleteTeacher = $profesorModel->delete($DNI_Profesor);

        if ($successDeleteTeacher) {

            // Eliminar el usuario correspondiente
            $usuarioModel = new UsuarioController();
            $userDeletedSuccess = $usuarioModel->delete($profesor['Id_Usuario']);
            if(!$userDeletedSuccess){
                Flight::json(["message" => "No se pudo eliminar el usuario"], 500);
                return;
            }

            Flight::json(["message" => "Profesor eliminado"], 200);
        } else {
            Flight::json(["message" => "No se pudo eliminar el profesor"], 500);
        }
    }
}

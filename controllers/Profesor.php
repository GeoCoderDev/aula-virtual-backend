<?php
use Config\S3Manager;
require_once __DIR__ . '/../models/Profesor.php';
require_once __DIR__ . '/Usuario.php';
require_once __DIR__ . '/../lib/helpers/encriptations/userEncriptation.php';
require_once __DIR__.'/../config/S3Manager.php';
require_once __DIR__ . '/../lib/helpers/functions/extractExtension.php';

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
        
        if(!$profesor){
            Flight::json(["message"=>"No existe el profesor con $DNI_Profesor"],404);
        }else{
            Flight::json($profesor,200);
        }

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

    public function multipleCreate($data) {
        $alerts = [];

        if (!isset($data['teacherValues']) || !is_array($data['teacherValues'])) {
            return Flight::json(["message" => "No se encontraron datos de profesores para crear"], 400);
        }

        $profesorModel = new Profesor();
        $userController = new UsuarioController();

        foreach ($data['teacherValues'] as $index => $teacherData) {
            $DNI_Profesor = $teacherData[0] ?? null;

            $existingProfesor = $profesorModel->getByDNI($DNI_Profesor);
            if ($existingProfesor) {
                $alerts[] = [
                    'type' => 'critical',
                    'content' => "Fila " . ($index + 1) . ": Ya existe un profesor con el DNI '$DNI_Profesor'"
                ];
                continue;
            }


            // Crear usuario
            $usuarioIdOrAlerts = $userController->create(array_slice($teacherData, 1), $DNI_Profesor, true, $index);

            if (!is_array($usuarioIdOrAlerts)) {
                // Crear estudiante
                $success = $profesorModel->create($DNI_Profesor, $usuarioIdOrAlerts);
                if ($success) {
                    $alerts[] = [
                        'type' => 'success',
                        'content' => "Fila " . ($index + 1) . ": Profesor creado exitosamente"
                    ];
                } else {
                    $alerts[] = [
                        'type' => 'critical',
                        'content' => "Fila " . ($index + 1) . ": No se pudo crear el profesor. Por favor, inténtalo de nuevo"
                    ];
                }
            }else{
                $alerts = array_merge($alerts, $usuarioIdOrAlerts);
            }
        }

        return Flight::json(["message" => "Creación de profesores completada", "alerts" => $alerts], 200);
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

    public function getUserIdByDNI($DNI_Profesor)
    {
        $profesorModel = new Profesor();
        $userId = $profesorModel->getUserIdByDNI($DNI_Profesor);
        
        if ($userId) {
            // Si se encontró el ID de usuario, responder con un JSON
            Flight::json(["Id_Usuario" => $userId], 200);
        } else {
            // Si no se encontró el profesor, responder con un mensaje de error
            Flight::json(["message" => "No se encontró ningún profesor con el DNI proporcionado"], 404);
        }
    }

    public function getAsignacionesByDNI($DNI_Profesor){
        $profesorModel = new Profesor();
        $asignations = $profesorModel->getAsignacionesByDNI($DNI_Profesor);
        return $asignations;
    }

    public function getProfilePhotoUrl($DNI_Profesor)
    {
        $profesorModel = new Profesor();
        $photoUrl = $profesorModel->getProfilePhotoUrl($DNI_Profesor);

        if ($photoUrl) {
            Flight::json(["Foto_Perfil_URL" => $photoUrl], 200);
        } else {
            Flight::json(["message" => "No se encontró tu foto de perfil"], 404);
        }
    }
    
    public function update($DNI_Profesor, $data)
    
    {
        // Verificar si el profesor existe
        $profesorModel = new Profesor();
        $existingProfesor = $profesorModel->getByDNI($DNI_Profesor);

        if (!$existingProfesor) {
            Flight::json(["message" => "No se encontró ningún profesor con el DNI proporcionado"], 404);
            return;
        }

        $userController = new UsuarioController();

        $data['Foto_Perfil_Key_S3'] = $existingProfesor['Foto_Perfil_Key_S3'];


        if($data['Foto_Perfil_Key_S3'] && $data["Nombre_Usuario"]!==$existingProfesor["Nombre_Usuario"]){

            $s3Manager = new S3Manager();
            $newKey = generateProfilePhotoKeyS3($data["Nombre_Usuario"],$DNI_Profesor,extraerExtension($data['Foto_Perfil_Key_S3']));

            $successUpdateOBject = $s3Manager->renameObject($data['Foto_Perfil_Key_S3'], $newKey);

            if(!$successUpdateOBject){
                return Flight::json(["message"=>"Ocurrio un error actualizando el estudiante"], 500);
            }

            $data['Foto_Perfil_Key_S3'] = $newKey;

        }

        $successUpdateUser = $userController->update($existingProfesor['Id_Usuario'], $data, $DNI_Profesor);

        if ($successUpdateUser) {        
            Flight::json(["message" => "Profesor actualizado correctamente"], 200);
        }else{
            Flight::json(["message" => "Error al actualizar el Profesor"], 500);
        }
    }

    public function updateByMe($DNI_Profesor, $data)
    {
        // Verificar si todos los campos requeridos están presentes en $data
        if (!areFieldsComplete($data, ['Direccion_Domicilio', 'Telefono', 'Nombre_Contacto_Emergencia', 'Parentezco_Contacto_Emergencia', 'Telefono_Contacto_Emergencia'])) {
            return;
        }

        // Verificar si el estudiante existe
        $profesorModel = new Profesor();
        $existingProfesor = $profesorModel->getByDNI($DNI_Profesor);

        if (!$existingProfesor) {
            Flight::json(["message" => "No se encontró ningún estudiante con el DNI proporcionado"], 404);
            return;
        }

        $data['Foto_Perfil_Key_S3'] = $existingProfesor["Foto_Perfil_Key_S3"];

        $Id_Usuario = $existingProfesor["Id_Usuario"];

        
        $userController = new UsuarioController();
        $successUpdateUser = $userController->updateByMe($Id_Usuario, $data);

        if ($successUpdateUser) {
            Flight::json(["message" => "Datos actualizados correctamente"], 200);
        }
    }

    public function toggleState($DNI_Profesor) {
        $profesorModel = new Profesor();

        // Obtener el profesor por su DNI
        $profesor = $profesorModel->getByDNI($DNI_Profesor);

        // Verificar si se encontró el profesor
        if (!$profesor) {
            Flight::json(["message" => "No se encontró ningún profesor con el DNI proporcionado"], 404);
            return;
        }

        $usuarioModel = new Usuario();
        // Cambiar el estado del profesor
        $success= $usuarioModel->toggleState($profesor["Id_Usuario"]);

        if ($success) {
            Flight::json(["message" => "Estado del profesor actualizado correctamente"], 200);
        } else {
            Flight::json(["message" => "Error al actualizar el estado del profesor"], 500);
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

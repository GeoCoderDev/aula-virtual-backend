<?php
use Config\S3Manager;
require_once __DIR__ . '/../models/Estudiante.php';
require_once __DIR__ . '/../lib/helpers/encriptations/userEncriptation.php';
require_once __DIR__.'/../config/S3Manager.php';
require_once __DIR__.'/../lib/helpers/functions/areFieldsComplete.php';
require_once __DIR__.'/Usuario.php';

class EstudianteController
{
    
    public function getAll($includePassword = false, $limit = 200, $startFrom = 0, $dni = null, $nombre = null, $apellidos = null, $grado = null, $seccion = null)
    {
        $estudianteModel = new Estudiante();
        $estudiantes = $estudianteModel->getAll($includePassword, $limit, $startFrom, $dni, $nombre, $apellidos, $grado, $seccion);
        return $estudiantes;
    }


    public function getStudentCount($dni = null, $nombre = null, $apellidos = null, $grado = null, $seccion = null) {
        $estudianteModel = new Estudiante();
        // Pasar los parámetros de consulta al modelo para obtener el conteo de estudiantes
        $count = $estudianteModel->getStudentCount($dni, $nombre, $apellidos, $grado, $seccion);
        return $count;
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
        // Verificar si todos los campos requeridos están presentes en $data
        if(!areFieldsComplete($data,  ['DNI_Estudiante', 'Grado', 'Seccion'])) return;    

        // Si todos los campos requeridos están presentes, continuar con la lógica para insertar en la base de datos
        $DNI_Estudiante = $data['DNI_Estudiante'];
        $Grado = $data['Grado'];
        $Seccion = $data['Seccion'];

        $estudianteModel = new Estudiante();
        $existingEstudiante = $estudianteModel->getByDNI($DNI_Estudiante);

        if ($existingEstudiante) {
            Flight::json(["message" => "Ya existe un estudiante con ese DNI"], 409);
            return;
        }


        $aulaController = new AulaController();

        // Obtener el ID del aula correspondiente al grado y la sección
        $aula = $aulaController->getByGradoSeccion($Grado, $Seccion);

        if (!$aula) {
            Flight::json(["message" => "No se encontró el aula correspondiente al grado $Grado y la sección $Seccion"], 404);
            return;
        }

        $Id_Aula = $aula['Id_Aula'];

    
        $userController = new UsuarioController();

        $Id_Usuario = $userController->create($data, $DNI_Estudiante);

        if ($Id_Usuario) {
            $success = $estudianteModel->create($DNI_Estudiante, $Id_Usuario, $Id_Aula);
            if ($success) {
                Flight::json(["message" => "Estudiante creado"], 201);
            } else {
                Flight::json(["message" => "Error al crear el estudiante"], 500);
            }
        }
    }


    public function update($DNI_Estudiante, $data) {


        // Verificar si todos los campos requeridos están presentes en $data
        if(!areFieldsComplete($data,  [ 'Grado', 'Seccion'])) return;    

        // Verificar si el estudiante existe
        $estudianteModel = new Estudiante();
        $existingEstudiante = $estudianteModel->getByDNI($DNI_Estudiante);

        if (!$existingEstudiante) {
            Flight::json(["message" => "No se encontró ningún estudiante con el DNI proporcionado"], 404);
            return;
        }

        $Grado = $data['Grado'];
        $Seccion = $data['Seccion'];

        $aulaController = new AulaController();

        // Obtener el ID del aula correspondiente al grado y la sección
        $aula = $aulaController->getByGradoSeccion($Grado, $Seccion);

        if (!$aula) {
            Flight::json(["message" => "No se encontró el aula correspondiente al grado $Grado y la sección $Seccion"], 404);
            return;
        }

        $Id_Aula = $aula['Id_Aula'];

        $userController = new UsuarioController();

        $data['Foto_Perfil_Key_S3'] = $existingEstudiante['Foto_Perfil_Key_S3'];

        $successUpdateUser = $userController->update($existingEstudiante['Id_Usuario'], $data, $DNI_Estudiante);

        if ($successUpdateUser) {
            $success = $estudianteModel->update($DNI_Estudiante, $existingEstudiante['Id_Usuario'], $Id_Aula);
            if ($success) {
                Flight::json(["message" => "Usuario actualizado correctamente"], 200);
            } else {
                Flight::json(["message" => "Error al actualizar el estudiante"], 500);
            }
        }else{
            Flight::json(["message" => "Error al actualizar el usuario"], 500);
        }


    }


    public function getCursosByDNI($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $cursos = $estudianteModel->getCursosByDNI($DNI_Estudiante);
        return $cursos;
    }


    public function delete($DNI_Estudiante)
{
    $estudianteModel = new Estudiante();
    $estudiante = $estudianteModel->getByDNI($DNI_Estudiante, true);

    if (!$estudiante) {
        Flight::json(["message" => "No se encontró ningún estudiante con el DNI proporcionado"], 404);
        return;
    }

    // Eliminar el registro del estudiante
    $successDeleteStudent = $estudianteModel->delete($DNI_Estudiante);

    if ($successDeleteStudent) {

        // Eliminar el usuario correspondiente
        $usuarioModel = new UsuarioController();
        $userDeletedSuccess = $usuarioModel->delete($estudiante['Id_Usuario']);
        if(!$userDeletedSuccess){
            Flight::json(["message" => "No se pudo eliminar el usuario"], 500);
            return;
        }

        Flight::json(["message" => "Estudiante eliminado"], 200);
    } else {
        Flight::json(["message" => "No se pudo eliminar el estudiante"], 500);
    }
}
}

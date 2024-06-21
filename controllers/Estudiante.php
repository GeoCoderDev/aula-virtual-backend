<?php
use Config\S3Manager;

require_once __DIR__ . '/../models/Estudiante.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../lib/helpers/encriptations/userEncriptation.php';
require_once __DIR__ . '/../lib/helpers/functions/extractExtension.php';
require_once __DIR__.'/../config/S3Manager.php';
require_once __DIR__.'/../lib/helpers/functions/areFieldsComplete.php';
require_once __DIR__.'/Usuario.php';


define("TEACHER_ASOCIATED_NOT_FOUND", "No hay ningun profesor asignado");

class EstudianteController
{
    
    public function getAll($includePassword = false, $limit = 200, $startFrom = 0, $dni = null, $nombre = null, $apellidos = null, $grado = null, $seccion = null, $estado = null) // Agrega el nuevo parámetro de consulta
{
    $estudianteModel = new Estudiante();
    $estudiantes = $estudianteModel->getAll($includePassword, $limit, $startFrom, $dni, $nombre, $apellidos, $grado, $seccion, $estado); // Pasa el nuevo parámetro de consulta
    return $estudiantes;
}


    public function getStudentCount($dni = null, $nombre = null, $apellidos = null, $grado = null, $seccion = null, $estado = null) {
        $estudianteModel = new Estudiante();
        // Pasar los parámetros de consulta al modelo para obtener el conteo de estudiantes
        $count = $estudianteModel->getStudentCount($dni, $nombre, $apellidos, $grado, $seccion, $estado);
        return $count;
    }

    public function getByDNI($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $estudiante = $estudianteModel->getByDNI($DNI_Estudiante);

        if(!$estudiante){
            Flight::json(["message"=>"No existe el estudiante con $DNI_Estudiante"],404);
        }else{
            Flight::json($estudiante,200);
        }
    }

    public function getProfilePhotoUrl($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $photoUrl = $estudianteModel->getProfilePhotoUrl($DNI_Estudiante);

        if ($photoUrl) {
            Flight::json(["Foto_Perfil_URL" => $photoUrl], 200);
        } else {
            Flight::json(["message" => "No se encontró tu foto de perfil"], 404);
        }
    }

    
    public function validateDNIAndUsername($data) {
    $estudianteModel = new Estudiante();
    $estudianteFinded = $estudianteModel->getByDNI($data->DNI_Estudiante);

    if ($estudianteFinded && $estudianteFinded["Nombre_Usuario"]==$data->Username_Estudiante) {
        return $estudianteFinded;      
    }

    return false;
    }

    public function getCourseData($idCursoAula) {
    $estudianteModelo = new Estudiante();

    // Obtener datos del curso
    $courseData = $estudianteModelo->fetchCourseData($idCursoAula);
    
    if (!$courseData) {
        Flight::json(['message' => 'No se encontraron datos del curso'], 404);
        return;
    }

    // Obtener temas del curso
    $courseTopics = $estudianteModelo->fetchCourseTopics($idCursoAula);

    // Construir la respuesta combinando datos del curso y, opcionalmente, los temas
    $response = [
        'Id_Curso_Aula' => $courseData['Id_Curso_Aula'],
        'Grado' => $courseData['Grado'],
        'Seccion' => $courseData['Seccion'],
        'Profesor_Asociado' => $courseData['Profesor_Asociado']===null?TEACHER_ASOCIATED_NOT_FOUND:$courseData['Profesor_Asociado'],
        'Nombre_Curso' => $courseData['Nombre_Curso']
    ];

    if (!empty($courseTopics)) {
        $response['Temas'] = $courseTopics;
    }

    Flight::json($response, 200);
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

    public function multipleCreate($data) {
        $alerts = [];

        if (!isset($data['studentValues']) || !is_array($data['studentValues'])) {
            return Flight::json(["message" => "No se encontraron datos de estudiantes para crear"], 400);
        }

        $estudianteModel = new Estudiante();
        $aulaController = new AulaController();
        $userController = new UsuarioController();

        foreach ($data['studentValues'] as $index => $studentData) {
            $DNI_Estudiante = $studentData[0] ?? null;
            $Grado = $studentData[1] ?? null;
            $Seccion = $studentData[2] ?? null;

            if (!$DNI_Estudiante || !$Grado || !$Seccion) {
                $alerts[] = [
                    'type' => 'critical',
                    'content' => "Fila " . ($index + 1) . ": DNI, Grado y Sección son obligatorios"
                ];
                continue;
            }

            $existingEstudiante = $estudianteModel->getByDNI($DNI_Estudiante);
            if ($existingEstudiante) {
                $alerts[] = [
                    'type' => 'critical',
                    'content' => "Fila " . ($index + 1) . ": Ya existe un estudiante con el DNI '$DNI_Estudiante'"
                ];
                continue;
            }

            $aula = $aulaController->getByGradoSeccion($Grado, $Seccion);
            if (!$aula) {
                $alerts[] = [
                    'type' => 'critical',
                    'content' => "Fila " . ($index + 1) . ": No se encontró el aula correspondiente al grado $Grado y la sección $Seccion"
                ];
                continue;
            }

            $Id_Aula = $aula['Id_Aula'];

            // Crear usuario
            $usuarioIdOrAlerts = $userController->create(array_slice($studentData, 3), $DNI_Estudiante, true, $index);

            if (!is_array($usuarioIdOrAlerts)) {
                // Crear estudiante
                $success = $estudianteModel->create($DNI_Estudiante, $usuarioIdOrAlerts, $Id_Aula);
                if ($success) {
                    $alerts[] = [
                        'type' => 'success',
                        'content' => "Fila " . ($index + 1) . ": Estudiante creado exitosamente"
                    ];
                } else {
                    $alerts[] = [
                        'type' => 'critical',
                        'content' => "Fila " . ($index + 1) . ": No se pudo crear el estudiante. Por favor, inténtalo de nuevo"
                    ];
                }
            }else{
                $alerts = array_merge($alerts, $usuarioIdOrAlerts);
            }
        }

        return Flight::json(["message" => "Creación de estudiantes completada", "alerts" => $alerts], 200);
    }


    public function update($DNI_Estudiante, $data) {

        // Verificar si todos los campos requeridos están presentes en $data
        if(!areFieldsComplete($data,  ['Grado', 'Seccion'])) return;    

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
        
        $existingUsuario = $userController->getByUsername($data["Nombre_Usuario"]);

        // Si existe un usuario con el mismo nombre de usuario y su ID es diferente del ID actual, devolver un mensaje de error
        if ($existingUsuario && $existingUsuario['Id_Usuario'] !== $existingEstudiante["Id_Usuario"]) {
            Flight::json(["message" => "Ya existe un usuario con ese nombre de usuario"], 409);
            return;
        }

        $data['Foto_Perfil_Key_S3'] = $existingEstudiante["Foto_Perfil_Key_S3"];

        if($data['Foto_Perfil_Key_S3'] && $data["Nombre_Usuario"]!==$existingEstudiante["Nombre_Usuario"]){

            $s3Manager = new S3Manager();
            $newKey = generateProfilePhotoKeyS3($data["Nombre_Usuario"],$DNI_Estudiante,extraerExtension($data['Foto_Perfil_Key_S3']));

            $successUpdateOBject = $s3Manager->renameObject($data['Foto_Perfil_Key_S3'], $newKey);

            if(!$successUpdateOBject){
                return Flight::json(["message"=>"Ocurrio un error actualizando el estudiante"], 500);
            }

            $data['Foto_Perfil_Key_S3'] = $newKey;

        }

        $successUpdateUser = $userController->update($existingEstudiante['Id_Usuario'], $data, $DNI_Estudiante);

        if ($successUpdateUser) {
            $success = $estudianteModel->update($DNI_Estudiante, $existingEstudiante['Id_Usuario'], $Id_Aula);
            if ($success) {
                Flight::json(["message" => "Estudiante actualizado correctamente"], 200);
            } else {
                Flight::json(["message" => "Error al actualizar el estudiante"], 500);
            }
        }


    }

    public function updateByMe($DNI_Estudiante, $data)
    {

        // Verificar si el estudiante existe
        $estudianteModel = new Estudiante();
        $existingEstudiante = $estudianteModel->getByDNI($DNI_Estudiante);

        if (!$existingEstudiante) {
            Flight::json(["message" => "No se encontró ningún estudiante con el DNI proporcionado"], 404);
            return;
        }

        $data['Foto_Perfil_Key_S3'] = $existingEstudiante["Foto_Perfil_Key_S3"];

        $Id_Usuario = $existingEstudiante["Id_Usuario"];

        $userController = new UsuarioController();
        $successUpdateUser = $userController->updateByMe($Id_Usuario, $data);

        if ($successUpdateUser) {
            Flight::json(["message" => "Datos actualizados correctamente"], 200);
        }
    }

    public function getCursosByDNI($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $cursos = $estudianteModel->getCursosByDNI($DNI_Estudiante);
        Flight::json($cursos , 200);
    }

    public function getUserIdByDNI($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $userId = $estudianteModel->getUserIdByDNI($DNI_Estudiante);

        if ($userId !== false) {
            Flight::json(["userId" => $userId], 200);
        } else {
            Flight::json(["message" => "No se encontró ningún estudiante con el DNI proporcionado"], 404);
        }
    }


    public function toggleState($DNI_Estudiante) {
        $estudianteModel = new Estudiante();

        // Obtener el estudiante por su DNI
        $estudiante = $estudianteModel->getByDNI($DNI_Estudiante);

        // Verificar si se encontró el estudiante
        if (!$estudiante) {
            Flight::json(["message" => "No se encontró ningún estudiante con el DNI proporcionado"], 404);
            return;
        }        

        $usuarioModel = new Usuario();
        // Cambiar el estado del estudiante
        $success= $usuarioModel->toggleState($estudiante["Id_Usuario"]);

        if ($success) {
            Flight::json(["message" => "Estado del estudiante actualizado correctamente"], 200);
        } else {
            Flight::json(["message" => "Error al actualizar el estado del estudiante"], 500);
        }
    }

    public function hasAccessToCourse($DNI_Estudiante, $cursoAulaID) {
        $estudiante = new Estudiante();
        $access = $estudiante->hasAccessToCourse($DNI_Estudiante, $cursoAulaID);
        Flight::json(['access' => $access], 200);
    }

    public function delete($DNI_Estudiante)
    {
        $estudianteModel = new Estudiante();
        $estudiante = $estudianteModel->getByDNI($DNI_Estudiante);

        if (!$estudiante) {
            Flight::json(["message" => "No se encontró ningún estudiante con el DNI proporcionado"], 404);
            return;
        }

        // Eliminar el registro del estudiante
        $successDeleteStudent = $estudianteModel->delete($DNI_Estudiante);

        if ($successDeleteStudent) {

            // Eliminar el usuario correspondiente
            $usuarioController = new UsuarioController();
            $userDeletedSuccess = $usuarioController->delete($estudiante['Id_Usuario']);
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

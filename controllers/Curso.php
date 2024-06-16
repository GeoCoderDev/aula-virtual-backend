<?php
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Tema.php';
require_once __DIR__.'/../lib/helpers/functions/areFieldsComplete.php';

class CursoController
{
    public function getAll( $startFrom = 0, $limit = 200, $nombre = null, $grado = null)
    {
        $cursoModel = new Curso();
        $cursos = $cursoModel->getAll( $startFrom, $limit,  $nombre, $grado);
        return $cursos;
    }

    public function getCursosCount($nombre = null, $grado = null)
    {
        $cursoModel = new Curso();
        $count = $cursoModel->getCursosCount($nombre, $grado);
        return $count;
    }


    public function getById($Id_Curso)
    {
        $cursoModel = new Curso();
        return $cursoModel->getById($Id_Curso);
    }

    public function getByNombre($Nombre)
    {
        $cursoModel = new Curso();
        return $cursoModel->getByNombre($Nombre);
    }

    public function getCursosConGrados()
    {
        $cursoModel = new Curso();
        return $cursoModel->getCursosConGrados();
    }

    public function create($Nombre)
    {
        // Verificar si el curso ya existe
        $cursoModel = new Curso();
        $existingCurso = $cursoModel->getByNombre($Nombre);

        if ($existingCurso) {
            return ["error" => "Ya existe un curso con ese nombre"];
        }

        // Crear el curso si no existe
        $cursoId = $cursoModel->create($Nombre);
        if ($cursoId) {
            return ["message" => "Curso creado correctamente", "id" => $cursoId];
        } else {
            return ["error" => "Error al crear el curso"];
        }
    }

    public function createWithAulas($data)
    {
        // Verificar si todos los campos requeridos están presentes en $data
        if(!areFieldsComplete($data,  [ 'nombre', 'grados'])) return; 

        $nombre = $data['nombre'];
        $grados = $data['grados'];

        // Validar el nombre del curso
        if (empty($nombre)) {
            Flight::json(["error" => "El nombre del curso no puede estar vacío"], 400);
            return;
        }

        // Convertir la cadena de grados en un array
        $gradosArray = explode(',', $grados);

        // Verificar si ya existe un curso con el mismo nombre (insensible a mayúsculas/minúsculas)
        $cursoModel = new Curso();
        $existingCurso = $cursoModel->getByNombre(strtolower($nombre));

        if ($existingCurso) {
            Flight::json(["error" => "Ya existe un curso con ese nombre"], 400);
            return;
        }

        // Crear el curso en la base de datos
        $cursoId = $cursoModel->create($nombre);

        if (!$cursoId) {
            Flight::json(["error" => "Error al crear el curso"], 500);
            return;
        }

        // Obtener todas las aulas correspondientes a los grados especificados
        $aulaModel = new Aula();
        $aulas = $aulaModel->getByGrados($gradosArray);

        // Agregar el nuevo curso a todas las aulas obtenidas
        foreach ($aulas as $aula) {
            $success = $aulaModel->addCursoToAula($aula['Id_Aula'], $cursoId);
            if (!$success) {
                // En caso de error, deshacer la creación del curso y devolver un error
                $cursoModel->delete($cursoId);
                Flight::json(["error" => "Error al asociar el curso al aula"], 500);
                return;
            }
        }

        // Si todo fue exitoso, devolver un mensaje de éxito
        Flight::json(["message" => "Curso creado y asociado a las aulas correspondientes"], 201);
    }

    public function updateWithAulas($Id_Curso, $data) {
    // Crear una instancia del modelo Curso y pasar la conexión
    $cursoModel = new Curso();

    try {
        // Iniciar la transacción desde el modelo
        $cursoModel->beginTransaction();

        // Verificar si todos los campos requeridos están presentes en $data
        if (!areFieldsComplete($data, ['nombre', 'grados'])) {
            Flight::json(["message" => "Campos incompletos"], 400);
            $cursoModel->rollback();
            return;
        }

        $nombre = $data['nombre'] ?? null;
        $grados = $data['grados'] ?? null;

        // Verificar si el curso existe
        $existingCurso = $cursoModel->getById($Id_Curso);

        if (!$existingCurso) {
            Flight::json(["message" => "No se encontró ningún curso con el ID proporcionado"], 404);
            $cursoModel->rollback();
            return;
        }

        // Verificar si ya existe un curso con el mismo nombre
        $cursoExistente = $cursoModel->getByNombre($nombre);
        if ($cursoExistente && $cursoExistente['Id_Curso'] != $Id_Curso) {
            Flight::json(["message" => "Ya existe otro curso con el mismo nombre"], 400);
            $cursoModel->rollback();
            return;
        }

        // Actualizar el nombre del curso
        $successUpdate = $cursoModel->update($Id_Curso, $nombre);

        if (!$successUpdate) {
            Flight::json(["message" => "Error al actualizar el curso"], 500);
            $cursoModel->rollback();
            return;
        }

        // Convertir la cadena de grados en un array
        $gradosArray = explode(',', $grados);

        // Obtener las aulas actuales asociadas al curso
        $aulaModel = new Aula();
        $currentAulas = $aulaModel->getAulasByCurso($Id_Curso);

        // Extraer los grados actuales
        $currentGrados = array_unique(array_map(function($aula) {
            return $aula['Grado'];
        }, $currentAulas));

        // Determinar grados a añadir y eliminar
        $gradosToAdd = array_diff($gradosArray, $currentGrados);
        $gradosToRemove = array_diff($currentGrados, $gradosArray);

        // Agregar nuevas asociaciones curso-aula
        foreach ($gradosToAdd as $grado) {
            $aulas = $aulaModel->getByGrado($grado);
            foreach ($aulas as $aula) {
                $success = $aulaModel->addCursoToAula($aula['Id_Aula'], $Id_Curso);
                if (!$success) {
                    Flight::json(["message" => "Error al asociar el curso al aula"], 500);
                    $cursoModel->rollback();
                    return;
                }
            }
        }

        // Eliminar asociaciones curso-aula para grados removidos
        foreach ($gradosToRemove as $grado) {
            $aulas = $aulaModel->getByGrado($grado);
            foreach ($aulas as $aula) {
                // Verificar que no haya dependencias antes de eliminar
                $hasDependencies = $cursoModel->checkDependencies($Id_Curso, $aula['Id_Aula']);
                if ($hasDependencies) {
                    Flight::json(["message" => "No se puede eliminar el curso porque ya hay contenido en algunas aulas"], 400);
                    $cursoModel->rollback();
                    return;
                }
                $success = $aulaModel->removeCursoFromAula($aula['Id_Aula'], $Id_Curso);
                if (!$success) {
                    Flight::json(["message" => "Error al desasociar el curso del aula"], 500);
                    $cursoModel->rollback();
                    return;
                }
            }
        }

        // Confirmar la transacción desde el modelo
        $cursoModel->commit();

        Flight::json(["message" => "Curso actualizado correctamente"], 200);
    } catch (Exception $e) {
        // Revertir la transacción en caso de error desde el modelo
        $cursoModel->rollback();
        Flight::json(["message" => "Ocurrió un error al actualizar el curso"], 500);
    }
}




public function delete($Id_Curso)
{
    // Verificar si el curso existe
    $cursoModel = new Curso();
    $existingCurso = $cursoModel->getById($Id_Curso);

    if (!$existingCurso) {
        return Flight::json(["message" => "No se encontró ningún curso con el ID proporcionado"], 404);
    }

    // Obtener las aulas asociadas al curso
    $aulaModel = new Aula();
    $aulas = $aulaModel->getAulasByCurso($Id_Curso);

    // Verificar dependencias para cada aula
    foreach ($aulas as $aula) {
        $hasDependencies = $cursoModel->checkDependencies($Id_Curso, $aula['Id_Aula']);
        if ($hasDependencies) {
            return Flight::json(["message" => "No se puede eliminar el curso porque ya hay contenido en algunas aulas"], 400);
        }
    }

    // Eliminar el curso de las aulas donde está asociado
    $success = $aulaModel->removeCursoFromAulas($Id_Curso);
    if (!$success) {
        return Flight::json(["message" => "Error al desasociar el curso de las aulas"], 409);
    }

    // Intentar eliminar el curso
    $success = $cursoModel->delete($Id_Curso);
    if ($success) {
        return Flight::json(["message" => "Curso eliminado correctamente"], 200);
    } else {
        return Flight::json(["message" => "Error al eliminar el curso"], 500);
    }
}


}
?>

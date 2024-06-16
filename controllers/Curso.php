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
    public function updateWithAulas($Id_Curso, $data)
    {
        // Verificar si todos los campos requeridos están presentes en $data
        if (!areFieldsComplete($data, ['nombre', 'grados'])) {
            Flight::json(["error" => "Faltan campos requeridos"], 400);
            return;
        }

        $nombre = $data['nombre'];
        $grados = $data['grados'];

        // Verificar si el curso existe
        $cursoModel = new Curso();
        $existingCurso = $cursoModel->getById($Id_Curso);

        if (!$existingCurso) {
            Flight::json(["error" => "No se encontró ningún curso con el ID proporcionado"], 404);
            return;
        }

        // Verificar si el nombre recibido ya lo tiene otro curso con diferente ID
        $cursoConMismoNombre = $cursoModel->getByNombre($nombre);
        if ($cursoConMismoNombre && $cursoConMismoNombre['Id_Curso'] != $Id_Curso) {
            Flight::json(["error" => "Ya existe un curso con ese nombre"], 400);
            return;
        }

        // Convertir la cadena de grados en un array
        $gradosArray = explode(',', $grados);

        // Obtener los grados actuales asociados con el curso
        $aulaModel = new Aula();
        $currentGrados = $aulaModel->getGradosByCurso($Id_Curso);

        // Determinar los grados añadidos y eliminados
        $gradosAñadidos = array_diff($gradosArray, $currentGrados);
        $gradosEliminados = array_diff($currentGrados, $gradosArray);

        // Verificar si se puede eliminar el curso de los grados desactivados
        $temasModel = new Tema();
        foreach ($gradosEliminados as $grado) {
            $aulas = $aulaModel->getByGrado($grado);
            foreach ($aulas as $aula) {
                $temasRelacionados = $temasModel->getByCursoAula($Id_Curso, $aula['Id_Aula']);
                if (!empty($temasRelacionados)) {
                    Flight::json(["error" => "No se puede desactivar el curso del grado $grado porque hay temas relacionados"], 400);
                    return;
                }
            }
        }

        // Iniciar la transacción
        $cursoModel->beginTransaction();

        try {
            // Actualizar el nombre del curso
            $successUpdate = $cursoModel->update($Id_Curso, $nombre);

            if (!$successUpdate) {
                throw new Exception("Error al actualizar el curso");
            }

            // Agregar el curso a los nuevos grados
            foreach ($gradosAñadidos as $grado) {
                $aulas = $aulaModel->getByGrado($grado);
                foreach ($aulas as $aula) {
                    $success = $aulaModel->addCursoToAula($aula['Id_Aula'], $Id_Curso);
                    if (!$success) {
                        throw new Exception("Error al asociar el curso al aula");
                    }
                }
            }

            // Eliminar el curso de los grados desactivados
            foreach ($gradosEliminados as $grado) {
                $aulas = $aulaModel->getByGrado($grado);
                foreach ($aulas as $aula) {
                    $success = $aulaModel->removeCursoFromAula($aula['Id_Aula'], $Id_Curso);
                    if (!$success) {
                        throw new Exception("Error al desasociar el curso del aula");
                    }
                }
            }

            // Confirmar la transacción
            $cursoModel->commit();

            Flight::json(["message" => "Curso actualizado correctamente"], 200);
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $cursoModel->rollBack();
            Flight::json(["message" => $e->getMessage()], 500);
        }
    }


    public function delete($Id_Curso)
    {
        // Verificar si el curso existe
        $cursoModel = new Curso();
        $existingCurso = $cursoModel->getById($Id_Curso);

        if (!$existingCurso) {

            return Flight::json(["message" => "No se encontró ningún curso con el ID proporcionado"],404);
            
        }

        // Eliminar el curso de las aulas donde está asociado
        $aulaModel = new Aula();
        $success = $aulaModel->removeCursoFromAulas($Id_Curso);

        if (!$success) {
            
            return Flight::json(["message" => "Error al desasociar el curso de las aulas"],409);
        }

        // Intentar eliminar el curso
        $success = $cursoModel->delete($Id_Curso);
        if ($success) {
            Flight::json(["message" => "Curso eliminado correctamente"],200);
        } else {
            Flight::json(["message" => "Error al eliminar el curso"],500);
        }
    }


}
?>

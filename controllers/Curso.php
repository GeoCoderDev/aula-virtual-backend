<?php
require_once __DIR__ . '/../models/Curso.php';
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
    if(!areFieldsComplete($data,  [ 'nombre', 'grados'])) return; 


    $nombre = $data['nombre'] ?? null;
    $grados = $data['grados'] ?? null;

    // Verificar si el curso existe
    $cursoModel = new Curso();
    $existingCurso = $cursoModel->getById($Id_Curso);

    if (!$existingCurso) {
        Flight::json(["error" => "No se encontró ningún curso con el ID proporcionado"], 404);
        return;
    }

    // Actualizar el nombre del curso
    $succesUpdate = $cursoModel->update($Id_Curso, $nombre);

    if ($succesUpdate) {
        // Convertir la cadena de grados en un array
        $gradosArray = explode(',', $grados);

        // Obtener todas las aulas correspondientes a los nuevos grados especificados
        $aulaModel = new Aula();
        $aulas = $aulaModel->getByGrados($gradosArray);

        // Eliminar el curso de las aulas donde ya no debe estar asociado
        $aulaModel->removeCursoFromAula($Id_Curso);

        // Agregar el curso a las nuevas aulas
        foreach ($aulas as $aula) {
            $success = $aulaModel->addCursoToAula($aula['Id_Aula'], $Id_Curso);
            if (!$success) {
                Flight::json(["error" => "Error al asociar el curso al aula"], 500);
                return;
            }
        }

        Flight::json(["message" => "Curso actualizado correctamente"], 200);
    } else {
        Flight::json(["error" => "Error al actualizar el curso"], 500);
    }
}


    public function delete($Id_Curso)
    {
        // Verificar si el curso existe
        $cursoModel = new Curso();
        $existingCurso = $cursoModel->getById($Id_Curso);

        if (!$existingCurso) {
            return ["error" => "No se encontró ningún curso con el ID proporcionado"];
        }

        // Eliminar el curso de las aulas donde está asociado
        $aulaModel = new Aula();
        $success = $aulaModel->removeCursoFromAula($Id_Curso);

        if (!$success) {
            return ["error" => "Error al desasociar el curso de las aulas"];
        }

        // Intentar eliminar el curso
        $rowCount = $cursoModel->delete($Id_Curso);
        if ($rowCount > 0) {
            return ["message" => "Curso eliminado correctamente"];
        } else {
            return ["error" => "Error al eliminar el curso"];
        }
    }


}
?>

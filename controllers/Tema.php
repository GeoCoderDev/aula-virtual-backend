<?php
require_once __DIR__ . '/../models/Tema.php';
require_once __DIR__ . '/../models/RecursoTema.php';
require_once __DIR__ . '/../models/Curso_Aula.php';
require_once __DIR__ . '/../lib/helpers/functions/areFieldsComplete.php';

class TemaController
{
    private $temaModel;
    private $recursoModel;
    private $cursoAulaModel;

    public function __construct()
    {
        $this->temaModel = new Tema();
        $this->recursoModel = new RecursoTema();
        $this->cursoAulaModel = new Curso_Aula();
    }

    public function getAll()
    {
        $temas = $this->temaModel->getAll();
        Flight::json($temas);
    }

    public function getById($id)
    {
        $tema = $this->temaModel->getById($id);
        if ($tema) {
            Flight::json($tema);
        } else {
            Flight::json(['message' => 'Tema no encontrado'], 404);
        }
    }

    public function create($data)
    {

        if(!areFieldsComplete($data,  ['Nombre_Tema', 'Id_Curso_Aula'])) return;   

        $nombre = $data['Nombre_Tema'];
        $cursoAulaId = $data['Id_Curso_Aula'];

        // Verificar si el curso aula existe
        if (!$this->cursoAulaModel->getById($cursoAulaId)) {
            Flight::json(['message' => 'El curso aula especificado no existe'], 400);
            return;
        }

        // Verificar si ya existe un tema con el mismo nombre en el mismo curso aula
        if ($this->temaModel->existsByNombreAndCursoAula($nombre, $cursoAulaId)) {
            Flight::json(['message' => 'Ya existe un tema con el mismo nombre en este curso de esta aula'], 400);
            return;
        }

        $idTemaCreated = $this->temaModel->create($nombre, $cursoAulaId);

        if ($idTemaCreated) {
            Flight::json(['message' => 'Tema creado exitosamente', "Id"=> $idTemaCreated], 201);
        } else {
            Flight::json(['message' => 'Error al crear el tema'], 500);
        }
    }

    public function update($id)
    {
        $requestData = Flight::request()->data;
        $nombre = $requestData->Nombre_Tema;
        $descripcion = $requestData->Descripcion;

        if ($this->temaModel->update($id, $nombre, $descripcion)) {
            Flight::json(['message' => 'Tema actualizado exitosamente']);
        } else {
            Flight::json(['message' => 'Error al actualizar el tema'], 500);
        }
    }

    public function delete($id)
    {
        if ($this->temaModel->delete($id)) {
            Flight::json(['message' => 'Tema eliminado exitosamente']);
        } else {
            Flight::json(['message' => 'Error al eliminar el tema'], 500);
        }
    }

    public function getResourcesByTopicId($id)
    {
        $recursos = $this->recursoModel->getByTopicId($id);

        if ($recursos === false) {
            Flight::json(['message' => 'Aun no hay recursos para este tema'], 404);
        } else {
            Flight::json($recursos);
        }
    }
}
?>

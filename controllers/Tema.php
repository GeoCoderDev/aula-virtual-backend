<?php
require_once __DIR__ . '/../models/Tema.php';
require_once __DIR__ . '/../models/RecursoTema.php';

class TemaController
{
    private $temaModel;
    private $recursoModel;

    public function __construct()
    {
        $this->temaModel = new Tema();
        $this->recursoModel = new RecursoTema();
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

    public function create()
    {
        $requestData = Flight::request()->data;

        $nombre = $requestData->Nombre;
        $cursoAulaId = $requestData->Id_Curso_Aula;

        if (empty($nombre) || empty($cursoAulaId)) {
            Flight::json(['message' => 'El nombre del tema y el ID del curso aula son requeridos'], 400);
            return;
        }

        $descripcion = $requestData->Descripcion ?? '';

        if ($this->temaModel->create($nombre, $cursoAulaId)) {
            Flight::json(['message' => 'Tema creado exitosamente'], 201);
        } else {
            Flight::json(['message' => 'Error al crear el tema'], 500);
        }
    }

    public function update($id)
    {
        $requestData = Flight::request()->data;

        $nombre = $requestData->Nombre;
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

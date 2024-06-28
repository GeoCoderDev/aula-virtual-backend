<?php

use Config\S3Manager;


require_once __DIR__ . '/../models/RecursoTema.php';
require_once __DIR__ . '/../models/Archivo.php';
require_once __DIR__ . '/../config/S3Manager.php';
require_once __DIR__ . '/../lib/helpers/functions/generateTopicFileKeyS3.php';
require_once __DIR__ . '/../lib/helpers/functions/generateResourceDescriptionImageKeyS3.php';

class RecursoTemaController
{
    private $recursoTemaModel;
    private $archivoModel;

    public function __construct()
    {
        $this->recursoTemaModel = new RecursoTema();
        $this->archivoModel = new Archivo();
    }

    public function getByTopicId($id)
    {
        $recursos = $this->recursoTemaModel->getByTopicId($id);
        if (!empty($recursos)) {
            Flight::json($recursos, 200);
        } else {
            Flight::json(['message' => 'No se encontraron recursos para el tema especificado'], 404);
        }
    }

    public function getById($id)
    {
        $recurso = $this->recursoTemaModel->getById($id);
        if (!empty($recurso)) {
            Flight::json($recurso, 200);
        } else {
            Flight::json(['message' => 'Recurso no encontrado'], 404);
        }
    }

    public function create()
    {
        $requestData = Flight::request()->data;

        $idTema = $requestData->Id_Tema;
        $titulo = $requestData->Titulo;
        $descripcion = $requestData->Descripcion_Recurso;
        $imagenKeyS3 = $requestData->Imagen_Key_S3 ?? null;
        $tipo = $requestData->Tipo;

        if (empty($idTema) || empty($titulo) || empty($descripcion) || !isset($tipo)) {
            Flight::json(['message' => 'Todos los campos son requeridos'], 400);
            return;
        }

        if ($this->recursoTemaModel->existsWithTitleAndType($idTema, $titulo, $tipo)) {
            Flight::json(['message' => 'Ya existe un recurso con el mismo título y tipo en el tema especificado'], 409);
            return;
        }

        if ($this->recursoTemaModel->create($idTema, $titulo, $descripcion, $imagenKeyS3, $tipo)) {
            Flight::json(['message' => 'Recurso creado exitosamente'], 201);
        } else {
            Flight::json(['message' => 'Error al crear el recurso'], 500);
        }
    }

    public function addFileToTopic($topicId, $data)
    {
        if (!areFieldsComplete($data, ['Titulo', 'Grado', 'Seccion', 'Nombre_Curso', 'Nombre_Archivo'])) {
            Flight::json(['message' => 'Todos los campos son requeridos'], 400);
            return;
        }

        if (!isset($_FILES['Archivo']) || $_FILES['Archivo']['error'] !== UPLOAD_ERR_OK) {
            Flight::json(['message' => 'No se recibió ningún archivo'], 400);
            return;
        }

        $archivo = $_FILES['Archivo'];
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);

        $titulo = $data['Titulo'];
        $descripcionRecurso = $data['Descripcion_Recurso'] ?? null;
        $grado = $data['Grado'];
        $seccion = $data['Seccion'];
        $nombreCurso = $data['Nombre_Curso'];
        $tipo = 0;
        $nombreArchivo = $data['Nombre_Archivo'];

        if ($this->recursoTemaModel->existsWithTitleAndType($topicId, $titulo, $tipo)) {
            Flight::json(['message' => 'Ya existe un archivo con el mismo título en el tema especificado'], 409);
            return;
        }

        if ($this->archivoModel->existsInTopic($topicId, $nombreArchivo, $extension)) {
            Flight::json(['message' => 'Ya existe un archivo con el mismo nombre y extensión en el tema especificado'], 409);
            return;
        }

        $archivoKeyS3 = generateTopicFileKeyS3($grado, $seccion, $nombreCurso, $topicId, $nombreArchivo, $extension);

        $this->recursoTemaModel->beginTransaction();
        $s3Manager = new S3Manager();

        $tempFilePath = $archivo['tmp_name'];
        $uploadResult = $s3Manager->uploadFile($tempFilePath, $archivoKeyS3);

        if (!$uploadResult) {
            $this->recursoTemaModel->rollBack();
            Flight::json(['message' => 'Error al subir el archivo a S3'], 500);
            return;
        }

        $idArchivo = $this->archivoModel->create($nombreArchivo, $extension, $archivoKeyS3);

        if (!$idArchivo) {
            $this->recursoTemaModel->rollBack();
            Flight::json(['message' => 'Error al crear el archivo en la base de datos'], 500);
            return;
        }

        if (!$this->recursoTemaModel->addFileToTopic($topicId, $titulo, $descripcionRecurso, null, $tipo, $idArchivo)) {
            $this->recursoTemaModel->rollBack();
            Flight::json(['message' => 'Error al asociar el archivo con el recurso'], 500);
            return;
        }

        $this->recursoTemaModel->commit();
        Flight::json(['message' => 'Archivo añadido al Tema exitosamente'], 201);
    }

    public function addHomeworkToTopic($topicId, $data)
    {

        if (!areFieldsComplete($data, ['Titulo', 'Grado', 'Seccion', 'Nombre_Curso', 'Fecha_hora_apertura', 'Fecha_hora_limite', 'Puntaje_Max'])) return;

        $this->recursoTemaModel->beginTransaction();

        $s3Manager = new S3Manager();

        $tipo = 2; // Tipo 2 para tareas
        $titulo = $data['Titulo'];
        $descripcionRecurso = $data['Descripcion_Recurso'] ?? null;

        if ($this->recursoTemaModel->existsWithTitleAndType($topicId, $titulo, $tipo)) {
            Flight::json(['message' => 'Ya existe una tarea con el mismo título en el tema especificado'], 409);
            $this->recursoTemaModel->rollBack();
            return;
        }

        $imagenDescripcionKeyS3 = null;
        if (isset($_FILES['Imagen_Descripcion']) && $_FILES['Imagen_Descripcion']['error'] === UPLOAD_ERR_OK) {
            $imagenDescripcion = $_FILES['Imagen_Descripcion'];
            $extensionImagenDescripcion = pathinfo($imagenDescripcion['name'], PATHINFO_EXTENSION);
            $nombreImagenDescripcion = $data['Imagen_Descripcion_Nombre'];

            if (!$nombreImagenDescripcion) {
                Flight::json(["message" => "Falta el campo: Imagen_Descripcion_Nombre"], 400);
                return;
            };
            

            $imagenDescripcionKeyS3 = generateResourceDescriptionImageKeyS3(
                $data['Grado'],
                $data['Seccion'],
                $data['Nombre_Curso'],
                $data['Id_Tema'],
                $nombreImagenDescripcion,
                $extensionImagenDescripcion
            );
            
            $tempImagenDescripcionPath = $imagenDescripcion['tmp_name'];
            $uploadImagenDescripcionResult = $s3Manager->uploadFile($tempImagenDescripcionPath, $imagenDescripcionKeyS3);

            if (!$uploadImagenDescripcionResult) {
                $this->recursoTemaModel->rollBack();
                Flight::json(['message' => 'Error al subir la imagen de descripción a S3'], 500);
            }

        }


        $recursoTemaId = $this->recursoTemaModel->create($topicId, $titulo, $descripcionRecurso, $imagenDescripcionKeyS3, $tipo);

        if (!$recursoTemaId) {
            Flight::json(['message' => 'Error al crear el recurso de tarea en la base de datos'], 500);
            $this->recursoTemaModel->rollBack();
            return;
        }
       

        $this->recursoTemaModel->commit();
        Flight::json(['message' => 'Tarea añadida al Tema exitosamente'], 201);


    }


    public function update($id)
    {
        $requestData = Flight::request()->data;

        $titulo = $requestData->Titulo;
        $descripcion = $requestData->Descripcion_Recurso;
        $imagenKeyS3 = $requestData->Imagen_Key_S3 ?? null;
        $tipo = $requestData->Tipo;

        if (empty($titulo) || empty($descripcion) || !isset($tipo)) {
            Flight::json(['message' => 'Todos los campos son requeridos'], 400);
            return;
        }

        if ($this->recursoTemaModel->update($id, $titulo, $descripcion, $imagenKeyS3, $tipo)) {
            Flight::json(['message' => 'Recurso actualizado exitosamente'], 200);
        } else {
            Flight::json(['message' => 'Error al actualizar el recurso'], 500);
        }
    }

    public function delete($id)
    {
        if ($this->recursoTemaModel->delete($id)) {
            Flight::json(['message' => 'Recurso eliminado exitosamente'], 200);
        } else {
            Flight::json(['message' => 'Error al eliminar el recurso'], 500);
        }
    }

    public function getResourcesByTopicId($id)
    {
        $recursos = $this->recursoTemaModel->getByTopicId($id);

        if ($recursos === false) {
            Flight::json(['message' => 'Aun no hay recursos para este tema'], 404);
        } else {
            Flight::json($recursos);
        }
    }
}

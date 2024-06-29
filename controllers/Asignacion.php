<?php
require_once __DIR__ . '/../models/Asignacion.php';
require_once __DIR__ . '/../models/Aula.php';

class AsignacionController
{
    private $asignacionesModel;
    private $aulaModel;

    public function __construct()
    {
        $this->asignacionesModel = new Asignacion();
        $this->aulaModel = new Aula();
    }

    public function getAsignationsByAula($Grado, $Seccion)
    {
        try {
            $aula = $this->aulaModel->getByGradoSeccion($Grado, $Seccion);
            if ($aula) {
                $Id_Aula = $aula['Id_Aula'];
                $result = $this->asignacionesModel->getAsignationsByAula($Id_Aula);
                if ($result) {
                    Flight::json($result, 200);
                } else {
                    Flight::json(['message' => 'No se encontraron asignaciones para el aula especificada.'], 404);
                }
            } else {
                Flight::json(['message' => 'No se encontró un aula con el grado y la sección especificados.'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['message' => 'Ocurrió un error al obtener las asignaciones del aula.'], 500);
        }
    }

    public function getAsignationsByTeacher($DNI_Profesor)
    {
        try {
            $result = $this->asignacionesModel->getAsignationsByTeacher($DNI_Profesor);
            if ($result) {
                Flight::json($result, 200);
            } else {
                Flight::json(['message' => 'No se encontraron asignaciones para el profesor especificado.'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['message' => 'Ocurrió un error al obtener las asignaciones del profesor.'], 500);
        }
    }
}

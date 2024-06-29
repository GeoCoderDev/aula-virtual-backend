<?php
require_once __DIR__ . '/../models/Asignacion.php';
require_once __DIR__ . '/../models/Aula.php';
require_once __DIR__ . '/../models/HoraAcademica.php';

class AsignacionController
{
    private $asignacionesModel;
    private $aulaModel;
    private $horaAcademicaModel;

    public function __construct()
    {
        $this->asignacionesModel = new Asignacion();
        $this->aulaModel = new Aula();
        $this->horaAcademicaModel = new HoraAcademica();
    }

    public function getAsignationsByAula($Grado, $Seccion)
    {
        try {
            if (!$Grado || !$Seccion) {
                Flight::json([], 200);
                return;
            }

            $aula = $this->aulaModel->getByGradoSeccion($Grado, $Seccion);
            if ($aula) {
                $Id_Aula = $aula['Id_Aula'];
                $asignaciones = $this->asignacionesModel->getAsignationsByAula($Id_Aula) ?? [];
                $horasAcademicas = $this->horaAcademicaModel->getAll();
                $response = [
                    'Asignaciones' => $asignaciones,
                    'Horas_Academicas' => $horasAcademicas
                ];
                Flight::json($response, 200);
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
            $asignaciones = $this->asignacionesModel->getAsignationsByTeacher($DNI_Profesor) ?? [];
            $horasAcademicas = $this->horaAcademicaModel->getAll();
            $response = [
                'Asignaciones' => $asignaciones,
                'Horas_Academicas' => $horasAcademicas
            ];
            Flight::json($response, 200);
        } catch (Exception $e) {
            Flight::json(['message' => 'Ocurrió un error al obtener las asignaciones del profesor.'], 500);
        }
    }
}

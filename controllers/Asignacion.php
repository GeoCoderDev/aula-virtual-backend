<?php

require_once __DIR__ . '/../models/Asignacion.php';
require_once __DIR__ . '/../models/Aula.php';
require_once __DIR__ . '/../models/HoraAcademica.php';
require_once __DIR__ . '/../models/HorarioCursoAula.php';

class AsignacionController
{
    private $asignacionesModel;
    private $aulaModel;
    private $horaAcademicaModel;
    private $horarioCursoAulaModel;

    public function __construct()
    {
        $this->asignacionesModel = new Asignacion();
        $this->aulaModel = new Aula();
        $this->horaAcademicaModel = new HoraAcademica();
        $this->horarioCursoAulaModel = new HorarioCursoAula();
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

    public function createAsignacion($data)
    {
        try {
            // Verificar si todos los campos requeridos están presentes en $data
            if (!areFieldsComplete($data, ['DNI_Profesor', 'Id_Curso_Aula', 'Dia_Semana', 'Id_Hora_Academica_Inicio', 'Cant_Horas_Academicas'])) return;

            $DNI_Profesor = $data['DNI_Profesor'];
            $Id_Curso_Aula = $data['Id_Curso_Aula'];
            $Dia_Semana = $data['Dia_Semana'];
            $Id_Hora_Academica_Inicio = $data['Id_Hora_Academica_Inicio'];
            $Cant_Horas_Academicas = $data['Cant_Horas_Academicas'];

            // Verificar disponibilidad
            $isAvailable = $this->asignacionesModel->checkAvailability(
                $DNI_Profesor,
                $Id_Curso_Aula,
                $Dia_Semana,
                $Id_Hora_Academica_Inicio,
                $Cant_Horas_Academicas
            );

            if (!$isAvailable) {
                Flight::json(['message' => 'Conflicto de horario'], 409);
                return;
            }

            // Iniciar la transacción
            $this->asignacionesModel->beginTransaction();

            // Crear el horario del curso aula
            $idHorarioCursoAula = $this->horarioCursoAulaModel->create($Dia_Semana, $Id_Hora_Academica_Inicio, $Cant_Horas_Academicas, $Id_Curso_Aula);

            if (!$idHorarioCursoAula) {
                $this->asignacionesModel->rollBack();
                Flight::json(['message' => 'Error al crear el horario del curso aula'], 500);
                return;
            }

            // Crear la asignación con el ID del horario del curso aula
            $asignationID = $this->asignacionesModel->create($DNI_Profesor, $idHorarioCursoAula);

            if ($asignationID) {
                // Confirmar la transacción
                $this->asignacionesModel->commit();
                Flight::json(['message' => 'Asignación creada exitosamente', "Id" => $asignationID, "Id2" => $idHorarioCursoAula], 201);
            } else {
                // Revertir la transacción
                $this->asignacionesModel->rollBack();
                Flight::json(['message' => 'Error al crear la asignación'], 500);
            }
        } catch (Exception $e) {
            // Revertir la transacción en caso de excepción
            $this->asignacionesModel->rollBack();
            Flight::json(['message' => 'Ocurrió un error al crear la asignación.'], 500);
        }
    }
}

<?php
require_once __DIR__ . '/../models/Aula.php';
require_once __DIR__ . '/../models/HoraAcademica.php';

class AulaController
{
    private $aulaModel;
    private $horaAcademicaModel;

    public function __construct()
    {
        $this->aulaModel = new Aula();
        $this->horaAcademicaModel = new HoraAcademica();
    }

    public function getAll($grado, $section)
    {
        $aulas = $this->aulaModel->getAll();
        return $aulas;
    }


    public function getAllSectionsByGrades()
    {
        $sectionsByGrade = $this->aulaModel->getAllSectionsByGrades();
        Flight::json($sectionsByGrade, 200);
    }

    public function getSectionsByGrade($Grado)
    {
        $sections = $this->aulaModel->getSectionsByGrade($Grado);
        Flight::json($sections, 200);
    }

    public function getById($Id_Aula)
    {
        $aula = $this->aulaModel->getById($Id_Aula);
        return $aula;
    }

    public function getByGradoSeccion($Grado, $Seccion)
    {
        $aula = $this->aulaModel->getByGradoSeccion($Grado, $Seccion);
        return $aula;
    }

    public function getCursosAulaByGradoSeccion($grado, $seccion)
    {

        if (!$grado || !$seccion) {
            Flight::json(['message' => 'No se recibieron los parametros de grado y seccion']);
            return;
        }

        $aula = $this->getByGradoSeccion($grado, $seccion);

        if (!$aula) {
            Flight::json(['message' => 'No se encontró la aula'], 404);
            return;
        }

        $Id_Aula = $aula["Id_Aula"];

        $cursos = $this->aulaModel->getCursosAulaById($Id_Aula);

        $horasAcademicas = $this->horaAcademicaModel->getAll();

        Flight::json(["Cursos_Aula" => $cursos, "Id_Aula" => $Id_Aula, "Horas_Academicas" => $horasAcademicas], 200);
    }

    public function agreeSection($Grado)
    {
        // Obtener la última sección agregada para el grado especificado
        $lastSection = $this->aulaModel->getLastSection($Grado);

        // Generar la próxima sección
        $newSection = $this->generateNextSection($lastSection);

        // Agregar la nueva sección
        $this->aulaModel->addSection($Grado, $newSection);

        Flight::json(["message" => "Sección $newSection agregada para el grado $Grado."], 201);
    }


    public function deleteLastSection($Grado)
    {
        // Obtener la última sección agregada para el grado especificado
        $lastSection = $this->aulaModel->getLastSection($Grado);

        // Verificar si hay estudiantes relacionados con la última sección
        $studentsCount = $this->aulaModel->getStudentsCountByGradoSeccion($Grado, $lastSection);

        // Si no hay estudiantes relacionados, eliminar los cursos asociados y la última sección
        if ($studentsCount == 0) {
            $this->aulaModel->deleteCursosByGradoSeccion($Grado, $lastSection);
            $this->aulaModel->deleteSection($Grado, $lastSection);

            Flight::json(["message" => "Sección $lastSection eliminada para el grado $Grado."], 200);
        } else {
            Flight::json(["message" => "No se puede eliminar la última sección, hay estudiantes relacionados con ella."], 400);
        }
    }



    // Método para generar la próxima sección (A, B, C, ...)
    private function generateNextSection($lastSection)
    {
        // Convertir la última sección a su valor ASCII y aumentar en uno
        return chr(ord($lastSection) + 1);
    }
}

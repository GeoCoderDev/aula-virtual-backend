<?php
require_once __DIR__ . '/../models/Aula.php';

class AulaController
{
    private $aulaModel;

    public function __construct()
    {
        $this->aulaModel = new Aula();
    }

    public function getAll()
    {
        $aulas = $this->aulaModel->getAll();
        return $aulas;
    }

    public function getSectionsByGrade($Grado)
    {
        $sections = $this->aulaModel->getSectionsByGrade($Grado);
        Flight::json($sections,200);

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

    public function agreeSection($Grado)
    {
        // Obtener la última sección agregada para el grado especificado
        $lastSection = $this->aulaModel->getLastSection($Grado);

        // Generar la próxima sección
        $newSection = $this->generateNextSection($lastSection);

        // Agregar la nueva sección
        $this->aulaModel->addSection($Grado, $newSection);
        
        Flight::json(["message"=>"Sección $newSection agregada para el grado $Grado."],201);
    }


    public function deleteLastSection($Grado)
    {
        // Obtener la última sección agregada para el grado especificado
        $lastSection = $this->aulaModel->getLastSection($Grado);

        // Verificar si hay estudiantes relacionados con la última sección
        $studentsCount = $this->aulaModel->getStudentsCountByGradoSeccion($Grado, $lastSection);

        // Si no hay estudiantes relacionados, eliminar la última sección
        if ($studentsCount == 0) {
            $this->aulaModel->deleteLastSection($Grado);

            Flight::json(["message" => "Sección $lastSection eliminada para el grado $Grado."], 200);
        } else {
            Flight::json(["message"=>"No se puede eliminar la última sección, hay estudiantes relacionados con ella."], 400);
        }
    }


    // Método para generar la próxima sección (A, B, C, ...)
    private function generateNextSection($lastSection)
    {
        // Convertir la última sección a su valor ASCII y aumentar en uno
        return chr(ord($lastSection) + 1);
    }

}
?>

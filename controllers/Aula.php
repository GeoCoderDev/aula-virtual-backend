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
        return $sections;
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

    public function create($Grado, $Seccion)
    {
        $lastInsertId = $this->aulaModel->create($Grado, $Seccion);
        return $lastInsertId;
    }

    public function update($Id_Aula, $Grado, $Seccion)
    {
        $rowCount = $this->aulaModel->update($Id_Aula, $Grado, $Seccion);
        return $rowCount;
    }

    public function delete($Id_Aula)
    {
        $rowCount = $this->aulaModel->delete($Id_Aula);
        return $rowCount;
    }
}
?>

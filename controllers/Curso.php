<?php

require_once __DIR__ . '/../Models/Curso.php';
require_once __DIR__ . '/../Models/Curso_Aula.php';

class CursoController {
    public function createCourse($data) {
        $cursoModel = new Curso();
        $nombreCurso = $data['nombre'];

        $existingCourse = $cursoModel->getByNombre($nombreCurso);
        if ($existingCourse) {
            return ["message" => "El curso '$nombreCurso' ya existe."];
        }

        $cursoId = $cursoModel->create($nombreCurso);
        return ["message" => "Curso creado exitosamente", "id" => $cursoId];
    }

    public function getAllCourses() {
        $cursoModel = new Curso();
        $courses = $cursoModel->getAll();
        return $courses;
    }

    public function updateCourse($id, $data) {
        $cursoModel = new Curso();
        $nombreCurso = $data['nombre'];

        $existingCourse = $cursoModel->getById($id);
        if (!$existingCourse) {
            return ["message" => "El curso con ID '$id' no existe."];
        }

        $rowsAffected = $cursoModel->update($id, $nombreCurso);
        return $rowsAffected > 0
            ? ["message" => "Curso actualizado exitosamente"]
            : ["message" => "Error al actualizar el curso"];
    }

    public function deleteCourse($id) {
        $cursoModel = new Curso();

        $existingCourse = $cursoModel->getById($id);
        if (!$existingCourse) {
            return ["message" => "El curso con ID '$id' no existe."];
        }

        $rowsAffected = $cursoModel->delete($id);
        return $rowsAffected > 0
            ? ["message" => "Curso eliminado exitosamente"]
            : ["message" => "Error al eliminar el curso"];
    }
}
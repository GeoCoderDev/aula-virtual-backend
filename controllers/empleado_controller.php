<?php

require __DIR__ . '../../Models/Empleado.php';

class EmpleadoController {

    public function getById($id) {
        $empleadoModel = new Empleado();
        $empleado = $empleadoModel->getById($id);
        return json_encode($empleado);
    }

    public function getAll() {
        $empleadoModel = new Empleado();
        $empleados = $empleadoModel->getAll();
        return json_encode($empleados);
    }

    public function create($data) {
        $nombre = $data['nombre'] ?? null;
        $sueldo = $data['sueldo'] ?? null;
        
        if (!$nombre || !$sueldo) {
            return json_encode(["message" => "Nombre y sueldo son obligatorios"]);
        }

        $empleadoModel = new Empleado();
        $empleadoId = $empleadoModel->create($nombre, $sueldo);
        
        return json_encode(["message" => "Empleado creado con ID: $empleadoId"]);
    }

    public function update($id, $data) {
        $nombre = $data['nombre'] ?? null;
        $sueldo = $data['sueldo'] ?? null;

        if (!$nombre && !$sueldo) {
            return json_encode(["message" => "Nombre o sueldo deben ser proporcionados para actualizar"]);
        }

        $empleadoModel = new Empleado();
        $rowCount = $empleadoModel->update($id, $nombre, $sueldo);
        
        if ($rowCount > 0) {
            return json_encode(["message" => "Empleado actualizado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún empleado con el ID proporcionado"]);
        }
    }

    public function delete($id) {
        $empleadoModel = new Empleado();
        $rowCount = $empleadoModel->delete($id);
        
        if ($rowCount > 0) {
            return json_encode(["message" => "Empleado eliminado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún empleado con el ID proporcionado"]);
        }
    }
}
?>

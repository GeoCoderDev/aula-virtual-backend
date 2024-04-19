<?php

require __DIR__ . '../../Models/Empleado.php';

class EmpleadoController {
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                // Obtener un solo empleado o todos los empleados
                $id = $_GET['id'] ?? null;
                if ($id !== null) {
                    echo $this->getById($id);
                } else {
                    echo $this->getAll();
                }
                break;
            case 'POST':
                // Crear un nuevo empleado
                $data = json_decode(file_get_contents('php://input'), true);
                echo $this->create($data);
                break;
            case 'PUT':
                // Actualizar un empleado
                parse_str(file_get_contents('php://input'), $putData);
                $id = $_GET['id'] ?? null;
                echo $this->update($id, $putData);
                break;
            case 'DELETE':
                // Eliminar un empleado
                $id = $_GET['id'] ?? null;
                echo $this->delete($id);
                echo "Eliminar";
                break;
            default:
                http_response_code(405); // Método no permitido
                echo json_encode(["message" => "Método no permitido"]);
                break;
        }
    }

    public function getById($id) {
        $empleadoModel = new Empleado();
        $empleado = $empleadoModel->getById($id);
        return json_encode($empleado);
    }

    public function getAll() {
        // Lógica para obtener todos los empleados
        $empleadoModel = new Empleado();
        $empleados = $empleadoModel->getAll(); // Suponiendo que tienes un método getAll() en tu clase Empleado
        return json_encode($empleados);
    }

    public function create($data) {
        $nombre = $data['nombre'] ?? null;
        $sueldo = $data['sueldo'] ?? null;
        
        if (!$nombre || !$sueldo) {
            // Devolver un error si faltan datos
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
            // Devolver un error si no hay datos para actualizar
            return json_encode(["message" => "Nombre o sueldo deben ser proporcionados para actualizar"]);
        }

        $empleadoModel = new Empleado();
        $rowCount = $empleadoModel->update($id, $nombre, $sueldo);
        
        // Verificar si se actualizó correctamente
        if ($rowCount > 0) {
            return json_encode(["message" => "Empleado actualizado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún empleado con el ID proporcionado"]);
        }
    }

    public function delete($id) {
        $empleadoModel = new Empleado();
        $rowCount = $empleadoModel->delete($id);
        
        // Verificar si se eliminó correctamente
        if ($rowCount > 0) {
            return json_encode(["message" => "Empleado eliminado"]);
        } else {
            return json_encode(["message" => "No se encontró ningún empleado con el ID proporcionado"]);
        }
    }
}
?>

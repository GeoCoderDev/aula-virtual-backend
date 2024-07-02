<?php

require_once __DIR__ . '/../models/Configuracion.php';

class ConfiguracionController
{

    public function getAll($limit = 200, $startFrom = 0)
    {
        $configuracionModel = new Configuracion();
        $configuraciones = $configuracionModel->getAll($limit, $startFrom);
        return $configuraciones;
    }

    public function getByName($nombreConf)
    {
        $configuracionModel = new Configuracion();
        $configuracion = $configuracionModel->getByName($nombreConf);
        if (!$configuracion) {
            Flight::json(["message" => "No existe la configuración con nombre $nombreConf"], 404);
        } else {
            Flight::json($configuracion, 200);
        }
    }

    public function getValueByName($nombreConf)
    {
        $configuracionModel = new Configuracion();
        $configuracion = $configuracionModel->getValueByName($nombreConf);
        if (!$configuracion) {
            Flight::json(["message" => "No existe la configuración con nombre $nombreConf"], 404);
        } else {
            Flight::json($configuracion, 200);
        }
    }





    public function create($data)
    {
        $nombreConf = $data['Nombre_Conf'] ?? null;
        $valor = $data['Valor'] ?? null;
        $descripcion = $data['Descripcion'] ?? null;

        if (!$nombreConf || !$valor) {
            return Flight::json(["message" => "Nombre de configuración y valor son obligatorios"], 400);
        }

        $configuracionModel = new Configuracion();
        $existingConfiguracion = $configuracionModel->getByName($nombreConf);

        if ($existingConfiguracion) {
            return Flight::json(['message' => 'Ya existe una configuración con ese nombre'], 409);
        }

        $configuracionId = $configuracionModel->create($nombreConf, $valor, $descripcion);
        if ($configuracionId) {
            return Flight::json(["message" => "Configuración creada", "id" => $configuracionId], 201);
        } else {
            return Flight::json(["message" => "Error al crear la configuración"], 500);
        }
    }

    public function update($nombreConf, $data)
    {
        $valor = $data['Valor'] ?? null;
        $descripcion = $data['Descripcion'] ?? null;

        if (!$valor) {
            return Flight::json(["message" => "El valor es obligatorio"], 400);
        }

        $configuracionModel = new Configuracion();
        $updateSuccess = $configuracionModel->update($nombreConf, $valor, $descripcion);

        if ($updateSuccess) {
            return Flight::json(["message" => "Configuración actualizada"], 200);
        } else {
            return Flight::json(["message" => "No se encontró ninguna configuración con el nombre proporcionado"], 404);
        }
    }

    public function delete($nombreConf)
    {
        $configuracionModel = new Configuracion();
        $rowCount = $configuracionModel->delete($nombreConf);

        if ($rowCount > 0) {
            return Flight::json(["message" => "Configuración eliminada correctamente"], 200);
        } else {
            return Flight::json(["message" => "No se encontró ninguna configuración con el nombre proporcionado"], 404);
        }
    }
}

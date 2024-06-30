<?php
require_once __DIR__ . '/../config/Database.php';

use Config\Database;

class HoraAcademica
{
    private $conn;
    private $interval = 45; // Intervalo de 45 minutos

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM T_Horas_Academicas ORDER BY Id_Hora_Academica");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addNextHour()
    {
        $lastHour = $this->getLastHour();
        if (!$lastHour) {
            return false; // No hay horas académicas para agregar la siguiente
        }

        $lastHourTime = new DateTime($lastHour['Valor']);
        $nextHourTime = clone $lastHourTime;
        $nextHourTime->modify("+{$this->interval} minutes");

        // Verificar si la próxima hora sigue dentro del mismo día
        if ($nextHourTime->format('H:i') <= $lastHourTime->format('H:i')) {
            return false; // No agregar si la próxima hora excede el rango de un día
        }

        $nextHourId = $lastHour['Id_Hora_Academica'] + 1;
        $stmt = $this->conn->prepare("INSERT INTO T_Horas_Academicas (Id_Hora_Academica, Valor) VALUES (:Id_Hora_Academica, :Valor)");
        return $stmt->execute([
            'Id_Hora_Academica' => $nextHourId,
            'Valor' => $nextHourTime->format('H:i')
        ]);
    }

    public function resetHours($horaInicio, $horaFin)
    {
        $this->conn->beginTransaction();

        try {
            // Borrar todas las horas académicas
            $this->conn->exec("DELETE FROM T_Horas_Academicas");

            // Crear nuevas horas académicas en el rango especificado
            $currentHourTime = new DateTime($horaInicio);
            $endHourTime = new DateTime($horaFin);
            $idHoraAcademica = 1;

            while ($currentHourTime < $endHourTime) {
                $stmt = $this->conn->prepare("INSERT INTO T_Horas_Academicas (Id_Hora_Academica, Valor) VALUES (:Id_Hora_Academica, :Valor)");
                $stmt->execute([
                    'Id_Hora_Academica' => $idHoraAcademica,
                    'Valor' => $currentHourTime->format('H:i')
                ]);
                $currentHourTime->modify("+{$this->interval} minutes");
                $idHoraAcademica++;
            }

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e; // Relanzar la excepción
        }
    }

    private function getLastHour()
    {
        $stmt = $this->conn->query("SELECT * FROM T_Horas_Academicas ORDER BY Id_Hora_Academica DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByRange($start, $end)
    {

        $startDef = $start - 3 > 0 ? $start - 3 : 0;

        $endDef = $end - $startDef >= 7 ? $end : $end + (7 - ($end - $startDef));

        $stmt = $this->conn->prepare("
            SELECT * FROM T_Horas_Academicas
            WHERE Id_Hora_Academica BETWEEN :start AND :end
            ORDER BY Id_Hora_Academica
        ");
        $stmt->execute(['start' => $startDef, 'end' => $endDef]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php
class Dashboard
{
    /**
     * Obtiene las métricas financieras clave del negocio.
     */
    public static function getMetricas(PDO $pdo): array
    {
        try {
            //total de ventas completadas
            $stmtVentas = $pdo->query("SELECT SUM(total) as total FROM ventas WHERE status = 'completada'");
            $totalVentas = $stmtVentas->fetch()['total'] ?? 0.00;

            //total de gastos activos
            $stmtGastos = $pdo->query("SELECT SUM(monto) as total FROM gastos WHERE deleted_at IS NULL");
            $totalGastos = $stmtGastos->fetch()['total'] ?? 0.00;

            //balance
            $balance = $totalVentas - $totalGastos;

            return [
                'ingresos' => (float) $totalVentas,
                'egresos' => (float) $totalGastos,
                'balance' => (float) $balance
            ];
        } catch (\PDOException $e) {
            error_log("Error en Dashboard::getMetricas -> " . $e->getMessage());
            return ['ingresos' => 0, 'egresos' => 0, 'balance' => 0];
        }
    }

    // función dedicada a consultar las métricas de un rango específico de fechas
    public static function getMetricasPorFecha(PDO $pdo, string $fechaInicio, string $fechaFin): array
    {

        $tzLocal = new DateTimeZone('America/Merida');
        $tzUTC = new DateTimeZone('UTC');

        // 1. Convertir '2026-08-26 00:00:00' local a su hora equivalente en UTC ('2026-08-26 06:00:00 UTC')
        $dtInicio = new DateTime($fechaInicio . ' 00:00:00', $tzLocal);
        $dtInicio->setTimezone($tzUTC);
        $inicioUTC = $dtInicio->format('Y-m-d H:i:s');

        // 2. Convertir '2026-08-26 23:59:59' local a su hora equivalente en UTC ('2026-08-27 05:59:59 UTC')
        $dtFin = new DateTime($fechaFin . ' 23:59:59', $tzLocal);
        $dtFin->setTimezone($tzUTC);
        $finUTC = $dtFin->format('Y-m-d H:i:s');

        $sqlIngresos = "SELECT COALESCE(SUM(total), 0) 
                        FROM ventas 
                        WHERE fecha BETWEEN :inicio_utc AND :fin_utc AND status = 'completada'";
        $stmt = $pdo->prepare($sqlIngresos);
        $stmt->execute([
            ':inicio_utc' => $inicioUTC,
            ':fin_utc' => $finUTC
        ]);
        $ingresos = (float) $stmt->fetchColumn();

        $sqlEgresos = "SELECT COALESCE(SUM(monto), 0) 
                       FROM gastos 
                       WHERE fecha BETWEEN :inicio_utc AND :fin_utc AND deleted_at IS NULL";
        $stmt = $pdo->prepare($sqlEgresos);
        $stmt->execute([
            ':inicio_utc' => $inicioUTC,
            ':fin_utc' => $finUTC
        ]);
        $egresos = (float) $stmt->fetchColumn();

        $balance = $ingresos - $egresos;

        return [
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'balance' => $balance,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
        ];
    }

    /**
     * Obtiene las últimas 5 ventas para el feed de actividad reciente.
     */
    public static function getVentasRecientes(PDO $pdo, int $limite = 5): array
    {
        try {
            $sql = "SELECT id, fecha, total, metodo_pago  
                    FROM ventas 
                    WHERE status = 'completada' 
                    ORDER BY fecha DESC LIMIT :limite";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error en Dashboard::getVentasRecientes -> " . $e->getMessage());
            return [];
        }
    }
}
<?php

class HistorialVentas {
    /**
     * Obtiene el listado general de todas las ventas realizadas.
     */
    public static function getVentas(PDO $pdo): array {
        try {
            $sql = "SELECT id, fecha, total, metodo_pago, status 
                    FROM ventas 
                    ORDER BY fecha DESC";
            return $pdo->query($sql)->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error en Historial::getVentas -> " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el detalle de artículos de una venta específica.
     */
    public static function getDetalleVenta(PDO $pdo, int $ventaId): array {
        try {
            $sql = "SELECT dv.cantidad, dv.precio_unitario, p.nombre 
                    FROM detalle_ventas dv
                    JOIN productos p ON dv.producto_id = p.id
                    WHERE dv.venta_id = :venta_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':venta_id' => $ventaId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error en Historial::getDetalleVenta -> " . $e->getMessage());
            return [];
        }
    }
}
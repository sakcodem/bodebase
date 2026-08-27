<?php
class Gastos {

    public static function registrar(PDO $pdo, string $concepto, float $monto, string $categoria, ?string $notas): bool {
        try {
            $sql = "INSERT INTO gastos (concepto, monto, categoria, notas) 
                    VALUES (:concepto, :monto, :categoria, :notas)";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':concepto'  => $concepto,
                ':monto'     => $monto,
                ':categoria' => $categoria,
                ':notas'     => $notas
            ]);
        } catch (\PDOException $e) {
            error_log("Error en Gastos::registrar -> " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los últimos 10 gastos registrados que no han sido eliminados.
     */
    public static function getRecientes(PDO $pdo, int $limite = 10): array {
        try {
            $sql = "SELECT id, fecha, concepto, monto, categoria, notas 
                    FROM gastos 
                    WHERE deleted_at IS NULL 
                    ORDER BY fecha DESC 
                    LIMIT :limite";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error en Gastos::getRecientes -> " . $e->getMessage());
            return [];
        }
    }

    

      public static function getAll(PDO $pdo): array {
        try {
            $sql = "SELECT id, fecha, concepto, monto, categoria, notas 
                    FROM gastos 
                    WHERE deleted_at IS NULL 
                    ORDER BY fecha DESC";
            return $pdo->query($sql)->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error en Gastos::getAll -> " . $e->getMessage());
            return [];
        }
    }
}
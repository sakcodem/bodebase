<?php


class Productos
{
    /**
     * Obtiene todos los productos activos y no eliminados para el punto de venta.
     * * @param PDO $pdo Instancia de la conexión a la base de datos.
     * @return array Lista de productos.
     */
    public static function getActivos(PDO $pdo): array
    {
        try {
            $sql = "SELECT id, categoria_id, nombre, descripcion, precio_venta 
                    FROM productos 
                    WHERE status = 'activo' 
                    AND deleted_at IS NULL 
                    ORDER BY nombre ASC";

            $stmt = $pdo->query($sql);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            // En un entorno real, aquí registrarías el error en un archivo log
            error_log("Error en Productos::getActivos -> " . $e->getMessage());
            return []; // Retornamos un array vacío para no romper la vista
        }
    }

    /**
     * Obtiene TODOS los productos que no han sido borrados (incluye activos e inactivos)
     * Ideal para el panel de administración.
     */
    public static function getAll(PDO $pdo): array
    {
        try {
            $sql = "SELECT p.id, p.categoria_id, p.nombre, p.descripcion, p.precio_venta, p.status,
                    c.nombre AS categoria_nombre
                    FROM productos p
                    INNER JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.deleted_at IS NULL 
                    ORDER BY p.status ASC, p.nombre ASC";
            return $pdo->query($sql)->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error en Productos::getAll -> " . $e->getMessage());
            return [];
        }
    }

    public static function create(PDO $pdo, int $categoria_id, string $nombre, ?string $descripcion, float $precio): bool
    {
        try {
            $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio_venta, status)
                   VALUES(:categoria_id, :nombre, :descripcion, :precio, 'activo')";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':categoria_id' => $categoria_id,
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio
            ]);

        } catch (PDOException $e) {
            error_log("Error en productos::create -> " . $e->getMessage());
            return false;
        }
    }

    public static function update(PDO $pdo, int $id, int $categoria_id, string $nombre, ?string $descripcion, float $precio): bool
    {
        try {
            $sql = "UPDATE productos 
                    SET categoria_id = :categoria_id, nombre = :nombre, descripcion = :descripcion, precio_venta = :precio 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':categoria_id' => $categoria_id,
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en productos::update -> " . $e->getMessage());
            return false;
        }
    }

    /**
     * Aplica Soft Delete al producto (guarda la fecha actual en deleted_at).
     */
    public static function delete(PDO $pdo, int $id): bool
    {
        try {
            $sql = "UPDATE productos SET deleted_at = NOW(), status = 'inactivo' WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            error_log("Error en Productos::delete -> " . $e->getMessage());
            return false;
        }
    }

    /**
     * Alterna el status de un producto (de activo a inactivo y viceversa).
     */
    public static function toggleStatus(PDO $pdo, int $id, string $status_actual): bool
    {
        try {
            $nuevo_status = ($status_actual === 'activo') ? 'inactivo' : 'activo';
            $sql = "UPDATE productos SET status = :status WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([':status' => $nuevo_status, ':id' => $id]);
        } catch (\PDOException $e) {
            error_log("Error en Productos::toggleStatus -> " . $e->getMessage());
            return false;
        }
    }

}
<?php

class Categorias
{
    /**
     * Obtiene todas las categorías activas y no eliminadas para el punto de venta.
     * * @param PDO $pdo Instancia de la conexión a la base de datos.
     * @return array Lista de categorías.
     */
    public static function getActivos(PDO $pdo): array
    {
        try {
            $sql = "SELECT id, nombre, descripcion, status 
                    FROM categorias 
                    WHERE status = 'activo' 
                    ORDER BY nombre ASC";
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            // En un entorno real, aquí registrarías el error en un archivo log
            //die("Error en Categorias::getActivos -> " . $e->getMessage());

            //die("Error crítico de conexión: " . $e->getMessage());
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
            $sql = "SELECT id, nombre, descripcion, status 
                    FROM categorias 
                    WHERE deleted_at IS NULL
                    ORDER BY status ASC, nombre ASC";
            return $pdo->query($sql)->fetchAll();
        } catch (\PDOException $e) {
            //die("Error en Categorias::getAll -> " . $e->getMessage());
            return [];
        }
    }

    public static function create(PDO $pdo, string $nombre, ?string $descripcion): bool
    {
        try {
            $sql = "INSERT INTO categorias (nombre, descripcion, status)
                   VALUES(:nombre, :descripcion, 'activo')";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion
            ]);

        } catch (PDOException $e) {
            //die("Error en categorias::create -> " . $e->getMessage());
            return false;
        }
    }

    public static function update(PDO $pdo, int $id, string $nombre, ?string $descripcion): bool
    {
        try {
            $sql = "UPDATE categorias 
                    SET nombre = :nombre, descripcion = :descripcion 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            //die("Error en categorias::update -> " . $e->getMessage());
            return false;
        }
    }

    /**
     * Aplica Soft Delete al producto (guarda la fecha actual en deleted_at).
     */
    public static function delete(PDO $pdo, int $id): bool
    {
        try {
            if (!self::verificarDelete($pdo, $id)) {
                //die("Error en Categorias::delete -> La categoría tiene productos asociados y no puede ser eliminada.");
                return false;
            }

            $sql = "UPDATE categorias SET deleted_at = NOW(), status = 'inactivo' WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            //die("Error en Categorias::delete -> " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si una categoría puede ser eliminada (no tiene productos asociados).
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     * @param int $id ID de la categoría a verificar.
     * @return bool Retorna true si puede eliminarse, false si tiene productos asociados.
     */
    public static function verificarDelete(PDO $pdo, int $id): bool
    {
        try {
            // Verificar si hay productos asociados a la categoría
            $sql = "SELECT COUNT(*) FROM productos WHERE categoria_id = :id AND deleted_at IS NULL";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $count = $stmt->fetchColumn();

            return $count == 0; // Retorna true si no hay productos asociados
        } catch (\PDOException $e) {
            //die("Error en Categorias::verificarDelete -> " . $e->getMessage());
            return false; // En caso de error, asumimos que no se puede eliminar
        }
    }

    /**
     * Alterna el status de una categoria (de activo a inactivo y viceversa).
     */
    public static function toggleStatus(PDO $pdo, int $id, string $status_actual): bool
    {
        try {
            $nuevo_status = ($status_actual === 'activo') ? 'inactivo' : 'activo';
            $sql = "UPDATE categorias SET status = :status WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([':status' => $nuevo_status, ':id' => $id]);
        } catch (\PDOException $e) {
            //die("Error en Categorias::toggleStatus -> " . $e->getMessage());
            return false;
        }
    }

}
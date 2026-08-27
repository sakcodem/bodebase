<?php

class Ventas
{
    /**
     * Registra una venta y sus detalles en la base de datos.
     *
     * @param PDO $pdo Instancia de la conexión a la base de datos.
     * @param float $total_venta Total de la venta.
     * @param string $metodo_pago Método de pago utilizado.
     * @param string $status Estado de la venta (por ejemplo, 'completada').
     * @param array $carrito Arreglo con los productos vendidos y sus cantidades.
     * @return bool Retorna true si la venta se registró correctamente, false en caso contrario.
     */
    public static function registrar(PDO $pdo, float $total_venta, string $metodo_pago, string $status, array $carrito): bool
    {
        try {
            $pdo->beginTransaction();

            //  Insertar en la tabla maestra 'ventas'
            $sqlVenta = "INSERT INTO ventas (total, metodo_pago, status) VALUES (:total, :metodo_pago, :status)";
            $stmtVenta = $pdo->prepare($sqlVenta);
            $stmtVenta->execute([
                ':total' => $total_venta,
                ':metodo_pago' => $metodo_pago,
                ':status' => $status
            ]);

            $ventaId = $pdo->lastInsertId();

            // Preparar el statement para los detalles (Reutilizable en el bucle)
            $sqlDetalle = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario) 
                   VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario)";
            $stmtDetalle = $pdo->prepare($sqlDetalle);

            //  Iterar el carrito e insertar cada producto vendido
            foreach ($carrito as $item) {

                $productoId = filter_var($item['id'], FILTER_VALIDATE_INT);
                $cantidad = filter_var($item['qty'], FILTER_VALIDATE_INT);
                $precioUnitario = filter_var($item['precio'], FILTER_VALIDATE_FLOAT);

                if (!$productoId || !$cantidad || !$precioUnitario || $cantidad <= 0) {
                    throw new Exception("Datos de ítem inválidos en el procesamiento del carrito.");
                }

                // Ejecutar inserción del detalle vinculando el $ventaId
                $stmtDetalle->execute([
                    ':venta_id' => $ventaId,
                    ':producto_id' => $productoId,
                    ':cantidad' => $cantidad,
                    ':precio_unitario' => $precioUnitario
                ]);
            }

            $pdo->commit();
            return true;


        } catch (\PDOException $e) {
            error_log("Error en Ventas::registrar -> " . $e->getMessage());
            $pdo->rollBack();
            return false;
        }
    }
}
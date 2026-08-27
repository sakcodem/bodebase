
SET FOREIGN_KEY_CHECKS = 0;

-- 1. TABLA: USUARIOS
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABLA: PRODUCTOS
CREATE TABLE IF NOT EXISTS `productos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `precio_venta` DECIMAL(10,2) NOT NULL,
    `status` ENUM('activo', 'inactivo') DEFAULT 'activo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABLA: VENTAS
CREATE TABLE IF NOT EXISTS `ventas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `total` DECIMAL(10,2) NOT NULL,
    `metodo_pago` ENUM('Efectivo', 'Transferencia', 'Tarjeta') NOT NULL,
    `status` ENUM('completada', 'cancelada') DEFAULT 'completada',
    `notas` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABLA: DETALLE_VENTAS
CREATE TABLE IF NOT EXISTS `detalle_ventas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `venta_id` INT NOT NULL,
    `producto_id` INT NOT NULL,
    `cantidad` INT NOT NULL,
    `precio_unitario` DECIMAL(10,2) NOT NULL,
    CONSTRAINT `fk_detalle_ventas_maestro` 
        FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) 
        ON DELETE CASCADE,
    CONSTRAINT `fk_detalle_ventas_producto` 
        FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABLA: GASTOS
CREATE TABLE IF NOT EXISTS `gastos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `concepto` VARCHAR(255) NOT NULL,
    `monto` DECIMAL(10,2) NOT NULL,
    `categoria` ENUM('Insumos', 'Servicios', 'Publicidad', 'Herramientas', 'Otros') NOT NULL,
    `notas` TEXT DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
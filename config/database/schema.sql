-- =========================================================================
-- SCRIPT DE CREACIÓN DE BASE DE DATOS - BODEBASE
-- SYSTEM: PHP Nativo + MySQL (InnoDB)
-- =========================================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `detalle_ventas`;
DROP TABLE IF EXISTS `ventas`;
DROP TABLE IF EXISTS `gastos`;
DROP TABLE IF EXISTS `productos`;
DROP TABLE IF EXISTS `usuarios`;
SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------------------------------------------------
-- 1. TABLA: USUARIOS
-- Control de acceso al panel administrativo de la app.
-- -------------------------------------------------------------------------
CREATE TABLE `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- 2. TABLA: PRODUCTOS
-- Catálogo de crepas y extras. Incluye Soft Delete y control de Status.
-- -------------------------------------------------------------------------
CREATE TABLE `productos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `descripcion` TEXT DEFAULT NULL,
    `precio_venta` DECIMAL(10,2) NOT NULL,
    `status` ENUM('activo', 'inactivo') DEFAULT 'activo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- 3. TABLA: VENTAS
-- Registro maestro de las entradas de dinero.
-- -------------------------------------------------------------------------
CREATE TABLE `ventas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `total` DECIMAL(10,2) NOT NULL,
    `metodo_pago` ENUM('Efectivo', 'Transferencia', 'Tarjeta') NOT NULL,
    `status` ENUM('completada', 'cancelada') DEFAULT 'completada',
    `notas` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- 4. TABLA: DETALLE_VENTAS
-- Relación Muchos a Muchos. Almacena el precio histórico de la venta.
-- -------------------------------------------------------------------------
CREATE TABLE `detalle_ventas` (
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

-- -------------------------------------------------------------------------
-- 5. TABLA: GASTOS
-- Registro de salidas de dinero (materia prima, servicios, etc.).
-- -------------------------------------------------------------------------
CREATE TABLE `gastos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `concepto` VARCHAR(255) NOT NULL,
    `monto` DECIMAL(10,2) NOT NULL,
    `categoria` ENUM('Insumos', 'Servicios', 'Publicidad', 'Herramientas', 'Otros') NOT NULL,
    `notas` TEXT DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================================
-- INSERCIÓN DE DATOS DE PRUEBA (OPCIONAL / SEMILLERO)
-- Puedes borrar estas líneas si prefieres la base de datos completamente limpia.
-- =========================================================================

-- Productos iniciales de ejemplo
INSERT INTO `productos` (`nombre`, `descripcion`, `precio_venta`, `status`) VALUES
('Crepa Nutella Clásica', 'Crepa con Nutella, plátano y fresas', 65.00, 'activo'),
('Crepa Cajeta y Nuez', 'Crepa con cajeta tradicional y trozos de nuez', 60.00, 'activo'),
('Crepa Triple Queso (Salada)', 'Queso crema, mozzarella y queso amarillo', 75.00, 'activo'),
('Extra de Helado', 'Bola de helado de vainilla como topping', 15.00, 'activo');
-- =========================================================================
-- SCRIPT PARA AGREGAR CATEGORIA_ID Y CLAVE FORÁNEA A PRODUCTOS
-- =========================================================================

ALTER TABLE productos 
ADD COLUMN categoria_id INT NULL AFTER id,
ADD CONSTRAINT fk_productos_categorias 
FOREIGN KEY (categoria_id) REFERENCES categorias(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
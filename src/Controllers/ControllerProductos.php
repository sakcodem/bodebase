<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: productos.php');
    exit;
}

require_once '../config/conexion.php';
require_once '../src/Models/Productos.php';
require_once '../src/Helpers/Flash.php';

$accion = filter_input(INPUT_POST, 'accion', FILTER_DEFAULT);

switch ($accion) {
    case 'create':
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_DEFAULT);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_DEFAULT);
        $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
        $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);

        if (!$nombre || !$precio || $precio <= 0) {
            die("Error: Datos del producto inválidos.");
        }

        $descripcion = !empty(trim($descripcion)) ? trim($descripcion) : null;

        if (Productos::create($pdo, $categoria_id, trim($nombre), $descripcion, $precio)) {
            Flash::success('¡Producto registrado con éxito!');
        } else {
            Flash::error('Ocurrió un error al registrar el producto.');
        }
        header('Location: productos.php');
        exit;

    case 'update':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_DEFAULT);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_DEFAULT);
        $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
        $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);

        if (!$id || !$nombre || !$precio || $precio <= 0) {
            Flash::error('Error: Datos del producto inválidos.');
            header('Location: productos.php');
            exit;
        }

        $descripcion = !empty(trim($descripcion)) ? trim($descripcion) : null;

        if (Productos::update($pdo, $id, $categoria_id, trim($nombre), $descripcion, $precio)) {
            Flash::success('¡Producto actualizado con éxito!');
        } else {
            Flash::error('Ocurrió un error al actualizar el producto.');

        }
        header('Location: productos.php');
        exit;

    case 'toggle_status':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status_actual = filter_input(INPUT_POST, 'status_actual', FILTER_DEFAULT);

        if (!$id || !in_array($status_actual, ['activo', 'inactivo'])) {
            Flash::error('Parámetros inválidos.');
            header('Location: productos.php');
            exit;
        }

        if (Productos::toggleStatus($pdo, $id, $status_actual)) {
            Flash::success('¡Estado del producto actualizado con éxito!');
        } else {
            Flash::error('No se pudo cambiar el estado del producto.');
        }
        header('Location: productos.php');
        exit;

    case 'delete':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            Flash::error('ID de producto inválido.');
            header('Location: productos.php');
            exit;
        }

        if (Productos::delete($pdo, $id)) {
            Flash::success('¡Producto eliminado con éxito!');
        } else {
            Flash::error('No se pudo eliminar el producto.');
        }

        header('Location: productos.php');
        exit;

    default:
        Flash::error('Acción no permitida o no reconocida.');
        header('Location: productos.php');
        exit;
}

die("Error crítico al procesar la solicitud.");
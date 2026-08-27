<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: categorias.php');
    exit;
}

require_once '../config/conexion.php';
require_once '../src/Models/Categorias.php';
require_once '../src/Helpers/Flash.php';


$accion = filter_input(INPUT_POST, 'accion', FILTER_DEFAULT);
$nombre = filter_input(INPUT_POST, 'nombre', FILTER_DEFAULT);
$descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_DEFAULT);

switch ($accion) {
    case 'create':
        if (!$nombre) {
            Flash::error('El nombre es obligatorio.');
            header('Location: categorias.php');
            exit;
        }
        $descripcion = !empty(trim($descripcion)) ? trim($descripcion) : null;

        if (Categorias::create($pdo, trim($nombre), $descripcion)) {
            Flash::success('¡Categoría creada con éxito!');
        } else {
            Flash::error('Ocurrió un error al intentar crear la categoría.');
        }

        header('Location: categorias.php');
        exit;

    case 'update':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || !$nombre) {
            Flash::error('Datos de la categoría inválidos.');
            header('Location: categorias.php');
            exit;
        }

        $descripcion = !empty(trim($descripcion)) ? trim($descripcion) : null;
        if (Categorias::update($pdo, $id, trim($nombre), $descripcion)) {
            Flash::success('¡Categoría actualizada con éxito!');
        } else {
            Flash::error('Ocurrió un error al actualizar la categoría.');
        }

        header('Location: categorias.php');
        exit;

    case 'toggle_status':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status_actual = filter_input(INPUT_POST, 'status_actual', FILTER_DEFAULT);

        if (!$id || !in_array($status_actual, ['activo', 'inactivo'])) {
            Flash::error('Parámetros inválidos para cambiar el estado.');
            header('Location: categorias.php');
            exit;
        }

        if (Categorias::toggleStatus($pdo, $id, $status_actual)) {
            Flash::success('¡Estado actualizado con éxito!');
        } else {
            Flash::error('No se pudo cambiar el estado de la categoría.');
        }

        header('Location: categorias.php');
        exit;

    case 'delete':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            Flash::error('ID de categoría inválido.');
            header('Location: categorias.php');
            exit;
        }

        if (Categorias::delete($pdo, $id)) {
            Flash::success('¡Categoría eliminada con éxito!');
        } else {
            Flash::error('La categoría tiene productos asociados y no puede ser eliminada.');
        }
        header('Location: categorias.php');
        exit;


    default:
        Flash::error('Acción no permitida o no reconocida.');
        header('Location: categorias.php');
        exit;
}

<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gastos.php');
    exit;
}

require_once '../config/conexion.php';
require_once '../src/Models/Gastos.php';
require_once '../src/Helpers/Flash.php';

$monto = filter_input(INPUT_POST, 'monto', FILTER_VALIDATE_FLOAT);
$concepto = filter_input(INPUT_POST, 'concepto', FILTER_DEFAULT);
$categoria = filter_input(INPUT_POST, 'categoria', FILTER_DEFAULT);
$notas = filter_input(INPUT_POST, 'notas', FILTER_DEFAULT);

$categorias_permitidas = ['Insumos', 'Servicios', 'Publicidad', 'Herramientas', 'Otros'];

if (!$monto || $monto <= 0 || empty($concepto) || !in_array($categoria, $categorias_permitidas)) {
    Flash::error('Los datos proporcionados son inválidos.');
    header('Location: gastos.php');
    exit;
}

// Limpiar las notas si vienen vacías o con espacios en blanco
$notas = !empty(trim($notas)) ? trim($notas) : null;
$concepto = trim($concepto);

$exito = Gastos::registrar($pdo, $concepto, $monto, $categoria, $notas);

if ($exito) {
    Flash::success('¡Gasto registrado con éxito!');
} else {
    Flash::error('No se pudo registrar el gasto en el sistema.');
}

header('Location: gastos.php');
exit;
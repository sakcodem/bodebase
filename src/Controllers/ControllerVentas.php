<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ventas.php');
    exit;
}

require_once '../config/conexion.php';
require_once '../src/Models/Ventas.php';
require_once '../src/Helpers/Flash.php';

// 3. Sanitizar y capturar los datos básicos del formulario
$metodo_pago = filter_input(INPUT_POST, 'metodo_pago', FILTER_DEFAULT);
$total_venta = filter_input(INPUT_POST, 'total_venta', FILTER_VALIDATE_FLOAT);
$items_json = filter_input(INPUT_POST, 'items_json', FILTER_DEFAULT);

$metodos_permitidos = ['Efectivo', 'Transferencia', 'Tarjeta'];

if (!$total_venta || $total_venta <= 0 || !in_array($metodo_pago, $metodos_permitidos) || empty($items_json)) {
    Flash::error('Los datos de la venta son inválidos.');
    header('Location: ventas.php');
    exit;
}

$carrito = json_decode($items_json, true);

if (json_last_error() !== JSON_ERROR_NONE || empty($carrito)) {
    Flash::error('La estructura de la orden está corrompida.');
    header('Location: ventas.php');
    exit;
}

$exito = Ventas::registrar($pdo, $total_venta, $metodo_pago, 'completada', $carrito);


if ($exito) {
    Flash::success('¡Venta registrada con éxito!');
} else {
    Flash::error('Error crítico: No se pudo registrar la venta en el sistema..');
}

header('Location: ventas.php');
exit;


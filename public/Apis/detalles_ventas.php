<?php

require_once '../../config/conexion.php';
require_once '../../src/Models/Auth.php';
require_once '../../src/Models/HistorialVentas.php';

// Proteger el endpoint para que solo usuarios logueados puedan consultar
Auth::initSession();
Auth::protegerRuta();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

$detalles = HistorialVentas::getDetalleVenta($pdo, $id);

header('Content-Type: application/json');
echo json_encode($detalles);
?>
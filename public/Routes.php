<?php

require_once '../src/Models/Auth.php';

Auth::protegerRuta();

$controlador = filter_input(INPUT_POST, 'ctrl', FILTER_DEFAULT);

switch ($controlador) {
    case 'producto':
        require_once __DIR__ . '/../src/Controllers/ControllerProductos.php';
        break;
        
    case 'venta':
        require_once __DIR__ . '/../src/Controllers/ControllerVentas.php';
        break;
        
    case 'gasto':
        require_once __DIR__ . '/../src/Controllers/ControllerGastos.php';
        break;
    
    case 'detalle_venta':
        require_once __DIR__ . '/../src/Controllers/ControllerDetalleVentas.php';
        break;
    
    case 'categoria':
        require_once __DIR__ . '/../src/Controllers/ControllerCategorias.php';
        break;
    
    default:
        header('Location: index.php');
}
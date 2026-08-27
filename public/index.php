<?php

require_once '../config/conexion.php';
require_once '../src/Models/Auth.php';

Auth::protegerRuta();
require_once '../src/Models/Dashboard.php';

$metricas = Dashboard::getMetricas($pdo);

$fechaInicio = filter_input(INPUT_GET, 'fecha_inicio', FILTER_DEFAULT) ?? date('Y-m-d');
$fechaFin = filter_input(INPUT_GET, 'fecha_fin', FILTER_DEFAULT) ?? date('Y-m-d');
$metricasFiltro = Dashboard::getMetricasPorFecha($pdo, $fechaInicio, $fechaFin);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bodebase</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-100 text-slate-800 antialiased min-h-screen">
    <div class="flex flex-col md:flex-row min-h-screen">

        <?php include_once '../includes/sidebar.php'; ?>

        <main class="flex-1 max-w-7xl mx-auto p-4 md:p-6 space-y-6 w-full">

            <header class="flex justify-between items-center">
                <div>
                    <p class="text-2xl font-black text-slate-900 mt-0.5">
                        ¡Hola, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?>!
                    </p>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Ventas (Entradas)</p>
                        <h3 class="text-2xl font-black text-emerald-600 mt-1">
                            $<?= number_format($metricas['ingresos'], 2) ?></h3>
                    </div>
                    <div
                        class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-xl text-emerald-800">
                        <i data-lucide="banknote"></i>
                    </div>
                </div>

                <div
                    class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Gastos (Salidas)</p>
                        <h3 class="text-2xl font-black text-rose-600 mt-1">
                            $<?= number_format($metricas['egresos'], 2) ?>
                        </h3>
                    </div>
                    <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-xl text-rose-800">
                        <i data-lucide="banknote-arrow-up"></i>
                    </div>
                </div>

                <div
                    class="p-5 rounded-2xl shadow-xs border flex items-center justify-between <?= $metricas['balance'] >= 0 ? 'bg-amber-50 border-amber-200' : 'bg-red-50 border-red-200' ?>">
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-wider <?= $metricas['balance'] >= 0 ? 'text-amber-700/70' : 'text-red-700/70' ?>">
                            Ganancia Real (Balance)</p>
                        <h3
                            class="text-2xl font-black mt-1 <?= $metricas['balance'] >= 0 ? 'text-amber-800' : 'text-red-800' ?>">
                            $<?= number_format($metricas['balance'], 2) ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-white/80 rounded-xl flex items-center justify-center text-xl shadow-xs">
                        <i data-lucide="<?= $metricas['balance'] >= 0 ? 'trending-up' : 'trending-down' ?>"
                            class="<?= $metricas['balance'] >= 0 ? 'text-amber-600' : 'text-red-600' ?>"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <a href="ventas.php"
                    class="bg-amber-500 hover:bg-amber-600 text-white p-5 rounded-2xl shadow-md font-bold text-center transition flex flex-col items-center justify-center gap-2 group active:scale-95">
                    <span class="group-hover:scale-110 transition" data-lucide="shopping-cart"></span>
                    <span class="text-sm md:text-base">Nueva Venta</span>
                </a>
                <a href="gastos.php"
                    class="bg-slate-800 hover:bg-slate-900 text-white p-5 rounded-2xl shadow-md font-bold text-center transition flex flex-col items-center justify-center gap-2 group active:scale-95">
                    <span class="group-hover:scale-110 transition" data-lucide="banknote-arrow-up"></span>
                    <span class="text-sm md:text-base">Registrar Gasto</span>
                </a>
            </div>

            <!-- 2. Nueva Sección: Consulta de Movimientos por Período de Fecha -->
            <section class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Consulta por Fechas</h2>
                        <p class="text-xs text-slate-400 font-medium">Filtra los ingresos y egresos de un día o período
                            específico</p>
                    </div>

                    <!-- Formulario de Rango de Fechas -->
                    <form method="GET" class="flex flex-wrap items-center gap-2">
                        <div
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 rounded-xl border border-slate-200 text-xs font-medium text-slate-600">
                            <span>Desde:</span>
                            <input type="date" name="fecha_inicio"
                                value="<?= htmlspecialchars($metricasFiltro['fecha_inicio']) ?>"
                                class="bg-transparent border-0 font-bold text-slate-800 focus:outline-none cursor-pointer">
                        </div>

                        <div
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 rounded-xl border border-slate-200 text-xs font-medium text-slate-600">
                            <span>Hasta:</span>
                            <input type="date" name="fecha_fin"
                                value="<?= htmlspecialchars($metricasFiltro['fecha_fin']) ?>"
                                class="bg-transparent border-0 font-bold text-slate-800 focus:outline-none cursor-pointer">
                        </div>

                        <button type="submit"
                            class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-1.5 rounded-xl text-xs font-bold transition">
                            Filtrar
                        </button>

                        <a href="index.php"
                            class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-xl text-xs font-bold transition">
                            Hoy
                        </a>
                    </form>
                </div>

                <!-- Tarjetas del Período Filtrado -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-4 bg-emerald-50/50 rounded-xl border border-emerald-100">
                        <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Ventas del Período</p>
                        <p class="text-xl font-black text-emerald-700 mt-1">
                            $
                            <?= number_format($metricasFiltro['ingresos'], 2) ?>
                        </p>
                    </div>

                    <div class="p-4 bg-rose-50/50 rounded-xl border border-rose-100">
                        <p class="text-xs font-bold text-rose-800 uppercase tracking-wider">Gastos del Período</p>
                        <p class="text-xl font-black text-rose-700 mt-1">
                            $
                            <?= number_format($metricasFiltro['egresos'], 2) ?>
                        </p>
                    </div>

                    <div
                        class="p-4 rounded-xl border <?= $metricasFiltro['balance'] >= 0 ? 'bg-amber-50/50 border-amber-100' : 'bg-red-50/50 border-red-100' ?>">
                        <p
                            class="text-xs font-bold uppercase tracking-wider <?= $metricasFiltro['balance'] >= 0 ? 'text-amber-800' : 'text-red-800' ?>">
                            Balance del Período
                        </p>
                        <p
                            class="text-xl font-black mt-1 <?= $metricasFiltro['balance'] >= 0 ? 'text-amber-700' : 'text-red-700' ?>">
                            $
                            <?= number_format($metricasFiltro['balance'], 2) ?>
                        </p>
                    </div>
                </div>
            </section>


        </main>
    </div>

    <?php include '../includes/lucide.php'; ?>

</body>

</html>
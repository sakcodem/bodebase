<?php // public/historial_ventas.php 
require_once '../config/conexion.php';
require_once '../src/Models/Auth.php';
Auth::protegerRuta();
require_once '../src/Models/HistorialVentas.php';
$ventas = HistorialVentas::getVentas($pdo);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Ventas - Bodebase</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-100 text-slate-800 antialiased min-h-screen"
    x-data="{ isOpen: false, selectedVenta: null, items: [], loading: false, async verDetalle(id, total, fecha, pago, cliente) { this.selectedVenta = { id, total, fecha, pago, cliente }; this.isOpen = true; this.loading = true; this.items = []; try { let response = await fetch(`Apis/detalles_ventas.php?id=${id}`); this.items = await response.json(); } catch (e) { console.error('Error al cargar el detalle', e); } finally { this.loading = false; } } }">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>
        <main class="flex-1 p-4 md:p-6 max-w-7xl mx-auto w-full space-y-6">
            <header>
                <h1 class="text-2xl font-black text-slate-900">Historial de Ventas</h1>
                <p class="text-xs text-slate-500 font-medium">Revisa las transacciones pasadas y el desglose de
                    productos vendidos.</p>
            </header>
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100 uppercase tracking-wider text-xs">
                                <th class="py-3 px-2">ID Venta</th>
                                <th class="py-3 px-2">Fecha y Hora</th>
                                <th class="py-3 px-2">Método de Pago</th>
                                <th class="py-3 px-2 text-right">Total Cobrado</th>
                                <th class="py-3 px-2 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($ventas)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-slate-400">Aún no se han registrado ventas.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ventas as $v): ?>
                                    <?php
                                    $fechaObj = new DateTime($v['fecha'], new DateTimeZone('UTC'));
                                    $fechaObj->setTimezone(new DateTimeZone('America/Merida'));
                                    $fechaFormateada = $fechaObj->format('d/m/Y - h:i a');
                                    ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 px-2 font-bold text-slate-950">#<?= $v['id'] ?></td>
                                        <td class="py-4 px-2 text-slate-500">
                                            <?= $fechaFormateada ?>
                                        </td>
                                        <td class="py-4 px-2">
                                            <span
                                                class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                                <?= htmlspecialchars($v['metodo_pago']) ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-2 text-right font-black text-emerald-600 text-base">
                                            $<?= number_format($v['total'], 2) ?>
                                        </td>
                                        <td class="py-4 px-2 text-center">
                                            <button
                                                @click="verDetalle(<?= $v['id'] ?>, '<?= number_format($v['total'], 2) ?>', '<?= $fechaFormateada ?>', '<?= htmlspecialchars($v['metodo_pago'], ENT_QUOTES) ?>', '<?= htmlspecialchars($v['cliente'], ENT_QUOTES)?>')"
                                                class="p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition cursor-pointer inline-flex items-center justify-center gap-1.5 text-xs font-bold">
                                                <i data-lucide="eye" class="w-4 h-4 text-amber-500"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" x-cloak>
        <div x-show="isOpen" @click="isOpen = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs"> </div>
        <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md p-6 relative z-10 space-y-5">
            <div class="text-center pb-4 border-b border-dashed border-slate-200 space-y-1">
                <span class="text-3xl"></span>
                <h3 class="text-lg font-black text-slate-900">Detalle de Venta</h3>
                <p class="text-xs text-slate-400 font-bold" x-text="'VENTA #' + (selectedVenta?.id || '')"></p>
                <p class="text-[11px] text-slate-400" x-text="selectedVenta?.fecha"></p>
            </div>
            <div class="space-y-3">
                <p class="text-xs font-black uppercase tracking-wider text-slate-400">Productos vendidos</p>
                <div x-show="loading"
                    class="py-8 flex items-center justify-center gap-2 text-slate-400 text-sm font-semibold">
                    <i data-lucide="loader"></i> <span> Cargando artículos...</span>
                </div>
                <div x-show="!loading" class="space-y-2.5 max-h-48 overflow-y-auto pr-1">
                    <template x-for="item in items">
                        <div class="flex justify-between items-start text-sm">
                            <div class="space-y-0.5">
                                <p class="font-bold text-slate-800" x-text="item.nombre"></p>
                                <p class="text-xs text-slate-400"
                                    x-text="item.cantidad + 'x $' + parseFloat(item.precio_unitario).toFixed(2)"></p>
                            </div>
                            <span class="font-bold text-slate-700"
                                x-text="'$' + (item.cantidad * item.precio_unitario).toFixed(2)"></span>
                        </div>
                    </template>
                </div>
            </div>
            <div class="pt-4 border-t border-dashed border-slate-200 space-y-2 text-sm">
                <div class="flex justify-between text-slate-500">
                    <span>Cliente:</span>
                    <span class="font-bold text-slate-700" x-text="selectedVenta?.cliente"></span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Método de Pago:</span>
                    <span class="font-bold text-slate-700" x-text="selectedVenta?.pago"></span>
                </div>
                <div class="flex justify-between text-base font-black pt-1 border-t border-slate-100">
                    <span class="text-slate-900">Total Venta:</span>
                    <span class="text-emerald-600" x-text="'$' + (selectedVenta?.total || '0.00')"></span>
                </div>
            </div>
            <button @click="isOpen = false"
                class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-black text-sm py-3 rounded-xl transition cursor-pointer text-center block">
                Cerrar Ticket </button>
        </div>
    </div>
    <script> lucide.createIcons(); </script>
</body>

</html>
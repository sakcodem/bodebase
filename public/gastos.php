
<?php
require_once '../config/conexion.php';
require_once '../src/Models/Auth.php';

Auth::protegerRuta();

require_once '../src/Models/Gastos.php';
require_once '../src/Helpers/Flash.php';

$todosLosGastos = Gastos::getAll($pdo);

// Métricas rápidas (opcional: pueden calcularse en SQL o dinámicamente)
$totalGastos = array_sum(array_column($todosLosGastos, 'monto'));
$totalRegistros = count($todosLosGastos);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Gastos - Bodebase</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen" x-data="{ modalOpen: false, search: '', categoriaFilter: '' }">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

        <main class="flex-1 w-full p-4 md:p-8 max-w-7xl mx-auto space-y-6">

            <?php if (isset($_GET['success'])): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
                    class="fixed top-5 right-5 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-xl z-50 flex items-center gap-3 font-medium text-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    ¡Gasto registrado correctamente!
                </div>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gastos</h1>
                    <p class="text-xs text-slate-500 mt-1">Historial general de salidas y registro de egresos</p>
                </div>
                <button @click="modalOpen = true" 
                    class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-semibold px-5 py-2.5 rounded-xl shadow-sm cursor-pointer transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Registrar Gasto
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Acumulado</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-1">
                        $<?= number_format($totalGastos, 2) ?>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Transacciones</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-1">
                        <?= $totalRegistros ?> <span class="text-xs font-normal text-slate-500">registros</span>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Promedio por Gasto</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-1">
                        $<?= $totalRegistros > 0 ? number_format($totalGastos / $totalRegistros, 2) : '0.00' ?>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                
                <!-- Barra de búsqueda y filtros -->
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-3 justify-between items-center">
                    <div class="relative w-full sm:w-72">
                        <input type="text" x-model="search" placeholder="Buscar por concepto..." 
                            class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <div class="w-full sm:w-auto">
                        <select x-model="categoriaFilter" 
                            class="w-full sm:w-48 bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-hidden focus:border-amber-500 cursor-pointer">
                            <option value="">Todas las categorías</option>
                            <option value="Insumos">Insumos</option>
                            <option value="Servicios">Servicios</option>
                            <option value="Publicidad">Publicidad</option>
                            <option value="Herramientas">Herramientas</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                </div>

                <!-- Tabla de datos -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-100">
                                <th class="py-3.5 px-4">Fecha y Hora</th>
                                <th class="py-3.5 px-4">Concepto / Detalle</th>
                                <th class="py-3.5 px-4">Categoría</th>
                                <th class="py-3.5 px-4 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($todosLosGastos)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-12 text-slate-400">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"/></svg>
                                        No hay gastos registrados en el sistema.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($todosLosGastos as $gasto): ?>
                                    <?php
                                    $fechaObj = new DateTime($gasto['fecha'], new DateTimeZone('UTC'));
                                    $fechaObj->setTimezone(new DateTimeZone('America/Merida'));
                                    $fechaFormateada = $fechaObj->format('d/m/Y');
                                    $horaFormateada = $fechaObj->format('H:i A');
                                    ?>
                                    <tr class="hover:bg-slate-50/80 transition-colors"
                                        x-show="(search === '' || '<?= strtolower(addslashes($gasto['concepto'])) ?>'.includes(search.toLowerCase())) && (categoriaFilter === '' || '<?= $gasto['categoria'] ?>' === categoriaFilter)">
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-medium text-slate-800"><?= $fechaFormateada ?></div>
                                            <div class="text-xs text-slate-400"><?= $horaFormateada ?></div>
                                        </td>
                                        <td class="py-3.5 px-4 max-w-xs">
                                            <div class="font-semibold text-slate-900"><?= htmlspecialchars($gasto['concepto']) ?></div>
                                            <?php if (!empty($gasto['notas'])): ?>
                                                <div class="text-xs text-slate-500 truncate" title="<?= htmlspecialchars($gasto['notas']) ?>">
                                                    <?= htmlspecialchars($gasto['notas']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                                                <?= htmlspecialchars($gasto['categoria']) ?>
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-bold text-rose-600 whitespace-nowrap text-base">
                                            -$<?= number_format($gasto['monto'], 2) ?>
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

    <!-- Modal de Registro de Gasto -->
    <div x-cloak x-show="modalOpen" 
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-lg p-6 space-y-5">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Registrar Nuevo Gasto</h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="Routes.php" method="POST" class="space-y-4">
                <input type="hidden" name="ctrl" value="gasto">
                
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Monto ($)</label>
                    <input type="number" step="0.01" name="monto" required min="0.01" placeholder="0.00" autofocus
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-xl text-slate-900 focus:outline-hidden focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Concepto / Descripción</label>
                    <input type="text" name="concepto" required placeholder="Ej. Kilo de fresas, Gas, Envases..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Categoría</label>
                    <select name="categoria" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 cursor-pointer">
                        <option value="Insumos">Insumos</option>
                        <option value="Servicios">Servicios (Gas, Luz, Internet)</option>
                        <option value="Publicidad">Publicidad</option>
                        <option value="Herramientas">Herramientas</option>
                        <option value="Otros">Otros</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Notas adicionales (Opcional)</label>
                    <textarea name="notas" rows="2" placeholder="Detalles extra..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 transition"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="modalOpen = false" 
                        class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2.5 rounded-xl transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" 
                        class="w-1/2 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 rounded-xl shadow-md transition cursor-pointer">
                        Guardar Gasto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'components/flash.php'; ?>
</body>
</html>

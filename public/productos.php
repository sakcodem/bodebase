<?php

require_once '../config/conexion.php';
require_once '../src/Models/Productos.php';
require_once '../src/Models/Categorias.php';
require_once '../src/Models/Auth.php';
require_once '../src/Helpers/Flash.php';
Auth::protegerRuta();

$catalogo = Productos::getAll($pdo);
$totalRegistros = count($catalogo);
$productosActivos = Productos::getActivos($pdo);
$totalActivos = count($productosActivos);
$totalInactivos = $totalRegistros - $totalActivos;
$categorias = Categorias::getActivos($pdo);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Bodebases</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-100 text-slate-800 antialiased min-h-screen" x-data="{ 
          openCreateModal: false,
          search: '', 
          categoriaFilter: '',
          isEditModalOpen: false, 
          editId: '', 
          editNombre: '', 
          editDescripcion: '', 
          editPrecio: '',
          editCategoriaId: '',
          openEditModal(id, nombre, desc, precio, categoria_id) {
              this.editId = id;
              this.editNombre = nombre;
              this.editDescripcion = desc;
              this.editPrecio = precio;
              this.editCategoriaId = categoria_id;
              this.isEditModalOpen = true;
          },
          openDeleteModal(id, nombre) {
              this.deleteId = id;
              this.deleteNombre = nombre;
              this.isDeleteModalOpen = true;
          }
        }">

    <div class="flex flex-col md:flex-row min-h-screen">

        <?php include '../includes/sidebar.php'; ?>

        <main class="flex-1 p-4 md:p-6 max-w-7xl mx-auto w-full space-y-6">

            <?php if (isset($_GET['success'])): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    class="fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 text-sm font-medium">
                    Operación realizada con éxito
                </div>
            <?php endif; ?>

            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Productos</h1>
                    <p class="text-xs text-slate-500 mt-1">Agregra nuevos productos a tu catálogo</p>
                </div>
                <button @click="openCreateModal = true"
                    class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-semibold px-5 py-2.5 rounded-xl shadow-sm cursor-pointer transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Registrar Producto
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Productos</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-1">
                        <?= $totalRegistros ?> <span class="text-xs font-normal text-slate-500">registros</span>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Activos</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-1">
                        <?= $totalActivos ?>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Inactivos</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-1">
                        <?= $totalInactivos ?>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80">

                <!-- Filtros de búsqueda y categoría -->
                <div
                    class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-3 justify-between items-center">
                    <div class="relative w-full sm:w-72">
                        <input type="text" x-model="search" placeholder="Buscar por producto..."
                            class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <div class="w-full sm:w-auto">
                        <select x-model="categoriaFilter"
                            class="w-full sm:w-48 bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-hidden focus:border-amber-500 cursor-pointer">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= htmlspecialchars($categoria['id']) ?>">
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto hidden md:block">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100 uppercase tracking-wider text-xs">
                                <th class="py-3 px-2">Producto</th>
                                <th class="py-3 px-2">Categoría</th>
                                <th class="py-3 px-2">Precio</th>
                                <th class="py-3 px-2">Estado</th>
                                <th class="py-3 px-2 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($catalogo as $prod): ?>
                                <tr class="hover:bg-slate-50 transition-colors"
                                    x-show="(search === '' || '<?= strtolower(addslashes($prod['nombre'])) ?>'.includes(search.toLowerCase())) && (categoriaFilter === '' || '<?= $prod['categoria_id'] ?>' === categoriaFilter)">
                                    <td class="py-4 px-2">
                                        <div class="font-bold text-slate-800"><?= htmlspecialchars($prod['nombre']) ?></div>
                                        <div class="text-xs text-slate-400">
                                            <?= htmlspecialchars($prod['descripcion'] ?? '') ?>
                                        </div>
                                    </td>
                                    <td class="py-4 px-2 font-bold text-slate-700">
                                        <?= htmlspecialchars($prod['categoria_nombre'] ?? '') ?>
                                    </td>
                                    <td class="py-4 px-2 font-bold text-slate-700">
                                        $<?= number_format($prod['precio_venta'], 2) ?></td>
                                    <td class="py-4 px-2">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-bold <?= $prod['status'] === 'activo' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                                            <?= ucfirst($prod['status']) ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-2 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="Routes.php" method="POST" class="inline">
                                                <input type="hidden" name="ctrl" value="producto">
                                                <input type="hidden" name="accion" value="toggle_status">
                                                <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                                                <input type="hidden" name="status_actual" value="<?= $prod['status'] ?>">
                                                <button type="submit"
                                                    title="<?= $prod['status'] === 'activo' ? 'Pausar producto' : 'Activar producto' ?>"
                                                    class="p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50  transition cursor-pointer flex items-center justify-center">
                                                    <?php if ($prod['status'] === 'activo'): ?>
                                                        <i data-lucide="pause" class="w-4 h-4 text-amber-500"></i>
                                                    <?php else: ?>
                                                        <i data-lucide="play" class="w-4 h-4 text-emerald-500"></i>
                                                    <?php endif; ?>
                                                </button>
                                            </form>

                                            <button
                                                @click="openEditModal(<?= $prod['id'] ?>, '<?= addslashes($prod['nombre']) ?>', '<?= addslashes($prod['descripcion'] ?? '') ?>', <?= $prod['precio_venta'] ?>, <?= $prod['categoria_id'] ?>)"
                                                title="Editar producto"
                                                class="p-2 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-500 transition cursor-pointer flex items-center justify-center">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </button>
                                            <button
                                                @click="openDeleteModal(<?= $prod['id'] ?>, '<?= addslashes($prod['nombre']) ?>')"
                                                title="Eliminar producto"
                                                class="p-2 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 transition cursor-pointer flex items-center justify-center">
                                                <i data-lucide="trash" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-1 gap-3 md:hidden">
                    <?php foreach ($catalogo as $prod): ?>
                        <div class="border border-slate-100 bg-slate-50 p-4 rounded-xl space-y-3"
                            x-show.transition.opacity="(search === '' || '<?= strtolower(addslashes($prod['nombre'])) ?>'.includes(search.toLowerCase())) && (categoriaFilter === '' || '<?= $prod['categoria_id'] ?>' === categoriaFilter)">
                            <p class="text-xs text-slate-700">
                                <?= htmlspecialchars($prod['categoria_nombre'] ?? '') ?>
                            </p>

                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-base"><?= htmlspecialchars($prod['nombre']) ?>
                                    </h4>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        <?= htmlspecialchars($prod['descripcion'] ?? '') ?>
                                    </p>
                                </div>
                                <span
                                    class="text-slate-700 font-black">$<?= number_format($prod['precio_venta'], 2) ?></span>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-200/60">
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-bold <?= $prod['status'] === 'activo' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' ?>">
                                    <?= ucfirst($prod['status']) ?>
                                </span>
                                <div class="flex gap-2">
                                    <form action="Routes.php" method="POST">
                                        <input type="hidden" name="ctrl" value="producto">
                                        <input type="hidden" name="accion" value="toggle_status">
                                        <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                                        <input type="hidden" name="status_actual" value="<?= $prod['status'] ?>">
                                        <button type="submit"
                                            title="<?= $prod['status'] === 'activo' ? 'Pausar producto' : 'Activar producto' ?>"
                                            class="p-2 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 transition cursor-pointer flex items-center justify-center">
                                            <?php if ($prod['status'] === 'activo'): ?>
                                                <i data-lucide="pause" class="w-4 h-4 text-amber-500"></i>
                                            <?php else: ?>
                                                <i data-lucide="play" class="w-4 h-4 text-emerald-500"></i>
                                            <?php endif; ?>
                                        </button>
                                    </form>

                                    <button
                                        @click="openEditModal(<?= $prod['id'] ?>, '<?= addslashes($prod['nombre']) ?>', '<?= addslashes($prod['descripcion'] ?? '') ?>', <?= $prod['precio_venta'] ?>, <?= $prod['categoria_id'] ?>)"
                                        title="Editar producto"
                                        class="p-2 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-500 transition cursor-pointer flex items-center justify-center">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>

                                    <button
                                        @click="openDeleteModal(<?= $prod['id'] ?>, '<?= addslashes($prod['nombre']) ?>')"
                                        class="p-2 bg-rose-50 border border-rose-100 rounded-lg text-rose-600 font-medium flex items-center justify-center">
                                        <i data-lucide="trash" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </main>
    </div>

    <div x-cloak x-show="openCreateModal"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div @click.away="openCreateModal = false"
            class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-lg p-6 space-y-5">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Registrar Nuevo Producto</h3>
                <button @click="openCreateModal = false"
                    class="text-slate-400 hover:text-slate-600 rounded-lg p-1 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="Routes.php" method="POST" class="space-y-4">
                <input type="hidden" name="ctrl" value="producto">
                <input type="hidden" name="accion" value="create">

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Nombre<span
                            class="text-red-500">*</span></label>
                    <input type="text" autocomplete="off" name="nombre" required placeholder="Ej. Crepa Oreo"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Descripción / Ingredientes</label>
                    <input type="text" name="descripcion" placeholder="Ej. Queso crema, galleta Oreo y chocolate"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Categoría<span
                            class="text-red-500">*</span></label>
                    <select required name="categoria_id"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 cursor-pointer">
                        <option value="">--Seleccionar--</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= htmlspecialchars($categoria['id']) ?>">
                                <?= htmlspecialchars($categoria['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Precio<span
                            class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="precio" required placeholder="0.00"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 font-bold focus:outline-hidden focus:border-amber-500 transition">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="openCreateModal = false"
                        class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2.5 rounded-xl transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="w-1/2 bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 rounded-xl shadow-md transition cursor-pointer">
                        Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="isEditModalOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" x-cloak>

        <div x-show="isEditModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="isEditModalOpen = false"
            class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs">
        </div>

        <div x-show="isEditModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md p-6 relative z-10 space-y-4">

            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-900">Editar Producto</h3>
                <button @click="isEditModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="Routes.php" method="POST" class="space-y-4">
                <input type="hidden" name="ctrl" value="producto">
                <input type="hidden" name="accion" value="update">
                <input type="hidden" name="id" :value="editId">

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombre del producto</label>
                    <input type="text" name="nombre" required x-model="editNombre"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Descripción /
                        Ingredientes</label>
                    <input type="text" name="descripcion" x-model="editDescripcion"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Categoría</label>
                    <select x-model="editCategoriaId" required name="categoria_id"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 cursor-pointer">
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= htmlspecialchars($categoria['id']) ?>">
                                <?= htmlspecialchars($categoria['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Precio ($)</label>
                    <input type="number" step="0.01" name="precio" required x-model="editPrecio"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 font-bold focus:outline-hidden focus:border-amber-500 transition">
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="isEditModalOpen = false"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-sm py-3 rounded-xl transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm py-3 rounded-xl shadow-xs transition cursor-pointer">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="isDeleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        x-cloak>

        <div x-show="isDeleteModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="isDeleteModalOpen = false"
            class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs">
        </div>

        <div x-show="isDeleteModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-sm p-6 relative z-10 text-center space-y-4">

            <div
                class="mx-auto w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl">
                <i data-lucide="triangle-alert"></i>
            </div>

            <div class="space-y-1">
                <h3 class="text-lg font-black text-slate-900">¿Eliminar Producto?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Estás a punto de ocultar <span class="font-bold text-slate-800" x-text="deleteNombre"></span> de tu
                    menú. Tu historial de ventas pasadas no se verá afectado.
                </p>
            </div>

            <form action="Routes.php" method="POST">
                <input type="hidden" name="ctrl" value="producto">
                <input type="hidden" name="accion" value="delete">
                <input type="hidden" name="id" :value="deleteId">

                <div class="flex gap-2">
                    <button type="button" @click="isDeleteModalOpen = false"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-sm py-3 rounded-xl transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm py-3 rounded-xl shadow-xs transition cursor-pointer">
                        Sí, eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php include '../includes/lucide.php'; ?>
    <?php include 'components/flash.php'; ?>
</body>

</html>
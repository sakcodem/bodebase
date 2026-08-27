<?php
require_once '../config/conexion.php';
require_once '../src/Models/Auth.php';

Auth::protegerRuta();

require_once '../src/Models/Categorias.php';
require_once '../src/Helpers/Flash.php';

$catalogo = Categorias::getAll($pdo);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Bodebase</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen" x-data="{ 
        isEditModalOpen: false, 
        editId: '', 
        editNombre: '', 
        editDescripcion: '', 
        openEditModal(id, nombre, desc) {
            this.editId = id;
            this.editNombre = nombre;
            this.editDescripcion = desc;
            this.isEditModalOpen = true;
        },
        openDeleteModal(id, nombre){
            this.deleteId = id;
            this.deleteNombre = nombre;
            this.isDeleteModalOpen = true
        }
    }">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include '../includes/sidebar.php'; ?>

        <main class="flex-1 w-full p-4 md:p-8 max-w-7xl mx-auto space-y-6">

            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900">Categorías</h1>
                    <p class="text-xs text-slate-500 font-medium">Agrega categorías y edita su información.</p>
                </div>
            </header>

            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4">Añadir Categoría</h2>
                <form action="Routes.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <input type="hidden" name="ctrl" value="categoria">
                    <input type="hidden" name="accion" value="create">

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Nombre de la categoría <span
                                class="text-red-500">*</span></label>
                        <input type="text" autocomplete="off" name="nombre" required placeholder="Ej. Frappes"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Descripción</label>
                        <input type="text" name="descripcion"
                            placeholder="Ej. Bebidas frías a base de café, leche y hielo"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 transition">
                    </div>
                    <div class="grid grid-cols-1 gap-2">
                        <button type="submit"
                            class="bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm rounded-xl transition cursor-pointer h-[42px] self-end shadow-xs">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
                <h2 class="text-base font-bold text-slate-900 mb-4">Lista de Categorías</h2>

                <div class="overflow-x-auto hidden md:block">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100 uppercase tracking-wider text-xs">
                                <th class="py-3 px-2">Categoría</th>
                                <th class="py-3 px-2">Estado</th>
                                <th class="py-3 px-2 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($catalogo as $cat): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-2">
                                        <div class="font-bold text-slate-800"><?= htmlspecialchars($cat['nombre']) ?></div>
                                        <div class="text-xs text-slate-400">
                                            <?= htmlspecialchars($cat['descripcion'] ?? '') ?>
                                        </div>
                                    </td>
                                    <td class="py-4 px-2">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-bold <?= $cat['status'] === 'activo' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                                            <?= ucfirst($cat['status']) ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-2 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="Routes.php" method="POST" class="inline">
                                                <input type="hidden" name="ctrl" value="categoria">
                                                <input type="hidden" name="accion" value="toggle_status">
                                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                                <input type="hidden" name="status_actual" value="<?= $cat['status'] ?>">
                                                <button type="submit"
                                                    title="<?= $cat['status'] === 'activo' ? 'Pausar categoría' : 'Activar categoría' ?>"
                                                    class="p-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50  transition cursor-pointer flex items-center justify-center">
                                                    <?php if ($cat['status'] === 'activo'): ?>
                                                        <i data-lucide="pause" class="w-4 h-4 text-amber-500"></i>
                                                    <?php else: ?>
                                                        <i data-lucide="play" class="w-4 h-4 text-emerald-500"></i>
                                                    <?php endif; ?>
                                                </button>
                                            </form>

                                            <button
                                                @click="openEditModal(<?= $cat['id'] ?>, '<?= addslashes($cat['nombre']) ?>', '<?= addslashes($cat['descripcion'] ?? '') ?>')"
                                                title="Editar categoría"
                                                class="p-2 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-500 transition cursor-pointer flex items-center justify-center">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </button>

                                            <button
                                                @click="openDeleteModal(<?= $cat['id'] ?>, '<?= addslashes($cat['nombre']) ?>')"
                                                title="Eliminar categoría"
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
                    <?php foreach ($catalogo as $cat): ?>
                        <div class="border border-slate-100 bg-slate-50 p-4 rounded-xl space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-base"><?= htmlspecialchars($cat['nombre']) ?>
                                    </h4>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        <?= htmlspecialchars($cat['descripcion'] ?? '') ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-200/60">
                                <span
                                    class="px-2 py-0.5 rounded-full text-xs font-bold <?= $cat['status'] === 'activo' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' ?>">
                                    <?= ucfirst($cat['status']) ?>
                                </span>
                                <div class="flex gap-2">
                                    <form action="Routes.php" method="POST">
                                        <input type="hidden" name="ctrl" value="categoria">
                                        <input type="hidden" name="accion" value="toggle_status">
                                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                        <input type="hidden" name="status_actual" value="<?= $cat['status'] ?>">
                                        <button type="submit"
                                            title="<?= $cat['status'] === 'activo' ? 'Pausar categoría' : 'Activar categoría' ?>"
                                            class="p-2 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 transition cursor-pointer flex items-center justify-center">
                                            <?php if ($cat['status'] === 'activo'): ?>
                                                <i data-lucide="pause" class="w-4 h-4 text-amber-500"></i>
                                            <?php else: ?>
                                                <i data-lucide="play" class="w-4 h-4 text-emerald-500"></i>
                                            <?php endif; ?>
                                        </button>
                                    </form>

                                    <button
                                        @click="openEditModal(<?= $cat['id'] ?>, '<?= addslashes($cat['nombre']) ?>', '<?= addslashes($cat['descripcion'] ?? '') ?>')"
                                        title="Editar categoría"
                                        class="p-2 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-500 transition cursor-pointer flex items-center justify-center">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>

                                    <button @click="openDeleteModal(<?= $cat['id'] ?>, '<?= addslashes($cat['nombre']) ?>')"
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

    <div x-show="isEditModalOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        x-cloak>

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
                <h3 class="text-lg font-black text-slate-900">Editar Categoría</h3>
                <button @click="isEditModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="Routes.php" method="POST" class="space-y-4">
                <input type="hidden" name="ctrl" value="categoria">
                <input type="hidden" name="accion" value="update">
                <input type="hidden" name="id" :value="editId">

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nombre</label>
                    <input type="text" name="nombre" required x-model="editNombre"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Descripción</label>
                    <input type="text" name="descripcion" x-model="editDescripcion"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-hidden focus:border-amber-500 transition">
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
                <h3 class="text-lg font-black text-slate-900">¿Eliminar categoría?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Estás a punto de eliminar <span class="font-bold text-slate-800" x-text="deleteNombre"></span> de
                    tus categorías.
                </p>
            </div>

            <form action="Routes.php" method="POST">
                <input type="hidden" name="ctrl" value="categoria">
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
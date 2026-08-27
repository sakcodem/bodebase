<?php
require_once '../config/conexion.php';
require_once '../src/Models/Auth.php';


Auth::protegerRuta();

require_once '../src/Models/Productos.php';
require_once '../src/Models/Categorias.php';
require_once '../src/Helpers/Flash.php';

$productos = Productos::getActivos($pdo);
$categorias = Categorias::getActivos($pdo);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta - Bodebase</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
</head>

<body class="bg-slate-100 text-slate-800 antialiased min-h-screen" x-data="posSystem()">

    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include_once '../includes/sidebar.php'; ?>

        <main class="flex-1 w-full">
            <?php if (isset($_GET['success'])): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    class="fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2 transition font-medium text-sm">
                    ¡Venta registrada exitosamente!
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 min-h-screen">

                <div class="lg:col-span-2 p-4 md:p-6 overflow-y-auto">
                    <header class="mb-6 flex items-center">
                        <h1 class="text-2xl font-bold text-slate-900">Nueva Venta</h1>
                    </header>

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

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-4">
                        <?php foreach ($productos as $prod): ?>
                            <button
                                @click="addItem(<?= $prod['id'] ?>, '<?= addslashes($prod['nombre']) ?>', <?= $prod['precio_venta'] ?>)"
                                class="bg-white p-4 rounded-xl shadow-xs border border-slate-200 text-left hover:border-amber-500 focus:outline-hidden 
                                focus:ring-2 focus:ring-amber-400 active:scale-95 transition flex flex-col justify-between h-32 cursor-pointer"
                                x-show="categoriaFilter === '' || categoriaFilter == <?= $prod['categoria_id'] ?>"
                                >
                                <span class="font-semibold text-sm md:text-base text-slate-800 line-clamp-2"><?= htmlspecialchars($prod['nombre']) ?></span>
                                <span class="text-slate-400 text-xs"><?= htmlspecialchars($prod['descripcion']) ?></span>
                                <span
                                    class="text-amber-600 font-bold text-base md:text-lg">$<?= number_format($prod['precio_venta'], 2) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div
                    class="bg-white border-t lg:border-t-0 lg:border-l border-slate-200 p-4 md:p-6 flex flex-col justify-between shadow-xl lg:shadow-none">
                    <div>
                        <h2
                            class="text-lg font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                            <span>Orden Actual</span>
                            <span class="bg-amber-100 text-amber-800 text-xs px-2 py-0.5 rounded-full"
                                x-text="countItems() + ' ítems'"></span>
                        </h2>

                        <div class="space-y-3 max-h-[40vh] lg:max-h-[55vh] overflow-y-auto pr-1">
                            <template x-for="item in cart" :key="item.id">
                                <div
                                    class="flex items-center justify-between bg-slate-50 p-3 rounded-lg border border-slate-100 text-sm">
                                    <div class="flex-1 min-w-0 pr-2">
                                        <p class="font-medium text-slate-800 truncate" x-text="item.nombre"></p>
                                        <p class="text-xs text-slate-500"
                                            x-text="'$' + item.precio.toFixed(2) + ' c/u'"></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="updateQty(item.id, -1)"
                                            class="w-6 h-6 bg-slate-200 rounded-md flex items-center justify-center font-bold text-slate-700 hover:bg-slate-300">-</button>
                                        <span class="w-6 text-center font-semibold text-slate-800"
                                            x-text="item.qty"></span>
                                        <button @click="updateQty(item.id, 1)"
                                            class="w-6 h-6 bg-slate-200 rounded-md flex items-center justify-center font-bold text-slate-700 hover:bg-slate-300">+</button>
                                    </div>
                                    <div class="w-16 text-right font-bold text-slate-800 pl-2"
                                        x-text="'$' + (item.precio * item.qty).toFixed(2)"></div>
                                </div>
                            </template>

                            <template x-if="cart.length === 0">
                                <div class="text-center py-8 text-slate-400 text-sm">
                                    <p>No hay productos en la orden.</p>
                                    <p class="text-xs mt-1">Toca un producto a la izquierda para agregarlo.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <form action="Routes.php" method="POST" class="mt-6 pt-4 border-t border-slate-200 tracking-wider"
                        @submit="prepareSubmit($event)">
                        <input type="hidden" name="ctrl" value="venta">
                        <input type="hidden" name="items_json" :value="JSON.stringify(cart)">

                        <div class="mb-4">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Método
                                de Pago</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label
                                    class="border p-2 rounded-lg text-center cursor-pointer flex flex-col items-center justify-center text-xs font-medium transition"
                                    :class="metodo_pago === 'Efectivo' ? 'bg-amber-500 text-white border-amber-500' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                                    <i data-lucide="banknote" class="w-5 h-5"></i>
                                    <input type="radio" name="metodo_pago" value="Efectivo" x-model="metodo_pago"
                                        class="sr-only">Efectivo
                                </label>
                                <label
                                    class="border p-2 rounded-lg text-center cursor-pointer flex flex-col items-center justify-center text-xs font-medium transition"
                                    :class="metodo_pago === 'Transferencia' ? 'bg-amber-500 text-white border-amber-500' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                                    <i data-lucide="banknote-arrow-down" class="w-5 h-5"></i>
                                    <input type="radio" name="metodo_pago" value="Transferencia" x-model="metodo_pago"
                                        class="sr-only">Transf.
                                </label>
                                <label
                                    class="border p-2 rounded-lg text-center cursor-pointer flex flex-col items-center justify-center text-xs font-medium transition"
                                    :class="metodo_pago === 'Tarjeta' ? 'bg-amber-500 text-white border-amber-500' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                                    <input type="radio" name="metodo_pago" value="Tarjeta" x-model="metodo_pago"
                                        class="sr-only"> Tarjeta
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="cliente" class="block text-sm font-medium text-slate-500 mb-1">Cliente (opcional)</label>
                            <input type="text" id="cliente" name="cliente" x-model="cliente"
                                class="border border-slate-300 focus:ring-2 hover:border-amber-500 focus:ring-amber-500 focus:outline-hidden py-1 px-2 rounded-lg">
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Total a Cobrar:</span>
                                <span class="text-2xl font-black text-slate-900" x-text="'$' + total.toFixed(2)"></span>
                                <input type="hidden" name="total_venta" :value="total">
                            </div>
                            <button type="submit" :disabled="cart.length === 0"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold py-3 px-4 rounded-xl shadow-md cursor-pointer transition text-center block">
                                Registrar Venta
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <script>
        function posSystem() {
            return {
                categoriaFilter: '', 
                cart: [],
                metodo_pago: 'Efectivo',
                total: 0,
                cliente: '',

                addItem(id, nombre, precio) {
                    let existing = this.cart.find(item => item.id === id);
                    if (existing) {
                        existing.qty++;
                    } else {
                        this.cart.push({
                            id,
                            nombre,
                            precio,
                            qty: 1
                        });
                    }
                    this.calculateTotal();
                },

                updateQty(id, amount) {
                    let item = this.cart.find(item => item.id === id);
                    if (!item) return;
                    item.qty += amount;
                    if (item.qty <= 0) {
                        this.cart = this.cart.filter(i => i.id !== id);
                    }
                    this.calculateTotal();
                },

                countItems() {
                    return this.cart.reduce((sum, item) => sum + item.qty, 0);
                },

                calculateTotal() {
                    this.total = this.cart.reduce((sum, item) => sum + (item.precio * item.qty), 0);
                },

                prepareSubmit(e) {
                    if (this.cart.length === 0) {
                        e.preventDefault();
                        alert('El carrito está vacío');
                    }
                }
            }
        }
    </script>
    <?php include '../includes/lucide.php'; ?>
    <?php include 'components/flash.php'; ?>
</body>

</html>
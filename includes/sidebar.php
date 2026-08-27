<?php

$pagina_actual = basename($_SERVER['SCRIPT_NAME']);
?>

<div x-data="{ isOpen: false }" class="relative">

    <div class="bg-slate-900 text-white flex items-center justify-between p-4 md:hidden w-full shadow-md z-40 relative">
        <div class="flex items-center gap-2">
            <span class="text-xl"></span>
            <span class="font-black tracking-wide text-sm">Bodebases</span>
        </div>
        <button @click="isOpen = !isOpen" class="p-2 text-slate-300 hover:text-white focus:outline-hidden cursor-pointer" aria-label="Menu">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="isOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div x-show="isOpen" x-cloak @click="isOpen = false" 
         class="fixed inset-0 bg-slate-900/50 z-40 md:hidden transition-opacity">
    </div>

    <aside 
        :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed md:sticky md:top-0 inset-y-0 left-0 w-64 h-screen bg-slate-900 text-slate-300 flex flex-col justify-between transform md:translate-x-0 transition-transform duration-300 ease-in-out z-50 border-r border-slate-800 shadow-xl md:shadow-none"
    >
        
        <div class="flex-1 overflow-y-auto p-5 space-y-6 scrollbar-thin">
            <div class="hidden md:flex items-center gap-3 px-2">
                <span class="text-2xl"></span>
                <span class="font-black text-xl text-white tracking-wide">Bodebase</span>
            </div>

            <hr class="border-slate-800 hidden md:block">

            <nav class="space-y-1">
                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2 mb-2">Menú Principal</span>
                
                <a href="index.php" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group <?= $pagina_actual === 'index.php' ? 'bg-amber-500 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <span class="text-base group-hover:scale-110 transition w-4 h-4" data-lucide="layout-dashboard"></span>
                    <span>Dashboard</span>
                </a>

                <a href="ventas.php" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group <?= $pagina_actual === 'ventas.php' ? 'bg-amber-500 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <span class="text-base group-hover:scale-110 transition w-4 h-4" data-lucide="shopping-cart"></span>
                    <span>Nueva Venta</span>
                </a>

                <a href="gastos.php" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group <?= $pagina_actual === 'gastos.php' ? 'bg-amber-500 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <span class="text-base group-hover:scale-110 transition w-4 h-4" data-lucide="banknote-arrow-up"></span>
                    <span>Registrar Gasto</span>
                </a>

                <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2 mb-2">Catálogos</span>

                <a href="productos.php" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group <?= $pagina_actual === 'productos.php' ? 'bg-amber-500 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <span class="text-base group-hover:scale-110 transition w-4 h-4" data-lucide="clipboard-list"></span>
                    <span>Productos</span>
                </a>

                <a href="categorias.php" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group <?= $pagina_actual === 'categorias.php' ? 'bg-amber-500 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <span class="text-base group-hover:scale-110 transition w-4 h-4" data-lucide="tag"></span>
                    <span>Categorías</span>
                </a>

                <a href="historialVentas.php" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group <?= $pagina_actual === 'historialVentas.php' ? 'bg-amber-500 text-white shadow-xs' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <span class="text-base group-hover:scale-110 transition w-4 h-4" data-lucide="clipboard-list"></span>
                    <span>Historial de Ventas</span>
                </a>

            </nav>
        </div>

        <div class="p-4 bg-slate-950 border-t border-slate-800/80 flex items-center justify-between px-5 text-xs text-slate-500 shrink-0">
            <div class="flex items-center gap-3 truncate">
                <div class="w-8 h-8 shrink-0 rounded-full bg-slate-800 flex items-center justify-center font-bold text-slate-300">
                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="truncate">
                    <p class="font-semibold text-slate-300 text-sm truncate"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Negocio') ?></p>
                    <p class="truncate text-[11px]">Panel de Control</p>
                </div>
            </div>
            
            <a href="logout.php" title="Cerrar sesión" class="p-1.5 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-rose-400 transition cursor-pointer">
                <i data-lucide="log-out" class="w-5 h-5"></i>
            </a>
        </div>
    </aside>

</div>
<?php include_once '../includes/lucide.php'; ?>
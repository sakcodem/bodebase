<?php if (Flash::has()): ?>
    <div x-data="{ 
            toasts: <?php echo htmlspecialchars(json_encode(Flash::get()), ENT_QUOTES, 'UTF-8'); ?>,
            dismiss(index) {
                this.toasts.splice(index, 1);
            }
         }" class="fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none">

        <template x-for="(toast, index) in toasts" :key="index">
            <div x-init="setTimeout(() => dismiss(index), 4500)" x-show="true"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                class="pointer-events-auto flex items-center justify-between gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm font-medium transition-all"
                :class="{
                     'bg-emerald-600 text-white border-emerald-500': toast.type === 'success',
                     'bg-rose-600 text-white border-rose-500': toast.type === 'error',
                     'bg-amber-500 text-white border-amber-400': toast.type === 'warning'
                 }">

                <div class="flex items-center gap-2.5">

                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>

                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>

                    <template x-if="toast.type === 'warning'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>

                    <span x-text="toast.message"></span>
                </div>

                <button @click="dismiss(index)" class="opacity-70 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
<?php endif; ?>
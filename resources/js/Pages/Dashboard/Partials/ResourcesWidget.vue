<script setup>
const props = defineProps({
    data: Object
});
</script>

<template>
    <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] p-6 shadow-sm border border-zinc-100 dark:border-zinc-800">
        <h3 class="text-lg font-medium text-zinc-800 dark:text-zinc-100 mb-4">Recursos Registrados</h3>
        
        <div class="space-y-4">
            <!-- Vehículos -->
            <div class="flex items-center justify-between p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                        <i class="pi pi-car text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Vehículos</span>
                </div>
                <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ data.vehicles }}</span>
            </div>

            <!-- Mascotas -->
            <div class="flex items-center justify-between p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center">
                        <i class="pi pi-heart text-orange-600 dark:text-orange-400 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Mascotas</span>
                </div>
                <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ data.pets }}</span>
            </div>

            <!-- Tags de Acceso -->
            <div class="flex items-center justify-between p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                        <i class="pi pi-id-card text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tags Activos</span>
                </div>
                <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ data.tags }}</span>
            </div>
        </div>
    </div>
</template>
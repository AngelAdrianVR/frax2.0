<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: Object,
    residents: Object
});

const occupancyPercentage = computed(() => {
    if (!props.data || props.data.total === 0) return 0;
    return Math.round((props.data.occupied / props.data.total) * 100);
});
</script>

<template>
    <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] p-6 shadow-sm border border-zinc-100 dark:border-zinc-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-zinc-800 dark:text-zinc-100">Ocupación</h3>
            <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                <i class="pi pi-home text-indigo-500 text-xl"></i>
            </div>
        </div>

        <div class="flex items-end gap-2 mb-2">
            <span class="text-4xl font-bold text-zinc-900 dark:text-white tracking-tight">{{ occupancyPercentage }}%</span>
            <span class="text-sm text-zinc-500 dark:text-zinc-400 mb-1">habitado</span>
        </div>

        <!-- Barra de progreso estilo iOS -->
        <div class="w-full h-3 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden mb-6">
            <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000" :style="`width: ${occupancyPercentage}%`"></div>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Unidades Totales</p>
                <p class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">{{ data.total }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-1">Residentes Activos</p>
                <p class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">{{ residents.active }}</p>
            </div>
        </div>
    </div>
</template>
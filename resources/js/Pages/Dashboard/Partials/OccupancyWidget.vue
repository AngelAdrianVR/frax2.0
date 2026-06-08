<script setup>
import { computed } from 'vue';

const props = defineProps({ data: Object, residents: Object });

const occupancyPercentage = computed(() => {
    if (!props.data || props.data.total === 0) return 0;
    return Math.round((props.data.occupied / props.data.total) * 100);
});

const progressColor = computed(() => {
    if (occupancyPercentage.value >= 90) return 'from-emerald-400 to-emerald-500';
    if (occupancyPercentage.value >= 70) return 'from-blue-400 to-blue-500';
    return 'from-amber-400 to-amber-500';
});
</script>

<template>
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)] h-full">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-[13px] font-semibold text-zinc-800 dark:text-zinc-100">Ocupación del Coto</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Unidades habitadas</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-zinc-100 to-zinc-50 dark:from-zinc-800 dark:to-zinc-800/50 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-zinc-500 dark:text-zinc-400"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"/><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"/></svg>
            </div>
        </div>

        <div class="flex items-baseline gap-2 mb-4">
            <span class="text-4xl font-bold text-zinc-900 dark:text-white tracking-tight tabular-nums">{{ occupancyPercentage }}</span>
            <span class="text-lg text-zinc-400 dark:text-zinc-500 font-medium">%</span>
        </div>

        <!-- Progress bar estilo premium -->
        <div class="w-full h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden mb-5">
            <div 
                :class="['h-full rounded-full bg-gradient-to-r transition-all duration-1000 ease-[cubic-bezier(0.16,1,0.3,1)]', progressColor]" 
                :style="{ width: `${occupancyPercentage}%` }"
            ></div>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800/50">
            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-3">
                <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.05em] mb-0.5">Unidades</p>
                <p class="text-lg font-bold text-zinc-800 dark:text-zinc-200 tabular-nums">{{ data.total }}</p>
            </div>
            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-3">
                <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.05em] mb-0.5">Activos</p>
                <p class="text-lg font-bold text-zinc-800 dark:text-zinc-200 tabular-nums">{{ residents.active }}</p>
            </div>
        </div>
    </div>
</template>
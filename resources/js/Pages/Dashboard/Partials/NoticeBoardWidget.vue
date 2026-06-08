<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    notices: {
        type: Array,
        default: () => [
            { id: 1, title: 'Corte de Agua Programado', type: 'alerta', time: 'Hace 2h' },
            { id: 2, title: 'Encuesta: Color de fachada', type: 'encuesta', time: 'Ayer' },
            { id: 3, title: 'Mantenimiento de Alberca', type: 'oficial', time: 'Hace 2 días' }
        ]
    }
});

const getStyle = (type) => {
    if (type === 'alerta') return { bg: 'bg-red-50 dark:bg-red-500/10', icon: 'text-red-500', dot: 'bg-red-500' };
    if (type === 'encuesta') return { bg: 'bg-violet-50 dark:bg-violet-500/10', icon: 'text-violet-500', dot: 'bg-violet-500' };
    return { bg: 'bg-blue-50 dark:bg-blue-500/10', icon: 'text-blue-500', dot: 'bg-blue-500' };
};
</script>

<template>
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)] h-full flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-[13px] font-semibold text-zinc-800 dark:text-zinc-100">Comunidad Reciente</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Últimos avisos</p>
            </div>
            <Link :href="route('notice-board.index')" class="text-[12px] font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors">
                Ver todo
            </Link>
        </div>

        <!-- Empty state -->
        <div v-if="notices.length === 0" class="flex-1 flex flex-col items-center justify-center py-8">
            <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
            </div>
            <p class="text-[13px] text-zinc-500 dark:text-zinc-400">No hay avisos recientes</p>
        </div>

        <!-- Notice list -->
        <div v-else class="space-y-1 flex-1">
            <div v-for="notice in notices" :key="notice.id" class="flex items-start gap-3 p-3 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors group cursor-pointer">
                <div :class="['w-8 h-8 rounded-[10px] flex items-center justify-center flex-shrink-0', getStyle(notice.type).bg]">
                    <svg v-if="notice.type === 'alerta'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" :class="['w-4 h-4', getStyle(notice.type).icon]"><path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0113.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 01-.297 1.206c-4.505 1.538-9.637 1.538-14.142 0a.75.75 0 01-.297-1.206A8.973 8.973 0 005.25 9.75V9zm4.502 9.75a2.25 2.25 0 004.496 0" clip-rule="evenodd" /></svg>
                    <svg v-else-if="notice.type === 'encuesta'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" :class="['w-4 h-4', getStyle(notice.type).icon]"><path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z" /></svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" :class="['w-4 h-4', getStyle(notice.type).icon]"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" /></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-medium text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-zinc-100 transition-colors truncate">{{ notice.title }}</p>
                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">{{ notice.time }}</p>
                </div>
            </div>
        </div>

        <!-- Tip card -->
        <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800/50">
            <div class="bg-gradient-to-r from-zinc-50 to-zinc-100/50 dark:from-zinc-800/50 dark:to-zinc-800/30 rounded-xl p-3.5">
                <div class="flex gap-3">
                    <div class="w-7 h-7 rounded-[10px] bg-white dark:bg-zinc-700 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 text-amber-500"><path d="M12 .75a8.25 8.25 0 00-4.135 15.412c-.682.505-.967 1.268-.717 1.942.31.837 1.173 1.396 1.985 1.396h5.734c.812 0 1.675-.559 1.985-1.396.25-.674-.035-1.437-.717-1.942A8.25 8.25 0 0012 .75z" /></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">Tip</p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 leading-relaxed">Usa encuestas en el muro para involucrar a los vecinos en decisiones del coto.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
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

const getIcon = (type) => {
    if (type === 'alerta') return 'pi pi-bell text-red-500 bg-red-100 dark:bg-red-500/20';
    if (type === 'encuesta') return 'pi pi-chart-bar text-indigo-500 bg-indigo-100 dark:bg-indigo-500/20';
    return 'pi pi-info-circle text-blue-500 bg-blue-100 dark:bg-blue-500/20';
};
</script>

<template>
    <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] p-6 shadow-sm border border-zinc-100 dark:border-zinc-800 flex flex-col h-full">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium text-zinc-800 dark:text-zinc-100">Comunidad Reciente</h3>
            <Link :href="route('notice-board.index')" class="text-sm text-indigo-500 hover:text-indigo-600 font-medium">
                Ver Muro
            </Link>
        </div>

        <div v-if="notices.length === 0" class="flex-1 flex flex-col items-center justify-center text-center p-6">
            <div class="w-16 h-16 rounded-full bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center mb-3">
                <i class="pi pi-comments text-zinc-300 dark:text-zinc-600 text-2xl"></i>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No hay avisos recientes</p>
        </div>

        <div v-else class="space-y-5">
            <div v-for="notice in notices" :key="notice.id" class="flex items-start gap-4">
                <div :class="['w-10 h-10 rounded-full flex items-center justify-center shrink-0', getIcon(notice.type).split(' ').slice(1).join(' ')]">
                    <i :class="getIcon(notice.type).split(' ')[0] + ' ' + getIcon(notice.type).split(' ')[1] + ' text-sm'"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ notice.title }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ notice.time }}</p>
                </div>
            </div>
        </div>

        <!-- Banner Promocional / Tip -->
        <div class="mt-auto pt-6">
            <div class="bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl p-4 border border-indigo-100 dark:border-indigo-500/20">
                <div class="flex gap-3">
                    <i class="pi pi-lightbulb text-indigo-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-indigo-900 dark:text-indigo-300">Tip de Administración</p>
                        <p class="text-xs text-indigo-700 dark:text-indigo-400 mt-1">Usa encuestas en el muro para involucrar a los vecinos en las decisiones del coto.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
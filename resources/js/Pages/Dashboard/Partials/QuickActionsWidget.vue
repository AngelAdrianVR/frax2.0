<script setup>
import { Link } from '@inertiajs/vue3';

const actions = [
    { label: 'Nueva Unidad', route: 'admin.private-units.create', icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />`, color: 'text-zinc-500 dark:text-zinc-400', bg: 'bg-zinc-100 dark:bg-zinc-800' },
    { label: 'Alta Residente', route: 'users.create', icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />`, color: 'text-blue-500', bg: 'bg-blue-50 dark:bg-blue-500/10' },
    { label: 'Publicar Aviso', route: 'notice-board.index', icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84a11.66 11.66 0 01-.858-.595 8.09 8.09 0 01-.78-.61l-.002-.002a4.814 4.814 0 01-.686-.906 4.57 4.57 0 01-.378-.728M4.06 18.34a10.49 10.49 0 002.339 2.307A10.378 10.378 0 0112 21.75a10.404 10.404 0 005.602-1.683 10.49 10.49 0 002.338-2.307M4.06 5.66A10.49 10.49 0 016.4 3.353 10.38 10.38 0 0112 2.25a10.39 10.39 0 015.598 1.682c.861.6 1.633 1.35 2.301 2.209M4.06 5.66L12 12.75l7.94-7.09M4.06 5.66l-.163.163a1.97 1.97 0 00-.434.72 1.93 1.93 0 00-.213.832V18c0 .62.504 1.125 1.125 1.125H19.5c.621 0 1.125-.504 1.125-1.125V7.375c0-.284-.045-.562-.152-.826a1.968 1.968 0 00-.495-.726L19.94 5.66" />`, color: 'text-violet-500', bg: 'bg-violet-50 dark:bg-violet-500/10' },
    { label: 'Ver Morosos', route: 'slowPayers.index', icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />`, color: 'text-red-500', bg: 'bg-red-50 dark:bg-red-500/10' },
];
</script>

<template>
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)]">
        <div class="mb-4">
            <h3 class="text-[13px] font-semibold text-zinc-800 dark:text-zinc-100">Acciones Rápidas</h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Atajos frecuentes</p>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <Link 
                v-for="action in actions" :key="action.label"
                :href="route(action.route)" 
                class="flex flex-col items-center gap-2 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all group"
            >
                <div :class="['w-8 h-8 rounded-[10px] flex items-center justify-center transition-transform group-hover:scale-110', action.bg]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" :class="['w-4 h-4', action.color]" v-html="action.icon"></svg>
                </div>
                <span class="text-[11px] font-medium text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-800 dark:group-hover:text-zinc-200 text-center leading-tight">{{ action.label }}</span>
            </Link>
        </div>
    </div>
</template>
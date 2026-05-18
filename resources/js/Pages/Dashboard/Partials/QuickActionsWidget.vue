<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] p-6 shadow-sm border border-zinc-100 dark:border-zinc-800">
        <h3 class="text-lg font-medium text-zinc-800 dark:text-zinc-100 mb-4">Acciones Rápidas</h3>
        
        <div class="grid grid-cols-2 gap-3">
            <Link :href="route('admin.private-units.create')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                <i class="pi pi-home text-indigo-500 text-xl"></i>
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-300">Nueva Unidad</span>
            </Link>

            <Link :href="route('users.create')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                <i class="pi pi-user-plus text-indigo-500 text-xl"></i>
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-300">Alta Residente</span>
            </Link>

            <Link :href="route('notice-board.index')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                <i class="pi pi-megaphone text-indigo-500 text-xl"></i>
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-300">Publicar Aviso</span>
            </Link>

            <Link :href="route('slowPayers.index')" class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                <i class="pi pi-exclamation-triangle text-red-500 text-xl"></i>
                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-300">Ver Morosos</span>
            </Link>
        </div>
    </div>
</template>
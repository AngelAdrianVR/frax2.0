<template>
    <div v-if="links.length > 3" class="flex flex-wrap items-center justify-center gap-1.5">
        <template v-for="(link, key) in links" :key="key">
            <!-- Botón deshabilitado (ej. "Anterior" en la página 1) -->
            <div v-if="link.url === null"
                 class="px-3 py-2 text-sm font-medium text-gray-400 bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg cursor-not-allowed"
                 v-html="link.label">
            </div>
            
            <!-- Botón Activo/Inactivo -->
            <Link v-else
                  :href="link.url"
                  class="px-3 py-2 text-sm font-medium rounded-lg transition-all active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:focus:ring-offset-gray-900"
                  :class="link.active 
                    ? 'bg-blue-600 text-white shadow-sm border border-blue-600' 
                    : 'bg-white dark:bg-[#1C1C1E] text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800'"
                  v-html="link.label">
            </Link>
        </template>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    patrols: Object
});

const getStatusColor = (status) => {
    switch(status) {
        case 'Terminado': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'Activo': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'Incidente': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default: return 'bg-zinc-100 text-zinc-800';
    }
};
</script>

<template>
    <AppLayout title="Bitácora de Rondines">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-6 px-4 sm:px-0">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Bitácora de Rondines</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Historial de recorridos de seguridad.</p>
                    </div>
                </div>

                <div v-if="patrols.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <i class="pi pi-shield text-4xl text-zinc-400 mb-3"></i>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Sin rondines registrados</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">El personal de seguridad aún no ha registrado recorridos.</p>
                </div>

                <div v-else class="bg-white dark:bg-zinc-800 shadow-xl sm:rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Guardia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Inicio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Fin</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Puntos Escaneados</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            <tr v-for="patrol in patrols.data" :key="patrol.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-zinc-900 dark:text-white">
                                    <i class="pi pi-user mr-2 text-indigo-500"></i> {{ patrol.guard_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ patrol.start_time }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ patrol.end_time }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-zinc-900 dark:text-white font-bold">
                                    {{ patrol.scanned_points }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold" :class="getStatusColor(patrol.status)">
                                        {{ patrol.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div v-if="patrols.links && patrols.links.length > 3" class="flex justify-center mt-6">
                    <div class="flex gap-1">
                        <Link v-for="(link, k) in patrols.links" :key="k" :href="link.url" v-html="link.label" class="px-3 py-1 border rounded text-sm transition-colors" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-600'" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
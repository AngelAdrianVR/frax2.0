<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    parcels: Object
});

const getStatusColor = (status) => {
    switch(status) {
        case 'Entregado': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'En Caseta': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'Devuelto': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default: return 'bg-zinc-100 text-zinc-800';
    }
};
</script>

<template>
    <AppLayout title="Paquetería">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-6 px-4 sm:px-0">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Recepción de Paquetería</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Control de entregas recibidas en caseta.</p>
                    </div>
                </div>

                <div v-if="parcels.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <i class="pi pi-box text-4xl text-zinc-400 mb-3"></i>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Sin paquetes</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">No tienes paquetes pendientes por recoger en caseta.</p>
                </div>

                <div v-else class="bg-white dark:bg-zinc-800 shadow-xl sm:rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Paquetería / Mensajería</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Número de Guía</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Fecha de Recepción</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            <tr v-for="parcel in parcels.data" :key="parcel.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-zinc-900 dark:text-white">
                                    <i class="pi pi-box mr-2 text-indigo-500"></i> {{ parcel.courier }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ parcel.tracking_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ parcel.received_at }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold" :class="getStatusColor(parcel.status)">
                                        {{ parcel.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div v-if="parcels.links && parcels.links.length > 3" class="flex justify-center mt-6">
                    <div class="flex gap-1">
                        <Link v-for="(link, k) in parcels.links" :key="k" :href="link.url" v-html="link.label" class="px-3 py-1 border rounded text-sm transition-colors" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-600'" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
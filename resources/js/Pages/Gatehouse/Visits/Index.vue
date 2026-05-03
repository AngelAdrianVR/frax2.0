<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    visits: Object
});

const getStatusColor = (status) => {
    switch(status) {
        case 'Ingresado': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'Pendiente': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'Cancelado': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        case 'Expirado': return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-400';
        default: return 'bg-blue-100 text-blue-800';
    }
};
</script>

<template>
    <AppLayout title="Bitácora de Visitas">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-6 px-4 sm:px-0">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Bitácora de Visitas</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Historial de accesos a tu domicilio.</p>
                    </div>
                    <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md text-sm font-medium transition-colors">
                        + Nueva Visita
                    </button>
                </div>

                <div v-if="visits.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <i class="pi pi-id-card text-4xl text-zinc-400 mb-3"></i>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Sin visitas registradas</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Aquí aparecerá el historial de tus invitados.</p>
                </div>

                <div v-else class="bg-white dark:bg-zinc-800 shadow-xl sm:rounded-lg overflow-hidden">
                    <!-- Vista Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Visitante</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Motivo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Fecha de Uso</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Estatus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                <tr v-for="visit in visits.data" :key="visit.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-zinc-900 dark:text-white">{{ visit.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ visit.reason || 'No especificado' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ visit.date_of_use }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                        <i :class="visit.access_type === 'Vehicular' ? 'pi pi-car' : 'pi pi-user'" class="mr-1"></i> {{ visit.access_type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 py-1 text-xs rounded-full font-semibold" :class="getStatusColor(visit.status)">
                                            {{ visit.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Vista Móvil -->
                    <div class="md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
                        <div v-for="visit in visits.data" :key="visit.id" class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white">{{ visit.name }}</h4>
                                <span class="px-2 py-1 text-[10px] font-semibold rounded-full" :class="getStatusColor(visit.status)">
                                    {{ visit.status }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">{{ visit.reason }}</p>
                            <div class="flex justify-between items-center text-xs text-zinc-500 dark:text-zinc-400">
                                <span><i class="pi pi-calendar mr-1"></i>{{ visit.date_of_use }}</span>
                                <span><i :class="visit.access_type === 'Vehicular' ? 'pi pi-car' : 'pi pi-user'" class="mr-1"></i>{{ visit.access_type }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="visits.links && visits.links.length > 3" class="flex justify-center mt-6">
                    <div class="flex gap-1">
                        <Link v-for="(link, k) in visits.links" :key="k" :href="link.url" v-html="link.label" class="px-3 py-1 border rounded text-sm transition-colors" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-600'" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
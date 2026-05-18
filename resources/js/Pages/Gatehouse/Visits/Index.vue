<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    visits: Object
});

const searchQuery = ref('');

const getStatusColor = (status) => {
    switch(status) {
        case 'Ingresado': return 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400';
        case 'Pendiente': return 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400';
        case 'Cancelado': return 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400';
        case 'Expirado': return 'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400';
        default: return 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400';
    }
};
</script>

<template>
    <AppLayout title="Bitácora de Visitas">
        <div class="py-8 md:py-12 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado y Acciones -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 px-4 sm:px-0 gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Bitácora de Visitas</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Historial de accesos e invitaciones a tu domicilio.</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <!-- Buscador UI (Preparado para backend) -->
                        <div class="relative w-full md:w-64">
                            <i class="pi pi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-zinc-400"></i>
                            <input v-model="searchQuery" type="text" placeholder="Buscar visita..." 
                                class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-zinc-800 border-none rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-indigo-500 dark:text-white transition-shadow" />
                        </div>
                        <Link :href="route('visits.create')" class="shrink-0 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md text-sm font-semibold transition-all flex items-center gap-2">
                            <i class="pi pi-plus text-xs"></i> <span class="hidden sm:inline">Nueva Visita</span>
                        </Link>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="visits.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-3xl shadow-sm p-12 text-center border border-zinc-100 dark:border-zinc-700/50 mx-4 sm:mx-0">
                    <div class="w-20 h-20 bg-zinc-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="pi pi-id-card text-4xl text-zinc-300 dark:text-zinc-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Sin visitas registradas</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 mb-6">Aún no has generado ninguna invitación para tus invitados.</p>
                    <Link :href="route('visits.create')" class="px-6 py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl font-semibold text-sm transition-colors">
                        Crear mi primera invitación
                    </Link>
                </div>

                <!-- Lista de Visitas -->
                <div v-else class="bg-white dark:bg-zinc-800 shadow-sm sm:rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-700/50">
                    <!-- Vista Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-700/50">
                            <thead class="bg-zinc-50/50 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Visitante</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Detalles</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Fecha / Uso</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Estatus</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                                <tr v-for="visit in visits.data" :key="visit.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/20 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-sm">
                                                {{ visit.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ visit.name }}</div>
                                                <div class="text-xs text-zinc-500">{{ visit.access_type }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-zinc-600 dark:text-zinc-300 line-clamp-1 max-w-[200px]">{{ visit.reason || 'Sin motivo' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ visit.date_of_use !== 'N/A' ? visit.date_of_use : 'No utilizada' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 text-xs rounded-full font-bold tracking-wide" :class="getStatusColor(visit.status)">
                                            {{ visit.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <Link :href="route('visits.show', visit.id)" class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:bg-indigo-100 hover:text-indigo-600 transition-colors">
                                                <i class="pi pi-eye text-xs"></i>
                                            </Link>
                                            <Link :href="route('visits.edit', visit.id)" class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:bg-amber-100 hover:text-amber-600 transition-colors">
                                                <i class="pi pi-pencil text-xs"></i>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Vista Móvil (Cards iOS Style) -->
                    <div class="md:hidden divide-y divide-zinc-100 dark:divide-zinc-700/50">
                        <div v-for="visit in visits.data" :key="visit.id" class="p-5 hover:bg-zinc-50 dark:hover:bg-zinc-700/20 active:bg-zinc-100 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ visit.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white leading-tight">{{ visit.name }}</h4>
                                        <span class="text-xs text-zinc-500">{{ visit.access_type }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full uppercase tracking-wider" :class="getStatusColor(visit.status)">
                                    {{ visit.status }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-4 line-clamp-1">{{ visit.reason || 'Sin motivo especificado' }}</p>
                            
                            <div class="flex justify-between items-center pt-3 border-t border-zinc-100 dark:border-zinc-700/50">
                                <div class="text-xs text-zinc-500 font-medium">
                                    <i class="pi pi-calendar text-[10px] mr-1"></i> {{ visit.date_of_use !== 'N/A' ? visit.date_of_use : 'Pendiente de uso' }}
                                </div>
                                <div class="flex gap-2">
                                    <Link :href="route('visits.show', visit.id)" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-700 rounded-lg text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                                        Ver QR
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación Estilo iOS -->
                <div v-if="visits.links && visits.links.length > 3" class="flex justify-center mt-8">
                    <div class="flex gap-1.5 bg-white dark:bg-zinc-800 p-1.5 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-700/50">
                        <Link v-for="(link, k) in visits.links" :key="k" :href="link.url" v-html="link.label" 
                            class="min-w-[32px] h-8 flex items-center justify-center rounded-xl text-sm font-medium transition-colors px-2" 
                            :class="link.active ? 'bg-indigo-600 text-white shadow-md' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700'" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
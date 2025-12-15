<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

// Definimos las props
const props = defineProps({
    debtors: Object
});

// Propiedad computada para ordenar los datos de la página actual
const sortedDebtors = computed(() => {
    // Si no hay datos, retornamos arreglo vacío
    if (!props.debtors || !props.debtors.data) return [];

    // Creamos una copia [...] para no mutar la prop original y ordenamos
    return [...props.debtors.data].sort((a, b) => {
        const textA = a.address || ''; // Aseguramos que sea string
        const textB = b.address || '';
        
        // localeCompare con 'numeric: true' ordena "Calle 2" antes que "Calle 10"
        return textA.localeCompare(textB, 'es', { numeric: true, sensitivity: 'base' });
    });
});
</script>

<template>
    <AppLayout title="Reporte de Morosidad">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 p-4 md:p-6">
            <div class="max-w-7xl mx-auto">
                
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">Reporte de Morosidad</h1>
                        <p class="text-sm md:text-base text-gray-500 dark:text-gray-400">Listado completo de deuda por unidad.</p>
                    </div>
                </div>

                <!-- VISTA ESCRITORIO (Tabla normal) -->
                <div class="hidden md:block bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Propietario</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Deuda Total</th>
                                <!-- <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th> -->
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="unit in sortedDebtors" :key="unit.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                    {{ unit.address }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ unit.owner_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold" 
                                    :class="unit.total_debt > 0 ? 'text-red-600' : 'text-gray-400'">
                                    ${{ Number(unit.total_debt).toLocaleString('es-MX', {minimumFractionDigits: 2}) }}
                                </td>
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <Link :href="route('admin.private-units.show', unit.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 mr-2">
                                        Ver Detalles
                                    </Link>
                                    <button v-if="unit.total_debt > 0" class="text-amber-600 hover:text-amber-900 dark:text-amber-400" title="Enviar Recordatorio">
                                        <i class="pi pi-bell"></i>
                                    </button>
                                </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- VISTA MÓVIL (Tarjetas) -->
                <div class="md:hidden space-y-4">
                    <div v-for="unit in sortedDebtors" :key="unit.id" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 border-l-4"
                         :class="unit.total_debt > 0 ? 'border-red-500' : 'border-green-500'">
                        
                        <div class="flex justify-between items-start border-b border-gray-100 dark:border-gray-700 pb-3 mb-3">
                            <div>
                                <span class="text-xs uppercase font-bold tracking-wider text-gray-400">Unidad</span>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ unit.address }}</h3>
                            </div>
                            <div class="text-right">
                                <span class="text-xs uppercase font-bold tracking-wider text-gray-400">Deuda</span>
                                <div class="text-lg font-bold" :class="unit.total_debt > 0 ? 'text-red-600' : 'text-gray-400'">
                                    ${{ Number(unit.total_debt).toLocaleString('es-MX', {minimumFractionDigits: 2}) }}
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-xs uppercase font-bold tracking-wider text-gray-400">Propietario</span>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ unit.owner_name }}</p>
                            </div>
                            <!-- Espacio para botones de acción futuros -->
                            <!-- <div v-if="unit.total_debt > 0" class="text-amber-500">
                                <i class="pi pi-bell text-xl"></i>
                            </div> -->
                        </div>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="debtors.links.length > 3" class="mt-6 flex justify-center">
                    <div class="flex flex-wrap gap-1 justify-center">
                        <Link v-for="(link, k) in debtors.links" :key="k" 
                              :href="link.url" v-html="link.label"
                              class="px-3 py-1 border rounded text-sm mb-1 transition-colors"
                              :class="link.active 
                                ? 'bg-indigo-600 text-white border-indigo-600' 
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
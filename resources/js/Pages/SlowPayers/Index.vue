<template>
    <AppLayout title="Reporte de Morosidad">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 p-6">
            <div class="max-w-7xl mx-auto">
                
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Reporte de Morosidad</h1>
                        <p class="text-gray-500 dark:text-gray-400">Listado completo de deuda por unidad.</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
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
                            <tr v-for="unit in debtors.data" :key="unit.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
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

                <!-- Paginación -->
                <div v-if="debtors.links.length > 3" class="mt-4 flex justify-center">
                    <div class="flex gap-1">
                        <Link v-for="(link, k) in debtors.links" :key="k" 
                              :href="link.url" v-html="link.label"
                              class="px-3 py-1 border rounded text-sm"
                              :class="link.active ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300'" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    components: { AppLayout, Link },
    props: { debtors: Object }
}
</script>
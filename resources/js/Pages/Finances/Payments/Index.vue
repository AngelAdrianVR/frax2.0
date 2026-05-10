<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

const props = defineProps({
    payments: Object,
    filters: Object
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('payments.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(amount);
};

// Diseño de etiquetas para los métodos de pago
const methodColors = {
    'Transferencia': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    'Efectivo': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    'Tarjeta': 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
    'Cheque': 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
};
</script>

<template>
    <AppLayout title="Ingresos y Pagos">
        <div class="py-12 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado estilo iOS -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 px-4 sm:px-0 gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Ingresos y Pagos</h1>
                        <p class="text-zinc-500 dark:text-zinc-400 mt-1">Historial de transacciones de tu fraccionamiento.</p>
                    </div>
                    
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input v-model="search" type="text" placeholder="Buscar folio o residente..." 
                                   class="w-full pl-10 pr-4 py-2 bg-white dark:bg-zinc-800 border-none rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 dark:text-white" />
                        </div>
                        <Link :href="route('payments.create')" class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm">
                            + Nuevo Pago
                        </Link>
                    </div>
                </div>

                <!-- Tabla Principal -->
                <div class="bg-white dark:bg-zinc-900 shadow-sm border border-zinc-200/60 dark:border-zinc-800 rounded-3xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                            <thead class="bg-zinc-50/50 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Folio / Fecha</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Residente</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Concepto</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Método</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Monto</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ payment.folio }}</div>
                                        <div class="text-xs text-zinc-500 mt-0.5">{{ payment.payment_date }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs mr-3">
                                                {{ payment.resident_name.charAt(0) }}
                                            </div>
                                            <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ payment.resident_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ payment.concept }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span :class="['px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', methodColors[payment.method] || 'bg-zinc-100 text-zinc-800']">
                                            {{ payment.method }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ formatCurrency(payment.amount) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('payments.show', payment.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                            Ver Detalles
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="payments.data.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-zinc-500">
                                        No se encontraron pagos registrados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
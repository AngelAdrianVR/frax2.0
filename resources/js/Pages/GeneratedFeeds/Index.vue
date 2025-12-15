<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    fees: Object,
    stats: Object,
});

// Helper para colores de estatus
const getStatusColor = (status, isOverdue) => {
    if (status === 'Pagado') return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
    if (status === 'Cancelado') return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
    if (isOverdue || status === 'Atrasada') return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
    return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'; // Pendiente/Parcial
};

// Formato de moneda
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
};
</script>

<template>
    <AppLayout title="Mis Cuotas">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Encabezado -->
                <div class="flex flex-col md:flex-row md:justify-between md:items-center px-4 sm:px-0">
                    <div>
                        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                            Mis Cuotas y Pagos
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            Consulta tu historial y saldo pendiente.
                        </p>
                    </div>
                </div>

                <!-- Tarjetas de Resumen (KPIs) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 sm:px-0">
                    <!-- Tarjeta 1: Deuda Total -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4"
                         :class="stats.total_debt > 0 ? 'border-red-500' : 'border-green-500'">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full" :class="stats.total_debt > 0 ? 'bg-red-100 text-red-500' : 'bg-green-100 text-green-500'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Pendiente</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ formatCurrency(stats.total_debt) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 2: Cuotas Pendientes -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Recibos por Pagar</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ stats.pending_count }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 3: Próximo Vencimiento -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Próximo Vencimiento</p>
                                <p class="text-lg font-bold text-gray-800 dark:text-white truncate">{{ stats.next_due_date }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Cuotas (Desktop y Móvil) -->
                <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Historial de Cuotas</h3>
                    </div>

                    <!-- Tabla Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Concepto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Periodo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vencimiento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Monto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pagado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estatus</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="fee in fees.data" :key="fee.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ fee.concept_name }}
                                        <div class="text-xs text-gray-400 font-normal">{{ fee.payment_reference }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ fee.period }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <span :class="{'text-red-500 font-bold': fee.is_overdue}">{{ fee.expiration_date }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-bold">{{ formatCurrency(fee.total_amount) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatCurrency(fee.amount_paid) }}
                                        <div v-if="fee.amount_paid > 0 && fee.balance > 0" class="w-full bg-gray-200 rounded-full h-1.5 mt-1 dark:bg-gray-700">
                                            <div class="bg-blue-600 h-1.5 rounded-full" :style="`width: ${(fee.amount_paid / fee.total_amount) * 100}%`"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                              :class="getStatusColor(fee.status, fee.is_overdue)">
                                            {{ fee.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <!-- Aquí iría el botón de pagar en el futuro -->
                                        <button v-if="fee.balance > 0" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 font-bold">
                                            Pagar
                                        </button>
                                        <span v-else class="text-green-600 dark:text-green-400 flex items-center justify-end gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                            </svg>
                                            Listo
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Lista Móvil (Cards) -->
                    <div class="md:hidden">
                        <div v-for="fee in fees.data" :key="fee.id" class="p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ fee.concept_name }}</h4>
                                    <p class="text-xs text-gray-500">{{ fee.period }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="getStatusColor(fee.status, fee.is_overdue)">
                                    {{ fee.status }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Monto</p>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ formatCurrency(fee.total_amount) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Pendiente</p>
                                    <p class="font-bold text-red-600 dark:text-red-400">{{ formatCurrency(fee.balance) }}</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center mt-2">
                                <p class="text-xs text-gray-500">Vence: <span :class="{'text-red-500 font-bold': fee.is_overdue}">{{ fee.expiration_date }}</span></p>
                                <button v-if="fee.balance > 0" class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg shadow hover:bg-indigo-700">
                                    Pagar Ahora
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación Simple -->
                <div v-if="fees.links.length > 3" class="flex justify-center mt-4">
                    <div class="flex gap-1">
                        <Link v-for="(link, key) in fees.links" :key="key" 
                              :href="link.url" 
                              v-html="link.label"
                              class="px-3 py-1 border rounded text-sm transition-colors"
                              :class="link.active 
                                ? 'bg-indigo-600 text-white border-indigo-600' 
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600'" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    fees: Object,
    stats: Object,
});

// Helper para colores de estatus estilo iOS
const getStatusColor = (status, isOverdue) => {
    if (status === 'Pagado') return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400';
    if (status === 'Cancelado') return 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400';
    if (isOverdue || status === 'Atrasada') return 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400';
    return 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400'; // Pendiente/Parcial
};

// Formato de moneda
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
};
</script>

<template>
    <AppLayout title="Mis Cuotas">
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Encabezado -->
                <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                            Mis Cuotas y Pagos
                        </h2>
                        <p class="mt-1 text-zinc-500 dark:text-zinc-400 text-sm">
                            Consulta tu historial, descarga recibos y mantente al corriente.
                        </p>
                    </div>
                </div>

                <!-- Tarjetas de Resumen (KPIs) Estilo iOS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Deuda Total -->
                    <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-900/50 flex items-center gap-5 transition-all hover:shadow-md">
                        <div class="p-4 rounded-2xl" :class="stats.total_debt > 0 ? 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Saldo Pendiente</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">{{ formatCurrency(stats.total_debt) }}</p>
                        </div>
                    </div>

                    <!-- Cuotas Pendientes -->
                    <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-900/50 flex items-center gap-5 transition-all hover:shadow-md">
                        <div class="p-4 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Recibos por Pagar</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">{{ stats.pending_count }}</p>
                        </div>
                    </div>

                    <!-- Próximo Vencimiento -->
                    <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm border border-zinc-200 dark:border-zinc-900/50 flex items-center gap-5 transition-all hover:shadow-md">
                        <div class="p-4 rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Próximo Vencimiento</p>
                            <p class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight truncate">{{ stats.next_due_date }}</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Cuotas -->
                <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-zinc-200 dark:border-zinc-900/50 overflow-hidden">
                    <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-700/50">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Historial de Cuotas</h3>
                    </div>

                    <!-- Tabla Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
                            <thead class="bg-zinc-50/50 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Concepto</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Vencimiento</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Monto</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Estatus</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700/50">
                                <tr v-for="fee in fees.data" :key="fee.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/20 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ fee.concept_name }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ fee.period }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="{'text-red-600 dark:text-red-400 font-semibold': fee.is_overdue, 'text-zinc-600 dark:text-zinc-400': !fee.is_overdue}">
                                            {{ fee.expiration_date }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ formatCurrency(fee.total_amount) }}</div>
                                        <div class="text-xs text-zinc-500 mt-0.5" v-if="fee.amount_paid > 0">
                                            Restante: {{ formatCurrency(fee.balance) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs font-medium rounded-full" :class="getStatusColor(fee.status, fee.is_overdue)">
                                            {{ fee.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <!-- Botón de Pagar que lleva a la nueva vista -->
                                        <Link v-if="fee.balance > 0" :href="route('fees.pay', fee.id)" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                                            Pagar
                                        </Link>
                                        <span v-else class="inline-flex items-center text-emerald-600 dark:text-emerald-400 font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-1">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                            </svg>
                                            Completado
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Lista Móvil -->
                    <div class="md:hidden divide-y divide-zinc-200 dark:divide-zinc-700/50">
                        <div v-for="fee in fees.data" :key="fee.id" class="p-5 hover:bg-zinc-50 dark:hover:bg-zinc-700/20 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="text-base font-semibold text-zinc-900 dark:text-white">{{ fee.concept_name }}</h4>
                                    <p class="text-xs text-zinc-500">{{ fee.period }}</p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full" :class="getStatusColor(fee.status, fee.is_overdue)">
                                    {{ fee.status }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-end mt-4">
                                <div>
                                    <p class="text-xs text-zinc-500 mb-0.5">Monto Total</p>
                                    <p class="font-bold text-lg text-zinc-900 dark:text-white">{{ formatCurrency(fee.total_amount) }}</p>
                                </div>
                                <div class="text-right">
                                    <Link v-if="fee.balance > 0" :href="route('fees.pay', fee.id)" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                                        Pagar Ahora
                                    </Link>
                                    <span v-else class="text-emerald-600 dark:text-emerald-400 text-sm font-medium flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                        Listo
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="fees.links.length > 3" class="flex justify-center mt-6">
                    <div class="flex gap-2">
                        <Link v-for="(link, key) in fees.links" :key="key" 
                              :href="link.url" 
                              v-html="link.label"
                              class="px-4 py-2 rounded-xl text-sm font-medium transition-colors border"
                              :class="link.active 
                                ? 'bg-zinc-900 text-white border-zinc-900 dark:bg-white dark:text-zinc-900 dark:border-white' 
                                : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700'" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
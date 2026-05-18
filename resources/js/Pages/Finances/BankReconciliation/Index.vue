<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    reconciliations: Object,
    kpis: Object,
    unreconciledPayments: Array,
});

const formatCurrency = (amount) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(amount);

const statusColor = (status) => {
    switch(status) {
        case 'Conciliado': 
        case 'Manual': 
            return 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 border border-green-200 dark:border-green-800';
        case 'Pendiente': 
            return 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800';
        case 'Error': 
            return 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 border border-red-200 dark:border-red-800';
        default: 
            return 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700';
    }
};

// --- Lógica del Modal de Conciliación Manual ---
const showModal = ref(false);
const selectedRecord = ref(null);

const form = useForm({
    payment_id: '',
});

const openManualMatch = (record) => {
    selectedRecord.value = record;
    form.payment_id = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedRecord.value = null;
    form.reset();
};

const submitMatch = () => {
    form.put(route('bank-reconciliations.update', selectedRecord.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <AppLayout title="Conciliación Bancaria">
        <div class="py-12 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Encabezado -->
                <div class="flex flex-col md:flex-row md:items-center justify-between px-4 sm:px-0 gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white tracking-tight">Conciliación Bancaria</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Cruce automático y manual entre movimientos del banco y pagos del sistema.</p>
                    </div>
                    <div class="flex gap-3">
                        <button class="inline-flex items-center px-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl font-medium text-sm text-zinc-700 dark:text-zinc-300 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Importar Estado
                        </button>
                    </div>
                </div>

                <!-- KPIs Cards (Estilo iOS) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-4 sm:px-0">
                    <div class="bg-white dark:bg-zinc-800/80 backdrop-blur-xl border border-zinc-200 dark:border-zinc-700/50 rounded-2xl p-5 shadow-sm">
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Conciliado</p>
                        <p class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(kpis.total_reconciled) }}</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-800/80 backdrop-blur-xl border border-zinc-200 dark:border-zinc-700/50 rounded-2xl p-5 shadow-sm">
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Pendiente de Cruce</p>
                        <p class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ formatCurrency(kpis.total_pending) }}</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-800/80 backdrop-blur-xl border border-zinc-200 dark:border-zinc-700/50 rounded-2xl p-5 shadow-sm">
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Errores / Discrepancias</p>
                        <p class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400">{{ kpis.count_errors }} registros</p>
                    </div>
                </div>

                <!-- Tabla Principal -->
                <div class="bg-white dark:bg-zinc-800/80 backdrop-blur-xl shadow-sm border border-zinc-200 dark:border-zinc-700/50 sm:rounded-3xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700/50">
                            <thead class="bg-zinc-50/50 dark:bg-zinc-800/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Ref. Bancaria</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Fecha</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Monto</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Estatus</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Folio Sistema</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700/50 bg-white dark:bg-transparent">
                                <tr v-for="item in reconciliations.data" :key="item.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/20 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-zinc-900 dark:text-zinc-200">
                                        {{ item.bank_reference }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ item.transaction_date }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-zinc-900 dark:text-white">
                                        {{ formatCurrency(item.amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2.5 py-1 text-xs rounded-full font-medium" :class="statusColor(item.status)">
                                            {{ item.status }}
                                        </span>
                                        <div v-if="item.error_message" class="text-xs text-red-500 mt-1 truncate max-w-xs" :title="item.error_message">
                                            {{ item.error_message }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span v-if="item.matched_payment_folio" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 font-medium bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded-md">
                                            #{{ item.matched_payment_folio }}
                                        </span>
                                        <span v-else class="text-zinc-400 dark:text-zinc-500">---</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button 
                                            v-if="item.status === 'Pendiente' || item.status === 'Error'"
                                            @click="openManualMatch(item)"
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors">
                                            Conciliar
                                        </button>
                                        <span v-else class="text-zinc-300 dark:text-zinc-600 select-none">Listo</span>
                                    </td>
                                </tr>
                                <tr v-if="reconciliations.data.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                        No hay movimientos bancarios registrados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Conciliación Manual (Estilo iOS) -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-[#1C1C1E] rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all border border-zinc-100 dark:border-zinc-800">
                <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Conciliar Movimiento</h3>
                    <button @click="closeModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form @submit.prevent="submitMatch" class="p-6">
                    <!-- Resumen del Banco -->
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl p-4 mb-6">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider font-semibold mb-1">Dato del Banco</p>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-sm font-mono text-zinc-900 dark:text-white">{{ selectedRecord?.bank_reference }}</p>
                                <p class="text-sm text-zinc-500 mt-0.5">{{ selectedRecord?.transaction_date }}</p>
                            </div>
                            <p class="text-lg font-bold text-zinc-900 dark:text-white">{{ formatCurrency(selectedRecord?.amount) }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Seleccionar Pago del Sistema</label>
                        <select 
                            v-model="form.payment_id"
                            class="w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition-colors"
                            required
                        >
                            <option value="" disabled>Seleccione un pago para cruzar...</option>
                            <option v-for="payment in unreconciledPayments" :key="payment.id" :value="payment.id">
                                Folio #{{ payment.transaction_folio }} - {{ formatCurrency(payment.amount) }} ({{ payment.payment_date }})
                            </option>
                        </select>
                        <p v-if="form.errors.payment_id" class="mt-2 text-sm text-red-600">{{ form.errors.payment_id }}</p>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" @click="closeModal" class="px-5 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500/20 disabled:opacity-50 transition-colors">
                            <span v-if="form.processing">Guardando...</span>
                            <span v-else>Aplicar Conciliación</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
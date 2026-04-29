<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    reconciliations: Object
});

const formatCurrency = (amount) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(amount);

const statusColor = (status) => {
    switch(status) {
        case 'Conciliado': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'Pendiente': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'Error': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        default: return 'bg-zinc-100 text-zinc-800';
    }
};
</script>

<template>
    <AppLayout title="Conciliación Bancaria">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 px-4 sm:px-0">
                    <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Conciliación Bancaria</h1>
                    <p class="text-zinc-500 dark:text-zinc-400">Estado de cruce entre banco y sistema.</p>
                </div>

                <div class="bg-white dark:bg-zinc-800 shadow-xl rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Ref. Bancaria</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Folio Sistema</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Estatus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Notas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            <tr v-for="item in reconciliations.data" :key="item.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-zinc-700 dark:text-zinc-300">
                                    {{ item.bank_reference }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 dark:text-indigo-400">
                                    {{ item.matched_payment_folio || '---' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-zinc-900 dark:text-white">
                                    {{ formatCurrency(item.amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ item.transaction_date }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold" :class="statusColor(item.status)">
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-red-500">
                                    {{ item.error_message }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
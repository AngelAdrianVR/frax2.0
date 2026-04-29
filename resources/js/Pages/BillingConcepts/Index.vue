b<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    concepts: Array
});

const formatCurrency = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val);
</script>

<template>
    <AppLayout title="Conceptos de Cobro">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6 px-4 sm:px-0">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Conceptos de Cobro</h1>
                        <p class="text-zinc-500 dark:text-zinc-400">Configuración de cuotas (Mantenimiento, Extraordinarias, etc).</p>
                    </div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow">
                        + Nuevo Concepto
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="concept in concepts" :key="concept.id" class="bg-white dark:bg-zinc-800 rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-bold text-zinc-800 dark:text-white">{{ concept.name }}</h3>
                            <span class="text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 px-2 py-1 rounded">
                                {{ concept.recurrence_type }}
                            </span>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 uppercase">Monto Base</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ formatCurrency(concept.base_amount) }}</p>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                            <span v-if="concept.slow_payers_apply" class="flex items-center text-amber-600 dark:text-amber-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Aplica Morosidad
                            </span>
                            <span v-else class="text-green-600">Sin recargos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
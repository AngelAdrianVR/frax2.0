<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    payment: Object
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(amount);
};

const deleteForm = useForm({});
const confirmDelete = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este pago? Esta acción afectará los saldos y no se puede deshacer.')) {
        deleteForm.delete(route('payments.destroy', props.payment.id));
    }
};
</script>

<template>
    <AppLayout title="Detalle de Pago">
        <div class="py-12 min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Navegación -->
                <div class="mb-6 flex justify-between items-center">
                    <Link :href="route('payments.index')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                        &larr; Volver a Pagos
                    </Link>
                    <div class="flex gap-2">
                        <Link :href="route('payments.edit', payment.id)" class="text-sm px-4 py-2 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 transition-colors">
                            Editar
                        </Link>
                        <button @click="confirmDelete" class="text-sm px-4 py-2 bg-red-50 text-red-600 font-medium rounded-lg shadow-sm border border-red-100 hover:bg-red-100 transition-colors">
                            Eliminar
                        </button>
                    </div>
                </div>

                <!-- Recibo (Diseño elegante tipo Ticket iOS) -->
                <div class="bg-white dark:bg-zinc-900 shadow-xl border border-zinc-100 dark:border-zinc-800 rounded-[2rem] overflow-hidden relative">
                    
                    <!-- Decal superior -->
                    <div class="h-4 w-full bg-indigo-600"></div>
                    
                    <div class="p-8 sm:p-10">
                        <div class="text-center mb-10">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-500 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h2 class="text-zinc-500 dark:text-zinc-400 text-sm font-semibold uppercase tracking-wider">Pago Recibido</h2>
                            <p class="text-4xl font-black text-zinc-900 dark:text-white mt-2 tracking-tight">{{ formatCurrency(payment.amount) }}</p>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-dashed border-zinc-200 dark:border-zinc-700 my-8"></div>

                        <!-- Detalles -->
                        <div class="space-y-6">
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-500 dark:text-zinc-400 font-medium">Residente</span>
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ payment.resident ? payment.resident.name : 'Usuario Eliminado' }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-500 dark:text-zinc-400 font-medium">Concepto</span>
                                <span class="font-semibold text-zinc-900 dark:text-white text-right">{{ payment.concept ? payment.concept.name : 'N/A' }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-zinc-500 dark:text-zinc-400 font-medium">Fecha de Pago</span>
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ payment.payment_date }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-zinc-500 dark:text-zinc-400 font-medium">Método de Pago</span>
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ payment.payment_method }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-zinc-500 dark:text-zinc-400 font-medium">Folio de Transacción</span>
                                <span class="font-mono text-sm font-semibold bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded text-zinc-700 dark:text-zinc-300">{{ payment.transaction_folio }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-zinc-500 dark:text-zinc-400 font-medium">Registrado en Sistema</span>
                                <span class="text-sm font-semibold text-zinc-500 dark:text-zinc-400">{{ payment.created_at }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón imprimir recibo (decorativo) -->
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 p-6 text-center border-t border-zinc-100 dark:border-zinc-800">
                        <button onclick="window.print()" class="text-indigo-600 dark:text-indigo-400 font-semibold text-sm hover:underline flex items-center justify-center gap-2 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Imprimir Recibo
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
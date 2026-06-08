<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    fee: Object,
});

// Formulario para registrar el comprobante de pago
const form = useForm({
    payment_method: 'transferencia',
    reference: '',
    amount: props.fee.balance,
    receipt_file: null,
});

const isDragging = ref(false);

const handleFileUpload = (e) => {
    form.receipt_file = e.target.files[0];
};

const handleDrop = (e) => {
    isDragging.value = false;
    if (e.dataTransfer.files.length > 0) {
        form.receipt_file = e.dataTransfer.files[0];
    }
};

const submitPayment = () => {
    form.post(route('fees.process', props.fee.id), {
        preserveScroll: true,
        onSuccess: () => {
            // El backend redirigirá con mensaje de éxito
        }
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
};
</script>

<template>
    <AppLayout title="Procesar Pago">
        <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Botón Volver -->
                <Link :href="route('fees.index')" class="inline-flex items-center text-sm font-medium text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 mb-6 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-1">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                    Volver a mis cuotas
                </Link>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <!-- Columna Izquierda: Detalles del Recibo (Checkout) -->
                    <div class="lg:col-span-5">
                        <div class="bg-white dark:bg-zinc-900 rounded-3xl p-8 shadow-sm border border-zinc-200 dark:border-zinc-900/50 relative overflow-hidden">
                            <!-- Patrón decorativo de recibo -->
                            <div class="absolute top-0 left-0 right-0 h-2 flex justify-around">
                                <div v-for="i in 20" :key="i" class="w-2 h-2 bg-zinc-50 dark:bg-zinc-900 rounded-b-full"></div>
                            </div>

                            <div class="text-center mt-4 mb-8">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">{{ fee.concept_name }}</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Ref: {{ fee.payment_reference }}</p>
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-3 border-b border-zinc-100 dark:border-zinc-700/50">
                                    <span class="text-zinc-500 dark:text-zinc-400 text-sm">Periodo</span>
                                    <span class="font-medium text-zinc-900 dark:text-white text-sm">{{ fee.period }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-zinc-100 dark:border-zinc-700/50">
                                    <span class="text-zinc-500 dark:text-zinc-400 text-sm">Vencimiento</span>
                                    <span class="font-medium text-sm" :class="{'text-red-600 dark:text-red-400 font-bold': fee.is_overdue, 'text-zinc-900 dark:text-white': !fee.is_overdue}">
                                        {{ fee.expiration_date }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-zinc-100 dark:border-zinc-700/50">
                                    <span class="text-zinc-500 dark:text-zinc-400 text-sm">Monto Total</span>
                                    <span class="font-medium text-zinc-900 dark:text-white text-sm">{{ formatCurrency(fee.total_amount) }}</span>
                                </div>
                                <div v-if="fee.amount_paid > 0" class="flex justify-between items-center py-3 border-b border-zinc-100 dark:border-zinc-700/50">
                                    <span class="text-emerald-600 dark:text-emerald-400 text-sm">Abonado</span>
                                    <span class="font-medium text-emerald-600 dark:text-emerald-400 text-sm">- {{ formatCurrency(fee.amount_paid) }}</span>
                                </div>
                            </div>

                            <div class="mt-8 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-2xl flex justify-between items-center">
                                <span class="font-semibold text-zinc-900 dark:text-white">Total a Pagar</span>
                                <span class="text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">{{ formatCurrency(fee.balance) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Método de Pago y Formulario -->
                    <div class="lg:col-span-7">
                        <div class="bg-white dark:bg-zinc-900 rounded-3xl p-8 shadow-sm border border-zinc-200 dark:border-zinc-900/50">
                            <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-6">Método de Pago</h3>
                            
                            <!-- Tabs de Método de Pago -->
                            <div class="grid grid-cols-2 gap-4 mb-8">
                                <button type="button" 
                                        @click="form.payment_method = 'transferencia'"
                                        class="flex flex-col items-center justify-center p-4 rounded-2xl border-2 transition-all"
                                        :class="form.payment_method === 'transferencia' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'border-zinc-200 dark:border-zinc-700 text-zinc-500 hover:border-zinc-300 dark:hover:border-zinc-600'">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mb-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                                    </svg>
                                    <span class="font-medium text-sm">Transferencia</span>
                                </button>
                                
                                <!-- Botón Tarjeta (Deshabilitado visualmente para MVP o listo para conectar a pasarela) -->
                                <button type="button" 
                                        @click="form.payment_method = 'tarjeta'"
                                        class="flex flex-col items-center justify-center p-4 rounded-2xl border-2 transition-all opacity-50 cursor-not-allowed border-zinc-200 dark:border-zinc-700 text-zinc-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mb-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                    </svg>
                                    <span class="font-medium text-sm">Tarjeta (Próximamente)</span>
                                </button>
                            </div>

                            <!-- Formulario de Transferencia -->
                            <form @submit.prevent="submitPayment" v-if="form.payment_method === 'transferencia'">
                                <div class="bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl p-5 mb-6 text-sm text-indigo-800 dark:text-indigo-300">
                                    <p class="font-bold mb-2">Instrucciones:</p>
                                    <p>Realiza la transferencia por la cantidad exacta a la CLABE <strong>012345678901234567</strong> del banco <strong>BBVA</strong> a nombre del Condominio.</p>
                                </div>

                                <div class="space-y-6">
                                    <!-- Monto a Pagar -->
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Monto a pagar</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <span class="text-zinc-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" step="0.01" v-model="form.amount"
                                                   class="pl-8 block w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                                        </div>
                                        <div v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</div>
                                    </div>

                                    <!-- Referencia/Concepto -->
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Concepto o Referencia usado en el banco</label>
                                        <input type="text" v-model="form.reference" placeholder="Ej. Pago Mantenimiento Casa 5"
                                               class="block w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                                        <div v-if="form.errors.reference" class="text-red-500 text-xs mt-1">{{ form.errors.reference }}</div>
                                    </div>

                                    <!-- Subir Comprobante (Drag & Drop iOS Style) -->
                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Comprobante de Pago</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-2xl transition-colors"
                                             :class="isDragging ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10' : 'border-zinc-300 dark:border-zinc-600'"
                                             @dragover.prevent="isDragging = true"
                                             @dragleave.prevent="isDragging = false"
                                             @drop.prevent="handleDrop">
                                            <div class="space-y-2 text-center">
                                                <svg v-if="!form.receipt_file" class="mx-auto h-12 w-12 text-zinc-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                
                                                <div class="flex text-sm text-zinc-600 dark:text-zinc-400 justify-center">
                                                    <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-zinc-800 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span v-if="!form.receipt_file">Sube un archivo</span>
                                                        <span v-else>Cambiar archivo</span>
                                                        <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="handleFileUpload" accept="image/*,.pdf" />
                                                    </label>
                                                    <p class="pl-1" v-if="!form.receipt_file">o arrastra y suelta</p>
                                                </div>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-500" v-if="!form.receipt_file">
                                                    PNG, JPG, PDF hasta 5MB
                                                </p>
                                                <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400" v-else>
                                                    {{ form.receipt_file.name }}
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="form.errors.receipt_file" class="text-red-500 text-xs mt-1">{{ form.errors.receipt_file }}</div>
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <button type="submit" :disabled="form.processing" 
                                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-colors">
                                        <span v-if="form.processing">Procesando...</span>
                                        <span v-else>Enviar Comprobante de Pago</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
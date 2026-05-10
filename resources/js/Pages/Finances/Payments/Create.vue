<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    users: Array,
    concepts: Array
});

const form = useForm({
    amount: '',
    payment_method: 'Transferencia',
    payment_date: new Date().toISOString().slice(0, 16), // Formato YYYY-MM-DDTHH:mm
    transaction_folio: '',
    user_id: '',
    billing_concept_id: ''
});

const submit = () => {
    form.post(route('payments.store'));
};
</script>

<template>
    <AppLayout title="Registrar Pago">
        <div class="py-12 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6">
                    <Link :href="route('payments.index')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                        &larr; Volver a Pagos
                    </Link>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white mt-2">Registrar Nuevo Pago</h1>
                    <p class="text-zinc-500 mt-1">Ingresa los detalles de la transacción recibida.</p>
                </div>

                <div class="bg-white dark:bg-zinc-900 shadow-sm border border-zinc-200/60 dark:border-zinc-800 rounded-3xl overflow-hidden p-6 sm:p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Residente y Concepto -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Residente (Obligatorio)</label>
                                <select v-model="form.user_id" required
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                    <option value="" disabled>Selecciona un residente...</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                                <div v-if="form.errors.user_id" class="text-red-500 text-xs mt-1">{{ form.errors.user_id }}</div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Concepto de Cobro</label>
                                <select v-model="form.billing_concept_id"
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                    <option value="">Abono Libre / General</option>
                                    <option v-for="concept in concepts" :key="concept.id" :value="concept.id">{{ concept.name }}</option>
                                </select>
                                <div v-if="form.errors.billing_concept_id" class="text-red-500 text-xs mt-1">{{ form.errors.billing_concept_id }}</div>
                            </div>
                        </div>

                        <!-- Monto y Método -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Monto ($ MXN)</label>
                                <input v-model="form.amount" type="number" step="0.01" min="0.01" required placeholder="0.00"
                                       class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" />
                                <div v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Método de Pago</label>
                                <select v-model="form.payment_method" required
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                    <option value="Transferencia">Transferencia Bancaria</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta (Terminal)</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                                <div v-if="form.errors.payment_method" class="text-red-500 text-xs mt-1">{{ form.errors.payment_method }}</div>
                            </div>
                        </div>

                        <!-- Fecha y Folio -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Fecha y Hora del Pago</label>
                                <input v-model="form.payment_date" type="datetime-local" required
                                       class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" />
                                <div v-if="form.errors.payment_date" class="text-red-500 text-xs mt-1">{{ form.errors.payment_date }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Folio (Opcional)</label>
                                <input v-model="form.transaction_folio" type="text" placeholder="Ej. SPEI-12345"
                                       class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" />
                                <p class="text-xs text-zinc-500 mt-1">Déjalo vacío para auto-generar uno.</p>
                                <div v-if="form.errors.transaction_folio" class="text-red-500 text-xs mt-1">{{ form.errors.transaction_folio }}</div>
                            </div>
                        </div>

                        <!-- Botón Submit -->
                        <div class="pt-4 flex justify-end">
                            <button type="submit" :disabled="form.processing"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl shadow-md transition-colors disabled:opacity-50">
                                {{ form.processing ? 'Registrando...' : 'Guardar Pago' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
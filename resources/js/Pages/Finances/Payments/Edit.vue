<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    payment: Object,
    users: Array,
    concepts: Array
});

const form = useForm({
    amount: props.payment.amount,
    payment_method: props.payment.payment_method,
    payment_date: props.payment.payment_date,
    transaction_folio: props.payment.transaction_folio,
    user_id: props.payment.user_id,
    billing_concept_id: props.payment.billing_concept_id || ''
});

const submit = () => {
    form.put(route('payments.update', props.payment.id));
};
</script>

<template>
    <AppLayout title="Editar Pago">
        <div class="py-12 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6">
                    <Link :href="route('payments.show', payment.id)" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                        &larr; Volver a Detalles
                    </Link>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white mt-2">Editar Pago</h1>
                    <p class="text-zinc-500 mt-1">Modificando la transacción {{ payment.transaction_folio }}.</p>
                </div>

                <div class="bg-white dark:bg-zinc-900 shadow-sm border border-zinc-200/60 dark:border-zinc-800 rounded-3xl overflow-hidden p-6 sm:p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Residente</label>
                                <select v-model="form.user_id" required
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Concepto de Cobro</label>
                                <select v-model="form.billing_concept_id"
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                    <option value="">Abono Libre / General</option>
                                    <option v-for="concept in concepts" :key="concept.id" :value="concept.id">{{ concept.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Monto ($ MXN)</label>
                                <input v-model="form.amount" type="number" step="0.01" min="0.01" required
                                       class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" />
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
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Fecha y Hora</label>
                                <input v-model="form.payment_date" type="datetime-local" required
                                       class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Folio (Opcional)</label>
                                <input v-model="form.transaction_folio" type="text"
                                       class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" />
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <Link :href="route('payments.show', payment.id)" class="px-6 py-3 font-semibold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-xl transition-colors">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl shadow-md transition-colors disabled:opacity-50">
                                {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
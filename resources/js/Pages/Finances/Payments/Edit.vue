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
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6">
                    <Link :href="route('payments.show', payment.id)" class="text-sm text-zinc-400 hover:text-zinc-200 font-medium flex items-center gap-1 transition-colors">
                        &larr; Volver a Detalles
                    </Link>
                    <h1 class="text-lg font-medium text-zinc-100 tracking-tight mt-2">Editar Pago</h1>
                    <p class="text-sm text-zinc-400 mt-1">Modificando la transacción {{ payment.transaction_folio }}.</p>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden p-6 sm:p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Residente</label>
                                <select v-model="form.user_id" required
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600">
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                            </div>
                            
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Concepto de Cobro</label>
                                <select v-model="form.billing_concept_id"
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600">
                                    <option value="">Abono Libre / General</option>
                                    <option v-for="concept in concepts" :key="concept.id" :value="concept.id">{{ concept.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Monto ($ MXN)</label>
                                <input v-model="form.amount" type="number" step="0.01" min="0.01" required
                                       class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Método de Pago</label>
                                <select v-model="form.payment_method" required
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600">
                                    <option value="Transferencia">Transferencia Bancaria</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta (Terminal)</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Fecha y Hora</label>
                                <input v-model="form.payment_date" type="datetime-local" required
                                       class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Folio (Opcional)</label>
                                <input v-model="form.transaction_folio" type="text"
                                       class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-800/60">
                            <Link :href="route('payments.show', payment.id)" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing"
                                    class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50">
                                {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
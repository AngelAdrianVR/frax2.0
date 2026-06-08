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
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6">
                    <Link :href="route('payments.index')" class="text-sm text-zinc-400 hover:text-zinc-200 font-medium flex items-center gap-1 transition-colors">
                        &larr; Volver a Pagos
                    </Link>
                    <h1 class="text-lg font-medium text-zinc-100 tracking-tight mt-2">Registrar Nuevo Pago</h1>
                    <p class="text-sm text-zinc-400 mt-1">Ingresa los detalles de la transacción recibida.</p>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden p-6 sm:p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Residente y Concepto -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Residente (Obligatorio)</label>
                                <select v-model="form.user_id" required
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600">
                                    <option value="" disabled>Selecciona un residente...</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                                <p v-if="form.errors.user_id" class="text-sm text-red-400">{{ form.errors.user_id }}</p>
                            </div>
                            
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Concepto de Cobro</label>
                                <select v-model="form.billing_concept_id"
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600">
                                    <option value="">Abono Libre / General</option>
                                    <option v-for="concept in concepts" :key="concept.id" :value="concept.id">{{ concept.name }}</option>
                                </select>
                                <p v-if="form.errors.billing_concept_id" class="text-sm text-red-400">{{ form.errors.billing_concept_id }}</p>
                            </div>
                        </div>

                        <!-- Monto y Método -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Monto ($ MXN)</label>
                                <input v-model="form.amount" type="number" step="0.01" min="0.01" required placeholder="0.00"
                                       class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                <p v-if="form.errors.amount" class="text-sm text-red-400">{{ form.errors.amount }}</p>
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
                                <p v-if="form.errors.payment_method" class="text-sm text-red-400">{{ form.errors.payment_method }}</p>
                            </div>
                        </div>

                        <!-- Fecha y Folio -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Fecha y Hora del Pago</label>
                                <input v-model="form.payment_date" type="datetime-local" required
                                       class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                <p v-if="form.errors.payment_date" class="text-sm text-red-400">{{ form.errors.payment_date }}</p>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Folio (Opcional)</label>
                                <input v-model="form.transaction_folio" type="text" placeholder="Ej. SPEI-12345"
                                       class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                <p class="text-xs text-zinc-500 mt-0.5">Déjalo vacío para auto-generar uno.</p>
                                <p v-if="form.errors.transaction_folio" class="text-sm text-red-400">{{ form.errors.transaction_folio }}</p>
                            </div>
                        </div>

                        <!-- Botón Submit -->
                        <div class="flex justify-end pt-4 border-t border-zinc-800/60">
                            <button type="submit" :disabled="form.processing"
                                    class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50">
                                {{ form.processing ? 'Registrando...' : 'Guardar Pago' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
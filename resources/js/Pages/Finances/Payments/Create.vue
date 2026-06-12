<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import Select from 'primevue/select';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';


const props = defineProps({
    users: Array,
    concepts: Array
});

const form = useForm({
    amount: '',
    payment_method: 'Transferencia',
    payment_date: new Date(), // Objeto Date nativo para evitar errores de parseo
    transaction_folio: '',
    user_id: '',
    billing_concept_id: ''
});

const submit = () => {
    form.post(route('payments.store'), {
        preserveScroll: true,
        preserveState: true,
    });
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
                    <h1 class="text-lg font-medium text-zinc-100 tracking-tight mt-2 m-0">Registrar nuevo pago</h1>
                    <p class="text-sm text-zinc-400 mt-1">Ingresa los detalles de la transacción recibida.</p>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden p-6 sm:p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Residente y Concepto -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Residente (obligatorio)</label>
                                <Select v-model="form.user_id" :options="users" optionLabel="name" optionValue="id" placeholder="Selecciona un residente..." class="w-full" />
                                <p v-if="form.errors.user_id" class="text-sm text-red-400">{{ form.errors.user_id }}</p>
                            </div>
                            
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Concepto de cobro</label>
                                <Select v-model="form.billing_concept_id" :options="concepts" optionLabel="name" optionValue="id" placeholder="Abono Libre / General" :showClear="true" class="w-full" />
                                <p v-if="form.errors.billing_concept_id" class="text-sm text-red-400">{{ form.errors.billing_concept_id }}</p>
                            </div>
                        </div>

                        <!-- Monto y Método -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Monto ($ MXN)</label>
                                <InputNumber v-model="form.amount" :min="0.01" :maxFractionDigits="2" :currency="'MXN'" :locale="'es-MX'" mode="currency" placeholder="$0.00" class="w-full" />
                                <p v-if="form.errors.amount" class="text-sm text-red-400">{{ form.errors.amount }}</p>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Método de pago</label>
                                <Select v-model="form.payment_method" :options="['Transferencia', 'Efectivo', 'Tarjeta', 'Cheque']" placeholder="Selecciona un método" class="w-full" />
                                <p v-if="form.errors.payment_method" class="text-sm text-red-400">{{ form.errors.payment_method }}</p>
                            </div>
                        </div>

                        <!-- Fecha y Folio -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Fecha y hora del pago</label>
                                <DatePicker v-model="form.payment_date" 
                                dateFormat="dd/mm/yy" 
                                showTime 
                                hourFormat="24" 
                                showIcon 
                                fluid 
                                iconDisplay="input" 
                                required />
                                <p v-if="form.errors.payment_date" class="text-sm text-red-400">{{ form.errors.payment_date }}</p>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Folio (opcional)</label>
                                <InputText v-model="form.transaction_folio" placeholder="Ej. SPEI-12345" class="w-full" />
                                <p class="text-xs text-zinc-500 mt-0.5">Déjalo vacío para auto-generar uno.</p>
                                <p v-if="form.errors.transaction_folio" class="text-sm text-red-400">{{ form.errors.transaction_folio }}</p>
                            </div>
                        </div>

                        <!-- Botón Submit -->
                        <div class="flex justify-end pt-4 border-t border-zinc-800/60">
                            <Button type="submit" :label="form.processing ? 'Registrando...' : 'Guardar pago'" :loading="form.processing" class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
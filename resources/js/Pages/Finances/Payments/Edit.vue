<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import Select from 'primevue/select';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

const props = defineProps({
    payment: Object,
    users: Array,
    concepts: Array
});

const form = useForm({
    amount: props.payment.amount,
    payment_method: props.payment.payment_method,
    payment_date: new Date(),
    transaction_folio: props.payment.transaction_folio,
    user_id: props.payment.user_id,
    billing_concept_id: props.payment.billing_concept_id || ''
});

const paymentMethodOptions = [
    { label: 'Transferencia bancaria', value: 'Transferencia' },
    { label: 'Efectivo', value: 'Efectivo' },
    { label: 'Tarjeta (Terminal)', value: 'Tarjeta' },
    { label: 'Cheque', value: 'Cheque' },
];

const submit = () => {
    form.put(route('payments.update', props.payment.id), {
        preserveScroll: true,
        preserveState: true,
    });
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
                    <h1 class="text-lg font-medium text-zinc-100 tracking-tight mt-2 m-0">Editar pago</h1>
                    <p class="text-sm text-zinc-400 mt-1">Modificando la transacción {{ payment.transaction_folio }}.</p>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden p-6 sm:p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Residente</label>
                                <Select v-model="form.user_id" :options="users" optionLabel="name" optionValue="id" placeholder="Selecciona residente" class="w-full !rounded-xl !text-[13px]" pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                            </div>
                            
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Concepto de cobro</label>
                                <Select v-model="form.billing_concept_id" :options="concepts" optionLabel="name" optionValue="id" placeholder="Abono libre / General" :showClear="true" class="w-full !rounded-xl !text-[13px]" pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Monto ($ MXN)</label>
                                <InputText v-model="form.amount" type="number" step="0.01" min="0.01" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Método de pago</label>
                                <Select v-model="form.payment_method" :options="paymentMethodOptions" optionLabel="label" optionValue="value" placeholder="Selecciona método" class="w-full !rounded-xl !text-[13px]" pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Fecha y hora</label>
                                <DatePicker v-model="form.payment_date" dateFormat="dd/mm/yy" showTime hourFormat="24" showIcon fluid iconDisplay="input" />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Folio (opcional)</label>
                                <InputText v-model="form.transaction_folio" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-800/60">
                            <Link :href="route('payments.show', payment.id)" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                                Cancelar
                            </Link>
                            <Button type="submit" :label="form.processing ? 'Guardando...' : 'Guardar cambios'" :loading="form.processing" class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
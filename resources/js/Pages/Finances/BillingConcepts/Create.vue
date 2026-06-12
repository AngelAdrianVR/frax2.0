<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

const form = useForm({
    name: '',
    base_amount: '',
    recurrence_type: 'Mensual',
    slow_payers_apply: false,
});

const recurrenceOptions = [
    { label: 'Semanal', value: 'Semanal' },
    { label: 'Quincenal', value: 'Quincenal' },
    { label: 'Mensual', value: 'Mensual' },
    { label: 'Bimestral', value: 'Bimestral' },
    { label: 'Anual', value: 'Anual' },
    { label: 'Pago único', value: 'Pago unico' },
];

const submit = () => {
    form.post(route('billing-concepts.store'), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AppLayout title="Nuevo Concepto de Cobro">
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 flex items-center gap-4">
                    <Link :href="route('billing-concepts.index')" class="w-10 h-10 bg-zinc-800 border border-zinc-700/50 rounded-full flex items-center justify-center text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight m-0">Crear concepto</h1>
                        <p class="text-sm text-zinc-400">Define una nueva cuota para tu fraccionamiento.</p>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    <form @submit.prevent="submit" class="p-8">
                        <div class="space-y-6">
                            
                            <!-- Nombre -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Nombre del concepto</label>
                                <InputText v-model="form.name" placeholder="Ej. Cuota de Mantenimiento" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500" :class="{ 'p-invalid': form.errors.name }" />
                                <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                            </div>

                            <!-- Monto y Recurrencia -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Monto base (MXN)</label>
                                    <InputText v-model="form.base_amount" type="number" step="0.01" min="0" placeholder="0.00" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500" />
                                    <p v-if="form.errors.base_amount" class="text-sm text-red-400">{{ form.errors.base_amount }}</p>
                                </div>

                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Frecuencia de cobro</label>
                                    <Select v-model="form.recurrence_type" :options="recurrenceOptions" optionLabel="label" optionValue="value" placeholder="Frecuencia" class="w-full !rounded-xl !text-[13px]" pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                    <p v-if="form.errors.recurrence_type" class="text-sm text-red-400">{{ form.errors.recurrence_type }}</p>
                                </div>
                            </div>

                            <!-- Opciones Adicionales -->
                            <div class="pt-4 border-t border-zinc-800/60">
                                <label class="flex items-center gap-4 cursor-pointer p-4 rounded-xl border border-zinc-700/40 bg-zinc-800/30 hover:bg-zinc-800/50 transition-colors">
                                    <div class="relative flex items-center">
                                        <input v-model="form.slow_payers_apply" type="checkbox" class="sr-only peer" />
                                        <div class="w-11 h-6 bg-zinc-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0E63B1]"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-zinc-200 text-sm">Aplica recargos por morosidad</p>
                                        <p class="text-xs text-zinc-400 mt-0.5">Si se activa, el sistema podrá sumar multas automáticas si el residente se atrasa en este concepto.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="mt-8 pt-4 border-t border-zinc-800/60 flex justify-end gap-3">
                            <Link :href="route('billing-concepts.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                                Cancelar
                            </Link>
                            <Button type="submit" :label="form.processing ? 'Guardando...' : 'Crear concepto'" :loading="form.processing" class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
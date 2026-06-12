<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';

const props = defineProps({
    visit: { type: Object, required: true }
});

const form = useForm({
    name: props.visit.name,
    reason: props.visit.reason,
    access_type: props.visit.access_type,
    expiration_date: props.visit.expiration_date ? props.visit.expiration_date.substring(0, 16) : '',
    status: props.visit.status,
});

const accessTypeOptions = [
    { label: '🚶‍♂️ Peatonal', value: 'Peatonal' },
    { label: '🚗 Vehicular', value: 'Vehicular' },
];

const submit = () => {
    form.put(route('visits.update', props.visit.id), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AppLayout title="Editar Visita">
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado -->
                <div class="mb-6 flex items-center gap-3 px-4 sm:px-0">
                    <Link :href="route('visits.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left text-sm"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight m-0">Editar invitación</h1>
                        <p class="text-sm text-zinc-400">Modifica los datos del pase de acceso.</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">
                    
                    <!-- Tarjeta de Estatus -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="p-6">
                            <label class="block text-xs font-medium text-zinc-400 mb-3">Estatus del pase</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-200"
                                    :class="form.status === 'Pendiente' ? 'border-amber-500/60 bg-amber-500/10' : 'border-zinc-700/40 hover:bg-zinc-800/50'">
                                    <input type="radio" v-model="form.status" value="Pendiente" class="sr-only" />
                                    <i class="pi pi-clock text-amber-400 mb-1"></i>
                                    <span class="text-xs font-medium text-zinc-300">Pendiente</span>
                                </label>
                                <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-200"
                                    :class="form.status === 'Ingresado' ? 'border-green-500/60 bg-green-500/10' : 'border-zinc-700/40 hover:bg-zinc-800/50'">
                                    <input type="radio" v-model="form.status" value="Ingresado" class="sr-only" />
                                    <i class="pi pi-check-circle text-green-400 mb-1"></i>
                                    <span class="text-xs font-medium text-zinc-300">Ingresado</span>
                                </label>
                                <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-200"
                                    :class="form.status === 'Cancelado' ? 'border-red-500/60 bg-red-500/10' : 'border-zinc-700/40 hover:bg-zinc-800/50'">
                                    <input type="radio" v-model="form.status" value="Cancelado" class="sr-only" />
                                    <i class="pi pi-times-circle text-red-400 mb-1"></i>
                                    <span class="text-xs font-medium text-zinc-300">Cancelado</span>
                                </label>
                                <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-200"
                                    :class="form.status === 'Expirado' ? 'border-zinc-500/60 bg-zinc-500/10' : 'border-zinc-700/40 hover:bg-zinc-800/50'">
                                    <input type="radio" v-model="form.status" value="Expirado" class="sr-only" />
                                    <i class="pi pi-ban text-zinc-400 mb-1"></i>
                                    <span class="text-xs font-medium text-zinc-300">Expirado</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Datos -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="p-6 space-y-5">
                            
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Visitante</label>
                                <InputText v-model="form.name" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" :class="{ 'p-invalid': form.errors.name }" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Tipo de acceso</label>
                                    <Select v-model="form.access_type" :options="accessTypeOptions" optionLabel="label" optionValue="value" placeholder="Tipo" class="w-full !rounded-xl !text-[13px]" pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Válido hasta</label>
                                    <InputText v-model="form.expiration_date" type="datetime-local" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                </div>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Motivo</label>
                                <Textarea v-model="form.reason" rows="2" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" autoResize />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <Link :href="route('visits.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm text-center">
                            Descartar
                        </Link>
                        <Button type="submit" label="Guardar cambios" :loading="form.processing" class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5" />
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
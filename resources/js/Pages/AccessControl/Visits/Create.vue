<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';

const props = defineProps({
    privateUnits: { type: Array, default: () => [] }
});

const form = useForm({
    name: '',
    reason: '',
    access_type: 'Peatonal',
    expiration_date: '',
    private_unit_id: '',
});

const accessTypeOptions = [
    { label: '🚶‍♂️ Peatonal', value: 'Peatonal' },
    { label: '🚗 Vehicular', value: 'Vehicular' },
];

const unitOptions = computed(() =>
    props.privateUnits.map(u => ({ label: `🏠 ${u.name}`, value: u.id }))
);

const submit = () => {
    form.post(route('visits.store'), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AppLayout title="Nueva Visita">
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado -->
                <div class="mb-6 flex items-center gap-3 px-4 sm:px-0">
                    <Link :href="route('visits.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left text-sm"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight m-0">Nueva invitación</h1>
                        <p class="text-sm text-zinc-400">Registra una visita para generar un pase de acceso.</p>
                    </div>
                </div>

                <!-- Formulario -->
                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">
                    
                    <!-- Tarjeta de Datos -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="p-6 space-y-5">
                            
                            <!-- Nombre -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Visitante</label>
                                <InputText v-model="form.name" placeholder="Nombre completo" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500" :class="{ 'p-invalid': form.errors.name }" />
                                <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                            </div>

                            <!-- Motivo -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Motivo (opcional)</label>
                                <Textarea v-model="form.reason" rows="2" placeholder="Ej. Fiesta, Entrega, Familiar..." class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500" autoResize />
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Detalles Logísticos -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="p-6 space-y-5">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Tipo de Acceso -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Tipo de acceso</label>
                                    <Select v-model="form.access_type" :options="accessTypeOptions" optionLabel="label" optionValue="value" placeholder="Selecciona tipo" class="w-full !rounded-xl !text-[13px]" pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                </div>

                                <!-- Fecha de Expiración -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Válido hasta</label>
                                    <InputText v-model="form.expiration_date" type="datetime-local" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                </div>
                            </div>

                            <!-- Selector de Unidad -->
                            <div v-if="privateUnits.length > 0" class="flex flex-col gap-1.5 pt-1">
                                <label class="text-xs font-medium text-zinc-400">Asignar a propiedad</label>
                                <Select v-model="form.private_unit_id" :options="unitOptions" optionLabel="label" optionValue="value" placeholder="Selecciona una casa/lote..." class="w-full !rounded-xl !text-[13px]" :class="{ 'p-invalid': form.errors.private_unit_id }" pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100" filter />
                            </div>

                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end gap-3 pt-2">
                        <Link :href="route('visits.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm text-center">
                            Cancelar
                        </Link>
                        <Button type="submit" :label="form.processing ? 'Generando...' : 'Crear pase de acceso'" :loading="form.processing" class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5" />
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
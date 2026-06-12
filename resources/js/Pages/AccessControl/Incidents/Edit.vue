<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';

const props = defineProps({
    incident: Object,
    privateUnits: { type: Array, default: () => [] },
    patrolsActivos: { type: Array, default: () => [] },
});

const form = useForm({
    title: props.incident.title || '',
    description: props.incident.description || '',
    severity: props.incident.severity || 'Media',
    status: props.incident.status || 'Abierto',
    incident_type: props.incident.incident_type || 'Seguridad',
    location_description: props.incident.location_description || '',
    foto_url: props.incident.foto_url || '',
    private_unit_id: props.incident.private_unit_id || '',
    patrol_id: props.incident.patrol_id || '',
});

const severityOptions = [
    { label: '🔵 Baja', value: 'Baja' },
    { label: '🟡 Media', value: 'Media' },
    { label: '🟠 Alta', value: 'Alta' },
    { label: '🔴 Crítica', value: 'Critica' },
];

const typeOptions = [
    { label: '🛡️ Seguridad', value: 'Seguridad' },
    { label: '🚗 Tráfico', value: 'Trafico' },
    { label: '🔧 Daños', value: 'Danios' },
    { label: '🔊 Ruido', value: 'Ruido' },
    { label: '🚨 Emergencia', value: 'Emergencia' },
    { label: '📋 Otro', value: 'Otro' },
];

const statusOptions = [
    { label: 'Abierto', value: 'Abierto' },
    { label: 'En proceso', value: 'EnProceso' },
    { label: 'Resuelto', value: 'Resuelto' },
    { label: 'Cerrado', value: 'Cerrado' },
];

const submit = () => {
    form.put(route('incidents.update', props.incident.id), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AppLayout title="Editar Incidencia">
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

                <div class="mb-6 flex items-center gap-3 px-4 sm:px-0">
                    <Link :href="route('incidents.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left text-sm"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight m-0">Editar incidencia</h1>
                        <p class="text-sm text-zinc-400">Actualiza la información del reporte.</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">

                    <!-- Datos del Reporte -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 tracking-wider">Datos del reporte</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Título *</label>
                                <InputText
                                    v-model="form.title"
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    :class="{ 'p-invalid': form.errors.title }"
                                    maxlength="150"
                                />
                                <p v-if="form.errors.title" class="text-sm text-red-400">{{ form.errors.title }}</p>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Descripción</label>
                                <Textarea
                                    v-model="form.description"
                                    rows="4"
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    autoResize
                                />
                            </div>

                            <!-- Severidad + Tipo + Status -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Severidad</label>
                                    <Select
                                        v-model="form.severity"
                                        :options="severityOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Severidad"
                                        class="w-full !rounded-xl !text-[13px]"
                                        pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    />
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Tipo</label>
                                    <Select
                                        v-model="form.incident_type"
                                        :options="typeOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Tipo"
                                        class="w-full !rounded-xl !text-[13px]"
                                        pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    />
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Estado</label>
                                    <Select
                                        v-model="form.status"
                                        :options="statusOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Estado"
                                        class="w-full !rounded-xl !text-[13px]"
                                        pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ubicación y Evidencia -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 tracking-wider">Ubicación y evidencia</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Ubicación</label>
                                <InputText
                                    v-model="form.location_description"
                                    placeholder="Ej. Caseta principal..."
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Foto (URL)</label>
                                <InputText
                                    v-model="form.foto_url"
                                    placeholder="https://..."
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 pt-2">
                        <Link :href="route('incidents.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm text-center">
                            Cancelar
                        </Link>
                        <Button
                            type="submit"
                            label="Guardar cambios"
                            :loading="form.processing"
                            class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5"
                        />
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

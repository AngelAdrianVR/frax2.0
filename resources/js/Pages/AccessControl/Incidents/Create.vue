<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';

const props = defineProps({
    privateUnits: { type: Array, default: () => [] },
    patrolsActivos: { type: Array, default: () => [] },
});

const form = useForm({
    title: '',
    description: '',
    severity: 'Media',
    incident_type: 'Seguridad',
    location_description: '',
    foto_url: '',
    private_unit_id: '',
    patrol_id: '',
});

const severityOptions = [
    { label: '🔵 Baja — Sin riesgo inmediato', value: 'Baja' },
    { label: '🟡 Media — Requiere atención', value: 'Media' },
    { label: '🟠 Alta — Riesgo potencial', value: 'Alta' },
    { label: '🔴 Crítica — Emergencia / Peligro', value: 'Critica' },
];

const typeOptions = [
    { label: '🛡️ Seguridad', value: 'Seguridad' },
    { label: '🚗 Tráfico / Estacionamiento', value: 'Trafico' },
    { label: '🔧 Daños materiales', value: 'Danios' },
    { label: '🔊 Ruido / Perturbación', value: 'Ruido' },
    { label: '🚨 Emergencia', value: 'Emergencia' },
    { label: '📋 Otro', value: 'Otro' },
];

const unitOptions = computed(() => [
    { label: 'Ninguna (área común)', value: '' },
    ...props.privateUnits.map(u => ({
        label: `🏠 ${u.lot_number || u.id}`,
        value: u.id,
    })),
]);

const patrolOptions = computed(() => [
    { label: 'Sin rondín asociado', value: '' },
    ...props.patrolsActivos.map(p => ({
        label: `🛡️ ${p.guardia} (${p.inicio})`,
        value: p.id,
    })),
]);

const submit = () => {
    form.post(route('incidents.store'), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AppLayout title="Reportar Incidencia">
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

                <!-- Encabezado -->
                <div class="mb-6 flex items-center gap-3 px-4 sm:px-0">
                    <Link :href="route('incidents.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left text-sm"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight m-0">Reportar incidencia</h1>
                        <p class="text-sm text-zinc-400">Registra una nueva incidencia en el fraccionamiento.</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">

                    <!-- Tarjeta: Datos del Reporte -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 tracking-wider">Datos del reporte</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <!-- Título -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Título de la incidencia *</label>
                                <InputText
                                    v-model="form.title"
                                    placeholder="Ej. Luminaria fundida en acceso norte, Bache en calle principal..."
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                    :class="{ 'p-invalid': form.errors.title }"
                                    maxlength="150"
                                />
                                <p v-if="form.errors.title" class="text-sm text-red-400">{{ form.errors.title }}</p>
                            </div>

                            <!-- Descripción -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Descripción detallada</label>
                                <Textarea
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Describe lo que observaste: ubicación exacta, personas involucradas, condiciones..."
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                    autoResize
                                />
                                <p v-if="form.errors.description" class="text-sm text-red-400">{{ form.errors.description }}</p>
                            </div>

                            <!-- Severidad + Tipo -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Severidad *</label>
                                    <Select
                                        v-model="form.severity"
                                        :options="severityOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Selecciona severidad"
                                        class="w-full !rounded-xl !text-[13px]"
                                        pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    />
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Tipo de incidencia *</label>
                                    <Select
                                        v-model="form.incident_type"
                                        :options="typeOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Selecciona tipo"
                                        class="w-full !rounded-xl !text-[13px]"
                                        pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Ubicación y Evidencia -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 tracking-wider">Ubicación y evidencia</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <!-- Ubicación -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Ubicación específica</label>
                                <InputText
                                    v-model="form.location_description"
                                    placeholder="Ej. Caseta principal, Acceso norte, Alberca..."
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                />
                            </div>

                            <!-- Foto (URL) -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Evidencia fotográfica (URL)</label>
                                <InputText
                                    v-model="form.foto_url"
                                    placeholder="https://... (link de la foto)"
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                />
                                <p class="text-xs text-zinc-500 mt-0.5">Pega aquí el enlace de la imagen. Próximamente: subida directa desde el celular.</p>
                            </div>

                            <!-- Unidad involucrada + Rondín -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Unidad involucrada</label>
                                    <Select
                                        v-model="form.private_unit_id"
                                        :options="unitOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Ninguna (área común)"
                                        class="w-full !rounded-xl !text-[13px]"
                                        pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    />
                                </div>
                                <div v-if="patrolsActivos.length > 0" class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Rondín activo</label>
                                    <Select
                                        v-model="form.patrol_id"
                                        :options="patrolOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Sin rondín asociado"
                                        class="w-full !rounded-xl !text-[13px]"
                                        pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    />
                                </div>
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
                            :label="form.processing ? 'Reportando...' : 'Reportar incidencia'"
                            :loading="form.processing"
                            class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5"
                        />
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

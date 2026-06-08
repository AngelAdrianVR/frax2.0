<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

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

const submit = () => {
    form.put(route('incidents.update', props.incident.id));
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
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight">Editar Incidencia</h1>
                        <p class="text-sm text-zinc-400">Actualiza la información del reporte.</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">

                    <!-- Datos del Reporte -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Datos del Reporte</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Título *</label>
                                <input v-model="form.title" type="text"
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" required maxlength="150" />
                                <p v-if="form.errors.title" class="text-sm text-red-400">{{ form.errors.title }}</p>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Descripción</label>
                                <textarea v-model="form.description" rows="4"
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 resize-none"></textarea>
                            </div>

                            <!-- Severidad + Tipo + Status -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Severidad</label>
                                    <div class="relative">
                                        <select v-model="form.severity"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                            <option value="Baja">🔵 Baja</option>
                                            <option value="Media">🟡 Media</option>
                                            <option value="Alta">🟠 Alta</option>
                                            <option value="Critica">🔴 Crítica</option>
                                        </select>
                                        <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Tipo</label>
                                    <div class="relative">
                                        <select v-model="form.incident_type"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                            <option value="Seguridad">🛡️ Seguridad</option>
                                            <option value="Trafico">🚗 Tráfico</option>
                                            <option value="Danios">🔧 Daños</option>
                                            <option value="Ruido">🔊 Ruido</option>
                                            <option value="Emergencia">🚨 Emergencia</option>
                                            <option value="Otro">📋 Otro</option>
                                        </select>
                                        <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Estado</label>
                                    <div class="relative">
                                        <select v-model="form.status"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                            <option value="Abierto">Abierto</option>
                                            <option value="EnProceso">En Proceso</option>
                                            <option value="Resuelto">Resuelto</option>
                                            <option value="Cerrado">Cerrado</option>
                                        </select>
                                        <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ubicación y Evidencia -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Ubicación y Evidencia</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Ubicación</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="pi pi-map-marker text-zinc-500"></i>
                                    </div>
                                    <input v-model="form.location_description" type="text" placeholder="Ej. Caseta principal..."
                                        class="w-full pl-11 bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                </div>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Foto (URL)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="pi pi-camera text-zinc-500"></i>
                                    </div>
                                    <input v-model="form.foto_url" type="url" placeholder="https://..."
                                        class="w-full pl-11 bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 pt-2">
                        <Link :href="route('incidents.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm text-center">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50 flex items-center gap-2">
                            <i v-if="form.processing" class="pi pi-spinner pi-spin"></i>
                            <span>Guardar Cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

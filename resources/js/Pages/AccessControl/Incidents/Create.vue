<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
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

const submit = () => {
    form.post(route('incidents.store'));
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
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight">Reportar Incidencia</h1>
                        <p class="text-sm text-zinc-400">Registra una nueva incidencia en el fraccionamiento.</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">

                    <!-- Tarjeta: Datos del Reporte -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Datos del Reporte</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <!-- Título -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Título de la Incidencia *</label>
                                <input v-model="form.title" type="text" placeholder="Ej. Luminaria fundida en acceso norte, Bache en calle principal..."
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" required maxlength="150" />
                                <p v-if="form.errors.title" class="text-sm text-red-400">{{ form.errors.title }}</p>
                            </div>

                            <!-- Descripción -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Descripción Detallada</label>
                                <textarea v-model="form.description" rows="4" placeholder="Describe lo que observaste: ubicación exacta, personas involucradas, condiciones..."
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 resize-none"></textarea>
                                <p v-if="form.errors.description" class="text-sm text-red-400">{{ form.errors.description }}</p>
                            </div>

                            <!-- Severidad + Tipo -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Severidad *</label>
                                    <div class="relative">
                                        <select v-model="form.severity"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                            <option value="Baja">🔵 Baja — Sin riesgo inmediato</option>
                                            <option value="Media">🟡 Media — Requiere atención</option>
                                            <option value="Alta">🟠 Alta — Riesgo potencial</option>
                                            <option value="Critica">🔴 Crítica — Emergencia / Peligro</option>
                                        </select>
                                        <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Tipo de Incidencia *</label>
                                    <div class="relative">
                                        <select v-model="form.incident_type"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                            <option value="Seguridad">🛡️ Seguridad</option>
                                            <option value="Trafico">🚗 Tráfico / Estacionamiento</option>
                                            <option value="Danios">🔧 Daños materiales</option>
                                            <option value="Ruido">🔊 Ruido / Perturbación</option>
                                            <option value="Emergencia">🚨 Emergencia</option>
                                            <option value="Otro">📋 Otro</option>
                                        </select>
                                        <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Ubicación y Evidencia -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Ubicación y Evidencia</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <!-- Ubicación -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Ubicación Específica</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="pi pi-map-marker text-zinc-500"></i>
                                    </div>
                                    <input v-model="form.location_description" type="text" placeholder="Ej. Caseta principal, Acceso norte, Alberca..."
                                        class="w-full pl-11 bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                </div>
                            </div>

                            <!-- Foto (URL) -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Evidencia Fotográfica (URL)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="pi pi-camera text-zinc-500"></i>
                                    </div>
                                    <input v-model="form.foto_url" type="url" placeholder="https://... (link de la foto)"
                                        class="w-full pl-11 bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                </div>
                                <p class="text-xs text-zinc-500 mt-0.5">Pega aquí el enlace de la imagen. Próximamente: subida directa desde el celular.</p>
                            </div>

                            <!-- Unidad involucrada + Rondín -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Unidad Involucrada</label>
                                    <div class="relative">
                                        <select v-model="form.private_unit_id"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                            <option value="">Ninguna (área común)</option>
                                            <option v-for="unit in privateUnits" :key="unit.id" :value="unit.id">
                                                🏠 {{ unit.lot_number || unit.id }}
                                            </option>
                                        </select>
                                        <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                    </div>
                                </div>
                                <div v-if="patrolsActivos.length > 0" class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Rondín Activo</label>
                                    <div class="relative">
                                        <select v-model="form.patrol_id"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                            <option value="">Sin rondín asociado</option>
                                            <option v-for="p in patrolsActivos" :key="p.id" :value="p.id">
                                                🛡️ {{ p.guardia }} ({{ p.inicio }})
                                            </option>
                                        </select>
                                        <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                    </div>
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
                            <span v-if="form.processing">Reportando...</span>
                            <span v-else>🚨 Reportar Incidencia</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

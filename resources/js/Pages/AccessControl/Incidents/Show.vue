<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

defineProps({
    incident: Object,
});

const confirm = useConfirm();
const toast = useToast();

const getSeverityClass = (severity) => ({
    'Baja': 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
    'Media': 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
    'Alta': 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
    'Critica': 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
}[severity] || 'bg-zinc-100 text-zinc-700');

const getStatusClass = (status) => ({
    'Abierto': 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
    'EnProceso': 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
    'Resuelto': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
    'Cerrado': 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400',
}[status] || 'bg-zinc-100');

const marcarResuelta = (id) => {
    confirm.require({
        message: '¿Marcar esta incidencia como resuelta?',
        header: 'Resolver Incidencia',
        icon: 'pi pi-check-circle',
        acceptLabel: 'Sí, resolver',
        accept: () => {
            router.post(route('incidents.resolver', id), {}, {
                onSuccess: () => toast.add({ severity: 'success', summary: 'Resuelta', detail: 'Incidencia resuelta.', life: 3000 })
            });
        }
    });
};

const iniciarAtencion = (id) => {
    router.post(route('incidents.atender', id), {}, {
        onSuccess: () => toast.add({ severity: 'info', summary: 'En Proceso', detail: 'Atención iniciada.', life: 3000 })
    });
};
</script>

<template>
    <AppLayout title="Detalle de Incidencia">
        <Toast />
        <ConfirmDialog />

        <div class="py-8 md:py-12 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

                <!-- Encabezado -->
                <div class="mb-6 flex items-center justify-between px-4 sm:px-0">
                    <div class="flex items-center gap-3">
                        <Link :href="route('incidents.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-200/50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 transition-colors">
                            <i class="pi pi-arrow-left text-sm"></i>
                        </Link>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Detalle de Incidencia</h1>
                    </div>
                    <div class="flex gap-2">
                        <Link :href="route('incidents.edit', incident.id)" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                            <i class="pi pi-pencil mr-1.5 text-xs"></i> Editar
                        </Link>
                    </div>
                </div>

                <div class="space-y-6 px-4 sm:px-0">

                    <!-- Header Card: Título + Badges -->
                    <div class="bg-white dark:bg-zinc-900 shadow-sm rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-900/50">
                        <div class="p-6">
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                <span :class="['px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider', getSeverityClass(incident.severity)]">
                                    {{ incident.severity }}
                                </span>
                                <span :class="['px-3 py-1 text-xs font-bold rounded-full', getStatusClass(incident.status)]">
                                    {{ incident.status === 'EnProceso' ? 'En Proceso' : incident.status }}
                                </span>
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-400">
                                    {{ incident.incident_type }}
                                </span>
                            </div>
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white leading-snug">{{ incident.title }}</h2>
                            <p v-if="incident.description" class="text-sm text-zinc-600 dark:text-zinc-400 mt-4 leading-relaxed whitespace-pre-line">{{ incident.description }}</p>
                        </div>
                    </div>

                    <!-- Metadata Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-zinc-900 shadow-sm rounded-2xl border border-zinc-100 dark:border-zinc-900/50 p-5">
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Reportado por</p>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                    {{ incident.guardia?.charAt(0)?.toUpperCase() || '?' }}
                                </div>
                                <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ incident.guardia }}</span>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-zinc-900 shadow-sm rounded-2xl border border-zinc-100 dark:border-zinc-900/50 p-5">
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Fechas</p>
                            <div class="space-y-1 text-xs text-zinc-600 dark:text-zinc-400">
                                <p><span class="font-semibold text-zinc-500">Reporte:</span> {{ incident.reported_at }}</p>
                                <p v-if="incident.resolved_at"><span class="font-semibold text-zinc-500">Resuelto:</span> {{ incident.resolved_at }}</p>
                            </div>
                        </div>
                        <div v-if="incident.unidad" class="bg-white dark:bg-zinc-900 shadow-sm rounded-2xl border border-zinc-100 dark:border-zinc-900/50 p-5">
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Unidad Involucrada</p>
                            <span class="text-sm font-semibold text-zinc-900 dark:text-white">🏠 {{ incident.unidad }}</span>
                        </div>
                        <div v-if="incident.location_description" class="bg-white dark:bg-zinc-900 shadow-sm rounded-2xl border border-zinc-100 dark:border-zinc-900/50 p-5">
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Ubicación</p>
                            <span class="text-sm text-zinc-600 dark:text-zinc-400"><i class="pi pi-map-marker mr-1.5 text-zinc-400"></i>{{ incident.location_description }}</span>
                        </div>
                    </div>

                    <!-- Foto de Evidencia -->
                    <div v-if="incident.foto_url" class="bg-white dark:bg-zinc-900 shadow-sm rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-900/50">
                        <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-900/50">
                            <h3 class="text-sm font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Evidencia Fotográfica</h3>
                        </div>
                        <div class="p-4">
                            <img :src="incident.foto_url" alt="Evidencia" class="w-full rounded-2xl object-cover max-h-80" />
                        </div>
                    </div>

                    <!-- Rondín Asociado -->
                    <div v-if="incident.patrol" class="bg-white dark:bg-zinc-900 shadow-sm rounded-2xl border border-zinc-100 dark:border-zinc-900/50 p-5">
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Rondín Asociado</p>
                        <div class="flex items-center gap-3">
                            <i class="pi pi-shield text-emerald-500"></i>
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ incident.patrol.guardia }}</p>
                                <p class="text-xs text-zinc-500">Inicio: {{ incident.patrol.inicio }}</p>
                            </div>
                        </div>
                    </div>  

                    <!-- Acciones -->
                    <div v-if="incident.status !== 'Resuelto' && incident.status !== 'Cerrado'" class="flex flex-wrap gap-3">
                        <button v-if="incident.status === 'Abierto'" @click="iniciarAtencion(incident.id)"
                            class="px-5 py-3 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-2xl shadow-md transition-colors flex items-center gap-2">
                            <i class="pi pi-play"></i> Iniciar Atención
                        </button>
                        <button @click="marcarResuelta(incident.id)"
                            class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-2xl shadow-md transition-colors flex items-center gap-2">
                            <i class="pi pi-check"></i> Marcar como Resuelta
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

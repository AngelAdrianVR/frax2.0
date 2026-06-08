<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

defineProps({
    incidents: Object,
    filters: Object,
});

const confirm = useConfirm();
const toast = useToast();

const activeFilter = ref('all');

const getSeverityClass = (severity) => ({
    'Baja': 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
    'Media': 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
    'Alta': 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
    'Critica': 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
}[severity] || 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300');

const getStatusClass = (status) => ({
    'Abierto': 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400 border-red-200 dark:border-red-500/20',
    'EnProceso': 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border-amber-200',
    'Resuelto': 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-200',
    'Cerrado': 'bg-zinc-50 text-zinc-500 dark:bg-zinc-700/50 dark:text-zinc-400 border-zinc-200',
}[status] || 'bg-zinc-50 text-zinc-500');

const getTypeIcon = (type) => ({
    'Seguridad': 'pi pi-shield',
    'Trafico': 'pi pi-car',
    'Danios': 'pi pi-wrench',
    'Ruido': 'pi pi-volume-up',
    'Emergencia': 'pi pi-exclamation-circle',
    'Otro': 'pi pi-flag',
}[type] || 'pi pi-circle');

const deleteIncident = (id) => {
    confirm.require({
        message: '¿Estás seguro de eliminar esta incidencia? La evidencia y notas asociadas se perderán permanentemente.',
        header: 'Eliminar Incidencia',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Sí, eliminar',
        rejectLabel: 'Cancelar',
        accept: () => {
            router.delete(route('incidents.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Eliminada', detail: 'Incidencia eliminada.', life: 3000 });
                }
            });
        }
    });
};

const resolveIncident = (id) => {
    confirm.require({
        message: '¿Marcar esta incidencia como resuelta?',
        header: 'Resolver Incidencia',
        icon: 'pi pi-check-circle',
        acceptLabel: 'Sí, resolver',
        rejectLabel: 'Cancelar',
        accept: () => {
            router.post(route('incidents.resolver', id), {}, {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Resuelta', detail: 'Incidencia marcada como resuelta.', life: 3000 });
                }
            });
        }
    });
};
</script>

<template>
    <AppLayout title="Incidencias y Rondines">
        <Toast />
        <ConfirmDialog />

        <div class="py-8 md:py-12 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 px-4 sm:px-0 gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Incidencias</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Bitácora de seguridad: reportes de guardias, rondines y novedades del fraccionamiento.</p>
                    </div>
                    <Link :href="route('incidents.create')" class="shrink-0 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl shadow-md text-sm font-semibold transition-all flex items-center gap-2">
                        <i class="pi pi-exclamation-triangle text-xs"></i> <span class="hidden sm:inline">Reportar Incidencia</span>
                    </Link>
                </div>

                <!-- Filter Pills -->
                <div class="flex gap-2 overflow-x-auto pb-2 px-4 sm:px-0 mb-6">
                    <button v-for="f in ['all','Abierto','EnProceso','Resuelto','Cerrado']" :key="f" @click="activeFilter = f"
                        :class="[
                            'px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all border',
                            activeFilter === f ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 hover:border-indigo-300'
                        ]">
                        {{ f === 'all' ? 'Todas' : f === 'EnProceso' ? 'En Proceso' : f }}
                    </button>
                </div>

                <!-- Empty State -->
                <div v-if="incidents.data.length === 0" class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm p-12 text-center border border-zinc-100 dark:border-zinc-900/50 mx-4 sm:mx-0">
                    <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="pi pi-shield text-4xl text-emerald-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Sin incidencias reportadas</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 mb-6 max-w-md mx-auto">Todo en orden. No hay novedades que reportar en este momento.</p>
                </div>

                <!-- Incident Cards Grid (iOS Style) -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-4 px-4 sm:px-0">
                    <div v-for="incident in incidents.data" :key="incident.id"
                        class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-zinc-100 dark:border-zinc-900/50 p-5 hover:shadow-md transition-all group">

                        <!-- Priority + Type -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span :class="['px-2.5 py-0.5 text-[10px] font-bold rounded-full uppercase tracking-wider', getSeverityClass(incident.severity)]">
                                    {{ incident.severity }}
                                </span>
                                <span class="text-[11px] text-zinc-400 flex items-center gap-1">
                                    <i :class="getTypeIcon(incident.incident_type)"></i> {{ incident.incident_type }}
                                </span>
                            </div>
                            <span :class="['text-[10px] font-bold px-2 py-0.5 rounded-full border', getStatusClass(incident.status)]">
                                {{ incident.status === 'EnProceso' ? 'En Proceso' : incident.status }}
                            </span>
                        </div>

                        <!-- Title -->
                        <Link :href="route('incidents.show', incident.id)" class="block">
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white mb-1.5 leading-snug group-hover:text-indigo-600 transition-colors">
                                {{ incident.title }}
                            </h3>
                        </Link>

                        <!-- Meta Footer -->
                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-700/50">
                            <div class="flex items-center gap-3 text-xs text-zinc-500">
                                <span class="flex items-center gap-1">
                                    <i class="pi pi-user text-[10px]"></i> {{ incident.guardia }}
                                </span>
                                <span v-if="incident.unidad" class="flex items-center gap-1">
                                    <i class="pi pi-home text-[10px]"></i> {{ incident.unidad }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] text-zinc-400">{{ incident.reported_at }}</span>
                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <Link :href="route('incidents.edit', incident.id)" class="w-7 h-7 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center hover:bg-amber-100 hover:text-amber-600 transition-colors">
                                        <i class="pi pi-pencil text-[10px]"></i>
                                    </Link>
                                    <button v-if="incident.status !== 'Resuelto' && incident.status !== 'Cerrado'" @click="resolveIncident(incident.id)" class="w-7 h-7 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center hover:bg-emerald-100 hover:text-emerald-600 transition-colors">
                                        <i class="pi pi-check text-[10px]"></i>
                                    </button>
                                    <button @click="deleteIncident(incident.id)" class="w-7 h-7 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center hover:bg-red-100 hover:text-red-600 transition-colors">
                                        <i class="pi pi-trash text-[10px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="incidents.links && incidents.links.length > 3" class="flex justify-center mt-8">
                    <div class="flex gap-1.5 bg-white dark:bg-zinc-900 p-1.5 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-900/50">
                        <Link v-for="(link, k) in incidents.links" :key="k" :href="link.url" v-html="link.label"
                            class="min-w-[32px] h-8 flex items-center justify-center rounded-xl text-sm font-medium transition-colors px-2"
                            :class="link.active ? 'bg-indigo-600 text-white shadow-md' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700'" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
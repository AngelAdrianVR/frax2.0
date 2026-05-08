<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

defineProps({
    patrols: Object
});

const confirm = useConfirm();
const toast = useToast();

const getStatusColor = (status) => {
    switch(status) {
        case 'Terminado': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'Activo': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'Incidente': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default: return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300';
    }
};

const deletePatrol = (id) => {
    confirm.require({
        message: '¿Estás seguro de que deseas eliminar este registro de rondín? Esta acción no se puede deshacer.',
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Sí, eliminar',
        rejectLabel: 'Cancelar',
        accept: () => {
            router.delete(route('patrols.destroy', id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Eliminado', detail: 'El rondín fue eliminado correctamente.', life: 3000 });
                }
            });
        }
    });
};
</script>

<template>
    <AppLayout title="Bitácora de Rondines">
        <Toast />
        <ConfirmDialog />

        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado y Acciones -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 px-4 sm:px-0 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Bitácora de Rondines</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Historial y control de recorridos de seguridad.</p>
                    </div>
                    <Link :href="route('patrols.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                        <i class="pi pi-plus mr-2"></i> Registrar Rondín
                    </Link>
                </div>

                <!-- Estado Vacío -->
                <div v-if="patrols.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-3xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="pi pi-shield text-2xl text-zinc-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Sin rondines registrados</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 mb-6">El personal de seguridad aún no ha registrado recorridos.</p>
                    <Link :href="route('patrols.create')" class="text-indigo-600 hover:text-indigo-500 font-medium text-sm">
                        Registrar el primero <span aria-hidden="true">&rarr;</span>
                    </Link>
                </div>

                <!-- Tabla de Datos -->
                <div v-else class="bg-white dark:bg-zinc-800 shadow-sm border border-zinc-200 dark:border-zinc-700 sm:rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50/50 dark:bg-zinc-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Guardia</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Inicio</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Fin</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Puntos</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Estatus</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                <tr v-for="patrol in patrols.data" :key="patrol.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mr-3 flex-shrink-0">
                                                <i class="pi pi-user text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                            </div>
                                            <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ patrol.guard_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">{{ patrol.start_time }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">{{ patrol.end_time }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ patrol.scanned_points }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2.5 py-1 text-xs rounded-full font-medium border border-transparent" :class="getStatusColor(patrol.status)">
                                            {{ patrol.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link :href="route('patrols.edit', patrol.id)" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-zinc-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors" title="Editar">
                                                <i class="pi pi-pencil"></i>
                                            </Link>
                                            <button @click="deletePatrol(patrol.id)" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-zinc-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Eliminar">
                                                <i class="pi pi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="patrols.links && patrols.links.length > 3" class="flex justify-center mt-8">
                    <div class="flex gap-1 bg-white dark:bg-zinc-800 p-1 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
                        <Link v-for="(link, k) in patrols.links" :key="k" :href="link.url || '#'" v-html="link.label" 
                              class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" 
                              :class="[
                                  link.active ? 'bg-indigo-600 text-white' : 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700',
                                  !link.url ? 'opacity-50 cursor-not-allowed' : ''
                              ]" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
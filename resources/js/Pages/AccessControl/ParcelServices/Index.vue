<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

const props = defineProps({
    parcels: Object
});

const confirm = useConfirm();
const toast = useToast();

const getStatusColor = (status) => {
    switch(status) {
        case 'Entregado': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400';
        case 'En Caseta': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'Recibido': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'Devuelto': return 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400';
        case 'Regresado': return 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400';
        default: return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300';
    }
};

const deleteParcel = (id) => {
    confirm.require({
        message: '¿Estás seguro de que deseas eliminar este registro de paquetería? Esta acción no se puede deshacer.',
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('parcel-services.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Eliminado', detail: 'Registro eliminado correctamente', life: 3000 });
                }
            });
        }
    });
};
</script>

<template>
    <AppLayout title="Paquetería">
        <Toast />
        <ConfirmDialog />

        <div class="py-12  min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Header Estilo iOS -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 px-4 sm:px-0 gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Paquetería</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Gestión de entregas y mensajería en caseta.</p>
                    </div>
                    <Link :href="route('parcel-services.create')" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-2xl hover:bg-indigo-700 transition-colors shadow-sm w-full sm:w-auto">
                        <i class="pi pi-plus mr-2 text-sm"></i>
                        Registrar Paquete
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-if="parcels.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-3xl shadow-sm p-12 text-center border border-zinc-200 dark:border-zinc-800/50">
                    <div class="w-20 h-20 bg-zinc-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="pi pi-box text-3xl text-zinc-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Sin paquetes registrados</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Aún no hay paquetes recibidos en caseta para tu propiedad.</p>
                </div>

                <!-- Tabla de Datos -->
                <div v-else class="bg-white dark:bg-zinc-800 shadow-sm rounded-3xl overflow-hidden border border-zinc-200 dark:border-zinc-800/50">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50/50 dark:bg-zinc-800/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Paquetería</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">No. Guía</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Recepción</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Estatus</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                                <tr v-for="parcel in parcels.data" :key="parcel.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/20 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                                <i class="pi pi-box"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ parcel.courier || parcel.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-zinc-600 dark:text-zinc-300 font-mono">{{ parcel.tracking_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ parcel.received_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusColor(parcel.status)">
                                            {{ parcel.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link :href="route('parcel-services.show', parcel.id)" class="p-2 text-zinc-400 hover:text-indigo-600 bg-zinc-50 hover:bg-indigo-50 dark:bg-zinc-700 dark:hover:bg-indigo-900/30 rounded-lg transition-colors" title="Ver Detalles">
                                                <i class="pi pi-eye"></i>
                                            </Link>
                                            <Link :href="route('parcel-services.edit', parcel.id)" class="p-2 text-zinc-400 hover:text-blue-600 bg-zinc-50 hover:bg-blue-50 dark:bg-zinc-700 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="Editar">
                                                <i class="pi pi-pencil"></i>
                                            </Link>
                                            <button @click="deleteParcel(parcel.id)" class="p-2 text-zinc-400 hover:text-rose-600 bg-zinc-50 hover:bg-rose-50 dark:bg-zinc-700 dark:hover:bg-rose-900/30 rounded-lg transition-colors" title="Eliminar">
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
                <div v-if="parcels.links && parcels.links.length > 3" class="flex justify-center mt-8">
                    <div class="flex gap-1 bg-white dark:bg-zinc-800 p-1 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700/50">
                        <Link v-for="(link, k) in parcels.links" :key="k" :href="link.url || '#'" v-html="link.label" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" 
                            :class="[
                                link.active ? 'bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-white' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700/50',
                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                            ]" 
                        />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
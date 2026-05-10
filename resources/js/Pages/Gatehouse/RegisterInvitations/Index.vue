<template>
    <AppLayout title="Mis Invitaciones">
        <!-- Contenedor Global (Fondo iOS) -->
        <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans">
            <div class="max-w-5xl mx-auto space-y-6">
                
                <!-- Header Estilo iOS -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Mis Invitaciones
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Gestiona las invitaciones enviadas para registrarse en la propiedad.
                        </p>
                    </div>
                    <Link :href="route('register-invitations.create')" 
                          class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all active:scale-95">
                        <i class="pi pi-plus mr-2 text-sm"></i>
                        Nueva Invitación
                    </Link>
                </div>

                <!-- Tarjeta Principal (Lista de Invitaciones) -->
                <div class="bg-white dark:bg-[#1C1C1E] rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                    
                    <!-- Estado Vacío -->
                    <div v-if="!invitations.data || invitations.data.length === 0" class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 dark:bg-blue-900/20 mb-4">
                            <i class="pi pi-envelope text-2xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Sin invitaciones</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                            Aún no has enviado invitaciones para unirse a esta propiedad. Haz clic en "Nueva Invitación" para empezar.
                        </p>
                    </div>

                    <!-- Lista de Invitaciones (Estilo iOS List) -->
                    <ul v-else class="divide-y divide-gray-100 dark:divide-gray-800">
                        <li v-for="invitation in invitations.data" :key="invitation.id" 
                            class="p-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center min-w-0 gap-x-4">
                                    <!-- Icono circular -->
                                    <div class="h-12 w-12 flex-none rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                                        <i class="pi pi-send text-gray-500 dark:text-gray-400"></i>
                                    </div>
                                    
                                    <!-- Info Principal -->
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-sm font-semibold leading-6 text-gray-900 dark:text-white truncate">
                                            {{ invitation.email }}
                                        </p>
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                            <span class="inline-flex items-center gap-x-1.5">
                                                <i class="pi pi-users text-[10px]"></i>
                                                {{ invitation.role_type }}
                                            </span>
                                            <span>&middot;</span>
                                            <span class="inline-flex items-center gap-x-1.5">
                                                <i class="pi pi-calendar text-[10px]"></i>
                                                Expira: {{ invitation.expires_at }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Badges y Acciones -->
                                <div class="flex items-center gap-x-4 shrink-0">
                                    <!-- Badge de Estado -->
                                    <span :class="[
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        getStatusClass(invitation.status)
                                    ]">
                                        {{ invitation.status }}
                                    </span>

                                    <!-- Menú de Acciones Rápido -->
                                <div class="flex items-center gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                    
                                    <!-- Botón Ver Detalles -->
                                    <Link :href="route('register-invitations.show', invitation.id)"
                                          class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                          title="Ver Detalles">
                                        <i class="pi pi-eye"></i>
                                    </Link>

                                    <!-- Solo editar si está Pendiente -->
                                    <Link v-if="invitation.status === 'Pendiente'" 
                                          :href="route('register-invitations.edit', invitation.id)"
                                              class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                              title="Editar">
                                            <i class="pi pi-pencil"></i>
                                        </Link>
                                        
                                        <!-- Eliminar / Revocar -->
                                        <button @click="confirmDelete(invitation.id)"
                                                class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                title="Revocar">
                                            <i class="pi pi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <!-- Paginación -->
                    <div v-if="invitations.links && invitations.data.length > 0" class="border-t border-gray-100 dark:border-gray-800 px-4 py-3 sm:px-6 bg-gray-50/50 dark:bg-gray-800/20">
                        <Pagination :links="invitations.links" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Componentes de PrimeVue para Notificaciones y Diálogos -->
        <Toast />
        <ConfirmDialog />
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    invitations: Object,
});

const confirm = useConfirm();
const toast = useToast();

const getStatusClass = (status) => {
    switch (status) {
        case 'Aceptado':
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'Pendiente':
            return 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400';
        case 'Expirado':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
};

const confirmDelete = (id) => {
    confirm.require({
        message: '¿Estás seguro de que deseas revocar esta invitación? El enlace dejará de ser válido.',
        header: 'Revocar Invitación',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Sí, revocar',
        rejectLabel: 'Cancelar',
        accept: () => {
            router.delete(route('register-invitations.destroy', id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Éxito', detail: 'Invitación revocada correctamente', life: 3000 });
                },
                onError: () => {
                    toast.add({ severity: 'error', summary: 'Error', detail: 'Hubo un problema al revocar la invitación', life: 3000 });
                }
            });
        }
    });
};
</script>
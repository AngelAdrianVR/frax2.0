<template>
    <AppLayout title="Detalles de Invitación">
        <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans">
            <div class="max-w-3xl mx-auto space-y-6">
                
                <!-- Header -->
                <div class="flex items-center gap-4">
                    <Link :href="route('register-invitations.index')" 
                          class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                        <i class="pi pi-arrow-left text-gray-600 dark:text-gray-300"></i>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Detalles de Invitación
                        </h1>
                    </div>
                </div>

                <!-- Tarjeta Estilo iOS -->
                <div class="bg-white dark:bg-[#1C1C1E] rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                    <div class="p-6 sm:p-8">
                        
                        <!-- Cabecera de la Tarjeta -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 flex-shrink-0 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                                    <i class="pi pi-envelope text-2xl text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white truncate">{{ invitation.email }}</h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Enviada el {{ invitation.created_at }}</p>
                                </div>
                            </div>
                            
                            <span :class="['inline-flex self-start sm:self-center px-3 py-1 rounded-full text-sm font-medium', getStatusClass(invitation.status)]">
                                {{ invitation.status }}
                            </span>
                        </div>

                        <!-- Detalles Relevantes -->
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rol asignado</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="pi pi-user text-gray-400 text-sm"></i> {{ invitation.role_type }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de expiración</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="pi pi-calendar text-gray-400 text-sm"></i> {{ invitation.expires_at }}
                                </dd>
                            </div>
                            
                            <!-- Sección del Enlace Seguro -->
                            <div class="sm:col-span-2 pt-6 border-t border-gray-100 dark:border-gray-800">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Enlace de Registro Seguro</dt>
                                <dd>
                                    <div class="flex items-center gap-2">
                                        <input type="text" readonly :value="registrationLink" 
                                               class="block w-full rounded-xl border-0 py-3 px-4 text-gray-500 bg-gray-50 dark:bg-gray-900 dark:text-gray-400 sm:text-sm shadow-sm ring-1 ring-inset ring-gray-200 dark:ring-gray-800 outline-none" />
                                        <button @click="copyLink" 
                                                class="flex-shrink-0 p-3 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors"
                                                title="Copiar enlace">
                                            <i class="pi pi-copy"></i>
                                        </button>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">Puedes copiar este enlace y enviarlo por WhatsApp al usuario en caso de que no haya recibido el correo.</p>
                                </dd>
                            </div>
                        </dl>
                    </div>
                    
                    <!-- Botonera Inferior -->
                    <div class="bg-gray-50 dark:bg-[#1C1C1E]/50 px-6 py-4 flex items-center justify-end border-t border-gray-100 dark:border-gray-800">
                        <Link v-if="invitation.status === 'Pendiente'" :href="route('register-invitations.edit', invitation.id)" 
                              class="rounded-xl bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                            Editar Invitación
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        <Toast />
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

const props = defineProps({
    invitation: Object,
});

const toast = useToast();

// Generar el enlace dinámicamente usando el token
const registrationLink = computed(() => {
    return `${window.location.origin}/register?token=${props.invitation.token}`;
});

// Función para copiar el enlace al portapapeles
const copyLink = () => {
    // Usamos execCommand por compatibilidad
    const textArea = document.createElement("textarea");
    textArea.value = registrationLink.value;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        toast.add({ severity: 'success', summary: '¡Copiado!', detail: 'Enlace copiado al portapapeles', life: 3000 });
    } catch (err) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo copiar el enlace', life: 3000 });
    }
    document.body.removeChild(textArea);
};

// Función para los colores de estado
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
</script>
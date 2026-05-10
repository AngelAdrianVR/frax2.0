<template>
    <AppLayout title="Editar Invitación">
        <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans">
            <div class="max-w-2xl mx-auto space-y-6">
                
                <!-- Header con botón de regreso -->
                <div class="flex items-center gap-4">
                    <Link :href="route('register-invitations.index')" 
                          class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors">
                        <i class="pi pi-arrow-left text-gray-600 dark:text-gray-300"></i>
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Editar Invitación
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Modifica los datos de la invitación enviada.
                        </p>
                    </div>
                </div>

                <!-- Formulario Estilo iOS -->
                <form @submit.prevent="submit" class="bg-white dark:bg-[#1C1C1E] rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                    
                    <div class="p-6 sm:p-8 space-y-6">
                        
                        <div v-if="invitation.status !== 'Pendiente'" class="rounded-xl bg-amber-50 dark:bg-amber-900/20 p-4 border border-amber-100 dark:border-amber-800/30 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="pi pi-exclamation-triangle text-amber-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-amber-800 dark:text-amber-300">Aviso</h3>
                                    <div class="mt-2 text-sm text-amber-700 dark:text-amber-400">
                                        <p>Esta invitación ya está <b>{{ invitation.status }}</b>. Solo se pueden editar invitaciones que aún están pendientes.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Correo Electrónico
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                v-model="form.email" 
                                :disabled="invitation.status !== 'Pendiente'"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 dark:ring-gray-800 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="{ 'ring-red-500 focus:ring-red-500': form.errors.email }"
                                required
                            />
                            <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <!-- Rol -->
                        <div>
                            <label for="role_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tipo de Relación / Rol
                            </label>
                            <select 
                                id="role_type" 
                                v-model="form.role_type" 
                                :disabled="invitation.status !== 'Pendiente'"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 dark:ring-gray-800 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="{ 'ring-red-500 focus:ring-red-500': form.errors.role_type }"
                                required
                            >
                                <option value="Familiar">Familiar / Residente</option>
                                <option value="Dueño">Dueño / Co-Propietario</option>
                            </select>
                            <p v-if="form.errors.role_type" class="mt-2 text-sm text-red-600">{{ form.errors.role_type }}</p>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="bg-gray-50 dark:bg-[#1C1C1E]/50 px-6 py-4 flex items-center justify-end gap-x-3 border-t border-gray-100 dark:border-gray-800">
                        <Link :href="route('register-invitations.index')" 
                              class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:text-gray-600 dark:hover:text-white transition-colors">
                            Cancelar
                        </Link>
                        <button 
                            v-if="invitation.status === 'Pendiente'"
                            type="submit" 
                            :disabled="form.processing"
                            class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95"
                        >
                            <span v-if="form.processing"><i class="pi pi-spin pi-spinner mr-2"></i> Guardando...</span>
                            <span v-else>Guardar Cambios</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
        <Toast />
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

const props = defineProps({
    invitation: Object,
});

const toast = useToast();

const form = useForm({
    email: props.invitation.email || '',
    role_type: props.invitation.role_type || '',
});

const submit = () => {
    form.put(route('register-invitations.update', props.invitation.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Actualizada', detail: 'Invitación modificada correctamente.', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Revisa los campos del formulario.', life: 3000 });
        }
    });
};
</script>
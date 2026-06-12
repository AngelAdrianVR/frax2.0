<template>
    <AppLayout title="Editar Invitación">
        <div class="py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto space-y-6">
                
                <div class="flex items-center gap-4">
                    <Link :href="route('register-invitations.index')" 
                          class="p-2 rounded-full bg-zinc-800 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight">
                            Editar Invitación
                        </h1>
                        <p class="mt-1 text-sm text-zinc-400">
                            Modifica los datos de la invitación enviada.
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    
                    <div class="p-6 sm:p-8 space-y-6">
                        
                        <div v-if="invitation.status !== 'Pendiente'" class="rounded-xl bg-amber-500/10 p-4 border border-amber-500/20 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="pi pi-exclamation-triangle text-amber-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-amber-300">Aviso</h3>
                                    <div class="mt-2 text-sm text-amber-400">
                                        <p>Esta invitación ya está <b>{{ invitation.status }}</b>. Solo se pueden editar invitaciones que aún están pendientes.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Correo Electrónico
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                v-model="form.email" 
                                :disabled="invitation.status !== 'Pendiente'"
                                class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.email }"
                                required
                            />
                            <p v-if="form.errors.email" class="text-sm text-red-400">{{ form.errors.email }}</p>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="role_type" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                Tipo de Relación / Rol
                            </label>
                            <select 
                                id="role_type" 
                                v-model="form.role_type" 
                                :disabled="invitation.status !== 'Pendiente'"
                                class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="{ 'border-red-500 focus:border-red-500': form.errors.role_type }"
                                required
                            >
                                <option value="Familiar">Familiar / Residente</option>
                                <option value="Dueño">Dueño / Co-Propietario</option>
                            </select>
                            <p v-if="form.errors.role_type" class="text-sm text-red-400">{{ form.errors.role_type }}</p>
                        </div>
                    </div>

                    <div class="bg-zinc-800/30 px-6 py-4 flex items-center justify-end gap-x-3 border-t border-zinc-800/60">
                        <Link :href="route('register-invitations.index')" 
                              class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                            Cancelar
                        </Link>
                        <button 
                            v-if="invitation.status === 'Pendiente'"
                            type="submit" 
                            :disabled="form.processing"
                            class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50"
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

const roleOptions = [
    { label: 'Familiar / Residente', value: 'Familiar' },
    { label: 'Dueño / Co-Propietario', value: 'Dueño' },
];

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
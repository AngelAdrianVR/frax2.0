<template>
    <AppLayout title="Nueva Invitación">
        <div class="py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto space-y-6">
                
                <!-- Header con botón de regreso -->
                <div class="flex items-center gap-4">
                    <Link :href="route('register-invitations.index')" 
                          class="p-2 rounded-full bg-zinc-800 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight m-0">
                            Nueva invitación
                        </h1>
                        <p class="mt-1 text-sm text-zinc-400">
                            Envía un acceso para que un familiar o dueño se registre en el sistema.
                        </p>
                    </div>
                </div>

                <!-- Formulario -->
                <form @submit.prevent="submit" class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    
                    <div class="p-6 sm:p-8 space-y-6">
                        <!-- Email -->
                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="text-xs font-medium text-zinc-400">
                                Correo electrónico
                            </label>
                            <InputText
                                id="email"
                                v-model="form.email"
                                placeholder="ejemplo@correo.com"
                                class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                :class="{ 'p-invalid': form.errors.email }"
                            />
                            <p v-if="form.errors.email" class="text-sm text-red-400">{{ form.errors.email }}</p>
                        </div>

                        <!-- Rol -->
                        <div class="flex flex-col gap-1.5">
                            <label for="role_type" class="text-xs font-medium text-zinc-400">
                                Tipo de relación / Rol
                            </label>
                            <Select
                                id="role_type"
                                v-model="form.role_type"
                                :options="roleOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecciona el tipo"
                                class="w-full !rounded-xl !text-[13px]"
                                :class="{ 'p-invalid': form.errors.role_type }"
                                pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                            />
                            <p v-if="form.errors.role_type" class="text-sm text-red-400">{{ form.errors.role_type }}</p>
                        </div>
                        
                        <!-- Mensaje Informativo -->
                        <div class="rounded-xl bg-[#0E63B1]/10 p-4 border border-[#0E63B1]/20">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="pi pi-info-circle text-[#0E63B1]"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-zinc-200">¿Qué sucederá?</h3>
                                    <div class="mt-2 text-sm text-zinc-400">
                                        <p>Se enviará un correo electrónico con un enlace único y seguro. El usuario tendrá 48 horas para hacer clic y crear su cuenta vinculada a esta propiedad.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="bg-zinc-800/30 px-6 py-4 flex items-center justify-end gap-x-3 border-t border-zinc-800/60">
                        <Link :href="route('register-invitations.index')" 
                              class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                            Cancelar
                        </Link>
                        <Button
                            type="submit"
                            :label="form.processing ? 'Enviando...' : 'Enviar invitación'"
                            :loading="form.processing"
                            class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5"
                        />
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
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

const toast = useToast();

const form = useForm({
    email: '',
    role_type: '',
});

const roleOptions = [
    { label: 'Familiar / Residente', value: 'Familiar' },
    { label: 'Dueño / Co-Propietario', value: 'Dueño' },
];

const submit = () => {
    form.post(route('register-invitations.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Enviada', detail: 'Invitación enviada correctamente.', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Revisa los campos del formulario.', life: 3000 });
        }
    });
};
</script>
<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

const props = defineProps({
    userEdit: Object,
    roles: Array
});

const showPassword = ref(false);

const form = useForm({
    name: props.userEdit.name || '',
    email: props.userEdit.email || '',
    password: '',
    alias: props.userEdit.alias || '',
    phone: props.userEdit.phone || '',
    role_name: props.userEdit.role_name || '',
    committee_role: props.userEdit.committee_role || ''
});

const roleOptions = computed(() =>
    props.roles.map(r => ({ label: r.name, value: r.name }))
);

const submit = () => {
    form.put(route('users.update', props.userEdit.id), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AppLayout title="Editar Residente">
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 px-4 sm:px-0 flex justify-between items-center">
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight m-0">Editar residente</h1>
                        <p class="text-sm text-zinc-400 mt-1">Actualiza la información de {{ form.name }}.</p>
                    </div>
                    <Link :href="route('users.index')" class="text-zinc-400 hover:text-zinc-200 font-medium text-sm flex items-center gap-1 transition-colors">
                        <i class="pi pi-arrow-left"></i> Volver al directorio
                    </Link>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    <div class="p-8">
                        <form @submit.prevent="submit" class="space-y-6" autocomplete="off">

                            <input type="email" style="display:none">
                            <input type="password" style="display:none">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nombre -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Nombre completo <span class="text-red-400">*</span></label>
                                    <InputText v-model="form.name" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" :class="{ 'p-invalid': form.errors.name }" />
                                    <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                                </div>

                                <!-- Alias -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Alias / Apodo (opcional)</label>
                                    <InputText v-model="form.alias" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                    <p v-if="form.errors.alias" class="text-sm text-red-400">{{ form.errors.alias }}</p>
                                </div>

                                <!-- Email -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Correo electrónico <span class="text-red-400">*</span></label>
                                    <InputText v-model="form.email" type="email" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" :class="{ 'p-invalid': form.errors.email }" />
                                    <p v-if="form.errors.email" class="text-sm text-red-400">{{ form.errors.email }}</p>
                                </div>

                                <!-- Teléfono -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Teléfono</label>
                                    <InputText v-model="form.phone" @input="form.phone = form.phone.replace(/[^0-9]/g, '')" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                    <p v-if="form.errors.phone" class="text-sm text-red-400">{{ form.errors.phone }}</p>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <p class="text-sm text-zinc-400 mb-4 bg-zinc-800/40 p-3 rounded-xl border border-zinc-700/40">
                                        <i class="pi pi-info-circle mr-1"></i> Deja la contraseña en blanco si no deseas cambiarla. Después el usuario podrá cambiarla desde su perfil.
                                    </p>
                                </div>

                                <!-- Contraseña -->
                                <div class="md:col-span-2 max-w-md">
                                    <label class="text-xs font-medium text-zinc-400">Nueva contraseña (temporal)</label>
                                    <div class="relative mt-1.5">
                                        <InputText v-model="form.password" :type="showPassword ? 'text' : 'password'" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 !pr-10" :class="{ 'p-invalid': form.errors.password }" />
                                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-500 hover:text-zinc-300 focus:outline-none transition-colors">
                                            <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'" class="text-lg"></i>
                                        </button>
                                    </div>
                                    <p v-if="form.errors.password" class="text-sm text-red-400 mt-1">{{ form.errors.password }}</p>
                                </div>

                                <!-- Separador visual -->
                                <div class="md:col-span-2 border-t border-zinc-800/60 pt-4 mt-2">
                                    <h3 class="text-sm font-medium text-zinc-200 mb-4">Clasificación y Permisos</h3>
                                </div>

                                <!-- Rol -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Tipo de usuario (Permisos) <span class="text-red-400">*</span></label>
                                    <Select v-model="form.role_name" :options="roleOptions" optionLabel="label" optionValue="value" placeholder="Selecciona el tipo de acceso" class="w-full !rounded-xl !text-[13px]" :class="{ 'p-invalid': form.errors.role_name }" pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                    <p class="text-xs text-zinc-500">Define a qué partes del sistema tendrá acceso.</p>
                                </div>

                                <!-- Puesto -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Puesto / Cargo (opcional)</label>
                                    <InputText v-model="form.committee_role" placeholder="Ej: Tesorero, Administrador, Vigilante..." class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500" />
                                    <p class="text-xs text-zinc-500">Este texto se mostrará públicamente en su tarjeta.</p>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-zinc-800/60">
                                <Button type="submit" label="Actualizar residente" icon="pi pi-save" :loading="form.processing" class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5" />
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
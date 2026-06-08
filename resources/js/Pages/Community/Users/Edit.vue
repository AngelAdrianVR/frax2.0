<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

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
    role_name: props.userEdit.role_name || '', // El rol que tiene actualmente en el sistema
    committee_role: props.userEdit.committee_role || ''
});

const submit = () => {
    form.put(route('users.update', props.userEdit.id));
};
</script>

<template>
    <AppLayout title="Editar Residente">
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 px-4 sm:px-0 flex justify-between items-center">
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight">Editar Residente</h1>
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
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Nombre Completo <span class="text-red-400">*</span></label>
                                    <input 
                                        v-model="form.name" 
                                        type="text" 
                                        required
                                        autocomplete="off"
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600"
                                    >
                                    <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                                </div>

                                <!-- Alias -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Alias / Apodo (Opcional)</label>
                                    <input 
                                        v-model="form.alias" 
                                        type="text" 
                                        autocomplete="off"
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600"
                                    >
                                    <p v-if="form.errors.alias" class="text-sm text-red-400">{{ form.errors.alias }}</p>
                                </div>

                                <!-- Email -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Correo Electrónico <span class="text-red-400">*</span></label>
                                    <input 
                                        v-model="form.email" 
                                        type="email" 
                                        required
                                        autocomplete="off"
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600"
                                    >
                                    <p v-if="form.errors.email" class="text-sm text-red-400">{{ form.errors.email }}</p>
                                </div>

                                <!-- Teléfono -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Teléfono</label>
                                    <input 
                                        v-model="form.phone" 
                                        type="text"
                                        inputmode="numeric"
                                        autocomplete="off"
                                        @input="form.phone = form.phone.replace(/[^0-9]/g, '')"
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600"
                                    >
                                    <p v-if="form.errors.phone" class="text-sm text-red-400">{{ form.errors.phone }}</p>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <p class="text-sm text-zinc-400 mb-4 bg-zinc-800/40 p-3 rounded-xl border border-zinc-700/40">
                                        <i class="pi pi-info-circle mr-1"></i> Deja la contraseña en blanco si no deseas cambiarla. Después el usuario podrá cambiarla desde su perfil.
                                    </p>
                                </div>

                                <!-- Contraseña con botón de visualización -->
                                <div class="md:col-span-2 max-w-md">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Nueva Contraseña (Temporal)</label>
                                    <div class="relative mt-1.5">
                                        <input 
                                            v-model="form.password" 
                                            :type="showPassword ? 'text' : 'password'" 
                                            autocomplete="new-password"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 pr-10 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600"
                                        >
                                        <button 
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-500 hover:text-zinc-300 focus:outline-none transition-colors"
                                        >
                                            <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'" class="text-lg"></i>
                                        </button>
                                    </div>
                                    <p v-if="form.errors.password" class="text-sm text-red-400 mt-1">{{ form.errors.password }}</p>
                                </div>

                                <!-- Separador visual -->
                                <div class="md:col-span-2 border-t border-zinc-800/60 pt-4 mt-2">
                                    <h3 class="text-sm font-medium text-zinc-200 mb-4">Clasificación y Permisos</h3>
                                </div>

                                <!-- Rol de Sistema (Selector) -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Tipo de Usuario (Permisos) <span class="text-red-400">*</span></label>
                                    <select 
                                        v-model="form.role_name" 
                                        required
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600"
                                    >
                                        <option value="">Selecciona el tipo de acceso</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    <p class="text-xs text-zinc-500">Define a qué partes del sistema tendrá acceso.</p>
                                </div>

                                <!-- Puesto / Mesa directiva (Texto) -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Puesto / Cargo (Opcional)</label>
                                    <input 
                                        v-model="form.committee_role" 
                                        type="text" 
                                        placeholder="Ej: Tesorero, Administrador, Vigilante..."
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600"
                                    >
                                    <p class="text-xs text-zinc-500">Este texto se mostrará públicamente en su tarjeta.</p>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-zinc-800/60">
                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50 flex items-center gap-2"
                                >
                                    <i class="pi pi-save" v-if="!form.processing"></i>
                                    <i class="pi pi-spinner pi-spin" v-else></i>
                                    Actualizar Residente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
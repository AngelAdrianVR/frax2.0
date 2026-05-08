<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    roles: Array
});

const showPassword = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    alias: '',
    phone: '',
    role_name: 'Residente', // Valor por defecto
    committee_role: ''
});

const submit = () => {
    form.post(route('users.store'));
};
</script>

<template>
    <AppLayout title="Crear Residente">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen transition-colors duration-300">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 px-4 sm:px-0 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Nuevo Residente</h1>
                        <p class="text-zinc-500 dark:text-zinc-400 mt-1">Registra un nuevo usuario en la comunidad.</p>
                    </div>
                    <Link :href="route('users.index')" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 font-medium text-sm flex items-center gap-1 transition-colors">
                        <i class="pi pi-arrow-left"></i> Volver al directorio
                    </Link>
                </div>

                <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-sm rounded-2xl border border-zinc-200 dark:border-zinc-700">
                    <div class="p-8">
                        <form @submit.prevent="submit" class="space-y-6" autocomplete="off">
                            
                            <!-- Campo oculto para engañar al navegador y que no autocomplete -->
                            <input type="email" style="display:none">
                            <input type="password" style="display:none">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                                    <input 
                                        v-model="form.name" 
                                        type="text" 
                                        required
                                        autocomplete="off"
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-colors"
                                    >
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                </div>

                                <!-- Alias -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Alias / Apodo (Opcional)</label>
                                    <input 
                                        v-model="form.alias" 
                                        type="text" 
                                        autocomplete="off"
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-colors"
                                    >
                                    <p v-if="form.errors.alias" class="mt-1 text-sm text-red-600">{{ form.errors.alias }}</p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                                    <input 
                                        v-model="form.email" 
                                        type="email" 
                                        required
                                        autocomplete="off"
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-colors"
                                    >
                                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Teléfono</label>
                                    <input 
                                        v-model="form.phone" 
                                        type="text"
                                        inputmode="numeric"
                                        autocomplete="off"
                                        @input="form.phone = form.phone.replace(/[^0-9]/g, '')"
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-colors"
                                    >
                                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4 bg-zinc-50 dark:bg-zinc-900 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                        <i class="pi pi-info-circle mr-1"></i> Asigna una contraseña temporal. Después el usuario podrá cambiarla desde su perfil.
                                    </p>
                                </div>

                                <!-- Contraseña con botón de visualización -->
                                <div class="md:col-span-2 max-w-md">
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Contraseña Temporal <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input 
                                            v-model="form.password" 
                                            :type="showPassword ? 'text' : 'password'" 
                                            required
                                            autocomplete="new-password"
                                            class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-colors pr-10"
                                        >
                                        <button 
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-indigo-500 focus:outline-none"
                                        >
                                            <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'" class="text-lg"></i>
                                        </button>
                                    </div>
                                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                                </div>

                                <!-- Separador visual -->
                                <div class="md:col-span-2 border-t border-zinc-200 dark:border-zinc-700 pt-4 mt-2">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Clasificación y Permisos</h3>
                                </div>

                                <!-- Rol de Sistema (Selector) -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tipo de Usuario (Permisos) <span class="text-red-500">*</span></label>
                                    <select 
                                        v-model="form.role_name" 
                                        required
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-colors"
                                    >
                                        <option value="">Selecciona el tipo de acceso</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    <p class="mt-1 text-xs text-zinc-500">Define a qué partes del sistema tendrá acceso.</p>
                                </div>

                                <!-- Puesto / Mesa directiva (Texto) -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Puesto / Cargo (Opcional)</label>
                                    <input 
                                        v-model="form.committee_role" 
                                        type="text" 
                                        placeholder="Ej: Tesorero, Administrador, Vigilante..."
                                        class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-colors"
                                    >
                                    <p class="mt-1 text-xs text-zinc-500">Este texto se mostrará públicamente en su tarjeta.</p>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-zinc-200 dark:border-zinc-700 flex justify-end">
                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-sm transition-colors disabled:opacity-75 flex items-center gap-2"
                                >
                                    <i class="pi pi-save" v-if="!form.processing"></i>
                                    <i class="pi pi-spinner pi-spin" v-else></i>
                                    Crear Residente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
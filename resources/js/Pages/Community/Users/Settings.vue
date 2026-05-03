<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

const form = useForm({
    alias: user.alias || '',
    phone: user.phone || '',
    show_phone: user.show_phone || false,
    show_email: user.show_email || false,
    accept_messages: user.accept_messages || false,
});

const submit = () => {
    // Actualizado a la ruta del nuevo controlador
    form.post(route('users.settings.update'), {
        preserveScroll: true,
        onSuccess: () => alert('Preferencias guardadas correctamente.')
    });
};
</script>

<template>
    <AppLayout title="Ajustes del Directorio">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <Link :href="route('users.index')" class="inline-flex items-center gap-2 text-sm font-medium text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 mb-6 transition-colors">
                    <i class="pi pi-arrow-left"></i> Volver al Directorio
                </Link>

                <div class="bg-white dark:bg-zinc-800 shadow-sm sm:rounded-xl p-8 border border-zinc-200 dark:border-zinc-700">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                            <i class="pi pi-user-edit text-indigo-500"></i> Mi Perfil Comunitario
                        </h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Controla cómo te ven tus vecinos en el directorio del fraccionamiento.</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Alias -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nombre o Alias a mostrar</label>
                            <input v-model="form.alias" type="text" placeholder="Ej: Familia Pérez, o tu Nombre" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="mt-1 text-xs text-zinc-400">Si lo dejas en blanco, se mostrará tu nombre completo de registro.</p>
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Número Telefónico</label>
                            <input v-model="form.phone" type="text" placeholder="Tu número celular o fijo" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <hr class="border-zinc-200 dark:border-zinc-700 my-6">
                        
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Interruptores de Visibilidad</h3>
                        
                        <!-- Toggles -->
                        <div class="space-y-4">
                            <label class="flex items-center justify-between p-4 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer transition">
                                <div>
                                    <span class="block font-medium text-zinc-900 dark:text-white"><i class="pi pi-phone text-zinc-400 mr-2"></i>Mostrar Teléfono</span>
                                    <span class="text-xs text-zinc-500">Permite que tus vecinos vean tu número y te llamen.</span>
                                </div>
                                <input type="checkbox" v-model="form.show_phone" class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500 bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600">
                            </label>

                            <label class="flex items-center justify-between p-4 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer transition">
                                <div>
                                    <span class="block font-medium text-zinc-900 dark:text-white"><i class="pi pi-envelope text-zinc-400 mr-2"></i>Mostrar Correo Electrónico</span>
                                    <span class="text-xs text-zinc-500">Muestra el correo con el que te registraste.</span>
                                </div>
                                <input type="checkbox" v-model="form.show_email" class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500 bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600">
                            </label>

                            <label class="flex items-center justify-between p-4 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer transition">
                                <div>
                                    <span class="block font-medium text-zinc-900 dark:text-white"><i class="pi pi-comments text-zinc-400 mr-2"></i>Recibir Mensajes Internos</span>
                                    <span class="text-xs text-zinc-500">Acepta que otros residentes inicien un chat contigo desde la plataforma.</span>
                                </div>
                                <input type="checkbox" v-model="form.accept_messages" class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500 bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600">
                            </label>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 shadow-sm disabled:opacity-50 transition-colors">
                                Guardar Preferencias
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
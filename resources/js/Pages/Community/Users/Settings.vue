<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from "primevue/usetoast";
import Toast from 'primevue/toast';

const props = defineProps({
    user: Object
});

const toast = useToast();
const photoInput = ref(null);
const photoPreview = ref(null);

// Inicializamos el formulario con los datos ACTUALES del usuario
const form = useForm({
    alias: props.user.alias || '',
    phone: props.user.phone || '',
    show_email: !!props.user.show_email, 
    show_phone: !!props.user.show_phone,
    accept_messages: !!props.user.accept_messages,
    photo: null, // Nuevo campo para la imagen
});

// Función para manejar la vista previa de la foto seleccionada
const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;
    
    // Asignar el archivo al formulario para que Inertia lo envíe
    form.photo = photo;
    
    // Leer el archivo localmente para mostrar la vista previa instantánea
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
};

const submit = () => {
    // La ruta en tu web.php es tipo POST, lo cual es ideal para subir archivos con Inertia
    form.post(route('users.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ 
                severity: 'success', 
                summary: 'Actualizado', 
                detail: 'Tus preferencias se han guardado correctamente.', 
                life: 3000 
            });
            // Limpiar el input de la foto por si quiere subir otra sin recargar
            if (photoInput.value) {
                photoInput.value.value = null;
            }
        }
    });
};
</script>

<template>
    <AppLayout title="Mis Ajustes">
        <Toast />
        
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen transition-colors duration-300">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 px-4 sm:px-0 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Mis Ajustes de Perfil</h1>
                        <p class="text-zinc-500 dark:text-zinc-400 mt-1">Controla cómo te ven tus vecinos en el directorio.</p>
                    </div>
                    <Link :href="route('users.index')" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 font-medium text-sm flex items-center gap-1 transition-colors">
                        <i class="pi pi-arrow-left"></i> Volver al directorio
                    </Link>
                </div>

                <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <form @submit.prevent="submit">
                        <div class="p-8 space-y-8">
                            
                            <!-- Sección: Información Personal y Foto -->
                            <div>
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">
                                    <i class="pi pi-id-card mr-2 text-indigo-500"></i> Información Visible
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    
                                    <!-- Selector de Foto de Perfil -->
                                    <div class="md:col-span-2 flex items-center gap-6 mb-2">
                                        <div class="h-20 w-20 rounded-full overflow-hidden bg-zinc-100 dark:bg-zinc-800 border-2 border-zinc-200 dark:border-zinc-700 shadow-sm shrink-0">
                                            <!-- Muestra la vista previa, o la foto guardada, o el avatar por defecto -->
                                            <img :src="photoPreview || user.profile_photo_url" alt="Avatar" class="h-full w-full object-cover">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Foto de Perfil</label>
                                            <input type="file" ref="photoInput" class="hidden" @change="updatePhotoPreview" accept="image/*">
                                            
                                            <button type="button" @click="$refs.photoInput.click()" class="bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 px-4 py-2 rounded-xl text-sm font-medium border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 shadow-sm transition-colors flex items-center gap-2">
                                                <i class="pi pi-camera"></i> Subir nueva foto
                                            </button>
                                            
                                            <p class="text-xs text-zinc-500 mt-2">JPG, JPEG o PNG. Máximo 1MB.</p>
                                            <p v-if="form.errors.photo" class="mt-1 text-sm text-red-600">{{ form.errors.photo }}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">¿Cómo prefieres que te llamen? (Alias)</label>
                                        <input 
                                            v-model="form.alias" 
                                            type="text" 
                                            placeholder="Ej. Familia López, o tu apodo"
                                            class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-colors"
                                        >
                                        <p class="mt-1 text-xs text-zinc-500">Si lo dejas en blanco, se mostrará tu nombre completo.</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tu número de teléfono</label>
                                        <input 
                                            v-model="form.phone" 
                                            type="text"
                                            inputmode="numeric"
                                            @input="form.phone = form.phone.replace(/[^0-9]/g, '')"
                                            placeholder="Ej. 5512345678"
                                            class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-colors"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Sección: Privacidad -->
                            <div>
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">
                                    <i class="pi pi-lock mr-2 text-indigo-500"></i> Preferencias de Privacidad
                                </h3>

                                <div class="space-y-4">
                                    <!-- Toggle: Correo -->
                                    <label class="flex items-start gap-3 cursor-pointer group">
                                        <div class="relative flex items-center mt-1">
                                            <input type="checkbox" v-model="form.show_email" class="w-5 h-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-700 dark:checked:bg-indigo-500 transition-colors cursor-pointer">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Mostrar mi correo electrónico</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Tus vecinos podrán ver tu correo y enviarte mensajes a esa dirección.</p>
                                        </div>
                                    </label>

                                    <!-- Toggle: Teléfono -->
                                    <div class="space-y-2">
                                        <label class="flex items-start gap-3 cursor-pointer group">
                                            <div class="relative flex items-center mt-1">
                                                <input type="checkbox" v-model="form.show_phone" class="w-5 h-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-700 dark:checked:bg-indigo-500 transition-colors cursor-pointer">
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Mostrar mi número de teléfono</p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Tus vecinos podrán ver tu número para llamadas o WhatsApp.</p>
                                            </div>
                                        </label>
                                        
                                        <!-- Alerta Reactiva si no tiene teléfono registrado -->
                                        <div v-if="form.show_phone && !form.phone" class="ml-8 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 flex gap-2 text-sm text-amber-800 dark:text-amber-200">
                                            <i class="pi pi-exclamation-triangle mt-0.5"></i>
                                            <p>Has elegido mostrar tu teléfono, pero <b>no tienes ningún número registrado</b>. Asegúrate de agregarlo arriba, de lo contrario no habrá nada que mostrar.</p>
                                        </div>
                                    </div>

                                    <!-- Toggle: Mensajes Internos -->
                                    <label class="flex items-start gap-3 cursor-pointer group">
                                        <div class="relative flex items-center mt-1">
                                            <input type="checkbox" v-model="form.accept_messages" class="w-5 h-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-700 dark:checked:bg-indigo-500 transition-colors cursor-pointer">
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Aceptar mensajes directos</p>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Permite que otros residentes te envíen mensajes internos a través de la plataforma.</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-8 py-5 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-700 flex justify-end">
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-sm transition-colors disabled:opacity-75 flex items-center gap-2"
                            >
                                <i class="pi pi-save" v-if="!form.processing"></i>
                                <i class="pi pi-spinner pi-spin" v-else></i>
                                Guardar Preferencias
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
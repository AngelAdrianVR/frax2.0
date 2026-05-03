<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router, useForm, Link } from '@inertiajs/vue3';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';

const props = defineProps({
    services: Object, 
    isAdmin: Boolean
});

const confirm = useConfirm();

const showModal = ref(false);
const isEditing = ref(false);
const photoPreviews = ref([]);

const form = useForm({
    id: null,
    category: 'profesional',
    title: '',
    contact_name: '',
    contact_phone: '',
    description: '',
    is_recommended: false,
    photos: [] 
});

const handlePhotoUpload = (e) => {
    const files = Array.from(e.target.files);
    
    if (files.length > 5) {
        alert("Solo puedes subir un máximo de 5 fotos.");
        e.target.value = '';
        return;
    }
    
    form.photos = files;

    photoPreviews.value = [];
    files.forEach(file => {
        photoPreviews.value.push(URL.createObjectURL(file));
    });
};

const openModal = (service = null) => {
    photoPreviews.value = [];
    if (service) {
        isEditing.value = true;
        form.id = service.id;
        form.category = service.category;
        form.title = service.title;
        form.contact_name = service.contact_name;
        form.contact_phone = service.contact_phone;
        form.description = service.description || '';
        form.is_recommended = service.is_recommended == 1;
        form.photos = []; 
    } else {
        isEditing.value = false;
        form.reset();
    }
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        // Actualizado para usar la nueva ruta de users
        form.transform((data) => ({ ...data, _method: 'put' }))
            .post(route('users.services.update', form.id), {
                onSuccess: () => { showModal.value = false; form.reset(); }
            });
    } else {
        form.post(route('users.services.store'), {
            onSuccess: () => { showModal.value = false; form.reset(); }
        });
    }
};

const deleteService = (id) => {
    confirm.require({
        message: '¿Estás seguro de que deseas eliminar este servicio del directorio?',
        header: 'Confirmar Eliminación',
        icon: 'pi pi-trash',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Sí, eliminar',
        rejectLabel: 'Cancelar',
        accept: () => {
            router.delete(route('users.services.destroy', id), { preserveScroll: true });
        }
    });
};

const selectedService = ref(null);
const viewDetails = (service) => {
    selectedService.value = service;
};
</script>

<template>
    <AppLayout title="Habilidades y Servicios">
        <ConfirmDialog></ConfirmDialog>

        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-8 px-4 sm:px-0">
                    <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight mb-6">Directorio Comunitario</h1>
                    
                    <div class="border-b border-zinc-200 dark:border-zinc-700 flex gap-6 overflow-x-auto">
                        <Link :href="route('users.index')" class="pb-3 font-medium text-sm border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 whitespace-nowrap">
                            <i class="pi pi-users mr-2"></i>Residentes
                        </Link>
                        <Link :href="route('users.emergencies')" class="pb-3 font-medium text-sm border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 whitespace-nowrap">
                            <i class="pi pi-shield mr-2"></i>Emergencias
                        </Link>
                        <div class="pb-3 font-medium text-sm border-b-2 border-green-500 text-green-600 dark:text-green-400 whitespace-nowrap">
                            <i class="pi pi-briefcase mr-2"></i>Habilidades y Servicios
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-6 px-4 sm:px-0">
                    <h2 class="text-xl font-bold text-zinc-800 dark:text-white">Directorio de Habilidades y Servicios</h2>
                    <button @click="openModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 shadow-sm transition-colors">
                        <i class="pi pi-plus mr-1"></i> Publicar Servicio
                    </button>
                </div>

                <div v-for="(group, category) in services" :key="category" class="mb-10 px-4 sm:px-0">
                    <h3 class="font-bold text-zinc-700 dark:text-zinc-300 mb-4 capitalize border-b border-zinc-200 dark:border-zinc-700 pb-2">
                        <i class="pi pi-tag text-green-500 mr-2"></i> 
                        {{ category === 'profesional' ? 'Servicios Profesionales' : (category === 'oficio' ? 'Oficios y Proveedores' : 'Emprendimientos') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div v-for="service in group" :key="service.id" class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm hover:shadow-md transition-shadow border border-zinc-100 dark:border-zinc-700 flex flex-col overflow-hidden relative group">
                            
                            <span v-if="service.is_recommended" class="absolute top-3 right-3 bg-yellow-400 text-yellow-900 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm z-10 flex items-center gap-1">
                                <i class="pi pi-star-fill"></i>Recomendado
                            </span>

                            <div @click="viewDetails(service)" class="h-32 bg-zinc-200 dark:bg-zinc-700 cursor-pointer relative overflow-hidden group-hover:opacity-90 transition-opacity">
                                <img v-if="service.photos && service.photos.length > 0" :src="service.photos[0]" class="w-full h-full object-cover">
                                <div v-else class="w-full h-full bg-gradient-to-br from-green-400 to-emerald-600 opacity-80 flex items-center justify-center">
                                    <i class="pi pi-briefcase text-4xl text-white/50"></i>
                                </div>
                            </div>
                            
                            <div class="p-5 flex-1 flex flex-col">
                                <h4 @click="viewDetails(service)" class="font-bold text-lg text-zinc-900 dark:text-white mb-1 cursor-pointer hover:text-green-600 transition-colors">{{ service.title }}</h4>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4 line-clamp-2 flex-1">{{ service.description }}</p>
                                
                                <div class="bg-zinc-50 dark:bg-zinc-700/50 p-3 rounded-xl flex justify-between items-center border border-zinc-100 dark:border-zinc-600/50">
                                    <div>
                                        <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">{{ service.contact_name }}</p>
                                        <a :href="`https://wa.me/${service.contact_phone.replace(/\D/g,'')}`" target="_blank" class="text-sm text-green-600 dark:text-green-400 font-medium flex items-center gap-1 mt-0.5 hover:underline">
                                            <i class="pi pi-whatsapp"></i> WhatsApp
                                        </a>
                                    </div>
                                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="openModal(service)" class="text-indigo-400 hover:text-indigo-600 p-2 bg-white dark:bg-zinc-800 rounded-lg shadow-sm"><i class="pi pi-pencil"></i></button>
                                        <button @click="deleteService(service.id)" class="text-red-400 hover:text-red-600 p-2 bg-white dark:bg-zinc-800 rounded-lg shadow-sm"><i class="pi pi-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- MODAL DE DETALLES DEL SERVICIO -->
        <div v-if="selectedService" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div @click="selectedService = null" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
            <div class="bg-white dark:bg-zinc-800 rounded-3xl w-full max-w-2xl overflow-hidden relative z-10 shadow-2xl flex flex-col max-h-[90vh]">
                <div class="h-48 bg-green-600 relative flex-shrink-0">
                    <img v-if="selectedService.photos && selectedService.photos.length > 0" :src="selectedService.photos[0]" class="w-full h-full object-cover">
                    <button @click="selectedService = null" class="absolute top-4 right-4 bg-black/50 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-black/70 transition"><i class="pi pi-times"></i></button>
                </div>
                
                <div class="p-8 overflow-y-auto">
                    <div class="flex justify-between items-start mb-2">
                        <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 text-xs font-bold rounded-full uppercase tracking-wider">{{ selectedService.category }}</span>
                        <span v-if="selectedService.is_recommended" class="text-yellow-500 font-bold text-sm flex items-center gap-1"><i class="pi pi-star-fill"></i> Recomendado</span>
                    </div>
                    
                    <h3 class="text-3xl font-bold text-zinc-900 dark:text-white mt-3">{{ selectedService.title }}</h3>
                    <p class="text-lg text-zinc-600 dark:text-zinc-400 mt-2 font-medium"><i class="pi pi-user mr-1 text-zinc-400"></i> Ofrecido por: {{ selectedService.contact_name }}</p>
                    
                    <div class="mt-6">
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-2">Acerca del Servicio</h4>
                        <p class="text-zinc-600 dark:text-zinc-300 whitespace-pre-wrap leading-relaxed">{{ selectedService.description }}</p>
                    </div>

                    <div v-if="selectedService.photos && selectedService.photos.length > 1" class="mt-8">
                        <h4 class="font-bold text-zinc-900 dark:text-white mb-3">Galería</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div v-for="(photo, index) in selectedService.photos.slice(1)" :key="index" class="aspect-square rounded-xl overflow-hidden bg-zinc-100">
                                <img :src="photo" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-end">
                        <a :href="`https://wa.me/${selectedService.contact_phone.replace(/\D/g,'')}`" target="_blank" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-green-500/30 transition-all flex justify-center items-center gap-2 text-lg">
                            <i class="pi pi-whatsapp text-xl"></i> Contactar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL CREAR / EDITAR SERVICIO -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @click="showModal = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
            <div class="bg-white dark:bg-zinc-800 rounded-2xl w-full max-w-2xl p-6 md:p-8 relative z-10 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold dark:text-white">{{ isEditing ? 'Editar Servicio' : 'Registrar Servicio' }}</h3>
                    <button @click="showModal = false" class="text-zinc-400 hover:text-zinc-600 bg-zinc-100 dark:bg-zinc-700 w-8 h-8 rounded-full flex items-center justify-center"><i class="pi pi-times"></i></button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Categoría</label>
                            <select v-model="form.category" class="w-full rounded-xl border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white focus:ring-green-500 focus:border-green-500">
                                <option value="profesional">Servicios Profesionales</option>
                                <option value="oficio">Oficios / Proveedores Externos</option>
                                <option value="emprendimiento">Emprendimientos Internos</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Título / Nombre del Negocio</label>
                            <input v-model="form.title" type="text" placeholder="Ej. Clases de Inglés, Panadería..." class="w-full rounded-xl border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white" required>
                        </div>
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Nombre de Contacto</label>
                            <input v-model="form.contact_name" type="text" class="w-full rounded-xl border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white" required>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Teléfono (WhatsApp)</label>
                            <input v-model="form.contact_phone" type="text" class="w-full rounded-xl border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white" required>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Descripción completa y qué ofrecen</label>
                            <textarea v-model="form.description" rows="4" placeholder="Describe a detalle los servicios que se ofrecen, horarios, etc..." class="w-full rounded-xl border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-2 dark:text-zinc-300">Fotos del Trabajo / Emprendimiento (Max 5)</label>
                            
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-xl cursor-pointer bg-zinc-50 dark:hover:bg-zinc-800 dark:bg-zinc-700 hover:bg-zinc-100 border-zinc-300 dark:border-zinc-600 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="pi pi-cloud-upload text-3xl text-zinc-400 mb-2"></i>
                                        <p class="mb-2 text-sm text-zinc-500 dark:text-zinc-400"><span class="font-semibold">Haz clic para subir</span> o arrastra las imágenes</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">PNG, JPG o JPEG (MAX. 5 imágenes)</p>
                                    </div>
                                    <input type="file" class="hidden" multiple accept="image/*" @change="handlePhotoUpload" />
                                </label>
                            </div>
                            
                            <div v-if="photoPreviews.length > 0" class="flex gap-3 mt-4 overflow-x-auto pb-2">
                                <div v-for="(preview, idx) in photoPreviews" :key="idx" class="w-20 h-20 rounded-lg overflow-hidden border border-zinc-200 flex-shrink-0">
                                    <img :src="preview" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-span-2 pt-2 border-t border-zinc-100 dark:border-zinc-700">
                            <label class="flex items-center p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-xl cursor-pointer border border-yellow-200 dark:border-yellow-800/30">
                                <input type="checkbox" v-model="form.is_recommended" class="w-5 h-5 rounded text-green-600 focus:ring-green-500 border-zinc-300"> 
                                <div class="ml-3">
                                    <span class="block font-semibold text-yellow-800 dark:text-yellow-500 flex items-center gap-2">Sello de "Recomendado" <i class="pi pi-star-fill"></i></span>
                                    <span class="text-xs text-yellow-700/70 dark:text-yellow-600">Destaca este servicio si ha sido aprobado por el comité o tiene excelentes referencias.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" @click="showModal = false" class="px-6 py-2.5 text-zinc-700 font-medium bg-zinc-100 rounded-xl hover:bg-zinc-200 transition-colors">Cancelar</button>
                        <button type="submit" :disabled="form.processing" class="px-6 py-2.5 text-white font-medium bg-green-600 rounded-xl hover:bg-green-700 disabled:opacity-50 transition-colors shadow-lg shadow-green-500/30">
                            <i class="pi pi-check mr-1"></i> {{ isEditing ? 'Guardar Cambios' : 'Publicar Servicio' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </AppLayout>
</template>
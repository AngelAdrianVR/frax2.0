<template>
    <AppLayout :title="'Mis Mascotas'">
        <ConfirmDialog></ConfirmDialog>

        <div class="text-zinc-800 dark:text-zinc-100 p-4 sm:p-8 transition-colors duration-300">
            
            <!-- Encabezado -->
            <div class="max-w-7xl mx-auto mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-zinc-100 tracking-tight m-0">
                        Mis mascotas
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Gestiona el perfil, historial médico y documentación de tus mascotas.
                    </p>
                </div>
                
                <PrimaryButton @click="$inertia.visit(route('pets.create'))">
                    <i class="pi pi-plus mr-2"></i> Registrar Mascota
                </PrimaryButton>
            </div>

            <div class="max-w-7xl mx-auto">
                
                <!-- ESTADO VACÍO -->
                <div v-if="pets.data.length === 0" class="rounded-xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <div class="mx-auto h-12 w-12 text-zinc-400">
                        <i class="pi pi-briefcase text-4xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">No hay mascotas registradas</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Comienza registrando a tu compañero.</p>
                </div>

                <div v-else>
                    <!-- VISTA DESKTOP (Tabla) -->
                    <div class="hidden md:block overflow-hidden rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-800/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-400">
                                        Mascota
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-400">
                                        Detalles & Especie
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-400">
                                        Salud & Documentos
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-zinc-400">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                <tr 
                                    v-for="pet in pets.data" 
                                    :key="pet.id" 
                                    @click="openModal(pet)"
                                    class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors cursor-pointer group"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 relative">
                                                <img 
                                                    v-if="pet.photo_url" 
                                                    :src="pet.photo_url" 
                                                    alt="Foto mascota" 
                                                    class="h-full w-full object-cover"
                                                >
                                                <div v-else class="h-full w-full flex items-center justify-center text-indigo-400 dark:text-indigo-300">
                                                    <i class="fa-solid fa-paw"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ pet.name }}
                                                </div>
                                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    Alta: {{ formatDate(pet.created_at) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-zinc-900 dark:text-white font-medium">{{ pet.race || 'Sin raza' }}</div>
                                        <span class="text-xs text-zinc-500 capitalize">{{ pet.species }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex gap-2">
                                                <!-- Badges Salud -->
                                                <span 
                                                    v-if="pet.additionals?.sterilized" 
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200"
                                                    title="Esterilizado"
                                                >
                                                    Est.
                                                </span>
                                                <span 
                                                    v-if="pet.additionals?.vaccinated" 
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                                                    title="Vacunado"
                                                >
                                                    Vac.
                                                </span>
                                            </div>
                                            <!-- Indicador Docs -->
                                            <span v-if="pet.documents && pet.documents.length > 0" class="flex items-center text-xs text-blue-600 dark:text-blue-400">
                                                <i class="pi pi-file mr-1"></i> {{ pet.documents.length }} docs
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            <i class="pi pi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- VISTA MÓVIL (Tarjetas) -->
                    <div class="md:hidden grid grid-cols-1 gap-4">
                        <div 
                            v-for="pet in pets.data" 
                            :key="pet.id" 
                            @click="openModal(pet)"
                            class="rounded-xl shadow-md overflow-hidden border border-zinc-200 dark:border-zinc-700 cursor-pointer active:scale-[0.98] transition-transform"
                        >
                            <div class="bg-zinc-50 dark:bg-zinc-700/50 px-4 py-3 border-b border-zinc-100 dark:border-zinc-700 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full overflow-hidden bg-zinc-200 dark:bg-zinc-600 flex items-center justify-center border border-zinc-300 dark:border-zinc-500">
                                        <img v-if="pet.photo_url" :src="pet.photo_url" class="h-full w-full object-cover">
                                        <i v-else class="pi pi-github text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-zinc-800 dark:text-white text-base">{{ pet.name }}</span>
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 capitalize">{{ pet.species }}</span>
                                    </div>
                                </div>
                                <div class="flex gap-1">
                                    <i v-if="pet.additionals?.vaccinated" class="pi pi-shield text-green-500" title="Vacunado"></i>
                                    <i v-if="pet.additionals?.sterilized" class="pi pi-heart text-purple-500" title="Esterilizado"></i>
                                </div>
                            </div>
                            <div class="p-4 flex justify-between items-end">
                                <div class="text-sm text-zinc-600 dark:text-zinc-300">
                                    <p class="mb-1"><span class="font-semibold">Raza:</span> {{ pet.race || 'N/A' }}</p>
                                    <p v-if="pet.documents?.length" class="text-blue-500 text-xs">
                                        <i class="pi pi-paperclip"></i> {{ pet.documents.length }} Documentos
                                    </p>
                                </div>
                                <span class="text-xs text-indigo-500 font-medium">Editar <i class="pi pi-chevron-right ml-1"></i></span>
                            </div>
                        </div>
                    </div>

                    <!-- Paginación -->
                    <div v-if="pets.links.length > 3" class="mt-6 flex justify-center">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, key) in pets.links" :key="key">
                                <div v-if="link.url === null" class="px-4 py-2 text-sm text-zinc-400 border border-transparent rounded-md" v-html="link.label" />
                                <Link v-else 
                                    :href="link.url" 
                                    class="px-4 py-2 text-sm border rounded-md transition-colors"
                                    :class="link.active 
                                        ? 'bg-[#0E63B1] text-white border-[#0E63B1]' 
                                        : 'text-zinc-700 border-zinc-300 hover:bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-600'"
                                    v-html="link.label" 
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL COMPLETO DE EDICIÓN -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div @click="closeModal" class="absolute inset-0 bg-zinc-900/75 transition-opacity backdrop-blur-sm"></div>
            
            <div class="relative rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all max-h-[90vh] flex flex-col">
                
                <!-- Header -->
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-700/50 border-b border-zinc-100 dark:border-zinc-700 flex justify-between items-center sticky top-0 z-10">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">
                        Editar Perfil: {{ form.name }}
                    </h3>
                    <button @click="closeModal" class="text-zinc-400 hover:text-zinc-500">
                        <i class="pi pi-times text-lg"></i>
                    </button>
                </div>

                <!-- Body (Scrollable) -->
                <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                    
                    <!-- Sección Principal -->
                    <div class="flex flex-col sm:flex-row gap-6">
                        <!-- Foto -->
                        <div class="flex flex-col items-center space-y-3">
                            <div class="relative group">
                                <div class="h-40 w-40 rounded-full overflow-hidden border-4 border-white dark:border-zinc-700 shadow-lg bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center">
                                    <img v-if="photoPreview || form.photo_url" :src="photoPreview || form.photo_url" class="h-full w-full object-cover">
                                    <i v-else class="pi pi-camera text-4xl text-zinc-300"></i>
                                </div>
                                <label class="absolute bottom-0 right-0 bg-[#0E63B1] text-white py-2 px-3 rounded-full shadow-md cursor-pointer hover:bg-[#0c5599] transition-colors">
                                    <i class="pi pi-pencil text-xs"></i>
                                    <input type="file" class="hidden" @change="updatePhotoPreview" accept="image/*">
                                </label>
                            </div>
                        </div>

                        <!-- Inputs Básicos -->
                        <div class="flex-1 grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Nombre</label>
                                <input v-model="form.name" type="text" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Especie</label>
                                    <select v-model="form.species" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-white text-sm">
                                        <option value="Perro">Perro</option>
                                        <option value="Gato">Gato</option>
                                        <option value="Ave">Ave</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Raza</label>
                                    <input v-model="form.race" type="text" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-white text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-zinc-100 dark:border-zinc-700">

                    <!-- ADICIONALES -->
                    <div>
                        <h4 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 mb-3 flex items-center">
                            <i class="pi pi-heart mr-2"></i> Salud e Identificación
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-zinc-500 mb-1">No. Chip</label>
                                <input v-model="form.additionals.chip_id" type="text" placeholder="Opcional" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-zinc-500 mb-1">Pedigree / Reg.</label>
                                <input v-model="form.additionals.pedigree" type="text" placeholder="Opcional" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-sm">
                            </div>
                        </div>
                        
                        <div class="mt-4 flex flex-wrap gap-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.additionals.sterilized" class="rounded border-zinc-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">Esterilizado</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.additionals.vaccinated" class="rounded border-zinc-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">Vacunado (Completo)</span>
                            </label>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs text-zinc-500 mb-1">Notas Médicas / Alergias</label>
                            <textarea v-model="form.additionals.notes" rows="2" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-sm" placeholder="Detalles importantes..."></textarea>
                        </div>
                    </div>

                    <hr class="border-zinc-100 dark:border-zinc-700">

                    <!-- DOCUMENTOS -->
                    <div>
                        <h4 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 mb-3 flex items-center">
                            <i class="pi pi-folder-open mr-2"></i> Documentación Digital
                        </h4>
                        
                        <!-- Lista Existente -->
                        <div v-if="form.existing_documents && form.existing_documents.length > 0" class="mb-4 grid grid-cols-1 gap-2">
                            <div v-for="doc in form.existing_documents" :key="doc.id" class="flex items-center justify-between p-2 bg-zinc-50 dark:bg-zinc-700 rounded border border-zinc-200 dark:border-zinc-600">
                                <div class="flex items-center overflow-hidden gap-2">
                                    <i class="pi pi-file-pdf text-red-500" v-if="doc.mime_type === 'application/pdf'"></i>
                                    <i class="pi pi-image text-blue-500" v-else></i>
                                    <a :href="doc.url" target="_blank" class="text-sm text-blue-600 hover:underline truncate max-w-[200px]">
                                        {{ doc.name }}
                                    </a>
                                </div>
                                <span class="text-xs text-zinc-400"><i class="pi pi-check"></i></span>
                            </div>
                        </div>
                        <div v-else class="text-xs text-zinc-400 italic mb-3">No has subido documentos aún.</div>

                        <!-- Subir nuevos -->
                        <div class="mt-2 bg-indigo-50 dark:bg-indigo-900/10 p-3 rounded-lg border border-dashed border-indigo-200 dark:border-indigo-800">
                            <label class="block text-xs font-bold text-indigo-700 dark:text-indigo-300 mb-1">Subir Cartilla o Certificados (PDF/Img)</label>
                            <input type="file" multiple @change="handleFileUpload" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 dark:file:bg-indigo-900 dark:file:text-indigo-300">
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-700/50 border-t border-zinc-100 dark:border-zinc-700 flex flex-col-reverse sm:flex-row sm:justify-between gap-3 sticky bottom-0 z-10">
                    <button 
                        @click="deletePet"
                        class="w-full sm:w-auto px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2"
                        :disabled="processing"
                    >
                        <i class="pi pi-trash"></i> Eliminar
                    </button>
                    <div class="flex gap-3 w-full sm:w-auto justify-end">
                        <button @click="closeModal" class="px-4 py-2 bg-white dark:bg-zinc-700 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-700 dark:text-zinc-200 text-sm">Cancelar</button>
                        <button @click="submitUpdate" class="px-4 py-2 bg-[#0E63B1] hover:bg-[#0c5599] text-white rounded-lg shadow-md text-sm font-medium transition-colors flex items-center gap-2" :disabled="processing">
                            <i v-if="processing" class="pi pi-spin pi-spinner"></i>
                            {{ processing ? 'Guardando...' : 'Guardar cambios' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, router } from '@inertiajs/vue3';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";

export default {
    name: 'PetsIndex',
    components: {
        Link,
        AppLayout,
        PrimaryButton,
        ConfirmDialog,
    },
    setup() {
        const confirm = useConfirm();
        return { confirm };
    },
    props: {
        pets: { type: Object, required: true },
        errors: Object
    },
    data() {
        return {
            showModal: false,
            processing: false,
            photoPreview: null,
            form: {
                id: null,
                name: '',
                species: '',
                race: '',
                photo: null,
                photo_url: null,
                
                // Campos nuevos
                additionals: {
                    chip_id: '',
                    pedigree: '',
                    sterilized: false,
                    vaccinated: false,
                    notes: ''
                },
                documents: [], // Para subida
                existing_documents: [], // Para visualización
                
                _method: 'PUT'
            },
        }
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString('es-MX', { year: '2-digit', month: 'short', day: 'numeric' });
        },
        openModal(pet) {
            this.photoPreview = null;
            // Inicializamos additionals si viene nulo
            const additionals = pet.additionals || {
                chip_id: '', pedigree: '', sterilized: false, vaccinated: false, notes: ''
            };

            this.form = {
                id: pet.id,
                name: pet.name,
                species: pet.species,
                race: pet.race,
                photo_url: pet.photo_url,
                photo: null,
                
                additionals: additionals,
                documents: [],
                existing_documents: pet.documents || [],
                
                _method: 'PUT'
            };
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.photoPreview = null;
        },
        updatePhotoPreview(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.form.photo = file;
            const reader = new FileReader();
            reader.onload = (e) => this.photoPreview = e.target.result;
            reader.readAsDataURL(file);
        },
        handleFileUpload(e) {
            this.form.documents = Array.from(e.target.files);
        },
        submitUpdate() {
            this.processing = true;
            // forceFormData es vital para enviar archivos con Inertia usando PUT (spoofing)
            router.post(route('pets.update', this.form.id), this.form, {
                preserveScroll: true,
                forceFormData: true, 
                onSuccess: () => {
                    this.processing = false;
                    this.closeModal();
                },
                onError: () => this.processing = false
            });
        },
        deletePet() {
            this.confirm.require({
                message: '¿Estás seguro de que deseas eliminar esta mascota? Se perderán todos sus documentos.',
                header: 'Confirmar Eliminación',
                icon: 'pi pi-exclamation-triangle',
                rejectLabel: 'Cancelar',
                acceptLabel: 'Eliminar',
                acceptClass: 'p-button-danger',
                accept: () => {
                    this.processing = true;
                    router.delete(route('pets.destroy', this.form.id), {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.processing = false;
                            this.closeModal();
                        },
                        onFinish: () => this.processing = false
                    });
                }
            });
        }
    }
}
</script>

<style scoped>
/* Scrollbar personalizado para el modal */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(107, 114, 128, 0.8);
}
</style>
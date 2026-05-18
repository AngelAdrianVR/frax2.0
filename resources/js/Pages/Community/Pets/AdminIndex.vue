<template>
    <AppLayout :title="'Control de Mascotas'">
        <ConfirmDialog></ConfirmDialog>

        <div class="min-h-screen text-zinc-800 dark:text-zinc-100 p-4 sm:p-8 transition-colors duration-300">
            
            <!-- Encabezado Admin -->
            <div class="max-w-7xl mx-auto mb-1 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">
                        Administración de Mascotas
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Gestión global e historial clínico.
                    </p>
                </div>
                
                <!-- Buscador -->
                <div class="w-full md:w-auto relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="pi pi-search text-zinc-400"></i>
                    </span>
                    <input 
                        v-model="search" 
                        type="text" 
                        placeholder="Buscar nombre, raza, chip..." 
                        class="pl-10 pr-4 py-2 w-full md:w-80 rounded-lg border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                    >
                </div>
            </div>

            <!-- Botón de registro -->
            <div class="flex justify-end mt-4 max-w-7xl mx-auto">
                <PrimaryButton @click="$inertia.visit(route('pets.create'))">
                    <i class="pi pi-plus mr-2"></i> Registrar Mascota
                </PrimaryButton>
            </div>

            <div class="max-w-7xl mx-auto mt-4">
                
                <!-- ESTADO VACÍO -->
                <div v-if="pets.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <div class="mx-auto h-12 w-12 text-zinc-400">
                        <i class="pi pi-search" style="font-size: 2rem"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">No se encontraron mascotas</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Intenta con otros términos de búsqueda.</p>
                </div>

                <div v-else>
                    <!-- TABLA ADMIN (Desktop) -->
                    <div class="hidden md:block overflow-hidden rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Mascota / Foto
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Ubicación (Casa)
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Detalles y Salud
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Docs
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-800">
                                <tr 
                                    v-for="pet in pets.data" 
                                    :key="pet.id" 
                                    @click="openModal(pet)"
                                    class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors cursor-pointer group"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 relative">
                                                <img 
                                                    v-if="pet.photo_url" 
                                                    :src="pet.photo_url" 
                                                    alt="Foto" 
                                                    class="h-full w-full object-cover"
                                                >
                                                <div v-else class="h-full w-full flex items-center justify-center text-indigo-400 dark:text-indigo-300">
                                                    <i class="fa-solid fa-paw"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                                    {{ pet.name }}
                                                </div>
                                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ pet.species }} - {{ pet.race }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                            <i class="pi pi-home mr-1 text-zinc-400"></i>
                                            {{ pet.house_info }}
                                        </div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            Residente: {{ pet.resident_name }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <!-- Badges de Salud -->
                                            <span 
                                                v-if="pet.additionals?.sterilized" 
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200"
                                                title="Esterilizado"
                                            >
                                                <i class="pi pi-check-circle mr-1 text-[10px]"></i> Est.
                                            </span>
                                            <span 
                                                v-if="pet.additionals?.vaccinated" 
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                                                title="Vacunado"
                                            >
                                                <i class="pi pi-shield mr-1 text-[10px]"></i> Vac.
                                            </span>
                                            <span v-if="!pet.additionals?.sterilized && !pet.additionals?.vaccinated" class="text-xs text-zinc-400">
                                                -
                                            </span>
                                        </div>
                                        <div v-if="pet.additionals?.chip_id" class="text-xs text-zinc-500 mt-1">
                                            Chip: {{ pet.additionals.chip_id }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="pet.documents && pet.documents.length > 0" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ pet.documents.length }} Archivos
                                        </span>
                                        <span v-else class="text-zinc-400 text-xs">Sin docs</span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            <i class="pi pi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- VISTA MÓVIL (Cards) -->
                    <div class="md:hidden grid grid-cols-1 gap-4">
                        <div 
                            v-for="pet in pets.data" 
                            :key="pet.id" 
                            @click="openModal(pet)"
                            class="bg-white dark:bg-zinc-800 rounded-xl shadow-md overflow-hidden border border-zinc-200 dark:border-zinc-700"
                        >
                            <div class="bg-indigo-50 dark:bg-zinc-700/50 px-4 py-2 border-b border-zinc-100 dark:border-zinc-700 flex justify-between items-center">
                                <span class="font-bold text-zinc-800 dark:text-white text-lg">{{ pet.name }}</span>
                                <span class="text-xs bg-white dark:bg-zinc-600 px-2 py-1 rounded shadow-sm">
                                    {{ pet.house_info }}
                                </span>
                            </div>
                            <div class="p-4 flex gap-4">
                                <div class="h-16 w-16 flex-shrink-0 rounded-lg bg-zinc-200 overflow-hidden relative">
                                     <img v-if="pet.photo_url" :src="pet.photo_url" class="h-full w-full object-cover">
                                     <div v-else class="h-full w-full flex items-center justify-center text-zinc-400"><i class="pi pi-image text-2xl"></i></div>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <div class="flex justify-between">
                                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ pet.race }}</p>
                                        <div class="flex gap-1">
                                            <i v-if="pet.additionals?.vaccinated" class="pi pi-shield text-green-500 text-xs" title="Vacunado"></i>
                                            <i v-if="pet.additionals?.sterilized" class="pi pi-check-circle text-purple-500 text-xs" title="Esterilizado"></i>
                                        </div>
                                    </div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Prop: {{ pet.resident_name }}</p>
                                    <div v-if="pet.documents && pet.documents.length > 0" class="text-xs text-blue-500">
                                        <i class="pi pi-file"></i> {{ pet.documents.length }} docs
                                    </div>
                                </div>
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
                                        ? 'bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-500' 
                                        : 'bg-white text-zinc-700 border-zinc-300 hover:bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-600'"
                                    v-html="link.label" 
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DE EDICIÓN -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div @click="closeModal" class="absolute inset-0 bg-zinc-900/75 transition-opacity backdrop-blur-sm"></div>
            <div class="relative bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all max-h-[90vh] flex flex-col">
                
                <!-- Header Modal -->
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-700/50 border-b border-zinc-100 dark:border-zinc-700 flex justify-between items-center sticky top-0 z-10">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">
                        {{ form.name || 'Editar Mascota' }}
                    </h3>
                    <button @click="closeModal" class="text-zinc-400 hover:text-zinc-500 transition-colors">
                        <i class="pi pi-times text-lg"></i>
                    </button>
                </div>
                
                <!-- Body Modal (Scrollable) -->
                <div class="p-6 overflow-y-auto custom-scrollbar space-y-6 flex-1">
                    
                    <div class="flex flex-col sm:flex-row gap-6">
                        <!-- Columna Izquierda: Foto -->
                        <div class="flex flex-col items-center space-y-3">
                            <div class="relative group">
                                <div class="h-40 w-40 rounded-full overflow-hidden border-4 border-white dark:border-zinc-700 shadow-lg bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center">
                                    <img v-if="photoPreview || form.photo_url" :src="photoPreview || form.photo_url" class="h-full w-full object-cover">
                                    <i v-else class="pi pi-camera text-4xl text-zinc-300"></i>
                                </div>
                                <label class="absolute bottom-0 right-0 bg-indigo-600 text-white py-2 px-3 rounded-full shadow-md cursor-pointer hover:bg-indigo-700 transition-colors">
                                    <i class="pi pi-pencil text-xs"></i>
                                    <input type="file" class="hidden" @change="updatePhotoPreview" accept="image/*">
                                </label>
                            </div>
                            <span class="text-xs text-zinc-400">Click para cambiar foto</span>
                        </div>

                        <!-- Columna Derecha: Campos Principales -->
                        <div class="flex-1 grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Nombre</label>
                                <input v-model="form.name" type="text" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Especie</label>
                                    <select v-model="form.species" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                                        <option value="Perro">Perro</option>
                                        <option value="Gato">Gato</option>
                                        <option value="Ave">Ave</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Raza</label>
                                    <input v-model="form.race" type="text" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-700 text-zinc-900 dark:text-white focus:border-indigo-500 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-zinc-100 dark:border-zinc-700">

                    <!-- SECCIÓN: ADICIONALES -->
                    <div>
                        <h4 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 mb-3 flex items-center">
                            <i class="pi pi-id-card mr-2"></i> Identificación y Salud
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
                                <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">Vacunado (Esquema completo)</span>
                            </label>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs text-zinc-500 mb-1">Notas / Observaciones</label>
                            <textarea v-model="form.additionals.notes" rows="2" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-sm" placeholder="Alergias, comportamiento, etc."></textarea>
                        </div>
                    </div>

                    <hr class="border-zinc-100 dark:border-zinc-700">

                    <!-- SECCIÓN: DOCUMENTOS -->
                    <div>
                        <h4 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 mb-3 flex items-center">
                            <i class="pi pi-folder mr-2"></i> Documentación
                        </h4>
                        
                        <!-- Lista de documentos existentes -->
                        <div v-if="form.existing_documents && form.existing_documents.length > 0" class="mb-4 space-y-2">
                            <div v-for="doc in form.existing_documents" :key="doc.id" class="flex items-center justify-between p-2 bg-zinc-50 dark:bg-zinc-700 rounded border border-zinc-200 dark:border-zinc-600">
                                <div class="flex items-center overflow-hidden">
                                    <i class="pi pi-file-pdf text-red-500 mr-2" v-if="doc.mime_type === 'application/pdf'"></i>
                                    <i class="pi pi-image text-blue-500 mr-2" v-else></i>
                                    <a :href="doc.url" target="_blank" class="text-sm text-blue-600 hover:underline truncate block max-w-[150px] sm:max-w-xs">
                                        {{ doc.name }}
                                    </a>
                                </div>
                                <span class="text-xs text-zinc-400">Guardado</span>
                            </div>
                        </div>
                        <div v-else class="text-sm text-zinc-400 italic mb-3">No hay documentos adjuntos.</div>

                        <!-- Subir nuevos -->
                        <div class="mt-2">
                            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1">Agregar nuevos archivos:</label>
                            <input type="file" multiple @change="handleFileUpload" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300">
                        </div>
                    </div>

                </div>
                
                <!-- Footer Modal -->
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
                        <button @click="submitUpdate" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md text-sm font-medium transition-colors flex items-center gap-2" :disabled="processing">
                            <i v-if="processing" class="pi pi-spin pi-spinner"></i>
                            {{ processing ? 'Guardando...' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import debounce from 'lodash/debounce';

export default {
    name: 'AdminPetsIndex',
    components: { Link, AppLayout, ConfirmDialog, PrimaryButton },
    setup() {
        const confirm = useConfirm();
        return { confirm };
    },
    props: {
        pets: Object,
        filters: Object,
        errors: Object
    },
    data() {
        return {
            search: this.filters.search || '',
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
                documents: [], // Nuevos archivos a subir
                existing_documents: [], // Solo para visualizar
                
                _method: 'PUT'
            }
        }
    },
    watch: {
        search: debounce(function(value) {
            router.get(route('admin.pets.index'), { search: value }, {
                preserveState: true,
                replace: true,
                preserveScroll: true
            });
        }, 500)
    },
    methods: {
        openModal(pet) {
            // console.log('Abriendo modal para mascota:', pet);
            this.photoPreview = null;
            
            // Inicializamos form asegurando que additionals sea un objeto si viene null
            const additionals = pet.additionals || {
                chip_id: '',
                pedigree: '',
                sterilized: !! pet.additionals?.sterilized,
                vaccinated: !! pet.additionals?.vaccinated,
                notes: ''
            };

            this.form = { 
                ...pet, 
                additionals: additionals,
                photo: null, 
                documents: [], // Reset uploads
                existing_documents: pet.documents || [],
                _method: 'PUT' 
            }; 
            
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
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
            // Convertir FileList a Array
            this.form.documents = Array.from(e.target.files);
        },
        submitUpdate() {
            this.processing = true;
            
            // Usamos router.post con forceFormData para manejar archivos + PUT method fake
            router.post(route('pets.update', this.form.id), this.form, {
                preserveScroll: true,
                forceFormData: true, // Importante para subir archivos
                onSuccess: () => { 
                    this.processing = false; 
                    this.closeModal(); 
                },
                onError: () => this.processing = false
            });
        },
        deletePet() {
             this.confirm.require({
                message: '¿Estás seguro de eliminar esta mascota? Esta acción no se puede deshacer.',
                header: 'Confirmar Eliminación',
                icon: 'pi pi-exclamation-triangle',
                acceptLabel: 'Eliminar',
                rejectLabel: 'Cancelar',
                acceptClass: 'p-button-danger',
                accept: () => {
                    this.processing = true;
                    router.delete(route('pets.destroy', this.form.id), {
                        onSuccess: () => { this.processing = false; this.closeModal(); }
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
<template>
    <AppLayout :title="'Registrar mascota'">
        <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-100 p-4 sm:p-8 transition-colors duration-300">
            <Toast position="top-right" />

            <div class="max-w-4xl mx-auto">
                <!-- Encabezado -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">
                            Nueva Mascota
                        </h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                            Registra una nueva mascota y su documentación.
                        </p>
                    </div>
                    <Link 
                        :href="isAdmin ? route('admin.pets.index') : route('pets.index')" 
                        class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors flex items-center gap-2 text-sm font-medium"
                    >
                        <i class="pi pi-arrow-left text-xs"></i> Volver
                    </Link>
                </div>

                <!-- Formulario -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <form @submit.prevent="submit" class="p-6 sm:p-8 space-y-8">
                        
                        <!-- 1. INFORMACIÓN BÁSICA -->
                        <div>
                            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200 mb-4 flex items-center gap-2">
                                <i class="pi pi-id-card text-blue-500"></i> Información Básica
                            </h3>
                            
                            <!-- Admin Selector -->
                            <div v-if="isAdmin && privateUnits.length > 0" class="p-4 mb-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                                <label for="private_unit" class="text-sm font-bold text-blue-800 dark:text-blue-300 block mb-2">
                                    Asignar a Propiedad (Modo Admin) <span class="text-red-500">*</span>
                                </label>
                                <Dropdown
                                    id="private_unit"
                                    v-model="form.private_unit_id"
                                    :options="privateUnits"
                                    optionLabel="label"
                                    optionValue="id"
                                    filter
                                    placeholder="Buscar calle, número o lote..."
                                    class="w-full"
                                    :class="{'p-invalid': form.errors.private_unit_id}"
                                />
                                <small v-if="form.errors.private_unit_id" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.private_unit_id }}
                                </small>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre -->
                                <div class="flex flex-col gap-2">
                                    <label for="name" class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>
                                    <InputText id="name" v-model="form.name" placeholder="Ej. Firulais" :class="{'p-invalid': form.errors.name}" class="w-full" />
                                    <small v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</small>
                                </div>

                                <!-- Especie -->
                                <div class="flex flex-col gap-2">
                                    <label for="species" class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                        Especie <span class="text-red-500">*</span>
                                    </label>
                                    <Dropdown id="species" v-model="form.species" :options="speciesOptions" placeholder="Seleccionar..." :class="{'p-invalid': form.errors.species}" class="w-full" />
                                    <small v-if="form.errors.species" class="text-red-500 text-xs mt-1">{{ form.errors.species }}</small>
                                </div>

                                <!-- Raza -->
                                <div class="flex flex-col gap-2">
                                    <label for="race" class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Raza</label>
                                    <InputText id="race" v-model="form.race" placeholder="Ej. Labrador" :class="{'p-invalid': form.errors.race}" class="w-full" />
                                </div>

                                <!-- Chip ID (Nuevo) -->
                                <div class="flex flex-col gap-2">
                                    <label for="chip_id" class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                        No. de Chip / Identificación
                                    </label>
                                    <InputText id="chip_id" v-model="form.additionals.chip_id" placeholder="Opcional" class="w-full" />
                                </div>
                            </div>
                        </div>

                        <hr class="border-zinc-100 dark:border-zinc-700">

                        <!-- 2. DETALLES ADICIONALES -->
                        <div>
                            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200 mb-4 flex items-center gap-2">
                                <i class="pi pi-heart text-red-500"></i> Salud y Registro
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                <!-- Pedigree -->
                                <div class="flex flex-col gap-2">
                                    <label for="pedigree" class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">No. Pedigree / Registro</label>
                                    <InputText id="pedigree" v-model="form.additionals.pedigree" placeholder="Opcional" class="w-full" />
                                </div>

                                <!-- Checkboxes de estado -->
                                <div class="flex flex-col gap-3 justify-center">
                                    <div class="flex items-center gap-3 p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-700/50">
                                        <Checkbox v-model="form.additionals.sterilized" :binary="true" inputId="sterilized" />
                                        <label for="sterilized" class="text-sm font-medium text-zinc-700 dark:text-zinc-200 cursor-pointer select-none">
                                            Mascota Esterilizada
                                        </label>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-700/50">
                                        <Checkbox v-model="form.additionals.vaccinated" :binary="true" inputId="vaccinated" />
                                        <label for="vaccinated" class="text-sm font-medium text-zinc-700 dark:text-zinc-200 cursor-pointer select-none">
                                            Esquema de Vacunación Completo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Notas / Observaciones -->
                            <div class="flex flex-col gap-2">
                                <label for="notes" class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Observaciones / Alergias</label>
                                <Textarea id="notes" v-model="form.additionals.notes" rows="3" placeholder="Detalles médicos relevantes, comportamiento, etc." class="w-full" autoResize />
                            </div>
                        </div>

                        <hr class="border-zinc-100 dark:border-zinc-700">

                        <!-- 3. DOCUMENTOS Y FOTOS -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Foto de Perfil -->
                            <div>
                                <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200 mb-4 flex items-center gap-2">
                                    <i class="pi pi-camera text-green-500"></i> Fotografía
                                </h3>
                                <div class="flex flex-col gap-4">
                                    <FileUpload 
                                        mode="basic" 
                                        name="photo" 
                                        accept="image/*" 
                                        :maxFileSize="5000000"
                                        customUpload
                                        auto 
                                        chooseLabel="Elegir Foto"
                                        class="p-button-outlined p-button-secondary w-full"
                                        @select="onPhotoSelect"
                                    />
                                    <div v-if="photoPreview" class="relative group w-32 h-32">
                                        <img :src="photoPreview" class="w-full h-full object-cover rounded-lg border border-zinc-200 shadow-sm" />
                                        <button type="button" @click="removePhoto" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600">
                                            <i class="pi pi-times text-xs"></i>
                                        </button>
                                    </div>
                                    <small v-if="form.errors.photo" class="text-red-500">{{ form.errors.photo }}</small>
                                </div>
                            </div>

                            <!-- Documentos -->
                            <div>
                                <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200 mb-4 flex items-center gap-2">
                                    <i class="pi pi-folder-open text-yellow-500"></i> Documentación
                                </h3>
                                <div class="bg-zinc-50 dark:bg-zinc-700/30 p-4 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-600">
                                    <FileUpload 
                                        name="documents[]" 
                                        mode="advanced" 
                                        :multiple="true"
                                        accept="image/*,.pdf" 
                                        :maxFileSize="10000000"
                                        :showUploadButton="false"
                                        :showCancelButton="false"
                                        chooseLabel="Agregar Archivos"
                                        @select="onDocumentsSelect"
                                        @remove="onRemoveDocument"
                                    >
                                        <template #empty>
                                            <div class="flex flex-col items-center justify-center p-4 text-center">
                                                <i class="pi pi-cloud-upload text-4xl text-zinc-400 mb-2"></i>
                                                <p class="text-sm text-zinc-500">Arrastra aquí cartillas de vacunación, certificados o PDFs.</p>
                                            </div>
                                        </template>
                                    </FileUpload>
                                    <small class="text-zinc-500 block mt-2 text-xs">
                                        Soporta PDF, JPG, PNG. Máx 10MB por archivo.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="border-t border-zinc-100 dark:border-zinc-700 pt-6 flex justify-end gap-3">
                            <Link 
                                :href="isAdmin ? route('admin.pets.index') : route('pets.index')" 
                                class="px-5 py-2.5 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 font-medium transition-colors text-sm"
                            >
                                Cancelar
                            </Link>
                            <Button 
                                type="submit" 
                                label="Guardar Registro Completo" 
                                icon="pi pi-check" 
                                :loading="form.processing" 
                                class="p-button-primary"
                            />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import FileUpload from 'primevue/fileupload';
import Toast from 'primevue/toast';
import Dropdown from 'primevue/dropdown';
import Checkbox from 'primevue/checkbox';

export default {
    name: 'PetsCreate',
    components: {
        Link, Toast, Button, Dropdown, InputText, Textarea, Checkbox, AppLayout, FileUpload
    },
    props: {
        privateUnits: { type: Array, default: () => [] },
        isAdmin: { type: Boolean, default: false }
    },
    data() {
        return {
            form: useForm({
                name: '',
                species: '',
                race: '',
                photo: null, 
                private_unit_id: this.isAdmin ? null : this.$page.props.auth.current_property?.property_id,
                
                // Campos Adicionales
                additionals: {
                    chip_id: '',
                    pedigree: '',
                    sterilized: false,
                    vaccinated: false,
                    notes: ''
                },
                // Array para múltiples documentos
                documents: []
            }),
            photoPreview: null,
            speciesOptions: ['Perro', 'Gato', 'Ave', 'Reptil', 'Otro']
        }
    },
    methods: {
        onPhotoSelect(event) {
            const file = event.files[0];
            if (file) {
                this.form.photo = file;
                this.photoPreview = URL.createObjectURL(file);
            }
        },
        removePhoto() {
            this.form.photo = null;
            this.photoPreview = null;
        },
        // Manejo de documentos múltiples con PrimeVue Advanced FileUpload
        onDocumentsSelect(event) {
            // event.files contiene los archivos seleccionados en esa acción
            // PrimeVue mantiene una lista interna, pero para Inertia necesitamos asignarlos manualmente si queremos control total,
            // o simplemente tomar todos los archivos del componente al enviar.
            // Para simplificar sync con Inertia useForm:
            this.form.documents = event.files;
        },
        onRemoveDocument(event) {
            // Actualizar la lista al remover
            this.form.documents = event.files;
        },
        submit() {
            this.form.post(route('pets.store'), {
                onSuccess: () => {
                    this.form.reset();
                    this.photoPreview = null;
                    if (!this.isAdmin) {
                        this.form.private_unit_id = this.$page.props.auth.current_property?.property_id;
                    }
                    this.$toast.add({ severity: 'success', summary: 'Éxito', detail: 'Mascota registrada.', life: 3000 });
                },
                onError: () => {
                    this.$toast.add({ severity: 'error', summary: 'Error', detail: 'Verifica los campos.', life: 3000 });
                }
            });
        }
    }
}
</script>

<style>
/* SOBRESCRIBIR VARIABLES CSS DE PRIMEVUE */
:deep(*) {
    --primary-color: #0f7bc1;
    --primary-color-text: #ffffff;
    --focus-ring: 0 0 0 2px #ffffff, 0 0 0 4px #0f7bc1;
}

:deep(.p-inputtext), :deep(.p-dropdown), :deep(.p-inputtextarea) {
    @apply border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white w-full;
}
:deep(.p-inputtext:enabled:focus), :deep(.p-dropdown.p-focus), :deep(.p-inputtextarea:enabled:focus) {
    @apply ring-2 border-[#0f7bc1];
    --tw-ring-color: #0f7bc1; 
}

/* ESTILOS DEL CHECKBOX */
:deep(.p-checkbox .p-checkbox-box) {
    @apply border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 transition-colors duration-200;
}

/* Estado Check (Seleccionado) */
:deep(.p-checkbox .p-checkbox-box.p-highlight) {
    @apply bg-[#0f7bc1] border-[#0f7bc1];
}

/* CORRECCIÓN: Forzar color blanco del icono (palomita) 
   Esto asegura que en Light Mode y Dark Mode la palomita sea blanca sobre el fondo azul
*/
:deep(.p-checkbox .p-checkbox-box.p-highlight .p-checkbox-icon) {
    @apply text-white;
    color: #ffffff !important; /* Refuerzo con important por si acaso */
}

/* FileUpload Advanced Customization */
:deep(.p-fileupload-advanced) {
    @apply border-0;
}
:deep(.p-fileupload-content) {
    @apply border-0 bg-transparent p-0;
}
:deep(.p-fileupload-buttonbar) {
    @apply bg-transparent border-0 p-0 mb-4 hidden; /* Ocultamos toolbar default si queremos control custom, o la dejamos simple */
}
/* Forzamos mostrar botón choose customizado y ocultamos el header default feo */
:deep(.p-fileupload .p-fileupload-buttonbar) {
    @apply bg-transparent border-0 p-0 pb-2;
}
</style>
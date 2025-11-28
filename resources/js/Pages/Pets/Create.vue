<template>
    <AppLayout :title="'Registrar mascota'">
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-4 sm:p-8 transition-colors duration-300">
            <!-- Toast para notificaciones -->
            <Toast position="top-right" />

            <div class="max-w-3xl mx-auto">
                <!-- Encabezado con Botón de Regreso -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                            Nueva Mascota
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Registra una nueva mascota asignada a tu unidad privativa.
                        </p>
                    </div>
                    <Link 
                        :href="route('pets.index')" 
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors flex items-center gap-2 text-sm font-medium"
                    >
                        <i class="pi pi-arrow-left text-xs"></i> Volver
                    </Link>
                </div>

                <!-- Card del Formulario -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <form @submit.prevent="submit" class="p-6 sm:p-8 space-y-6">
                        
                        <!-- Grid de Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Nombre -->
                            <div class="flex flex-col gap-2">
                                <label for="name" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Nombre de la mascota <span class="text-red-500">*</span>
                                </label>
                                <InputText 
                                    id="name" 
                                    v-model="form.name" 
                                    placeholder="Ej. Firulais" 
                                    :class="{'p-invalid': form.errors.name}"
                                    class="w-full"
                                />
                                <small v-if="form.errors.name" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.name }}
                                </small>
                            </div>

                            <!-- Especie (Dropdown) -->
                            <div class="flex flex-col gap-2">
                                <label for="species" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Especie <span class="text-red-500">*</span>
                                </label>
                                <Dropdown 
                                    id="species"
                                    v-model="form.species" 
                                    :options="speciesOptions" 
                                    placeholder="Seleccionar..." 
                                    :class="{'p-invalid': form.errors.species}"
                                    class="w-full"
                                />
                                <small v-if="form.errors.species" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.species }}
                                </small>
                            </div>

                            <!-- Raza -->
                            <div class="flex flex-col gap-2">
                                <label for="race" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Raza
                                </label>
                                <InputText 
                                    id="race" 
                                    v-model="form.race" 
                                    placeholder="Ej. Labrador, Siamés..." 
                                    :class="{'p-invalid': form.errors.race}"
                                    class="w-full"
                                />
                                <small v-if="form.errors.race" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.race }}
                                </small>
                            </div>

                            <!-- Campo dummy para mantener el grid alineado si quieres, o dejar vacío -->
                            <div class="hidden md:block"></div>

                        </div>

                        <!-- Sección de Imagen (Spatie Media Library) -->
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-6 mt-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">
                                Fotografía (opcional)
                            </label>
                            
                            <div class="flex items-start gap-4">
                                <div class="flex-1">
                                    <FileUpload 
                                        mode="basic" 
                                        name="photo" 
                                        accept="image/*" 
                                        :maxFileSize="5000000"
                                        customUpload
                                        auto 
                                        chooseLabel="Seleccionar Imagen"
                                        class="p-button-outlined p-button-secondary w-full sm:w-auto"
                                        @select="onFileSelect"
                                    />
                                    <small class="text-gray-500 block mt-2">
                                        Formatos: JPG, PNG. Máx 5MB.
                                    </small>
                                    <small v-if="form.errors.photo" class="text-red-500 text-xs mt-1 block">
                                        {{ form.errors.photo }}
                                    </small>
                                </div>
                                
                                <div v-if="photoPreview" class="relative group">
                                    <img :src="photoPreview" alt="Preview" class="h-24 w-24 object-cover rounded-lg border border-gray-200 shadow-sm" />
                                    <button 
                                        type="button"
                                        @click="removePhoto"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition-colors"
                                        title="Quitar imagen"
                                    >
                                        <i class="pi pi-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-6 flex justify-end gap-3">
                            <Link 
                                :href="route('pets.index')" 
                                class="px-5 py-2.5 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium transition-colors text-sm"
                            >
                                Cancelar
                            </Link>
                            <Button 
                                type="submit" 
                                label="Guardar Mascota" 
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
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import FileUpload from 'primevue/fileupload';
import Toast from 'primevue/toast';
import Dropdown from 'primevue/dropdown';

export default {
    name: 'PetsCreate',
    components: {
        Link,
        Toast,
        Button,
        Dropdown,
        InputText,
        AppLayout,
        FileUpload,
        PrimaryButton,
    },
    data() {
        return {
            form: useForm({
                name: '',
                species: '',
                race: '',
                photo: null, 
                private_unit_id: this.$page.props.auth.current_property.property_id,
            }),
            photoPreview: null,
            speciesOptions: ['Perro', 'Gato', 'Ave', 'Reptil', 'Otro']
        }
    },
    methods: {
        onFileSelect(event) {
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
        submit() {
            this.form.post(route('pets.store'), {
                onSuccess: () => {
                    this.form.reset();
                    this.photoPreview = null;
                },
                onError: () => {
                    this.$toast.add({
                        severity: 'error', 
                        summary: 'Error', 
                        detail: 'Por favor verifica los campos obligatorios.', 
                        life: 3000
                    });
                }
            });
        }
    }
}
</script>

<style scoped>
/* SOBRESCRIBIR VARIABLES CSS DE PRIMEVUE */
:deep(*) {
    --primary-color: #0f7bc1;
    --primary-color-text: #ffffff;
    --primary-500: #0f7bc1;
    --primary-600: #0d6ca8;
    --primary-700: #0b5c8f;
    
    /* Focus rings */
    --focus-ring: 0 0 0 2px #ffffff, 0 0 0 4px #0f7bc1;
}

/* Ajustes de compatibilidad PrimeVue + Tailwind */
:deep(.p-inputtext), :deep(.p-dropdown) {
    @apply border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white w-full;
}
:deep(.p-inputtext:enabled:focus), :deep(.p-dropdown.p-focus) {
    @apply ring-2 border-[#0f7bc1];
    --tw-ring-color: #0f7bc1; 
}
:deep(.p-inputtext.p-invalid), :deep(.p-dropdown.p-invalid) {
    @apply border-red-500 ring-red-500;
}

/* Forzar estilo del botón primario */
:deep(.p-button.p-button-primary) {
    background: var(--primary-color);
    border-color: var(--primary-color);
}
:deep(.p-button.p-button-primary:enabled:hover) {
    background: var(--primary-600);
    border-color: var(--primary-600);
}

/* Ajuste del panel del dropdown en modo oscuro */
:deep(.p-dropdown-panel) {
    @apply bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700;
}
:deep(.p-dropdown-item) {
    @apply text-gray-700 dark:text-gray-200;
}
:deep(.p-dropdown-item:not(.p-highlight):not(.p-disabled).p-focus), 
:deep(.p-dropdown-item:not(.p-highlight):not(.p-disabled):hover) {
    @apply bg-gray-100 dark:bg-gray-700;
}
</style>
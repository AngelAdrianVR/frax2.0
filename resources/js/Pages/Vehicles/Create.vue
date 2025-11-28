<template>
    <AppLayout :title="'Registrar vehículo'">
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-4 sm:p-8 transition-colors duration-300">
            <!-- Toast para notificaciones -->
            <Toast position="top-right" />

            <div class="max-w-3xl mx-auto">
                <!-- Encabezado con Botón de Regreso -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                            Nuevo Vehículo
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Ingresa los datos del vehículo para registrarlo en tu unidad.
                        </p>
                    </div>
                    <Link 
                        href="/vehicles" 
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
                            
                            <!-- Placa -->
                            <div class="flex flex-col gap-2">
                                <label for="plate" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Placa / Matrícula <span class="text-red-500">*</span>
                                </label>
                                <InputText 
                                    id="plate" 
                                    v-model="form.plate" 
                                    placeholder="Ej. ABC-1234" 
                                    :class="{'p-invalid': form.errors.plate}"
                                    class="w-full"
                                />
                                <small v-if="form.errors.plate" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.plate }}
                                </small>
                            </div>

                            <!-- Tag de Acceso -->
                            <div class="flex flex-col gap-2">
                                <label for="tag" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Tag de Acceso
                                    <i class="pi pi-wifi z-10" />
                                </label>
                                <span class="p-input-icon-left w-full">
                                    <InputText 
                                        id="tag" 
                                        v-model="form.tag_access" 
                                        placeholder="Código del sensor" 
                                        :class="{'p-invalid': form.errors.tag_access}"
                                        class="w-full"
                                    />
                                </span>
                                <small class="text-gray-400 dark:text-gray-500 text-xs">Opcional. Para acceso automatizado.</small>
                                <small v-if="form.errors.tag_access" class="text-red-500 text-xs">
                                    {{ form.errors.tag_access }}
                                </small>
                            </div>

                            <!-- Marca -->
                            <div class="flex flex-col gap-2">
                                <label for="brand" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Marca <span class="text-red-500">*</span>
                                </label>
                                <InputText 
                                    id="brand" 
                                    v-model="form.brand" 
                                    placeholder="Ej. Toyota" 
                                    :class="{'p-invalid': form.errors.brand}"
                                    class="w-full"
                                />
                                <small v-if="form.errors.brand" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.brand }}
                                </small>
                            </div>

                            <!-- Modelo -->
                            <div class="flex flex-col gap-2">
                                <label for="model" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Modelo <span class="text-red-500">*</span>
                                </label>
                                <InputText 
                                    id="model" 
                                    v-model="form.model" 
                                    placeholder="Ej. Corolla" 
                                    :class="{'p-invalid': form.errors.model}"
                                    class="w-full"
                                />
                                <small v-if="form.errors.model" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.model }}
                                </small>
                            </div>

                            <!-- Color con Picker -->
                            <div class="flex flex-col gap-2">
                                <label for="color" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Color <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2 relative">
                                    <InputText 
                                        id="color" 
                                        v-model="form.color" 
                                        placeholder="Ej. Rojo o #FF0000" 
                                        :class="{'p-invalid': form.errors.color}"
                                        class="w-full"
                                        @input="syncPickerFromText"
                                    />
                                    
                                    <!-- Contenedor del Picker + Visualización -->
                                    <div class="relative flex-shrink-0">
                                        <!-- 
                                        Usamos el ColorPicker de PrimeVue pero lo hacemos transparente 
                                        para que el div personalizado (abajo) sea el que se vea,
                                        pero el click lo reciba el picker.
                                        -->
                                        <ColorPicker 
                                            v-model="pickerColor" 
                                            format="hex"
                                            class="opacity-0 absolute top-8 -right-2 z-10 cursor-pointer"
                                            @change="syncTextFromPicker"
                                        />
                                        
                                        <!-- Visualización del color (Texto o Hex) -->
                                        <div 
                                            class="w-10 h-10 rounded border border-gray-300 dark:border-gray-600 shadow-sm transition-colors duration-200"
                                            :style="{ backgroundColor: finalColorPreview }"
                                            title="Clic para seleccionar un color personalizado"
                                        ></div>
                                    </div>
                                </div>
                                <small v-if="form.errors.color" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.color }}
                                </small>
                            </div>

                        </div>

                        <!-- Sección de Imagen (Spatie Media Library) -->
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-6 mt-2">
                            <label class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">
                                Fotografía del Vehículo (opcional)
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
                                href="/vehicles" 
                                class="px-5 py-2.5 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium transition-colors text-sm"
                            >
                                Cancelar
                            </Link>
                            <Button 
                                type="submit" 
                                label="Guardar Vehículo" 
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
import ColorPicker from 'primevue/colorpicker';

export default {
    name: 'VehiclesCreate',
    components: {
        Link,
        Toast,
        Button,
        InputText,
        AppLayout,
        FileUpload,
        ColorPicker,
        PrimaryButton,
    },
    data() {
        return {
            form: useForm({
                plate: '',
                brand: '',
                resident_id: this.$page.props.auth.user.resident_id,
                model: '',
                color: '',
                tag_access: '',
                photo: null, 
            }),
            photoPreview: null,
            pickerColor: '000000', // Valor interno del ColorPicker (hex sin #)
            colorMap: {
                'blanco': '#ffffff', 'white': '#ffffff',
                'negro': '#000000', 'black': '#000000',
                'rojo': '#ef4444', 'red': '#ef4444',
                'azul': '#3b82f6', 'blue': '#3b82f6',
                'gris': '#6b7280', 'gray': '#6b7280',
                'verde': '#22c55e', 'green': '#22c55e',
                'amarillo': '#eab308', 'yellow': '#eab308',
                'plata': '#c0c0c0', 'silver': '#c0c0c0'
            }
        }
    },
    computed: {
        // Calcula qué color mostrar en el cuadro visual
        finalColorPreview() {
            // 1. Si el texto coincide con un nombre de color (ej "Rojo")
            const textLower = this.form.color ? this.form.color.toLowerCase().trim() : '';
            if (this.colorMap[textLower]) {
                return this.colorMap[textLower];
            }
            // 2. Si es un Hex válido
            if (/^#[0-9A-F]{6}$/i.test(this.form.color)) {
                return this.form.color;
            }
            // 3. Si no, transparente o gris muy claro
            return 'transparent';
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
        // Cuando el usuario usa el Picker, actualizamos el texto a Hex
        syncTextFromPicker() {
            // PrimeVue ColorPicker retorna hex sin '#' (ej: "ff0000")
            if (this.pickerColor) {
                this.form.color = '#' + this.pickerColor;
            }
        },
        // Cuando el usuario escribe, intentamos sincronizar el picker (opcional)
        // para que si abren el picker ya esté cerca del color escrito si es hex
        syncPickerFromText() {
            const val = this.form.color;
            // Si es un hex válido (con #), actualizamos el picker
            if (val && /^#[0-9A-F]{6}$/i.test(val)) {
                this.pickerColor = val.substring(1);
            }
        },
        submit() {
            this.form.post('/vehicles', {
                onSuccess: () => {
                    this.form.reset();
                    this.pickerColor = '000000';
                    this.photoPreview = null;
                },
                onError: () => {
                    this.$toast.add({
                        severity: 'error', 
                        summary: 'Error', 
                        detail: 'Por favor corrige los errores en el formulario.', 
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
/* Esto afectará a todos los componentes PrimeVue DENTRO de este archivo */
:deep(*) {
    --primary-color: #0f7bc1;
    --primary-color-text: #ffffff;
    --primary-500: #0f7bc1;
    --primary-600: #0d6ca8; /* Un poco más oscuro para hover */
    --primary-700: #0b5c8f; /* Aún más oscuro para active */
    
    /* Focus rings */
    --focus-ring: 0 0 0 2px #ffffff, 0 0 0 4px #0f7bc1;
}

/* Ajustes de compatibilidad PrimeVue + Tailwind */
:deep(.p-inputtext) {
    @apply border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white;
}
:deep(.p-inputtext:enabled:focus) {
    @apply ring-2 border-[#0f7bc1];
    /* Usamos el color hexadecimal directo para asegurar consistencia con Tailwind ring */
    --tw-ring-color: #0f7bc1; 
}
:deep(.p-inputtext.p-invalid) {
    @apply border-red-500 ring-red-500;
}

/* Forzar estilo del botón primario para usar las variables nuevas */
:deep(.p-button.p-button-primary) {
    background: var(--primary-color);
    border-color: var(--primary-color);
}
:deep(.p-button.p-button-primary:enabled:hover) {
    background: var(--primary-600);
    border-color: var(--primary-600);
}

/* Estilos para el overlay del ColorPicker de PrimeVue para que se vea bien en Dark Mode */
:deep(.p-colorpicker-panel) {
    background: #1f2937; /* bg-gray-800 */
    border-color: #374151; /* border-gray-700 */
}
</style>
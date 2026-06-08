<template>
    <AppLayout title="Registrar vehículo">
        <div class="p-4 sm:p-8">
            <Toast position="top-right" />

            <div class="max-w-3xl mx-auto">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight">
                            Nuevo Vehículo
                        </h1>
                        <p class="text-sm text-zinc-400 mt-1">
                            Ingresa los datos del vehículo para registrarlo en tu unidad.
                        </p>
                    </div>
                    <Back />
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    <form @submit.prevent="submit" class="p-6 sm:p-8 space-y-6">
                        
                        <div v-if="isAdmin && privateUnits.length > 0" class="p-4 mb-4 bg-zinc-800/50 rounded-xl border border-zinc-700/40">
                            <label for="private_unit" class="text-xs font-medium text-zinc-400 uppercase tracking-wider block mb-2">
                                Asignar a Propiedad (Modo Admin) <span class="text-red-400">*</span>
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
                            <small class="text-zinc-500 block mt-1">
                                Selecciona la casa a la cual pertenecerá este vehículo.
                            </small>
                            <small v-if="form.errors.private_unit_id" class="text-red-400 text-xs mt-1">
                                {{ form.errors.private_unit_id }}
                            </small>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            <div class="flex flex-col gap-1.5">
                                <label for="plate" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                    Placa / Matrícula <span class="text-red-400">*</span>
                                </label>
                                <InputText 
                                    id="plate" 
                                    v-model="form.plate" 
                                    placeholder="Ej. ABC-1234" 
                                    :class="{'p-invalid': form.errors.plate}"
                                    class="w-full"
                                />
                                <small v-if="form.errors.plate" class="text-red-400 text-xs mt-1">
                                    {{ form.errors.plate }}
                                </small>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="tag" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                    Tag de Acceso
                                </label>
                                <InputText 
                                    id="tag" 
                                    v-model="form.tag_access" 
                                    placeholder="Código del sensor" 
                                    :class="{'p-invalid': form.errors.tag_access}"
                                    class="w-full"
                                />
                                <small class="text-zinc-500 text-xs">Opcional. Para acceso automatizado.</small>
                                <small v-if="form.errors.tag_access" class="text-red-400 text-xs">
                                    {{ form.errors.tag_access }}
                                </small>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="brand" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                    Marca <span class="text-red-400">*</span>
                                </label>
                                <InputText 
                                    id="brand" 
                                    v-model="form.brand" 
                                    placeholder="Ej. Toyota" 
                                    :class="{'p-invalid': form.errors.brand}"
                                    class="w-full"
                                />
                                <small v-if="form.errors.brand" class="text-red-400 text-xs mt-1">
                                    {{ form.errors.brand }}
                                </small>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="model" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                    Modelo <span class="text-red-400">*</span>
                                </label>
                                <InputText 
                                    id="model" 
                                    v-model="form.model" 
                                    placeholder="Ej. Corolla" 
                                    :class="{'p-invalid': form.errors.model}"
                                    class="w-full"
                                />
                                <small v-if="form.errors.model" class="text-red-400 text-xs mt-1">
                                    {{ form.errors.model }}
                                </small>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="color" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                    Color <span class="text-red-400">*</span>
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
                                    
                                    <div class="relative flex-shrink-0">
                                        <ColorPicker 
                                            v-model="pickerColor" 
                                            format="hex"
                                            class="opacity-0 absolute top-8 -right-2 z-10 cursor-pointer"
                                            @change="syncTextFromPicker"
                                        />
                                        
                                        <div 
                                            class="w-10 h-10 rounded-xl border border-zinc-600 shadow-sm transition-colors duration-200"
                                            :style="{ backgroundColor: finalColorPreview }"
                                            title="Clic para seleccionar un color personalizado"
                                        ></div>
                                    </div>
                                </div>
                                <small v-if="form.errors.color" class="text-red-400 text-xs mt-1">
                                    {{ form.errors.color }}
                                </small>
                            </div>

                        </div>

                        <div class="border-t border-zinc-800/60 pt-6 mt-2">
                            <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2 block">
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
                                    <small class="text-zinc-500 block mt-2">
                                        Formatos: JPG, PNG. Máx 5MB.
                                    </small>
                                    <small v-if="form.errors.photo" class="text-red-400 text-xs mt-1 block">
                                        {{ form.errors.photo }}
                                    </small>
                                </div>
                                
                                <div v-if="photoPreview" class="relative group">
                                    <img :src="photoPreview" alt="Preview" class="h-24 w-24 object-cover rounded-lg border border-zinc-700 shadow-sm" />
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

                        <div class="border-t border-zinc-800/60 pt-6 flex justify-end gap-3">
                            <Link 
                                href="/vehicles" 
                                class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm"
                            >
                                Cancelar
                            </Link>
                            <Button 
                                type="submit" 
                                label="Guardar Vehículo" 
                                icon="pi pi-check" 
                                :loading="form.processing" 
                                class="!bg-[#0E63B1] !border-[#0E63B1] hover:!bg-[#0c5599] !rounded-xl !text-sm !font-medium !px-5 !py-2.5"
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
import Back from '@/Components/MyComponents/Back.vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import FileUpload from 'primevue/fileupload';
import Toast from 'primevue/toast';
import ColorPicker from 'primevue/colorpicker';
import Dropdown from 'primevue/dropdown';

export default {
    name: 'VehiclesCreate',
    components: {
        Link,
        Back,
        Toast,
        Button,
        InputText,
        AppLayout,
        FileUpload,
        ColorPicker,
        PrimaryButton,
        Dropdown
    },
    // Recibimos las props desde el controlador
    props: {
        privateUnits: {
            type: Array,
            default: () => []
        },
        isAdmin: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            form: useForm({
                plate: '',
                brand: '',
                user_id: this.$page.props.auth.user.usert_id, // Puede ser null si es admin
                private_unit_id: null, // Campo nuevo para el admin
                model: '',
                color: '',
                tag_access: '',
                photo: null, 
            }),
            photoPreview: null,
            pickerColor: '000000',
            colorMap: {
                'blanco': '#ffffff', 'white': '#ffffff',
                'negro': '#000000', 'black': '#000000',
                'rojo': '#ef4444', 'red': '#ef4444',
                'azul': '#3b82f6', 'blue': '#3b82f6',
                'gris': '#6b7280', 'zinc': '#6b7280',
                'verde': '#22c55e', 'green': '#22c55e',
                'amarillo': '#eab308', 'yellow': '#eab308',
                'plata': '#c0c0c0', 'silver': '#c0c0c0'
            }
        }
    },
    computed: {
        finalColorPreview() {
            const textLower = this.form.color ? this.form.color.toLowerCase().trim() : '';
            if (this.colorMap[textLower]) {
                return this.colorMap[textLower];
            }
            if (/^#[0-9A-F]{6}$/i.test(this.form.color)) {
                return this.form.color;
            }
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
        syncTextFromPicker() {
            if (this.pickerColor) {
                this.form.color = '#' + this.pickerColor;
            }
        },
        syncPickerFromText() {
            const val = this.form.color;
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
:deep(*) {
    --primary-color: #0f7bc1;
    --primary-color-text: #ffffff;
    --primary-500: #0f7bc1;
    --primary-600: #0d6ca8; 
    --primary-700: #0b5c8f;
    --focus-ring: 0 0 0 2px #ffffff, 0 0 0 4px #0f7bc1;
}

:deep(.p-inputtext),
:deep(.p-dropdown) {
    @apply border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white;
}

:deep(.p-inputtext:enabled:focus),
:deep(.p-dropdown.p-focus) {
    @apply ring-2 border-[#0f7bc1];
    --tw-ring-color: #0f7bc1; 
}
:deep(.p-inputtext.p-invalid),
:deep(.p-dropdown.p-invalid) {
    @apply border-red-500 ring-red-500;
}

:deep(.p-button.p-button-primary) {
    background: var(--primary-color);
    border-color: var(--primary-color);
}
:deep(.p-button.p-button-primary:enabled:hover) {
    background: var(--primary-600);
    border-color: var(--primary-600);
}

:deep(.p-colorpicker-panel),
:deep(.p-dropdown-panel) {
    background: #1f2937; 
    border-color: #374151; 
}

:deep(.p-dropdown-item) {
    @apply text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-600;
}
:deep(.p-dropdown-item.p-highlight) {
    @apply bg-[#0f7bc1] text-white;
}
:deep(.p-dropdown-filter-container input) {
    @apply bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100;
}
</style>
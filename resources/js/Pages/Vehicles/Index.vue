<template>
    <AppLayout :title="'Mis Vehículos'">
        <!-- Componente de Diálogo de Confirmación (PrimeVue) -->
        <ConfirmDialog></ConfirmDialog>

        <!-- Contenedor Principal -->
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-4 sm:p-8 transition-colors duration-300">
            
            <!-- Encabezado -->
            <div class="max-w-7xl mx-auto mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Vehículos Registrados
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Administra el parque vehicular. Haz clic en un vehículo para ver detalles o editar.
                    </p>
                </div>
                
                <PrimaryButton @click="$inertia.visit(route('vehicles.create'))">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Registrar Vehículo
                </PrimaryButton>
            </div>

            <div class="max-w-7xl mx-auto">
                
                <!-- ESTADO VACÍO -->
                <div v-if="vehicles.data.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center border border-dashed border-gray-300 dark:border-gray-700">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay vehículos</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza registrando tu primer vehículo.</p>
                </div>

                <div v-else>
                    <!-- VISTA DESKTOP (Tabla) -->
                    <div class="hidden md:block overflow-hidden rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Vehículo / Placa
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Detalles
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tag de Acceso
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                <tr 
                                    v-for="vehicle in vehicles.data" 
                                    :key="vehicle.id" 
                                    @click="openModal(vehicle)"
                                    class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors cursor-pointer group"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <!-- Imagen o Icono -->
                                            <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 relative">
                                                <img 
                                                    v-if="vehicle.photo_url" 
                                                    :src="vehicle.photo_url" 
                                                    alt="Foto vehículo" 
                                                    class="h-full w-full object-cover"
                                                >
                                                <div v-else class="h-full w-full flex items-center justify-center text-indigo-400 dark:text-indigo-300">
                                                    <i class="fa-solid fa-car"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ vehicle.plate }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Registrado: {{ formatDate(vehicle.created_at) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white font-medium">{{ vehicle.brand }} {{ vehicle.model }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <!-- Indicador de Color Mejorado -->
                                            <span 
                                                class="inline-block w-4 h-4 rounded-full border border-gray-300 shadow-sm"
                                                :style="{ backgroundColor: translateColor(vehicle.color) }"
                                                :title="vehicle.color"
                                            ></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ vehicle.color }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="vehicle.tag_access" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800">
                                            {{ vehicle.tag_access }}
                                        </span>
                                        <span v-else class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            Sin Tag
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- VISTA MÓVIL (Tarjetas) -->
                    <div class="md:hidden grid grid-cols-1 gap-4">
                        <div 
                            v-for="vehicle in vehicles.data" 
                            :key="vehicle.id" 
                            @click="openModal(vehicle)"
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700 cursor-pointer active:scale-95 transition-transform"
                        >
                            <!-- Header Tarjeta -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <!-- Imagen Pequeña -->
                                    <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                        <img v-if="vehicle.photo_url" :src="vehicle.photo_url" class="h-full w-full object-cover">
                                        <i v-else class="fa-solid fa-car"></i>
                                    </div>
                                    <span class="font-bold text-gray-800 dark:text-white text-lg">{{ vehicle.plate }}</span>
                                </div>
                                <span v-if="vehicle.tag_access" class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded">
                                    TAG
                                </span>
                            </div>

                            <!-- Cuerpo Tarjeta -->
                            <div class="p-4 space-y-2">
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ vehicle.brand }} - {{ vehicle.model }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span 
                                        class="w-4 h-4 rounded-full border border-gray-300"
                                        :style="{ backgroundColor: translateColor(vehicle.color) }"
                                    ></span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ vehicle.color }}</span>
                                </div>
                                <div class="pt-2 text-right">
                                    <span class="text-xs text-indigo-500 font-medium">Toca para editar</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paginación -->
                    <div v-if="vehicles.links.length > 3" class="mt-6 flex justify-center">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, key) in vehicles.links" :key="key">
                                <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-400 border border-transparent rounded-md" v-html="link.label" />
                                <Link v-else 
                                    :href="link.url" 
                                    class="px-4 py-2 text-sm border rounded-md transition-colors"
                                    :class="link.active 
                                        ? 'bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-500' 
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700'"
                                    v-html="link.label" 
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DE EDICIÓN / DETALLES -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <!-- Backdrop -->
            <div @click="closeModal" class="absolute inset-0 bg-gray-900/75 transition-opacity"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all max-h-[90vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center sticky top-0 z-10 backdrop-blur-md">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Editar Vehículo
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (Formulario) -->
                <div class="p-5 space-y-5">
                    
                    <!-- Previsualización de Imagen -->
                    <div class="flex justify-center">
                        <div class="relative group">
                            <div class="h-40 w-40 rounded-full overflow-hidden border-4 border-white dark:border-gray-700 shadow-lg bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
                                <img 
                                    v-if="photoPreview || form.photo_url" 
                                    :src="photoPreview || form.photo_url" 
                                    class="h-full w-full object-cover"
                                >
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <!-- Botón para subir foto -->
                            <label class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-full shadow-md cursor-pointer hover:bg-indigo-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <input type="file" class="hidden" @change="updatePhotoPreview" accept="image/*">
                            </label>
                        </div>
                    </div>

                    <!-- Campos -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Placa / Matrícula</label>
                            <input v-model="form.plate" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="Ej: ABC-123">
                            <p v-if="errors.plate" class="text-red-500 text-xs mt-1">{{ errors.plate }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Marca</label>
                            <input v-model="form.brand" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <p v-if="errors.brand" class="text-red-500 text-xs mt-1">{{ errors.brand }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modelo</label>
                            <input v-model="form.model" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <p v-if="errors.model" class="text-red-500 text-xs mt-1">{{ errors.model }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                            <div class="flex items-center gap-2">
                                <input 
                                    v-model="form.color" 
                                    type="text" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                                    placeholder="Ej: Rojo o #FF0000"
                                >
                                <!-- Previsualización del color en el formulario -->
                                <div 
                                    class="w-10 h-10 rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm flex-shrink-0"
                                    :style="{ backgroundColor: translateColor(form.color) }"
                                    title="Previsualización de color"
                                ></div>
                            </div>
                            <p v-if="errors.color" class="text-red-500 text-xs mt-1">{{ errors.color }}</p>
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tag Acceso</label>
                            <input v-model="form.tag_access" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row sm:justify-between gap-3">
                    <button 
                        @click="deleteVehicle"
                        class="w-full sm:w-auto px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        :disabled="processing"
                    >
                        Eliminar Vehículo
                    </button>

                    <div class="flex gap-3 w-full sm:w-auto">
                        <button 
                            @click="closeModal"
                            class="flex-1 sm:flex-none px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            @click="submitUpdate"
                            class="flex-1 sm:flex-none px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-md transition-colors flex items-center justify-center"
                            :disabled="processing"
                        >
                            <span v-if="processing">Guardando...</span>
                            <span v-else>Guardar Cambios</span>
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

// Importaciones de PrimeVue
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";

export default {
    name: 'VehiclesIndex',
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
        vehicles: {
            type: Object,
            required: true
        },
        errors: Object
    },
    data() {
        return {
            showModal: false,
            processing: false,
            photoPreview: null,
            form: {
                id: null,
                plate: '',
                brand: '',
                model: '',
                color: '',
                tag_access: '',
                photo: null,
                photo_url: null,
                _method: 'PUT'
            },
            // Mapeo básico de colores comunes
            colorMap: {
                'blanco': '#ffffff',
                'white': '#ffffff',
                'negro': '#000000',
                'black': '#000000',
                'rojo': '#ef4444',
                'red': '#ef4444',
                'azul': '#3b82f6',
                'blue': '#3b82f6',
                'gris': '#6b7280',
                'gray': '#6b7280',
                'plata': '#9ca3af',
                'silver': '#c0c0c0',
                'verde': '#22c55e',
                'green': '#22c55e',
                'amarillo': '#eab308',
                'yellow': '#ffff00',
                'naranja': '#f97316',
                'orange': '#ffa500'
            }
        }
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return '';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('es-MX', options);
        },
        // MÉTODO ACTUALIZADO: Soporta Texto y Hexadecimal
        translateColor(colorName) {
            if (!colorName) return '#cccccc';
            
            // 1. Limpiamos espacios
            const input = colorName.trim();

            // 2. Si empieza con #, asumimos que es Hex y lo devolvemos tal cual
            if (input.startsWith('#')) {
                return input;
            }

            // 3. Si no es hex, buscamos en el mapa (normalizado a minúsculas)
            const normalized = input.toLowerCase();
            return this.colorMap[normalized] || '#cccccc'; // Default gris si no se encuentra
        },
        openModal(vehicle) {
            this.photoPreview = null;
            this.form = {
                id: vehicle.id,
                plate: vehicle.plate,
                brand: vehicle.brand,
                model: vehicle.model,
                color: vehicle.color,
                tag_access: vehicle.tag_access,
                photo_url: vehicle.photo_url,
                photo: null,
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
            reader.onload = (e) => {
                this.photoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        submitUpdate() {
            this.processing = true;
            router.post(route('vehicles.update', this.form.id), this.form, {
                preserveScroll: true,
                onSuccess: () => {
                    this.processing = false;
                    this.closeModal();
                },
                onError: () => {
                    this.processing = false;
                }
            });
        },
        deleteVehicle() {
            this.confirm.require({
                message: '¿Estás seguro de que deseas eliminar este vehículo? Esta acción no se puede deshacer.',
                header: 'Confirmar Eliminación',
                icon: 'pi pi-exclamation-triangle',
                rejectLabel: 'Cancelar',
                acceptLabel: 'Eliminar',
                rejectClass: 'bg-white text-gray-800 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-200 transition-colors mr-2',
                acceptClass: 'bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 transition-colors border-none',
                
                accept: () => {
                    this.processing = true;
                    router.delete(route('vehicles.destroy', this.form.id), {
                        preserveScroll: true,
                        onSuccess: () => {
                            this.processing = false;
                            this.closeModal();
                        },
                        onFinish: () => {
                            this.processing = false;
                        }
                    });
                }
            });
        }
    }
}
</script>

<style scoped>
tr {
    transition: all 0.15s ease-in-out;
}
</style>
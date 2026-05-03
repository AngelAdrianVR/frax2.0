<template>
    <AppLayout :title="'Control Vehicular'">
        <ConfirmDialog></ConfirmDialog>

        <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 text-zinc-800 dark:text-zinc-100 p-4 sm:p-8 transition-colors duration-300">
            
            <!-- Encabezado Admin -->
            <div class="max-w-7xl mx-auto mb-1 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">
                        Administración Vehicular
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Gestión global de vehículos del fraccionamiento.
                    </p>
                </div>
                
                <!-- Buscador -->
                <div class="w-full md:w-auto relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input 
                        v-model="search" 
                        type="text" 
                        placeholder="Buscar placa, casa o residente..." 
                        class="pl-10 pr-4 py-2 w-full md:w-80 rounded-lg border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                    >
                </div>
            </div>

            <div class="flex justify-end">
                <PrimaryButton @click="$inertia.visit(route('vehicles.create'))">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Registrar Vehículo
                </PrimaryButton>
            </div>

            <div class="max-w-7xl mx-auto mt-4">
                
                <!-- ESTADO VACÍO -->
                <div v-if="vehicles.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <div class="mx-auto h-12 w-12 text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">No se encontraron vehículos</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Intenta con otros términos de búsqueda.</p>
                </div>

                <div v-else>
                    <!-- TABLA ADMIN (Desktop) -->
                    <div class="hidden md:block overflow-hidden rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Placa / Foto
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Ubicación (Casa)
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Vehículo
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Tag Acceso
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-800">
                                <tr 
                                    v-for="vehicle in vehicles.data" 
                                    :key="vehicle.id" 
                                    @click="openModal(vehicle)"
                                    class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors cursor-pointer group"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 relative">
                                                <img 
                                                    v-if="vehicle.photo_url" 
                                                    :src="vehicle.photo_url" 
                                                    alt="Foto" 
                                                    class="h-full w-full object-cover"
                                                >
                                                <div v-else class="h-full w-full flex items-center justify-center text-indigo-400 dark:text-indigo-300">
                                                    <i class="fa-solid fa-car"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                                    {{ vehicle.plate }}
                                                </div>
                                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    Reg: {{ formatDate(vehicle.created_at) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Nueva Columna: Ubicación y Dueño -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                            <i class="fa-solid fa-house-user mr-1 text-zinc-400"></i>
                                            {{ vehicle.house_info }}
                                        </div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                            {{ vehicle.resident_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-zinc-900 dark:text-white">{{ vehicle.brand }} {{ vehicle.model }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span 
                                                class="inline-block w-3 h-3 rounded-full border border-zinc-300"
                                                :style="{ backgroundColor: translateColor(vehicle.color) }"
                                            ></span>
                                            <span class="text-xs text-zinc-500 capitalize">{{ vehicle.color }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="vehicle.tag_access" class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            {{ vehicle.tag_access }}
                                        </span>
                                        <span v-else class="text-xs text-zinc-400 italic">N/A</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            Editar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- VISTA MÓVIL ADMIN (Tarjetas) -->
                    <div class="md:hidden grid grid-cols-1 gap-4">
                        <div 
                            v-for="vehicle in vehicles.data" 
                            :key="vehicle.id" 
                            @click="openModal(vehicle)"
                            class="bg-white dark:bg-zinc-800 rounded-xl shadow-md overflow-hidden border border-zinc-200 dark:border-zinc-700"
                        >
                            <div class="bg-indigo-50 dark:bg-zinc-700/50 px-4 py-2 border-b border-zinc-100 dark:border-zinc-700 flex justify-between items-center">
                                <span class="font-bold text-zinc-800 dark:text-white text-lg">{{ vehicle.plate }}</span>
                                <span class="text-xs bg-white dark:bg-zinc-600 px-2 py-1 rounded shadow-sm">
                                    {{ vehicle.house_info }}
                                </span>
                            </div>
                            <div class="p-4 flex gap-4">
                                <div class="h-16 w-16 flex-shrink-0 rounded-lg bg-zinc-200 overflow-hidden">
                                     <img v-if="vehicle.photo_url" :src="vehicle.photo_url" class="h-full w-full object-cover">
                                     <div v-else class="h-full w-full flex items-center justify-center text-zinc-400"><i class="fa-solid fa-car fa-lg"></i></div>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ vehicle.brand }} - {{ vehicle.model }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Prop: {{ vehicle.resident_name }}</p>
                                    <p v-if="vehicle.tag_access" class="text-xs text-green-600 font-bold">TAG: {{ vehicle.tag_access }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paginación -->
                    <div v-if="vehicles.links.length > 3" class="mt-6 flex justify-center">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, key) in vehicles.links" :key="key">
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

        <!-- REUTILIZAMOS EL MISMO MODAL (Copiado de Index.vue para mantener funcionalidad) -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div @click="closeModal" class="absolute inset-0 bg-zinc-900/75 transition-opacity"></div>
            <div class="relative bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-700/50 border-b border-zinc-100 dark:border-zinc-700 flex justify-between items-center sticky top-0 z-10 backdrop-blur-md">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Editar Vehículo (Admin)</h3>
                    <button @click="closeModal" class="text-zinc-400 hover:text-zinc-500"><i class="fa-solid fa-times"></i></button>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Previsualización de Imagen -->
                    <div class="flex justify-center">
                        <div class="relative group">
                            <div class="h-40 w-40 rounded-full overflow-hidden border-4 border-white dark:border-zinc-700 shadow-lg bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center">
                                <img v-if="photoPreview || form.photo_url" :src="photoPreview || form.photo_url" class="h-full w-full object-cover">
                                <i v-else class="fa-solid fa-car text-3xl text-zinc-300"></i>
                            </div>
                            <label class="absolute bottom-0 right-0 bg-indigo-600 text-white py-2 px-3 rounded-full shadow-md cursor-pointer hover:bg-indigo-700 transition-colors">
                                <i class="fa-solid fa-camera"></i>
                                <input type="file" class="hidden" @change="updatePhotoPreview" accept="image/*">
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Placa</label>
                            <input v-model="form.plate" type="text" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Marca</label>
                            <input v-model="form.brand" type="text" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Modelo</label>
                            <input v-model="form.model" type="text" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Color</label>
                            <input v-model="form.color" type="text" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tag Acceso</label>
                            <input v-model="form.tag_access" type="text" class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-700/50 border-t border-zinc-100 dark:border-zinc-700 flex justify-end gap-3">
                    <button @click="closeModal" class="px-4 py-2 bg-white border border-zinc-300 rounded-lg text-zinc-700">Cancelar</button>
                    <button @click="submitUpdate" class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-md" :disabled="processing">
                        {{ processing ? 'Guardando...' : 'Guardar Cambios' }}
                    </button>
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
import debounce from 'lodash/debounce'; // Asegúrate de tener lodash instalado

export default {
    name: 'AdminVehiclesIndex',
    components: { Link, AppLayout, ConfirmDialog, PrimaryButton },
    setup() {
        const confirm = useConfirm();
        return { confirm };
    },
    props: {
        vehicles: Object,
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
                plate: '',
                brand: '',
                model: '',
                color: '',
                tag_access: '',
                photo: null,
                photo_url: null,
                _method: 'PUT'
            },
            colorMap: {
                'blanco': '#ffffff', 'white': '#ffffff',
                'negro': '#000000', 'black': '#000000',
                'rojo': '#ef4444', 'red': '#ef4444',
                'azul': '#3b82f6', 'blue': '#3b82f6',
                'gris': '#6b7280', 'zinc': '#6b7280',
                'plata': '#9ca3af', 'silver': '#c0c0c0',
            }
        }
    },
    watch: {
        search: debounce(function(value) {
            router.get(route('admin.vehicles.index'), { search: value }, {
                preserveState: true,
                replace: true,
                preserveScroll: true
            });
        }, 500)
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString('es-MX', { year: '2-digit', month: 'short', day: 'numeric' });
        },
        translateColor(colorName) {
            if (!colorName) return '#cccccc';
            const input = colorName.trim();
            if (input.startsWith('#')) return input;
            return this.colorMap[input.toLowerCase()] || '#cccccc';
        },
        openModal(vehicle) {
            this.photoPreview = null;
            this.form = { ...vehicle, photo: null, _method: 'PUT' }; // Simple clone
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
        submitUpdate() {
            this.processing = true;
            router.post(route('vehicles.update', this.form.id), this.form, {
                preserveScroll: true,
                onSuccess: () => { this.processing = false; this.closeModal(); },
                onError: () => this.processing = false
            });
        }
    }
}
</script>
<template>
    <AppLayout :title="'Gestión de Unidades Privadas'">
        <ConfirmDialog></ConfirmDialog>

        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 p-4 sm:p-8 transition-colors duration-300">
            
            <!-- Encabezado Admin -->
            <div class="max-w-7xl mx-auto mb-1 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Unidades Privadas
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Gestión de casas, propietarios y estados de cuenta.
                    </p>
                </div>
                
                <!-- Buscador -->
                <div class="w-full md:w-auto relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="pi pi-search text-gray-400"></i>
                    </span>
                    <input 
                        v-model="search" 
                        type="text" 
                        placeholder="Buscar calle, número o propietario..." 
                        class="pl-10 pr-4 py-2 w-full md:w-80 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all"
                    >
                </div>
            </div>

            <!-- Botón de registro (Ahora redirige a ruta create) -->
            <div class="flex justify-end mt-4 max-w-7xl mx-auto">
                <Link :href="route('admin.private-units.create')">
                    <PrimaryButton>
                        <i class="pi pi-home mr-2"></i> Registrar Nueva Casa
                    </PrimaryButton>
                </Link>
            </div>

            <div class="max-w-7xl mx-auto mt-4">
                
                <!-- ESTADO VACÍO -->
                <div v-if="units.data.length === 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center border border-dashed border-gray-300 dark:border-gray-700">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <i class="pi pi-home" style="font-size: 2rem"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No se encontraron unidades</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Intenta con otros términos de búsqueda.</p>
                </div>

                <div v-else>
                    <!-- TABLA ADMIN (Desktop) -->
                    <div class="hidden md:block overflow-hidden rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Unidad / Dirección
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Propietario
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estado de Pagos
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estatus Sistema
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                <tr 
                                    v-for="unit in units.data" 
                                    :key="unit.id"
                                    @click="$inertia.visit(route('admin.private-units.show', unit.id))" 
                                    class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors group"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300">
                                                <i class="pi pi-home"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ unit.unit_street }} {{ unit.exterior_number }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Lote: {{ unit.lot_number }} 
                                                    <span v-if="unit.int_number">- Int: {{ unit.int_number }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white font-medium">
                                            {{ unit.owner_name }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <!-- Semáforo -->
                                        <div class="flex items-center gap-2">
                                            <span 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                                :class="{
                                                    'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/50 dark:text-green-200 dark:border-green-800': unit.payment_status === 'green',
                                                    'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/50 dark:text-amber-200 dark:border-amber-800': unit.payment_status === 'amber',
                                                    'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/50 dark:text-red-200 dark:border-red-800': unit.payment_status === 'red'
                                                }"
                                            >
                                                <span class="w-2 h-2 mr-1.5 rounded-full" 
                                                    :class="{
                                                        'bg-green-500': unit.payment_status === 'green',
                                                        'bg-amber-500': unit.payment_status === 'amber',
                                                        'bg-red-500': unit.payment_status === 'red'
                                                    }">
                                                </span>
                                                {{ 
                                                    unit.payment_status === 'green' ? 'Al Corriente' : 
                                                    (unit.payment_status === 'amber' ? 'Pendiente' : 'Vencido') 
                                                }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                            :class="unit.status === 'Activo' ? 'text-green-600 bg-green-50 dark:bg-green-900/20' : 'text-gray-500 bg-gray-100 dark:bg-gray-700'"
                                        >
                                            {{ unit.status }}
                                        </span>
                                        <span v-if="unit.access_block" class="ml-2 text-xs text-red-500 font-bold" title="Acceso Bloqueado">
                                            <i class="pi pi-lock"></i>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <button @click="openEditModal(unit)" class="p-1 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="Editar">
                                                <i class="pi pi-pencil"></i>
                                            </button>
                                            <button @click="toggleStatus(unit)" class="p-1" :class="unit.status === 'Activo' ? 'text-amber-600 hover:text-amber-900' : 'text-green-600 hover:text-green-900'" :title="unit.status === 'Activo' ? 'Inactivar' : 'Activar'">
                                                <i :class="unit.status === 'Activo' ? 'pi pi-power-off' : 'pi pi-check-circle'"></i>
                                            </button>
                                            <button @click="$inertia.visit(route('admin.private-units.show', unit.id))" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="Ver Detalles">
                                                <i class="pi pi-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- VISTA MÓVIL (Cards) -->
                    <div class="md:hidden grid grid-cols-1 gap-4">
                        <div 
                            v-for="unit in units.data" 
                            :key="unit.id" 
                            @click="$inertia.visit(route('admin.private-units.show', unit.id))"
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border-l-4"
                            :class="{
                                'border-l-green-500': unit.payment_status === 'green',
                                'border-l-amber-500': unit.payment_status === 'amber',
                                'border-l-red-500': unit.payment_status === 'red',
                            }"
                        >
                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ unit.unit_street }} {{ unit.exterior_number }}</h3>
                                        <p class="text-xs text-gray-500">Lote: {{ unit.lot_number }}</p>
                                    </div>
                                    <span 
                                        class="px-2 py-1 text-xs rounded font-bold"
                                        :class="unit.status === 'Activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                    >
                                        {{ unit.status }}
                                    </span>
                                </div>
                                
                                <div class="mt-3 flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="pi pi-user text-indigo-500"></i>
                                    {{ unit.owner_name }}
                                </div>

                                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                    <span 
                                        class="text-xs font-bold px-2 py-1 rounded"
                                        :class="{
                                            'text-green-600 bg-green-50': unit.payment_status === 'green',
                                            'text-amber-600 bg-amber-50': unit.payment_status === 'amber',
                                            'text-red-600 bg-red-50': unit.payment_status === 'red'
                                        }"
                                    >
                                        {{ unit.payment_status === 'green' ? 'AL CORRIENTE' : (unit.payment_status === 'amber' ? 'PENDIENTE' : 'VENCIDO') }}
                                    </span>
                                    
                                    <div class="flex gap-3">
                                        <button @click="openEditModal(unit)" class="text-indigo-600"><i class="pi pi-pencil"></i></button>
                                        <button @click="toggleStatus(unit)" class="text-amber-600"><i class="pi pi-power-off"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paginación -->
                    <div v-if="units.links.length > 3" class="mt-6 flex justify-center">
                        <div class="flex flex-wrap gap-1">
                            <template v-for="(link, key) in units.links" :key="key">
                                <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-400 border border-transparent rounded-md" v-html="link.label" />
                                <Link v-else 
                                    :href="link.url" 
                                    class="px-4 py-2 text-sm border rounded-md transition-colors"
                                    :class="link.active 
                                        ? 'bg-indigo-600 text-white border-indigo-600 dark:bg-indigo-500' 
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600'"
                                    v-html="link.label" 
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DE EDICIÓN (Creación eliminada de aquí) -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div @click="closeModal" class="absolute inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Editar Unidad Privada
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
                        <i class="pi pi-times text-lg"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto space-y-4">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Calle / Avenida</label>
                            <input v-model="form.unit_street" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Número Exterior</label>
                            <input v-model="form.exterior_number" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Número Interior</label>
                            <input v-model="form.int_number" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lote (Catastro)</label>
                            <input v-model="form.lot_number" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">M2 Construcción</label>
                            <input v-model="form.square_meters" type="number" step="0.01" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Configuración de Estado</label>
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/30 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                            <span class="text-sm font-medium dark:text-white">Estado de la Unidad</span>
                            <select v-model="form.status" class="rounded-md border-gray-300 dark:border-gray-600 text-sm bg-white dark:bg-gray-800">
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between bg-red-50 dark:bg-red-900/10 p-3 rounded-lg border border-red-200 dark:border-red-800 mt-3">
                            <span class="text-sm font-medium text-red-800 dark:text-red-300">Bloquear Acceso (Seguridad)</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.access_block" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-red-300 dark:peer-focus:ring-red-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                    <button @click="closeModal" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 text-sm">Cancelar</button>
                    <button @click="submitUpdate" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md text-sm font-medium transition-colors flex items-center gap-2" :disabled="processing">
                        <i v-if="processing" class="pi pi-spin pi-spinner"></i>
                        {{ processing ? 'Guardando...' : 'Actualizar' }}
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
import debounce from 'lodash/debounce';

export default {
    name: 'PrivateUnitsIndex',
    components: { Link, AppLayout, ConfirmDialog, PrimaryButton },
    setup() {
        const confirm = useConfirm();
        return { confirm };
    },
    props: {
        units: Object,
        filters: Object,
    },
    data() {
        return {
            search: this.filters.search || '',
            showModal: false,
            processing: false,
            // Eliminada variable isEditing porque ahora el modal es SOLO para editar
            form: {
                id: null,
                lot_number: '',
                unit_street: '',
                exterior_number: '',
                int_number: '',
                square_meters: 0,
                status: 'Activo',
                access_block: false,
            }
        }
    },
    watch: {
        search: debounce(function(value) {
            router.get(route('admin.private-units.index'), { search: value }, {
                preserveState: true,
                replace: true,
                preserveScroll: true
            });
        }, 500)
    },
    methods: {
        // Método resetForm simplificado o eliminado si no se usa mucho
        resetForm() {
            this.form = {
                id: null,
                lot_number: '',
                unit_street: '',
                exterior_number: '',
                int_number: '',
                square_meters: 0,
                status: 'Activo',
                access_block: false
            };
        },
        // Eliminado openCreateModal
        openEditModal(unit) {
            this.form = { ...unit }; 
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.resetForm();
        },
        submitUpdate() {
            this.processing = true;
            router.put(route('admin.private-units.update', this.form.id), this.form, {
                onSuccess: () => { 
                    this.processing = false; 
                    this.closeModal(); 
                },
                onError: () => this.processing = false
            });
        },
        toggleStatus(unit) {
            this.confirm.require({
                message: `¿Deseas ${unit.status === 'Activo' ? 'inactivar' : 'activar'} la unidad ${unit.unit_street} ${unit.exterior_number}?`,
                header: 'Confirmar cambio de estado',
                icon: 'pi pi-exclamation-circle',
                acceptLabel: 'Sí, cambiar',
                rejectLabel: 'Cancelar',
                acceptClass: unit.status === 'Activo' ? 'p-button-warning' : 'p-button-success',
                accept: () => {
                    router.patch(route('admin.private-units.toggle-status', unit.id), {}, {
                         preserveScroll: true
                    });
                }
            });
        }
    }
}
</script>
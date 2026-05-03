<template>
    <AppLayout :title="'Gestión de Propiedades'">
        <ConfirmDialog></ConfirmDialog>

        <!-- Fondo estilo iOS: Gris muy claro (#F2F2F7 en hex, usamos zinc-50) -->
        <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 text-gray-800 dark:text-zinc-100 p-4 sm:p-8 transition-colors duration-300 font-sans tracking-tight">
            
            <div class="max-w-7xl mx-auto">
                
                <!-- Encabezado Admin -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                            Propiedades
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Gestión de casas, propietarios y estados financieros.
                        </p>
                    </div>
                    
                    <div class="flex gap-3 w-full md:w-auto">
                        <!-- Buscador -->
                        <div class="relative w-full md:w-72">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="pi pi-search text-gray-400 text-sm"></i>
                            </span>
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Buscar calle, número..." 
                                class="pl-9 pr-4 py-2 w-full rounded-xl border-none bg-black/5 dark:bg-white/10 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 shadow-none transition-all placeholder-gray-500"
                            >
                        </div>
                        <Link :href="route('admin.private-units.create')" class="hidden md:flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors shadow-sm">
                            <i class="pi pi-plus mr-2"></i> Registrar
                        </Link>
                    </div>
                </div>

                <!-- ================= KPIs ESTILO iOS ================= -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    
                    <!-- KPI: Índice de Morosidad -->
                    <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] p-5 shadow-[0_2px_10px_rgba(0,0,0,0.04)] dark:shadow-none border border-black/5 dark:border-white/5 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-50 text-blue-500 dark:bg-blue-500/20 dark:text-blue-400">
                            <i class="pi pi-chart-pie text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[13px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Índice de Pagos</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ kpis.payment_rate }}%</h3>
                            <p class="text-[11px] text-gray-400 font-medium">{{ kpis.debtor_units }} casas con adeudo</p>
                        </div>
                    </div>

                    <!-- KPI: Caja Total (Deudas) -->
                    <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] p-5 shadow-[0_2px_10px_rgba(0,0,0,0.04)] dark:shadow-none border border-black/5 dark:border-white/5 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center bg-red-50 text-red-500 dark:bg-red-500/20 dark:text-red-400">
                            <i class="pi pi-wallet text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[13px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Deuda Pendiente</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ formatCurrency(kpis.total_debt) }}</h3>
                            <p class="text-[11px] text-gray-400 font-medium">Por cobrar a morosos</p>
                        </div>
                    </div>

                    <!-- KPI: Ocupación -->
                    <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] p-5 shadow-[0_2px_10px_rgba(0,0,0,0.04)] dark:shadow-none border border-black/5 dark:border-white/5 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center bg-green-50 text-green-500 dark:bg-green-500/20 dark:text-green-400">
                            <i class="pi pi-home text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[13px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ocupación</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-0.5">{{ kpis.occupancy_rate }}%</h3>
                            <p class="text-[11px] text-gray-400 font-medium">{{ kpis.occupied_units }} de {{ kpis.total_units }} casas habitadas</p>
                        </div>
                    </div>

                </div>

                <!-- ================= LISTA DE PROPIEDADES ================= -->
                
                <div v-if="units.data.length === 0" class="bg-white dark:bg-[#1C1C1E] rounded-[24px] shadow-sm p-12 text-center border border-black/5 dark:border-white/5">
                    <div class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600 mb-4">
                        <i class="pi pi-home" style="font-size: 3rem"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No se encontraron propiedades</h3>
                    <p class="mt-1 text-sm text-gray-500">Intenta con otros términos de búsqueda o registra una nueva.</p>
                </div>

                <div v-else>
                    <!-- TABLA ADMIN (Desktop) -->
                    <div class="hidden md:block overflow-hidden rounded-[24px] shadow-[0_2px_15px_rgba(0,0,0,0.03)] border border-black/5 dark:border-white/5 bg-white dark:bg-[#1C1C1E]">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-zinc-800">
                            <thead class="bg-black/10 dark:bg-white/10 backdrop-blur-md">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider">Unidad</th>
                                    <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider">Propietario</th>
                                    <th scope="col" class="px-6 py-4 text-center text-[11px] font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-4 text-right text-[11px] font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider">Acciones Rápidas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-zinc-800 bg-white dark:bg-[#1C1C1E]">
                                <tr 
                                    v-for="unit in units.data" 
                                    :key="unit.id"
                                    class="hover:bg-gray-50/80 dark:hover:bg-[#2C2C2E]/50 transition-colors group"
                                >
                                    <!-- Celda Unidad -->
                                    <td class="px-6 py-4 whitespace-nowrap cursor-pointer" @click="$inertia.visit(route('admin.private-units.show', unit.id))">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-100 dark:bg-[#2C2C2E] flex items-center justify-center text-gray-500 dark:text-gray-400">
                                                <i class="pi pi-home"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ unit.unit_street }} {{ unit.exterior_number }}
                                                </div>
                                                <div class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                                                    Lote: {{ unit.lot_number }} 
                                                    <span v-if="unit.int_number">- Int: {{ unit.int_number }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Celda Propietario -->
                                    <td class="px-6 py-4 whitespace-nowrap cursor-pointer" @click="$inertia.visit(route('admin.private-units.show', unit.id))">
                                        <div class="text-sm text-gray-900 dark:text-gray-200 font-medium" :class="{'italic text-gray-400': unit.owner_name === 'Sin asignar'}">
                                            {{ unit.owner_name }}
                                        </div>
                                    </td>

                                    <!-- Celda Estado -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center gap-1.5">
                                            <!-- Semáforo iOS Style -->
                                            <span 
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide"
                                                :class="{
                                                    'bg-[#E5F5E9] text-[#128B36] dark:bg-[#128B36]/20 dark:text-[#34C759]': unit.payment_status === 'green',
                                                    'bg-[#FFF4E5] text-[#B26E00] dark:bg-[#B26E00]/20 dark:text-[#FF9F0A]': unit.payment_status === 'amber',
                                                    'bg-[#FFECEB] text-[#C41C1C] dark:bg-[#C41C1C]/20 dark:text-[#FF453A]': unit.payment_status === 'red'
                                                }"
                                            >
                                                {{ 
                                                    unit.payment_status === 'green' ? 'AL CORRIENTE' : 
                                                    (unit.payment_status === 'amber' ? 'PENDIENTE' : 'VENCIDO') 
                                                }}
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Celda Acciones Rápidas -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Botones iOS sutiles -->
                                            <button @click="quickAction(unit, 'statement')" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 dark:hover:text-indigo-400 transition-all tooltip" title="Ver Estado de Cuenta">
                                                <i class="pi pi-file-pdf"></i>
                                            </button>
                                            <button @click="quickAction(unit, 'pay')" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-500/20 dark:hover:text-green-400 transition-all tooltip" title="Registrar Pago">
                                                <i class="pi pi-dollar"></i>
                                            </button>
                                            <button @click="quickAction(unit, 'remind')" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/20 dark:hover:text-amber-400 transition-all tooltip" title="Enviar Recordatorio">
                                                <i class="pi pi-bell"></i>
                                            </button>
                                            
                                            <div class="w-px h-5 bg-gray-200 dark:bg-zinc-700 mx-1"></div>

                                            <button @click="openEditModal(unit)" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-zinc-700 dark:hover:text-white transition-all tooltip" title="Editar Unidad">
                                                <i class="pi pi-ellipsis-h"></i>
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
                            class="bg-white dark:bg-[#1C1C1E] rounded-[20px] shadow-sm overflow-hidden border border-black/5 dark:border-white/5 p-5 relative"
                        >
                            <!-- Indicador de estado lateral (iOS Style dot) -->
                            <div class="absolute top-5 right-5 w-3 h-3 rounded-full shadow-sm"
                                :class="{
                                    'bg-[#34C759]': unit.payment_status === 'green',
                                    'bg-[#FF9F0A]': unit.payment_status === 'amber',
                                    'bg-[#FF453A]': unit.payment_status === 'red',
                                }">
                            </div>

                            <div @click="$inertia.visit(route('admin.private-units.show', unit.id))" class="cursor-pointer">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white pr-6">{{ unit.unit_street }} {{ unit.exterior_number }}</h3>
                                <p class="text-xs text-gray-500 font-medium mt-0.5">Lote: {{ unit.lot_number }} | Propietario: {{ unit.owner_name }}</p>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <button @click="quickAction(unit, 'statement')" class="flex-1 bg-gray-100 dark:bg-[#2C2C2E] hover:bg-gray-200 text-gray-700 dark:text-gray-200 py-2 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1">
                                    <i class="pi pi-file-pdf"></i> Edo. Cta
                                </button>
                                <button @click="quickAction(unit, 'pay')" class="flex-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 py-2 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1">
                                    <i class="pi pi-dollar"></i> Pagar
                                </button>
                                <button @click="openEditModal(unit)" class="w-10 bg-gray-100 dark:bg-[#2C2C2E] hover:bg-gray-200 text-gray-600 dark:text-gray-300 rounded-xl flex items-center justify-center transition">
                                    <i class="pi pi-pencil"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Paginación -->
                    <div v-if="units.links.length > 3" class="mt-8 flex justify-center pb-8">
                        <div class="flex flex-wrap gap-1 bg-white dark:bg-[#1C1C1E] p-1 rounded-2xl shadow-sm border border-black/5 dark:border-white/5">
                            <template v-for="(link, key) in units.links" :key="key">
                                <div v-if="link.url === null" class="px-3 py-1.5 text-sm text-gray-300 dark:text-zinc-600 rounded-xl" v-html="link.label" />
                                <Link v-else 
                                    :href="link.url" 
                                    class="px-3 py-1.5 text-sm rounded-xl transition-all font-medium"
                                    :class="link.active 
                                        ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' 
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#2C2C2E]'"
                                    v-html="link.label" 
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DE EDICIÓN ESTILO iOS -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div @click="closeModal" class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative bg-[#F2F2F7] dark:bg-[#1C1C1E] rounded-[32px] shadow-2xl w-full max-w-lg overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 bg-white dark:bg-[#2C2C2E] border-b border-black/5 dark:border-white/5 flex justify-between items-center rounded-t-[32px]">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Editar Propiedad
                    </h3>
                    <button @click="closeModal" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-[#1C1C1E] text-gray-500 flex items-center justify-center hover:bg-gray-200 transition">
                        <i class="pi pi-times"></i>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto space-y-5 bg-[#F2F2F7] dark:bg-[#1C1C1E]">
                    
                    <div class="bg-white dark:bg-[#2C2C2E] rounded-[20px] p-5 border border-black/5 dark:border-white/5 grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Calle / Avenida</label>
                            <input v-model="form.unit_street" type="text" class="w-full rounded-xl dark:text-white border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-[#1C1C1E] text-sm focus:ring-2 focus:ring-indigo-500 transition-shadow">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Núm. Exterior</label>
                            <input v-model="form.exterior_number" type="text" class="w-full rounded-xl dark:text-white border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-[#1C1C1E] text-sm focus:ring-2 focus:ring-indigo-500 transition-shadow">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Núm. Interior</label>
                            <input v-model="form.int_number" type="text" class="w-full rounded-xl dark:text-white border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-[#1C1C1E] text-sm focus:ring-2 focus:ring-indigo-500 transition-shadow">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Lote</label>
                            <input v-model="form.lot_number" type="text" class="w-full rounded-xl dark:text-white border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-[#1C1C1E] text-sm focus:ring-2 focus:ring-indigo-500 transition-shadow">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">M2 Const.</label>
                            <input v-model="form.square_meters" type="number" step="0.01" class="w-full rounded-xl dark:text-white border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-[#1C1C1E] text-sm focus:ring-2 focus:ring-indigo-500 transition-shadow">
                        </div>
                    </div>

                    <div class="bg-white dark:bg-[#2C2C2E] rounded-[20px] p-5 border border-black/5 dark:border-white/5 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white block">Estado del Sistema</span>
                                <span class="text-xs text-gray-500">¿Esta casa está habilitada en la app?</span>
                            </div>
                            <select v-model="form.status" class="rounded-xl dark:text-white border-gray-200 dark:border-zinc-600 text-sm bg-gray-50 dark:bg-[#1C1C1E] font-medium focus:ring-indigo-500">
                                <option value="Activo">🟢 Activa</option>
                                <option value="Inactivo">⚪ Inactiva</option>
                            </select>
                        </div>

                        <div class="h-px bg-gray-100 dark:bg-zinc-700 w-full"></div>

                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-red-600 dark:text-red-400 block">Bloqueo de Seguridad</span>
                                <span class="text-xs text-gray-500">Bloquear acceso en plumas y caseta</span>
                            </div>
                            <!-- Toggle switch iOS style -->
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.access_block" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-[#1C1C1E] peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-[#FF453A] border border-gray-300 dark:border-zinc-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-5 bg-white dark:bg-[#2C2C2E] border-t border-black/5 dark:border-white/5 flex justify-end gap-3 rounded-b-[32px]">
                    <button @click="closeModal" class="px-5 py-2.5 bg-gray-100 dark:bg-[#1C1C1E] hover:bg-gray-200 rounded-xl text-gray-700 dark:text-gray-300 text-sm font-bold transition">Cancelar</button>
                    <button @click="submitUpdate" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md text-sm font-bold transition-all flex items-center gap-2" :disabled="processing">
                        <i v-if="processing" class="pi pi-spin pi-spinner"></i>
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
import debounce from 'lodash/debounce';

export default {
    name: 'PrivateUnitsIndex',
    components: { Link, AppLayout, ConfirmDialog },
    setup() {
        const confirm = useConfirm();
        return { confirm };
    },
    props: {
        units: Object,
        filters: Object,
        kpis: Object // Recibimos los KPIs desde el controlador
    },
    data() {
        return {
            search: this.filters.search || '',
            showModal: false,
            processing: false,
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
        formatCurrency(value) {
            return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value || 0);
        },
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
        // Método para manejar las acciones rápidas
        quickAction(unit, actionType) {
            // Aquí puedes integrar tus rutas reales o abrir modales
            switch(actionType) {
                case 'statement':
                    // Ejemplo: router.visit(route('admin.fees.statement', unit.id));
                    alert(`Ir al Estado de Cuenta de la unidad ${unit.unit_street} ${unit.exterior_number}`);
                    break;
                case 'pay':
                    // Ejemplo: router.visit(route('admin.fees.pay', unit.id));
                    alert(`Abrir modal de registrar pago para ${unit.unit_street} ${unit.exterior_number}`);
                    break;
                case 'remind':
                    this.confirm.require({
                        message: `¿Deseas enviar un correo/notificación de recordatorio de pago a ${unit.owner_name}?`,
                        header: 'Enviar Recordatorio',
                        icon: 'pi pi-bell',
                        acceptLabel: 'Sí, enviar',
                        rejectLabel: 'Cancelar',
                        acceptClass: 'p-button-primary',
                        accept: () => {
                            // router.post(route('admin.fees.remind', unit.id));
                            alert("Recordatorio enviado con éxito.");
                        }
                    });
                    break;
            }
        }
    }
}
</script>
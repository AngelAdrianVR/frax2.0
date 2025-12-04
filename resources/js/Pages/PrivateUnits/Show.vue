<template>
    <AppLayout :title="`Detalle ${unit.unit_street} ${unit.exterior_number}`">
        <ConfirmDialog></ConfirmDialog>

        <!-- Contenedor Principal estilo iOS -->
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 pb-12 transition-colors duration-300">
            
            <!-- Header con efecto Blur (Estilo iOS Navigation Bar) -->
            <div class="sticky top-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Botón Atras -->
                        <Link :href="route('admin.private-units.index')" class="flex items-center text-indigo-600 dark:text-indigo-400 hover:opacity-70 transition-opacity">
                            <i class="pi pi-chevron-left text-lg"></i>
                            <span class="ml-1 text-base font-medium">Atrás</span>
                        </Link>

                        <h1 class="text-base font-bold text-gray-900 dark:text-white truncate max-w-[150px] sm:max-w-xs">
                            {{ unit.unit_street }} {{ unit.exterior_number }}
                        </h1>

                        <!-- Menú de Acciones -->
                        <div class="flex items-center gap-3">
                            <button @click="toggleStatus" 
                                class="p-2 rounded-full transition-colors"
                                :class="unit.status === 'Activo' ? 'text-green-500 bg-green-50 dark:bg-green-900/20' : 'text-gray-400 bg-gray-100'"
                                title="Toggle Status">
                                <i :class="unit.status === 'Activo' ? 'pi pi-power-off' : 'pi pi-lock'"></i>
                            </button>
                            
                            <button @click="confirmDelete" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 p-2 rounded-full transition-colors">
                                <i class="pi pi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 mt-6">
                
                <!-- Tarjeta Resumen Principal -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden">
                    <div class="relative z-10">
                        <span class="text-xs font-bold tracking-wider text-gray-400 uppercase mb-1 block">Unidad Privada</span>
                        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                            {{ unit.unit_street }} {{ unit.exterior_number }}
                        </h2>
                        <div class="flex items-center gap-3 mt-2">
                            <span v-if="unit.int_number" class="text-sm text-gray-500 dark:text-gray-400">
                                Int. {{ unit.int_number }}
                            </span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ unit.lot_number }}
                            </span>
                        </div>
                        
                        <!-- Badges de Estado -->
                        <div class="flex gap-2 mt-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1"
                                :class="unit.status === 'Activo' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'">
                                <span class="w-2 h-2 rounded-full" :class="unit.status === 'Activo' ? 'bg-green-500' : 'bg-gray-500'"></span>
                                {{ unit.status }}
                            </span>
                            
                            <span v-if="unit.access_block" class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 flex items-center gap-1">
                                <i class="pi pi-lock text-[10px]"></i> Acceso Bloqueado
                            </span>

                            <span class="px-3 py-1 rounded-full text-xs font-bold border"
                                :class="financialStatus === 'al_corriente' ? 'border-green-200 text-green-600 bg-green-50 dark:bg-green-900/10' : 'border-amber-200 text-amber-600 bg-amber-50 dark:bg-amber-900/10'">
                                {{ financialStatus === 'al_corriente' ? 'Pagos al día' : 'Pagos Pendientes' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Botón Flotante Editar -->
                    <button @click="showEditModal = true" class="px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full text-sm font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="pi pi-pencil"></i> Editar
                    </button>
                </div>

                <!-- Navegación por Pestañas (Segmented Control Style) -->
                <div class="overflow-x-auto pb-2 mb-4 no-scrollbar">
                    <div class="flex p-1 bg-gray-200/50 dark:bg-gray-700/50 rounded-xl whitespace-nowrap min-w-max sm:min-w-0">
                        <button 
                            v-for="tab in tabs" 
                            :key="tab.id"
                            @click="currentTab = tab.id"
                            class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-out flex items-center justify-center gap-2"
                            :class="currentTab === tab.id 
                                ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-sm scale-100' 
                                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-200/50 dark:hover:bg-gray-600/30'"
                        >
                            <i :class="tab.icon"></i>
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <!-- Área de Contenido Dinámico con Transición -->
                <Transition
                    mode="out-in"
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-2"
                >
                    <component 
                        :is="currentTabComponent" 
                        :unit="unit" 
                        class="min-h-[300px]"
                    />
                </Transition>

            </div>
        </div>

        <!-- Reutilizamos el Modal de Edición (Copiado de lógica anterior pero simplificado para Show) -->
        <EditUnitModal 
            v-if="showEditModal" 
            :unit="unit" 
            @close="showEditModal = false" 
        />

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";

// Importación de Componentes Modulares
import InfoTab from './Tabs/InfoTab.vue';
import ResidentsTab from './Tabs/ResidentsTab.vue';
import ResourcesTab from './Tabs/ResourcesTab.vue'; // Vehículos y Mascotas
import FinanceTab from './Tabs/FinanceTab.vue';
import VisitsTab from './Tabs/VisitsTab.vue';
import EditUnitModal from './Partials/EditUnitModal.vue'; // Extraeremos el modal a su propio archivo

export default {
    name: 'PrivateUnitShow',
    components: { 
        AppLayout, 
        Link, 
        ConfirmDialog, 
        InfoTab, 
        ResidentsTab, 
        ResourcesTab, 
        FinanceTab,
        VisitsTab,
        EditUnitModal
    },
    props: {
        unit: Object,
        financialStatus: String
    },
    setup() {
        const confirm = useConfirm();
        return { confirm };
    },
    data() {
        return {
            currentTab: 'info',
            showEditModal: false,
            tabs: [
                { id: 'info', label: 'Info', icon: 'pi pi-info-circle' },
                { id: 'residents', label: 'Residentes', icon: 'pi pi-users' },
                { id: 'resources', label: 'Vehículos y Mascotas', icon: 'pi pi-car' },
                { id: 'finance', label: 'Finanzas', icon: 'pi pi-wallet' },
                { id: 'visits', label: 'Visitas', icon: 'pi pi-id-card' },
            ]
        }
    },
    computed: {
        currentTabComponent() {
            const map = {
                'info': 'InfoTab',
                'residents': 'ResidentsTab',
                'resources': 'ResourcesTab',
                'finance': 'FinanceTab',
                'visits': 'VisitsTab'
            };
            return map[this.currentTab];
        }
    },
    methods: {
        toggleStatus() {
            this.confirm.require({
                message: `¿Cambiar estado a ${this.unit.status === 'Activo' ? 'Inactivo' : 'Activo'}?`,
                header: 'Confirmación',
                icon: 'pi pi-exclamation-triangle',
                acceptLabel: 'Sí, cambiar',
                rejectLabel: 'Cancelar',
                accept: () => {
                    router.patch(route('admin.private-units.toggle-status', this.unit.id), {}, { preserveScroll: true });
                }
            });
        },
        confirmDelete() {
            this.confirm.require({
                message: 'Esta acción eliminará la unidad y todos sus datos relacionados permanentemente. ¿Continuar?',
                header: 'Eliminar Unidad',
                icon: 'pi pi-trash',
                acceptClass: 'p-button-danger',
                accept: () => {
                    router.delete(route('admin.private-units.destroy', this.unit.id));
                }
            });
        }
    }
}
</script>

<style scoped>
/* Ocultar scrollbar pero permitir scroll */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";

// Importación de las Pestañas (Hijas)
import InfoTab from './Tabs/InfoTab.vue';
import ResidentsTab from './Tabs/ResidentsTab.vue';
import ResourcesTab from './Tabs/ResourcesTab.vue';
import FinanceTab from './Tabs/FinanceTab.vue';
import VisitsTab from './Tabs/VisitsTab.vue';

const props = defineProps({
    unit: Object,
    financialStatus: String
});

const confirm = useConfirm();
const toast = useToast();
const currentTab = ref('info');

const toggleStatus = () => {
    confirm.require({
        message: `¿Cambiar estado a ${props.unit.status === 'Activo' ? 'Inactivo' : 'Activo'}?`,
        header: 'Confirmar Acción',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sí, cambiar',
        rejectLabel: 'Cancelar',
        acceptClass: props.unit.status === 'Activo' ? 'p-button-warning' : 'p-button-success',
        accept: () => {
            router.patch(route('admin.private-units.toggle-status', props.unit.id), {}, { 
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Actualizado', detail: 'El estado de la propiedad ha cambiado correctamente.', life: 3000 });
                }
            });
        }
    });
};

const confirmDelete = () => {
    confirm.require({
        message: 'Esta acción eliminará la unidad y todos sus datos (pagos, vehículos, etc.) permanentemente. ¿Continuar?',
        header: 'Eliminar Propiedad',
        icon: 'pi pi-trash',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Eliminar',
        rejectLabel: 'Cancelar',
        accept: () => {
            router.delete(route('admin.private-units.destroy', props.unit.id));
        }
    });
};
</script>

<template>
    <AppLayout :title="`Detalle ${unit.unit_street} ${unit.exterior_number}`">
        <ConfirmDialog></ConfirmDialog>
        <Toast position="bottom-right" />

        <!-- Fondo iOS #F2F2F7 -->
        <div class="min-h-screen pb-12 transition-colors duration-300 font-sans tracking-tight">
            
            <!-- Header Blur (iOS Navigation Bar) -->
            <div class="top-0 z-40">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <Link :href="route('admin.private-units.index')" class="flex items-center text-indigo-600 dark:text-indigo-400 hover:opacity-70 transition-opacity font-medium">
                            <i class="pi pi-chevron-left text-lg"></i>
                            <span class="ml-1 text-[15px]">Propiedades</span>
                        </Link>

                        <h1 class="text-[15px] font-bold text-gray-900 dark:text-white truncate">
                            {{ unit.unit_street }} {{ unit.exterior_number }}
                        </h1>

                        <div class="flex items-center gap-2">
                            <button @click="confirmDelete" class="text-red-500 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 w-8 h-8 rounded-full flex items-center justify-center transition-colors">
                                <i class="pi pi-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 mt-6">
                
                <!-- Tarjeta Resumen Principal -->
                <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] shadow-sm border border-black/5 dark:border-white/5 p-6 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden">
                    <div class="relative z-10">
                        <span class="text-[11px] font-bold tracking-wider text-gray-400 uppercase mb-1 block">Ficha de la Propiedad</span>
                        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                            {{ unit.unit_street }} {{ unit.exterior_number }} <span v-if="unit.int_number" class="text-gray-400 text-2xl font-medium">Int. {{ unit.int_number }}</span>
                        </h2>
                        <div class="flex items-center gap-3 mt-1.5">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Lote: {{ unit.lot_number }}</span>
                            <span class="w-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></span>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ unit.square_meters }} m²</span>
                        </div>
                        
                        <!-- Badges -->
                        <div class="flex gap-2 mt-4">
                            <button @click="toggleStatus" class="px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5 transition-colors"
                                :class="unit.status === 'Activo' ? 'bg-[#E5F5E9] text-[#128B36] dark:bg-[#128B36]/20 dark:text-[#34C759]' : 'bg-gray-100 text-gray-600 dark:bg-[#2C2C2E] dark:text-gray-300'">
                                <span class="w-2 h-2 rounded-full" :class="unit.status === 'Activo' ? 'bg-[#34C759]' : 'bg-gray-400'"></span>
                                {{ unit.status }}
                            </button>
                            
                            <span v-if="unit.access_block" class="px-3 py-1 rounded-full text-xs font-bold bg-[#FFECEB] text-[#C41C1C] dark:bg-[#C41C1C]/20 dark:text-[#FF453A] flex items-center gap-1">
                                <i class="pi pi-lock text-[10px]"></i> Acceso Bloqueado
                            </span>

                            <span class="px-3 py-1 rounded-full text-xs font-bold border"
                                :class="financialStatus === 'al_corriente' ? 'border-green-200 text-green-600 bg-green-50 dark:border-green-900/30 dark:bg-green-900/10' : 'border-amber-200 text-amber-600 bg-amber-50 dark:border-amber-900/30 dark:bg-amber-900/10'">
                                {{ financialStatus === 'al_corriente' ? 'Al corriente' : 'Con Adeudo' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Segmented Control (Pestañas iOS Style) -->
                <div class="bg-gray-200/60 dark:bg-[#2C2C2E] p-1 rounded-[14px] flex overflow-x-auto no-scrollbar mb-6">
                    <button @click="currentTab = 'info'" :class="currentTab === 'info' ? 'bg-white dark:bg-[#1C1C1E] text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="flex-1 min-w-[120px] py-1.5 text-[13px] font-semibold rounded-[10px] transition-all flex items-center justify-center gap-2">
                        <i class="pi pi-info-circle"></i> Info
                    </button>
                    <button @click="currentTab = 'residents'" :class="currentTab === 'residents' ? 'bg-white dark:bg-[#1C1C1E] text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="flex-1 min-w-[120px] py-1.5 text-[13px] font-semibold rounded-[10px] transition-all flex items-center justify-center gap-2">
                        <i class="pi pi-users"></i> Residentes
                    </button>
                    <button @click="currentTab = 'resources'" :class="currentTab === 'resources' ? 'bg-white dark:bg-[#1C1C1E] text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="flex-1 min-w-[140px] py-1.5 text-[13px] font-semibold rounded-[10px] transition-all flex items-center justify-center gap-2">
                        <i class="pi pi-car"></i> Recursos
                    </button>
                    <button @click="currentTab = 'finance'" :class="currentTab === 'finance' ? 'bg-white dark:bg-[#1C1C1E] text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="flex-1 min-w-[120px] py-1.5 text-[13px] font-semibold rounded-[10px] transition-all flex items-center justify-center gap-2">
                        <i class="pi pi-wallet"></i> Finanzas
                    </button>
                    <button @click="currentTab = 'visits'" :class="currentTab === 'visits' ? 'bg-white dark:bg-[#1C1C1E] text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="flex-1 min-w-[120px] py-1.5 text-[13px] font-semibold rounded-[10px] transition-all flex items-center justify-center gap-2">
                        <i class="pi pi-id-card"></i> Visitas
                    </button>
                </div>

                <!-- ===================== CONTENEDOR DE PESTAÑAS ===================== -->
                <InfoTab v-if="currentTab === 'info'" :unit="unit" />
                <ResidentsTab v-if="currentTab === 'residents'" :unit="unit" />
                <ResourcesTab v-if="currentTab === 'resources'" :unit="unit" />
                <FinanceTab v-if="currentTab === 'finance'" :unit="unit" />
                <VisitsTab v-if="currentTab === 'visits'" :unit="unit" />

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
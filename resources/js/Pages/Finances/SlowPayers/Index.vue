<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import debounce from 'lodash/debounce';

const props = defineProps({
    units: Object,
    filters: Object,
});

const confirm = useConfirm();
const toast = useToast();
const search = ref(props.filters.search || '');

// Búsqueda en tiempo real
watch(search, debounce((value) => {
    router.get(route('admin.private-units.slow-payers'), { search: value }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}, 500));

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value || 0);
};

// Suma de la deuda de la página actual para mostrar un KPI rápido
const currentPageDebt = computed(() => {
    return props.units.data.reduce((total, unit) => total + parseFloat(unit.total_debt), 0);
});

// Acciones rápidas contra morosos
const takeAction = (unit, action) => {
    if (action === 'remind') {
        confirm.require({
            message: `¿Deseas enviar una notificación de cobro con el estado de cuenta actualizado a ${unit.owner_name}?`,
            header: 'Enviar Recordatorio de Pago',
            icon: 'pi pi-send',
            acceptLabel: 'Enviar ahora',
            rejectLabel: 'Cancelar',
            acceptClass: 'p-button-danger',
            accept: () => {
                // Aquí iría tu petición real al backend para enviar el correo/notificación
                toast.add({ severity: 'success', summary: 'Recordatorio Enviado', detail: `Se notificó a ${unit.owner_name} sobre su adeudo.`, life: 3000 });
            }
        });
    } else if (action === 'block') {
        confirm.require({
            message: `¿Estás seguro de que deseas bloquear el acceso automático a la propiedad ${unit.unit_street} ${unit.exterior_number}? Los residentes tendrán que registrarse manualmente en caseta.`,
            header: 'Restringir Acceso',
            icon: 'pi pi-lock',
            acceptLabel: 'Sí, bloquear acceso',
            rejectLabel: 'Cancelar',
            acceptClass: 'p-button-danger',
            accept: () => {
                // Aquí podrías llamar al endpoint de toggleStatus o uno específico de bloqueo
                toast.add({ severity: 'warn', summary: 'Acceso Restringido', detail: 'Se ha bloqueado el acceso a la unidad.', life: 3000 });
            }
        });
    }
};
</script>

<template>
    <AppLayout title="Control de Morosidad">
        <ConfirmDialog></ConfirmDialog>

        <div class="min-h-screen text-gray-800 dark:text-zinc-100 p-4 sm:p-8 transition-colors duration-300 font-sans tracking-tight">
            
            <div class="max-w-7xl mx-auto">
                
                <!-- Encabezado y Buscador -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center">
                                <i class="pi pi-exclamation-triangle text-lg"></i>
                            </div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                                Control de Morosidad
                            </h1>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 ml-13">
                            Propiedades con adeudos pendientes. Ordenadas por mayor deuda.
                        </p>
                    </div>
                    
                    <div class="relative w-full md:w-80">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="pi pi-search text-gray-400 text-sm"></i>
                        </span>
                        <input 
                            v-model="search" 
                            type="text" 
                            placeholder="Buscar propiedad o titular..." 
                            class="pl-9 pr-4 py-2.5 w-full rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#1C1C1E] text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 shadow-sm transition-all placeholder-gray-400"
                        >
                    </div>
                </div>

                <!-- Estado Vacío (¡No hay morosos!) -->
                <div v-if="units.data.length === 0" class="bg-white dark:bg-[#1C1C1E] rounded-[32px] shadow-sm p-16 text-center border border-black/5 dark:border-white/5 flex flex-col items-center justify-center">
                    <div class="w-24 h-24 bg-green-50 dark:bg-green-500/10 rounded-full flex items-center justify-center mb-6">
                        <i class="pi pi-check-circle text-5xl text-green-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">¡Excelentes noticias!</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                        No se encontraron propiedades con morosidad. Todas las unidades están al corriente o no coinciden con tu búsqueda.
                    </p>
                    <Link :href="route('admin.private-units.index')" class="mt-8 px-6 py-2.5 bg-gray-100 dark:bg-[#2C2C2E] hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-900 dark:text-white rounded-full font-semibold transition">
                        Volver a todas las propiedades
                    </Link>
                </div>

                <!-- Lista de Morosos -->
                <div v-else class="space-y-6">
                    
                    <!-- Tarjeta de Resumen (Página Actual) -->
                    <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-[24px] p-6 text-white shadow-lg shadow-red-600/20 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                                <i class="pi pi-chart-line text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-red-100 text-sm font-medium uppercase tracking-wider">Adeudo total en esta página</p>
                                <h2 class="text-3xl font-bold tracking-tight">{{ formatCurrency(currentPageDebt) }}</h2>
                            </div>
                        </div>
                        <button class="px-5 py-2.5 bg-white text-red-700 hover:bg-red-50 rounded-xl font-bold text-sm transition shadow-sm flex items-center gap-2">
                            <i class="pi pi-envelope"></i> Notificar a todos
                        </button>
                    </div>

                    <!-- Cuadrícula de Deudores -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                        
                        <div 
                            v-for="unit in units.data" 
                            :key="unit.id"
                            class="bg-white dark:bg-[#1C1C1E] rounded-[24px] border border-black/5 dark:border-white/5 shadow-sm overflow-hidden flex flex-col transition-all hover:shadow-md hover:border-red-200 dark:hover:border-red-900/50 group"
                        >
                            <!-- Header Tarjeta -->
                            <div class="p-5 border-b border-gray-100 dark:border-zinc-800 flex justify-between items-start">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-500/10 text-red-500 flex items-center justify-center flex-shrink-0">
                                        <i class="pi pi-home text-lg"></i>
                                    </div>
                                    <div>
                                        <Link :href="route('admin.private-units.show', unit.id)" class="text-lg font-bold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            {{ unit.unit_street }} {{ unit.exterior_number }}
                                        </Link>
                                        <p class="text-xs text-gray-500 mt-0.5 font-medium">Lote: {{ unit.lot_number }} <span v-if="unit.int_number">| Int: {{ unit.int_number }}</span></p>
                                    </div>
                                </div>
                                <div class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)] mt-1"></div>
                            </div>

                            <!-- Info Financiera -->
                            <div class="p-5 flex-1 flex flex-col justify-center bg-gray-50/50 dark:bg-black/20">
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-1 text-center">Deuda Acumulada</p>
                                <p class="text-3xl font-extrabold text-red-600 dark:text-red-500 text-center tracking-tight">
                                    {{ formatCurrency(unit.total_debt) }}
                                </p>
                                <div class="mt-4 bg-white dark:bg-[#2C2C2E] rounded-xl p-3 border border-black/5 dark:border-white/5 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-zinc-700 flex items-center justify-center text-gray-500">
                                        <i class="pi pi-user text-sm"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Titular Responsable</p>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" :class="{'italic text-gray-400': unit.owner_name === 'Sin asignar'}">
                                            {{ unit.owner_name }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Acciones -->
                            <div class="p-4 border-t border-gray-100 dark:border-zinc-800 grid grid-cols-3 gap-2">
                                <button @click="takeAction(unit, 'remind')" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition group/btn">
                                    <i class="pi pi-send text-lg mb-1 group-hover/btn:-translate-y-0.5 transition-transform"></i>
                                    <span class="text-[10px] font-bold">Recordar</span>
                                </button>
                                
                                <button @click="takeAction(unit, 'block')" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl text-gray-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition group/btn relative">
                                    <i class="pi pi-lock text-lg mb-1 group-hover/btn:-translate-y-0.5 transition-transform"></i>
                                    <span class="text-[10px] font-bold">Restringir</span>
                                    <span v-if="unit.access_block" class="absolute top-1 right-2 w-2 h-2 rounded-full bg-amber-500 border-2 border-white dark:border-[#1C1C1E]"></span>
                                </button>
                                
                                <Link :href="route('admin.private-units.show', unit.id)" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-zinc-700 dark:hover:text-white transition group/btn">
                                    <i class="pi pi-folder-open text-lg mb-1 group-hover/btn:-translate-y-0.5 transition-transform"></i>
                                    <span class="text-[10px] font-bold">Expediente</span>
                                </Link>
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
                                        ? 'bg-red-600 text-white shadow-md shadow-red-600/20' 
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#2C2C2E]'"
                                    v-html="link.label" 
                                />
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
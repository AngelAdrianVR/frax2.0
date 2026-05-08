<script setup>
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

// Recibimos las props correctas desde el PrivateUnitController
const props = defineProps({
    slowPayers: Object,
    filters: Object
});

// Estado reactivo para el buscador
const search = ref(props.filters?.search || '');

// Buscador con delay (debounce) para no saturar el servidor en cada tecla
let searchTimeout = null;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('slowPayers.index'), { search: value }, { 
            preserveState: true, 
            replace: true 
        });
    }, 300);
});

// Formateador de moneda
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-MX', { 
        style: 'currency', 
        currency: 'MXN' 
    }).format(amount);
};

// Formateador de WhatsApp
const getWhatsappLink = (phone) => {
    if (!phone) return '#';
    const cleanPhone = phone.toString().replace(/[^0-9]/g, '');
    return `https://wa.me/${cleanPhone}`;
};
</script>

<template>
    <AppLayout title="Reporte de Morosidad">
        <!-- Fondo estilo iOS (#F2F2F7) -->
        <div class="min-h-screen bg-[#F2F2F7] dark:bg-zinc-900 p-4 md:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">
                
                <!-- Encabezado y Buscador -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Morosidad</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Gestión integral de unidades con adeudos pendientes.</p>
                    </div>
                    
                    <!-- Buscador -->
                    <div class="w-full md:w-80">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-400">
                                <i class="pi pi-search"></i>
                            </span>
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Buscar por unidad o vecino..."
                                class="w-full pl-10 pr-4 py-2 bg-white dark:bg-zinc-800 border-none rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 text-sm dark:text-white transition-all" 
                            />
                        </div>
                    </div>
                </div>

                <!-- Estado Vacío (Sin Morosos) -->
                <div v-if="slowPayers.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-3xl p-12 text-center shadow-sm">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/30 text-green-500 mb-4">
                        <i class="pi pi-check-circle text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">¡Excelentes noticias!</h3>
                    <p class="text-zinc-500 dark:text-zinc-400">No se encontraron unidades con adeudos que coincidan con tu búsqueda.</p>
                </div>

                <!-- VISTA ESCRITORIO (Tabla elegante estilo Apple) -->
                <div v-else class="hidden md:block bg-white dark:bg-zinc-800 shadow-sm rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-700/50">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50/50 dark:bg-zinc-800/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Unidad Privada</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Propietario / Residente</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Estatus</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Deuda Total</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                            <tr v-for="unit in slowPayers.data" :key="unit.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/20 transition-colors">
                                <!-- Unidad -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mr-3">
                                            <i class="pi pi-home"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ unit.full_address }}</div>
                                            <div v-if="unit.access_block" class="text-xs text-red-500 mt-0.5 flex items-center gap-1">
                                                <i class="pi pi-lock text-[10px]"></i> Acceso Bloqueado
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Propietario y Contacto -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ unit.owner_name }}</div>
                                    <div class="flex items-center gap-3 mt-1">
                                        <a v-if="unit.owner_phone" :href="getWhatsappLink(unit.owner_phone)" target="_blank" class="text-zinc-400 hover:text-green-500 transition-colors" title="Enviar WhatsApp">
                                            <i class="pi pi-whatsapp"></i>
                                        </a>
                                        <a v-if="unit.owner_email" :href="`mailto:${unit.owner_email}`" class="text-zinc-400 hover:text-indigo-500 transition-colors" title="Enviar Correo">
                                            <i class="pi pi-envelope"></i>
                                        </a>
                                        <span v-if="!unit.owner_phone && !unit.owner_email" class="text-xs text-zinc-400">Sin datos de contacto</span>
                                    </div>
                                </td>

                                <!-- Estatus (Moroso vs Atrasado) -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="unit.is_slow_payer" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        Moroso Activo
                                    </span>
                                    <span v-else class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        Con Atraso
                                    </span>
                                </td>

                                <!-- Deuda -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-red-600 dark:text-red-400">
                                    {{ formatCurrency(unit.total_debt) }}
                                </td>

                                <!-- Acciones -->
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <Link :href="route('admin.private-units.show', unit.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-indigo-100 hover:text-indigo-600 dark:hover:bg-indigo-900/50 dark:hover:text-indigo-400 transition-colors" title="Ver Expediente de la Propiedad">
                                        <i class="pi pi-angle-right"></i>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- VISTA MÓVIL (Tarjetas limpias) -->
                <div v-if="slowPayers.data.length > 0" class="md:hidden space-y-4">
                    <div v-for="unit in slowPayers.data" :key="unit.id" class="bg-white dark:bg-zinc-800 shadow-sm rounded-2xl p-5 border border-zinc-100 dark:border-zinc-700/50">
                        
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ unit.full_address }}</h3>
                                <div v-if="unit.access_block" class="text-xs font-medium text-red-500 mt-1 flex items-center gap-1">
                                    <i class="pi pi-lock text-[10px]"></i> Acceso Caseta Bloqueado
                                </div>
                            </div>
                            <span v-if="unit.is_slow_payer" class="px-2 py-1 text-[10px] font-bold rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 uppercase tracking-wider">
                                Moroso
                            </span>
                        </div>

                        <div class="bg-zinc-50 dark:bg-zinc-700/30 rounded-xl p-3 mb-4 flex justify-between items-center">
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">Deuda Total</span>
                            <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ formatCurrency(unit.total_debt) }}</span>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-700/50">
                            <div>
                                <p class="text-xs text-zinc-400 uppercase tracking-wider font-bold mb-0.5">Propietario</p>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ unit.owner_name }}</p>
                            </div>
                            <div class="flex gap-2">
                                <a v-if="unit.owner_phone" :href="getWhatsappLink(unit.owner_phone)" target="_blank" class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400">
                                    <i class="pi pi-whatsapp"></i>
                                </a>
                                <Link :href="route('admin.private-units.show', unit.id)" class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                    <i class="pi pi-home"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="slowPayers.links.length > 3" class="mt-8 flex justify-center">
                    <div class="flex flex-wrap gap-1 bg-white dark:bg-zinc-800 p-1 rounded-xl shadow-sm border border-zinc-100 dark:border-zinc-700/50">
                        <Link v-for="(link, k) in slowPayers.links" :key="k" 
                              :href="link.url" v-html="link.label"
                              class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                              :class="link.active 
                                ? 'bg-indigo-600 text-white shadow-md' 
                                : 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700'" />
                    </div>
                </div>
                
            </div>
        </div>
    </AppLayout>
</template>
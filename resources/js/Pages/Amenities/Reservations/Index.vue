<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

// Importamos los modales
import ReservationModal from '@/Components/ReservationModal.vue';
import ReservationDetailsModal from '@/Pages/Amenities/Reservations/ReservationDetailsModal.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    reservations: Array,
    amenities: Array, 
});

const confirm = useConfirm();
const toast = useToast();

// Estados para los Modales
const showPickAmenityModal = ref(false);
const showCreateModal = ref(false);
const showDetailsModal = ref(false);

const selectedAmenityForCreate = ref(null);
const selectedReservation = ref(null);

// ==========================================
// LÓGICA DE VISTAS (Lista vs Calendario)
// ==========================================
const viewMode = ref('list'); // 'list' o 'calendar'

const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());
const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

// Agrupar reservaciones por fecha (YYYY-MM-DD)
const reservationsMap = computed(() => {
    const map = {};
    if (!props.reservations) return map;
    
    props.reservations.forEach(res => {
        // Extraemos solo la fecha de 'YYYY-MM-DD HH:mm'
        const datePart = res.start_date.split(' ')[0];
        if (!map[datePart]) map[datePart] = [];
        map[datePart].push(res);
    });
    return map;
});

// Generar los días del mes actual para la cuadrícula
const calendarDays = computed(() => {
    const days = [];
    const firstDay = new Date(currentYear.value, currentMonth.value, 1).getDay();
    const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();

    // Espacios vacíos al inicio del mes
    for (let i = 0; i < firstDay; i++) {
        days.push({ empty: true });
    }

    // Días reales
    for (let i = 1; i <= daysInMonth; i++) {
        const m = String(currentMonth.value + 1).padStart(2, '0');
        const d = String(i).padStart(2, '0');
        const dateStr = `${currentYear.value}-${m}-${d}`;

        days.push({
            empty: false,
            date: dateStr,
            dayNumber: i,
            reservations: reservationsMap.value[dateStr] || []
        });
    }
    return days;
});

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

const isToday = (dateStr) => {
    const today = new Date();
    const m = String(today.getMonth() + 1).padStart(2, '0');
    const d = String(today.getDate()).padStart(2, '0');
    const todayStr = `${today.getFullYear()}-${m}-${d}`;
    return dateStr === todayStr;
};

const formatTimeOnly = (dateTimeStr) => {
    const parts = dateTimeStr.split(' ');
    return parts.length > 1 ? parts[1] : dateTimeStr;
};

// ==========================================
// FUNCIONES GENERALES
// ==========================================
const startReservation = (amenity) => {
    selectedAmenityForCreate.value = amenity;
    showPickAmenityModal.value = false;
    showCreateModal.value = true;
};

const openDetails = (reservation) => {
    selectedReservation.value = reservation;
    showDetailsModal.value = true;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
};

const getStatusColor = (status) => {
    const colors = {
        'Pendiente': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800/50',
        'Aprobada': 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400 border border-blue-200 dark:border-blue-800/50',
        'Completada': 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400 border border-green-200 dark:border-green-800/50',
        'Rechazada': 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-400 border border-red-200 dark:border-red-800/50',
        'Cancelada': 'bg-zinc-200 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700',
    };
    return colors[status] || 'bg-zinc-100 text-zinc-800 border border-zinc-200';
};
</script>

<template>
    <AppLayout title="Catálogo de Reservaciones">
        <Toast />
        <ConfirmDialog />

        <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-8 transition-colors duration-300">
            <Head title="Reservaciones" />

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Encabezado y Controles -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Reservaciones</h2>
                        <Link :href="route('amenities.index')" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition mt-1 block">
                            &larr; Volver a Amenidades
                        </Link>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                        <!-- Toggle Lista/Calendario (Estilo iOS) -->
                        <div class="bg-zinc-200/60 dark:bg-zinc-800 p-1 rounded-xl inline-flex w-full sm:w-auto">
                            <button @click="viewMode = 'list'" :class="['flex-1 sm:flex-none px-6 py-2 text-sm font-bold rounded-lg transition-all duration-200', viewMode === 'list' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300']">
                                Lista
                            </button>
                            <button @click="viewMode = 'calendar'" :class="['flex-1 sm:flex-none px-6 py-2 text-sm font-bold rounded-lg transition-all duration-200', viewMode === 'calendar' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300']">
                                Calendario
                            </button>
                        </div>

                        <button @click="showPickAmenityModal = true" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5 whitespace-nowrap">
                            + Nueva Reservación
                        </button>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- VISTA DE LISTA (Tabla Clásica) -->
                <!-- ============================================== -->
                <div v-if="viewMode === 'list'" class="bg-white dark:bg-zinc-800 shadow-sm rounded-3xl overflow-hidden border border-zinc-200 dark:border-zinc-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-zinc-100 dark:bg-zinc-700/50 text-zinc-600 dark:text-zinc-300 text-sm uppercase tracking-wider">
                                    <th class="p-4 font-semibold">Amenidad</th>
                                    <th class="p-4 font-semibold">Residente / Unidad</th>
                                    <th class="p-4 font-semibold">Inicio</th>
                                    <th class="p-4 font-semibold">Costo</th>
                                    <th class="p-4 font-semibold">Estado</th>
                                    <th class="p-4 font-semibold text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 text-sm text-zinc-800 dark:text-zinc-200">
                                <tr v-if="reservations.length === 0">
                                    <td colspan="6" class="p-12 text-center text-zinc-500 dark:text-zinc-400">
                                        <span class="text-4xl block mb-2">📅</span>
                                        No hay reservaciones registradas aún.
                                    </td>
                                </tr>
                                <tr v-for="res in reservations" :key="res.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition cursor-pointer" @click="openDetails(res)">
                                    <td class="p-4 font-bold text-zinc-900 dark:text-white">{{ res.amenity_name }}</td>
                                    <td class="p-4">
                                        {{ res.unit_name }} <br> 
                                        <span class="text-xs text-zinc-500 font-medium">{{ res.attendees }} asistentes</span>
                                    </td>
                                    <td class="p-4 text-zinc-600 dark:text-zinc-400">
                                        {{ res.start_date }}
                                        <div class="text-xs text-zinc-400 mt-0.5">al {{ res.end_date }}</div>
                                    </td>
                                    <td class="p-4 font-bold text-blue-600 dark:text-blue-400">{{ formatCurrency(res.total_cost) }}</td>
                                    <td class="p-4">
                                        <span :class="['px-3 py-1 rounded-full text-xs font-bold inline-block', getStatusColor(res.status)]">
                                            {{ res.status }}
                                        </span>
                                    </td>
                                    <td class="p-4 flex gap-2 justify-center">
                                        <button @click.stop="openDetails(res)" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-semibold transition shadow-sm text-xs">
                                            Detalles
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- VISTA DE CALENDARIO -->
                <!-- ============================================== -->
                <div v-else-if="viewMode === 'calendar'" class="bg-white dark:bg-zinc-800 shadow-sm rounded-3xl overflow-hidden border border-zinc-200 dark:border-zinc-700 p-6">
                    
                    <!-- Controles del Mes -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-zinc-900 dark:text-white capitalize">{{ monthNames[currentMonth] }} {{ currentYear }}</h3>
                        <div class="flex gap-2">
                            <button @click="prevMonth" class="p-2.5 bg-zinc-100 dark:bg-zinc-700 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-600 text-zinc-600 dark:text-zinc-300 transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button @click="nextMonth" class="p-2.5 bg-zinc-100 dark:bg-zinc-700 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-600 text-zinc-600 dark:text-zinc-300 transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Cuadrícula del Calendario -->
                    <div class="grid grid-cols-7 gap-px bg-zinc-200 dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-2xl overflow-hidden">
                        
                        <!-- Días de la semana -->
                        <div v-for="d in ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb']" :key="d" class="bg-zinc-50 dark:bg-zinc-800/80 p-3 text-center text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ d }}
                        </div>

                        <!-- Celdas de los Días -->
                        <div v-for="(day, idx) in calendarDays" :key="idx" class="bg-white dark:bg-zinc-800 min-h-[140px] p-2 transition hover:bg-zinc-50 dark:hover:bg-zinc-700/50 group">
                            <div v-if="!day.empty" class="h-full flex flex-col">
                                <!-- Número del Día -->
                                <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2 w-7 h-7 flex items-center justify-center rounded-full self-end" :class="isToday(day.date) ? 'bg-blue-600 text-white shadow-md' : 'group-hover:bg-zinc-100 dark:group-hover:bg-zinc-700'">
                                    {{ day.dayNumber }}
                                </span>
                                
                                <!-- Lista de Reservaciones en ese día -->
                                <div class="flex-1 overflow-y-auto space-y-1.5 custom-scrollbar pr-1">
                                    <div v-for="res in day.reservations" :key="res.id"
                                         @click.stop="openDetails(res)"
                                         class="text-left text-[11px] leading-tight p-2 rounded-lg cursor-pointer transition shadow-sm hover:shadow-md transform hover:-translate-y-px"
                                         :class="getStatusColor(res.status)">
                                        <div class="font-bold mb-0.5">{{ formatTimeOnly(res.start_date) }}</div>
                                        <div class="truncate font-medium">{{ res.amenity_name }}</div>
                                        <div class="truncate opacity-75 mt-0.5 text-[9px]">{{ res.unit_name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ============================================== -->
            <!-- MODAL: SELECCIONAR AMENIDAD (Paso 1 de Crear) -->
            <!-- ============================================== -->
            <Modal :show="showPickAmenityModal" @close="showPickAmenityModal = false" maxWidth="sm">
                <div class="p-6 bg-white dark:bg-zinc-900 rounded-2xl">
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">¿Qué deseas reservar?</h3>
                    <p class="text-sm text-zinc-500 mb-6">Selecciona la amenidad de la lista.</p>
                    
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                        <button 
                            v-for="am in amenities" 
                            :key="am.id" 
                            @click="startReservation(am)" 
                            class="w-full text-left p-4 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-blue-50 dark:hover:bg-zinc-800 transition flex justify-between items-center group shadow-sm"
                        >
                            <div>
                                <span class="font-bold text-zinc-800 dark:text-zinc-200 group-hover:text-blue-600 block">{{ am.name }}</span>
                                <span class="text-xs text-zinc-500 font-medium mt-1">{{ formatCurrency(am.reservation_cost) }}</span>
                            </div>
                            <span class="text-zinc-400 group-hover:text-blue-500 font-bold">→</span>
                        </button>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showPickAmenityModal = false">Cancelar</SecondaryButton>
                    </div>
                </div>
            </Modal>

            <!-- ============================================== -->
            <!-- MODAL: CALENDARIO DE RESERVACIÓN (Paso 2) -->
            <!-- ============================================== -->
            <ReservationModal 
                :show="showCreateModal" 
                :amenity="selectedAmenityForCreate" 
                @close="showCreateModal = false" 
            />

            <!-- ============================================== -->
            <!-- MODAL: DETALLES Y EDICIÓN -->
            <!-- ============================================== -->
            <ReservationDetailsModal 
                :show="showDetailsModal" 
                :reservation="selectedReservation"
                :isAdmin="true"
                @close="showDetailsModal = false"
            />

        </div>
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
</style>
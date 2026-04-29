<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// PrimeVue Imports (Asegúrate de tenerlos instalados o configurados globalmente)
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import InputSwitch from 'primevue/inputswitch';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";

const props = defineProps({
    amenities: Array,
    isAdmin: Boolean,
    userRole: String,
});

const confirm = useConfirm();
const toast = useToast();

// Estados
const showRulesModal = ref(false);
const showReserveModal = ref(false);
const selectedAmenity = ref(null);
const calendarData = ref([]);
const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());
const loadingCalendar = ref(false);
const selectedDate = ref(null);

// Formulario de Reserva
const reserveForm = useForm({
    date: '',
    start_time: '',
    end_time: '',
    attendees: 1,
});

// --- Métodos Admin ---

const confirmDelete = (event, amenity) => {
    confirm.require({
        target: event.currentTarget,
        message: '¿Estás seguro de eliminar esta amenidad?',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('amenities.destroy', amenity.id), {
                onSuccess: () => toast.add({ severity: 'success', summary: 'Eliminado', detail: 'Amenidad eliminada', life: 3000 })
            });
        }
    });
};

const toggleStatus = (amenity) => {
    router.patch(route('amenities.toggle', amenity.id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.add({ severity: 'info', summary: 'Estado Actualizado', life: 2000 })
    });
};

// --- Métodos Residente (Calendario & Reserva) ---

const openReserveModal = (amenity) => {
    selectedAmenity.value = amenity;
    reserveForm.reset();
    selectedDate.value = null;
    currentMonth.value = new Date().getMonth();
    currentYear.value = new Date().getFullYear();
    fetchAvailability(amenity.id);
    showReserveModal.value = true;
};

const openRulesModal = (amenity) => {
    selectedAmenity.value = amenity;
    showRulesModal.value = true;
};

// Fetch API de disponibilidad
const fetchAvailability = async (amenityId) => {
    loadingCalendar.value = true;
    try {
        // Asumiendo que definiste la ruta 'api.amenities.availability' o similar en web.php o api.php
        // Si no, usa una ruta web directa
        const response = await axios.get(`/amenities/${amenityId}/availability`, {
            params: { month: currentMonth.value + 1, year: currentYear.value }
        });
        calendarData.value = response.data;
    } catch (error) {
        console.error("Error cargando calendario", error);
    } finally {
        loadingCalendar.value = false;
    }
};

const selectDate = (dayData) => {
    if (dayData.status === 'closed' || dayData.status === 'full') return;
    
    selectedDate.value = dayData;
    reserveForm.date = dayData.date;
    
    // Sugerir horario default basado en apertura
    // Esto podría venir del backend en dayData
};

const changeMonth = (delta) => {
    let newMonth = currentMonth.value + delta;
    if (newMonth > 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else if (newMonth < 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value = newMonth;
    }
    fetchAvailability(selectedAmenity.value.id);
};

const submitReservation = () => {
    reserveForm.post(route('reservations.store', {amenityId: selectedAmenity.value.id}), {
        onSuccess: () => {
            showReserveModal.value = false;
            toast.add({ severity: 'success', summary: 'Reserva Creada', detail: 'Tu solicitud ha sido enviada.' });
        }
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
};

// Nombres de meses y días
const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
const dayHeaders = ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"];

</script>

<template>
    <AppLayout title="Amenidades">
        <!-- Toast y ConfirmDialog globales -->
        <Toast />
        <ConfirmDialog />

        <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 transition-colors duration-300">
            <Head title="Amenidades" />

            <header class="sticky top-0 z-10 bg-white/80 dark:bg-zinc-900 backdrop-blur-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Amenidades</h2>
                    <div v-if="isAdmin">
                        <Link :href="route('amenities.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-full font-semibold text-xs text-white uppercase hover:bg-blue-500 shadow-lg transition">
                            + Nueva Amenidad
                        </Link>
                    </div>
                </div>
            </header>

            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Grid de Amenidades -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="amenity in amenities" :key="amenity.id" class="group relative bg-white dark:bg-zinc-800 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col h-full border border-zinc-100 dark:border-zinc-700">
                        
                        <!-- Imagen -->
                        <div class="h-56 overflow-hidden relative">
                            <img :src="amenity.photo_url || `https://ui-avatars.com/api/?name=${amenity.name}`" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                            <!-- Badge Costo -->
                            <div class="absolute top-4 right-4 bg-white/90 dark:bg-black/80 backdrop-blur text-zinc-900 dark:text-white px-3 py-1 rounded-full text-sm font-bold shadow-sm">
                                {{ formatCurrency(amenity.reservation_cost) }}
                            </div>
                            <!-- Switch Admin Activo/Inactivo -->
                            <div v-if="isAdmin" class="absolute bottom-4 right-4 bg-white/90 p-1 rounded-full shadow-lg flex items-center gap-2 px-3">
                                <span class="text-xs font-bold text-zinc-600">{{ amenity.is_active ? 'Activa' : 'Inactiva' }}</span>
                                <InputSwitch v-model="amenity.is_active" @input="toggleStatus(amenity)" />
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">{{ amenity.name }}</h3>
                            </div>
                            <p class="text-zinc-500 dark:text-zinc-400 text-sm line-clamp-3 mb-4 flex-grow">{{ amenity.description }}</p>
                            
                            <!-- Botones Residente -->
                            <div v-if="!isAdmin" class="flex flex-col gap-2 mt-auto">
                                <button @click="openRulesModal(amenity)" class="text-blue-600 text-sm hover:underline font-medium text-left">
                                    📜 Ver Reglamento
                                </button>
                                <button @click="openReserveModal(amenity)" class="w-full flex justify-center items-center px-4 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-sm font-bold shadow-lg transition">
                                    Reservar Ahora
                                </button>
                            </div>

                            <!-- Botones Admin -->
                            <div v-else class="flex gap-2 mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-700">
                                <!-- Como Create.vue es una vista nueva, Edit también debería serlo. Por ahora mantenemos el link -->
                                <button @click="$inertia.visit(route('amenities.edit', amenity.id))" class="flex-1 px-4 py-2 bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 rounded-xl text-sm font-semibold">Editar</button>
                                <button @click="confirmDelete($event, amenity)" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-sm font-semibold hover:bg-red-100">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- ======================= -->
            <!-- MODAL REGLAS (Residente) -->
            <!-- ======================= -->
            <Modal :show="showRulesModal" @close="showRulesModal = false">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4">Reglamento de {{ selectedAmenity?.name }}</h3>
                    <ul class="list-disc pl-5 space-y-2 text-zinc-600 dark:text-zinc-300" v-if="selectedAmenity?.rules && selectedAmenity.rules.length">
                        <li v-for="(rule, idx) in selectedAmenity.rules" :key="idx">{{ rule }}</li>
                    </ul>
                    <p v-else class="text-zinc-500 italic">No hay reglas específicas registradas.</p>
                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showRulesModal = false">Cerrar</SecondaryButton>
                    </div>
                </div>
            </Modal>

            <!-- ======================= -->
            <!-- MODAL RESERVA ROBUSTO -->
            <!-- ======================= -->
            <Modal :show="showReserveModal" @close="showReserveModal = false" maxWidth="4xl">
                <div class="p-6 dark:text-white flex flex-col md:flex-row gap-6 h-[600px]" v-if="selectedAmenity">
                    
                    <!-- Panel Izquierdo: Calendario -->
                    <div class="w-full md:w-1/2 flex flex-col border-r border-zinc-200 dark:border-zinc-700 pr-6">
                        <div class="flex justify-between items-center mb-4">
                            <button @click="changeMonth(-1)" class="p-2 hover:bg-zinc-100 rounded-full">◀</button>
                            <h3 class="font-bold text-lg">{{ monthNames[currentMonth] }} {{ currentYear }}</h3>
                            <button @click="changeMonth(1)" class="p-2 hover:bg-zinc-100 rounded-full">▶</button>
                        </div>

                        <!-- Grid Calendario -->
                        <div class="grid grid-cols-7 gap-2 text-center text-sm mb-2 font-medium text-zinc-400">
                            <span v-for="d in dayHeaders" :key="d">{{ d }}</span>
                        </div>
                        <div class="grid grid-cols-7 gap-2 flex-grow overflow-y-auto" v-if="!loadingCalendar">
                            <!-- Placeholder días vacíos al inicio -->
                            <div v-for="n in new Date(currentYear, currentMonth, 1).getDay()" :key="'empty-'+n"></div>
                            
                            <!-- Días Reales -->
                            <div v-for="day in calendarData" :key="day.date" 
                                @click="selectDate(day)"
                                :class="[
                                    'h-14 flex flex-col items-center justify-center rounded-lg cursor-pointer transition border relative',
                                    day.status === 'closed' ? 'bg-zinc-100 text-zinc-300 cursor-not-allowed' :
                                    day.status === 'full' ? 'bg-red-50 border-red-200 text-red-400 cursor-not-allowed' :
                                    selectedDate?.date === day.date ? 'ring-2 ring-blue-500 bg-blue-50' : 'hover:bg-zinc-50 bg-white border-zinc-200'
                                ]"
                            >
                                <span class="font-bold">{{ day.day }}</span>
                                <!-- Indicador Visual -->
                                <div class="flex gap-1 mt-1">
                                    <span v-if="day.status === 'high'" class="w-2 h-2 rounded-full bg-green-500" title="Alta disponibilidad"></span>
                                    <span v-if="day.status === 'low'" class="w-2 h-2 rounded-full bg-orange-400" title="Poca disponibilidad"></span>
                                    <span v-if="day.status === 'full'" class="w-2 h-2 rounded-full bg-red-500" title="Lleno"></span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex justify-center items-center h-full">
                            <span class="animate-spin text-2xl">⏳</span>
                        </div>
                        
                        <div class="mt-4 flex gap-4 text-xs text-zinc-500 justify-center">
                            <div class="flex items-center"><span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span> Disponible</div>
                            <div class="flex items-center"><span class="w-2 h-2 bg-orange-400 rounded-full mr-1"></span> Pocos cupos</div>
                            <div class="flex items-center"><span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span> Lleno</div>
                        </div>
                    </div>

                    <!-- Panel Derecho: Horarios y Formulario -->
                    <div class="w-full md:w-1/2 flex flex-col relative">
                        <h3 class="text-xl font-bold mb-1">{{ selectedAmenity.name }}</h3>
                        <p class="text-sm text-zinc-500 mb-6">Completa tu reservación</p>

                        <div v-if="selectedDate" class="animate-fade-in-up space-y-5">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-800 text-center">
                                <p class="text-blue-800 dark:text-blue-300 font-semibold">
                                    Seleccionado: {{ selectedDate.date }}
                                </p>
                            </div>

                            <!-- Mostrar horas ocupadas (informativo) -->
                            <div v-if="selectedDate.busy_slots.length > 0" class="text-xs text-zinc-500 bg-zinc-50 p-2 rounded">
                                <p class="font-bold mb-1">Horarios Ocupados:</p>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="slot in selectedDate.busy_slots" :key="slot.start" class="bg-red-100 text-red-600 px-2 py-0.5 rounded">
                                        {{ slot.start }} - {{ slot.end }}
                                    </span>
                                </div>
                            </div>

                            <form @submit.prevent="submitReservation" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium">Inicio</label>
                                        <input type="time" v-model="reserveForm.start_time" class="w-full rounded-lg border-zinc-300 mt-1" required>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Fin</label>
                                        <input type="time" v-model="reserveForm.end_time" class="w-full rounded-lg border-zinc-300 mt-1" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-sm font-medium">Asistentes (Max: {{ selectedAmenity.capacity }})</label>
                                    <input type="number" v-model="reserveForm.attendees" min="1" :max="selectedAmenity.capacity" class="w-full rounded-lg border-zinc-300 mt-1">
                                </div>

                                <div class="pt-4">
                                    <PrimaryButton :disabled="reserveForm.processing" class="w-full justify-center py-3">
                                        Confirmar ($ {{ selectedAmenity.reservation_cost }})
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>

                        <div v-else class="flex flex-col items-center justify-center h-full text-zinc-400">
                            <span class="text-4xl mb-2">📅</span>
                            <p>Selecciona un día en el calendario</p>
                        </div>
                    </div>
                </div>
            </Modal>
        </div>
    </AppLayout>
</template>
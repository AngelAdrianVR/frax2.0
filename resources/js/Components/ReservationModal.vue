<script setup>
import { ref, watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: Boolean,
    amenity: Object,
});

const emit = defineEmits(['close']);

// Estados del Calendario
const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());
const calendarData = ref([]);
const loadingCalendar = ref(false);
const selectedDate = ref(null);

// Formulario
const reserveForm = useForm({
    date: '',
    start_time: '',
    end_time: '',
    attendees: 1,
});

const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
const dayHeaders = ["D", "L", "M", "M", "J", "V", "S"];

// Calcula los días vacíos al principio del mes para alinear la cuadrícula
const paddingDays = computed(() => {
    return new Date(currentYear.value, currentMonth.value, 1).getDay();
});

const fetchAvailability = async () => {
    if (!props.amenity) return;
    loadingCalendar.value = true;
    try {
        const response = await axios.get(route('api.amenities.availability', props.amenity.id), {
            params: { month: currentMonth.value + 1, year: currentYear.value }
        });
        calendarData.value = response.data;
    } catch (error) {
        // Fallback robusto si la ruta nombrada falla (intenta URL directa)
        try {
            const fallbackResponse = await axios.get(`/amenities/${props.amenity.id}/availability`, {
                params: { month: currentMonth.value + 1, year: currentYear.value }
            });
            calendarData.value = fallbackResponse.data;
        } catch (e) {
            console.error("Error cargando calendario", e);
        }
    } finally {
        loadingCalendar.value = false;
    }
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
    fetchAvailability();
};

const selectDate = (dayData) => {
    if (dayData.status === 'closed' || dayData.status === 'full') return;
    selectedDate.value = dayData;
    reserveForm.date = dayData.date;
    
    // Auto-scroll suave al formulario si en móvil
    setTimeout(() => {
        const formSection = document.getElementById('reservation-form-section');
        if (formSection) formSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
};

const submitReservation = () => {
    reserveForm.post(route('reservations.store', { amenityId: props.amenity.id }), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            // El toast de éxito ya lo maneja IndexAmenity si lo envías desde el controlador
        }
    });
};

const closeModal = () => {
    emit('close');
};

// Reiniciar estado al abrir
watch(() => props.show, (newVal) => {
    if (newVal && props.amenity) {
        const today = new Date();
        currentMonth.value = today.getMonth();
        currentYear.value = today.getFullYear();
        selectedDate.value = null;
        reserveForm.reset();
        fetchAvailability();
    }
});
</script>

<template>
    <Modal :show="show" @close="closeModal" maxWidth="md">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
            
            <!-- Header Estilo iOS -->
            <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md sticky top-0 z-10">
                <button @click="closeModal" class="text-blue-500 font-medium text-base hover:text-blue-600 transition">Cancelar</button>
                <h3 class="font-semibold text-zinc-900 dark:text-white text-base">Reservar {{ amenity?.name }}</h3>
                <!-- Placeholder para balance visual -->
                <div class="w-16"></div> 
            </div>

            <div class="overflow-y-auto p-4 md:p-6 pb-8 custom-scrollbar">
                
                <!-- Navegación del Mes -->
                <div class="flex justify-between items-center mb-6 px-2">
                    <button @click="changeMonth(-1)" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <h2 class="text-lg font-bold text-zinc-800 dark:text-zinc-100">
                        {{ monthNames[currentMonth] }} {{ currentYear }}
                    </h2>
                    <button @click="changeMonth(1)" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </div>

                <!-- Cabeceras de Días -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <div v-for="d in dayHeaders" :key="d" class="text-center text-xs font-semibold text-zinc-400 dark:text-zinc-500">
                        {{ d }}
                    </div>
                </div>

                <!-- Cuadrícula del Calendario (iOS Style) -->
                <div class="relative min-h-[220px]">
                    <div v-if="loadingCalendar" class="absolute inset-0 flex justify-center items-center bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm z-10 rounded-xl">
                        <div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>

                    <div class="grid grid-cols-7 gap-y-2 gap-x-1">
                        <!-- Padding -->
                        <div v-for="n in paddingDays" :key="'pad-'+n" class="h-10"></div>
                        
                        <!-- Días -->
                        <div v-for="day in calendarData" :key="day.date" class="flex justify-center items-center h-10">
                            <button 
                                @click="selectDate(day)"
                                :disabled="day.status === 'closed' || day.status === 'full'"
                                :class="[
                                    'w-9 h-9 flex flex-col justify-center items-center rounded-full text-base font-medium transition-all duration-200 relative',
                                    day.status === 'closed' || day.status === 'full' ? 'text-zinc-300 dark:text-zinc-600 cursor-not-allowed' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200',
                                    selectedDate?.date === day.date ? '!bg-blue-500 !text-white shadow-md transform scale-105' : ''
                                ]"
                            >
                                {{ day.day }}
                                <!-- Puntos indicadores de ocupación estilo iOS -->
                                <div v-if="selectedDate?.date !== day.date && day.status !== 'closed' && day.status !== 'full'" class="absolute bottom-1 flex gap-[2px]">
                                    <span v-if="day.status === 'high'" class="w-1 h-1 rounded-full bg-zinc-300 dark:bg-zinc-600"></span>
                                    <span v-if="day.status === 'low'" class="w-1 h-1 rounded-full bg-orange-400"></span>
                                </div>
                                <div v-if="day.status === 'full' && selectedDate?.date !== day.date" class="absolute bottom-1">
                                    <span class="w-1 h-1 rounded-full bg-red-400 block"></span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Separador -->
                <div v-if="selectedDate" class="w-full h-px bg-zinc-100 dark:bg-zinc-800 my-6"></div>

                <!-- Formulario Desplegable -->
                <div v-if="selectedDate" id="reservation-form-section" class="animate-fade-in-up space-y-5">
                    <h4 class="font-semibold text-zinc-900 dark:text-white text-lg">
                        Horario de Reserva
                    </h4>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium -mt-4 mb-4">
                        {{ new Date(selectedDate.date).toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long' }) }}
                    </p>

                    <!-- Horarios Ocupados -->
                    <div v-if="selectedDate.busy_slots && selectedDate.busy_slots.length > 0" class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-3">
                        <p class="text-xs font-semibold text-orange-800 dark:text-orange-300 mb-2">Horarios ya reservados:</p>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="slot in selectedDate.busy_slots" :key="slot.start" class="text-xs font-medium bg-white dark:bg-orange-950 text-orange-700 dark:text-orange-400 px-2 py-1 rounded-md shadow-sm border border-orange-100 dark:border-orange-900">
                                {{ slot.start }} - {{ slot.end }}
                            </span>
                        </div>
                    </div>

                    <form @submit.prevent="submitReservation" class="space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 p-3 rounded-xl border border-zinc-200 dark:border-zinc-700">
                                <label class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block mb-1">Inicia</label>
                                <input type="time" v-model="reserveForm.start_time" class="w-full bg-transparent border-none p-0 text-zinc-900 dark:text-white focus:ring-0 text-lg font-medium" required>
                            </div>
                            <div class="bg-zinc-50 dark:bg-zinc-800/50 p-3 rounded-xl border border-zinc-200 dark:border-zinc-700">
                                <label class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block mb-1">Termina</label>
                                <input type="time" v-model="reserveForm.end_time" class="w-full bg-transparent border-none p-0 text-zinc-900 dark:text-white focus:ring-0 text-lg font-medium" required>
                            </div>
                        </div>

                        <div class="bg-zinc-50 dark:bg-zinc-800/50 p-3 rounded-xl border border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                            <div>
                                <label class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Asistentes</label>
                                <span class="text-xs text-zinc-400">Máx. {{ amenity.capacity }}</span>
                            </div>
                            <input type="number" v-model="reserveForm.attendees" min="1" :max="amenity.capacity" class="w-24 text-center border-none bg-white dark:bg-zinc-700 rounded-lg shadow-sm font-medium text-zinc-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="reserveForm.processing" 
                            class="w-full bg-blue-500 hover:bg-blue-600 disabled:bg-blue-300 text-white font-semibold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98] mt-2 flex justify-center items-center"
                        >
                            <span v-if="reserveForm.processing" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span>
                            Confirmar Reserva · $ {{ amenity.reservation_cost }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
/* Transición suave para entrada en vista del formulario */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Ocultar barra de scroll para estética iOS limpia */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1; 
    border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #475569; 
}
</style>
<script setup>
import { ref, watch, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    amenity: Object,
});

const emit = defineEmits(['close']);

// Estado del Calendario
const currentMonth = ref(new Date().getMonth() + 1);
const currentYear = ref(new Date().getFullYear());
const availability = ref([]);
const isLoading = ref(false);

const form = useForm({
    amenityId: '',
    date: '',
    start_time: '',
    end_time: '',
    attendees: 1,
});

// Nombres de los meses para la UI
const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

// Cargar disponibilidad desde la API del controlador
const fetchAvailability = async () => {
    if (!props.amenity) return;
    
    isLoading.value = true;
    try {
        // Asegúrate de que esta ruta coincida con tu web.php o api.php
        const response = await axios.get(`/amenities/${props.amenity.id}/availability`, {
            params: { month: currentMonth.value, year: currentYear.value }
        });
        availability.value = response.data;
    } catch (error) {
        console.error("Error cargando disponibilidad:", error);
    } finally {
        isLoading.value = false;
    }
};

// Observar cuando se abre el modal para inicializar
watch(() => props.show, (newVal) => {
    if (newVal && props.amenity) {
        form.reset();
        form.amenityId = props.amenity.id;
        currentMonth.value = new Date().getMonth() + 1;
        currentYear.value = new Date().getFullYear();
        fetchAvailability();
    }
});

// Cambiar de mes
const prevMonth = () => {
    if (currentMonth.value === 1) {
        currentMonth.value = 12;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
    fetchAvailability();
};

const nextMonth = () => {
    if (currentMonth.value === 12) {
        currentMonth.value = 1;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
    fetchAvailability();
};

// Generar el grid del calendario (con espacios en blanco al inicio para alinear los días)
const calendarDays = computed(() => {
    if (!availability.value.length) return [];
    
    const firstDayDate = new Date(currentYear.value, currentMonth.value - 1, 1);
    const startDayOfWeek = firstDayDate.getDay(); // 0 = Domingo, 1 = Lunes...
    
    const emptyDays = Array(startDayOfWeek).fill({ empty: true });
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const actualDays = availability.value.map(d => {
        // Agregar zonas horarias seguras
        const dateObj = new Date(d.date + 'T00:00:00'); 
        const isPast = dateObj < today;
        return { 
            ...d, 
            isPast, 
            selected: form.date === d.date 
        };
    });

    return [...emptyDays, ...actualDays];
});

const selectDate = (day) => {
    if (day.status === 'full' || day.status === 'closed' || day.isPast) return;
    form.date = day.date;
};

// Enviar el formulario
const submit = () => {
    form.post(route('reservations.store'), {
        preserveState: false, // <-- ESTO ES CLAVE: Fuerza a Inertia a recargar los datos del Index para que la reserva aparezca inmediatamente
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
            form.reset();
        }
    });
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="md">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50 dark:bg-zinc-800">
                <div>
                    <h3 class="font-bold text-zinc-900 dark:text-white text-lg">Reservar {{ amenity?.name }}</h3>
                    <p class="text-xs text-zinc-500 font-medium mt-0.5">Selecciona fecha y hora</p>
                </div>
                <button @click="$emit('close')" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition bg-zinc-200/50 dark:bg-zinc-700 p-2 rounded-full">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="overflow-y-auto p-6 space-y-6">
                <!-- Controles del Mes -->
                <div class="flex items-center justify-between mb-2">
                    <button @click="prevMonth" class="p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 transition">
                        &larr;
                    </button>
                    <h4 class="font-bold text-zinc-800 dark:text-zinc-200">{{ monthNames[currentMonth - 1] }} {{ currentYear }}</h4>
                    <button @click="nextMonth" class="p-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-300 transition">
                        &rarr;
                    </button>
                </div>

                <!-- Calendario -->
                <div v-if="isLoading" class="flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>

                <div v-else>
                    <div class="grid grid-cols-7 gap-1 text-center mb-2">
                        <span class="text-xs font-bold text-zinc-400" v-for="d in ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa']" :key="d">{{ d }}</span>
                    </div>
                    
                    <div class="grid grid-cols-7 gap-1.5">
                        <template v-for="(day, index) in calendarDays" :key="index">
                            <div v-if="day.empty" class="h-10"></div>
                            <button 
                                v-else
                                type="button"
                                :disabled="day.status === 'full' || day.status === 'closed' || day.isPast"
                                :title="day.isPast ? 'Fecha pasada' : day.status === 'full' ? 'Día ocupado' : 'Disponible'"
                                :class="[ 
                                    'h-10 rounded-xl text-sm font-bold transition flex items-center justify-center',
                                    /* Días ocupados / Rojos */
                                    (day.status === 'full' || day.status === 'closed') && !day.isPast ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 cursor-not-allowed opacity-60' : '',
                                    /* Días pasados */
                                    day.isPast ? 'bg-zinc-100 text-zinc-400 dark:bg-zinc-800 dark:text-zinc-600 cursor-not-allowed' : '',
                                    /* Días disponibles / Verdes */
                                    (day.status === 'high' || day.status === 'low') && !day.isPast && !day.selected ? 'bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40 cursor-pointer shadow-sm border border-green-200 dark:border-green-800/50' : '',
                                    /* Día seleccionado */
                                    day.selected ? 'bg-blue-600 text-white ring-2 ring-blue-600 ring-offset-2 dark:ring-offset-zinc-900 shadow-md shadow-blue-500/30' : ''
                                ]"
                                @click="selectDate(day)"
                            >
                                {{ day.day }}
                            </button>
                        </template>
                    </div>

                    <!-- Leyenda -->
                    <div class="flex justify-center gap-4 mt-4 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-green-100 border border-green-300"></div> Disponible</span>
                        <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-red-100 border border-red-300"></div> Reservado / Cerrado</span>
                    </div>
                </div>

                <!-- Formulario de Horario y Asistentes (Aparece al seleccionar fecha) -->
                <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0">
                    <form v-if="form.date" @submit.prevent="submit" class="mt-6 space-y-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Hora Inicio</label>
                                <input type="time" v-model="form.start_time" required class="w-full rounded-xl border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-zinc-900 dark:text-white" />
                                <span class="text-xs text-red-500 mt-1" v-if="form.errors.start_time">{{ form.errors.start_time }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Hora Fin</label>
                                <input type="time" v-model="form.end_time" required class="w-full rounded-xl border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-zinc-900 dark:text-white" />
                                <span class="text-xs text-red-500 mt-1" v-if="form.errors.end_time">{{ form.errors.end_time }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Número de Asistentes</label>
                            <input type="number" v-model="form.attendees" min="1" :max="amenity?.capacity || 100" required class="w-full rounded-xl border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-zinc-900 dark:text-white" />
                            <span class="text-xs text-zinc-500 mt-1">Aforo máximo: {{ amenity?.capacity || 'N/A' }} personas.</span>
                            <span class="text-xs text-red-500 mt-1 block" v-if="form.errors.attendees">{{ form.errors.attendees }}</span>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <SecondaryButton type="button" @click="$emit('close')">Cancelar</SecondaryButton>
                            <PrimaryButton :disabled="form.processing" class="bg-blue-600 hover:bg-blue-500 px-6">
                                Confirmar Reserva
                            </PrimaryButton>
                        </div>
                    </form>
                </transition>
            </div>
        </div>
    </Modal>
</template>
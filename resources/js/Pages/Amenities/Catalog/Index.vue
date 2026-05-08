<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ReservationModal from '@/Components/ReservationModal.vue';

// PrimeVue Imports
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

// --- Métodos Residente ---
const openReserveModal = (amenity) => {
    selectedAmenity.value = amenity;
    showReserveModal.value = true;
};

const openRulesModal = (amenity) => {
    selectedAmenity.value = amenity;
    showRulesModal.value = true;
};

// Formato de moneda
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
};

// Función para obtener y mostrar el horario del día de hoy en la tarjeta
const getTodaySchedule = (schedule) => {
    if (!schedule) return 'Horario no definido';
    
    let parsedSchedule = schedule;
    if (typeof schedule === 'string') {
        try { parsedSchedule = JSON.parse(schedule); } catch (e) { return 'Horario no definido'; }
    }

    const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    const today = days[new Date().getDay()];
    
    const todayConfig = parsedSchedule[today];
    
    // Verificamos si está activo validando booleanos y strings ('true' o '1')
    const isActive = todayConfig && (todayConfig.active === true || todayConfig.active === 'true' || todayConfig.active === 1);

    if (isActive && todayConfig.start && todayConfig.end) {
        return `Hoy: ${todayConfig.start} a ${todayConfig.end}`;
    }
    
    return 'Cerrado hoy';
};

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
                    <!-- Se eliminó el v-if="isAdmin" para que todos puedan ver el botón -->
                    <div>
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
                            <div class="absolute bottom-4 right-4 bg-white/90 p-1 rounded-full shadow-lg flex items-center gap-2 px-3">
                                <span class="text-xs font-bold text-zinc-600">{{ amenity.is_active ? 'Activa' : 'Inactiva' }}</span>
                                <InputSwitch v-model="amenity.is_active" @input="toggleStatus(amenity)" />
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">{{ amenity.name }}</h3>
                            </div>
                            
                            <!-- Insignia del Horario de Hoy -->
                            <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400 mb-4 bg-zinc-50 dark:bg-zinc-700/50 p-2 rounded-lg w-fit border border-zinc-100 dark:border-zinc-700">
                                <span>🕒</span>
                                <span class="font-medium">{{ getTodaySchedule(amenity.availability_schedule) }}</span>
                            </div>

                            <p class="text-zinc-500 dark:text-zinc-400 text-sm line-clamp-3 mb-4 flex-grow">{{ amenity.description }}</p>
                            
                            <!-- Botones Residente -->
                            <div class="flex flex-col gap-2 mt-auto">
                                <button @click="openRulesModal(amenity)" class="text-blue-600 text-sm hover:underline font-medium text-left">
                                    📜 Ver Reglamento
                                </button>
                                <button @click="openReserveModal(amenity)" class="w-full flex justify-center items-center px-4 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-sm font-bold shadow-lg transition">
                                    Reservar Ahora
                                </button>
                            </div>

                            <!-- Botones Admin -->
                            <div class="flex gap-2 mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-700">
                                <Link :href="route('amenities.edit', amenity.id)" class="flex-1 px-4 py-2 bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200 rounded-xl text-sm font-semibold text-center hover:bg-zinc-200 transition">
                                    Editar
                                </Link>
                                <button @click="confirmDelete($event, amenity)" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-sm font-semibold hover:bg-red-100 transition">
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
            <!-- MODAL RESERVA ESTILO iOS -->
            <!-- ======================= -->
            <ReservationModal 
                :show="showReserveModal" 
                :amenity="selectedAmenity" 
                @close="showReserveModal = false" 
            />
        </div>
    </AppLayout>
</template>
<script setup>
import { ref } from 'vue';
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
    amenities: Array, // Nuevo: Recibimos las amenidades para poder crear
});

const confirm = useConfirm();
const toast = useToast();

// Estados para los Modales
const showPickAmenityModal = ref(false);
const showCreateModal = ref(false);
const showDetailsModal = ref(false);

const selectedAmenityForCreate = ref(null);
const selectedReservation = ref(null);

// Funciones para Crear
const startReservation = (amenity) => {
    selectedAmenityForCreate.value = amenity;
    showPickAmenityModal.value = false;
    showCreateModal.value = true;
};

// Funciones para Ver/Editar Detalles
const openDetails = (reservation) => {
    selectedReservation.value = reservation;
    showDetailsModal.value = true;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
};

const getStatusColor = (status) => {
    const colors = {
        'Pendiente': 'bg-yellow-100 text-yellow-800',
        'Aprobada': 'bg-blue-100 text-blue-800',
        'Completada': 'bg-green-100 text-green-800',
        'Rechazada': 'bg-red-100 text-red-800',
        'Cancelada': 'bg-zinc-200 text-zinc-800',
    };
    return colors[status] || 'bg-zinc-100 text-zinc-800';
};
</script>

<template>
    <AppLayout title="Catálogo de Reservaciones">
        <Toast />
        <ConfirmDialog />

        <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-8 transition-colors duration-300">
            <Head title="Reservaciones" />

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Encabezado con el Botón de Crear -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Reservaciones</h2>
                        <Link :href="route('amenities.index')" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition mt-1 block">
                            &larr; Volver a Amenidades
                        </Link>
                    </div>
                    
                    <button @click="showPickAmenityModal = true" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5">
                        + Nueva Reservación
                    </button>
                </div>

                <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-3xl overflow-hidden border border-zinc-200 dark:border-zinc-700">
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
                                <tr v-for="res in reservations" :key="res.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
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
                                        <span :class="['px-3 py-1 rounded-full text-xs font-bold', getStatusColor(res.status)]">
                                            {{ res.status }}
                                        </span>
                                    </td>
                                    <td class="p-4 flex gap-2 justify-center">
                                        <button @click="openDetails(res)" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg font-semibold transition shadow-sm text-xs">
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
</style>
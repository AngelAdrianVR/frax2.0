<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: Boolean,
    reservation: Object,
    isAdmin: Boolean
});

const emit = defineEmits(['close']);

// Formulario para editar el estado y las notas
const form = useForm({
    status: '',
    admin_notes: ''
});

// Sincronizar el formulario cada vez que se abre una reservación diferente
watch(() => props.reservation, (newVal) => {
    if (newVal) {
        form.status = newVal.status;
        form.admin_notes = newVal.admin_notes || '';
    }
});

const submit = () => {
    form.patch(route('reservations.update', props.reservation.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
            // El toast de éxito se maneja en el Index o globalmente
        }
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
};

const getStatusColor = (status) => {
    const colors = {
        'Pendiente': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        'Aprobada': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'Completada': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'Rechazada': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        'Cancelada': 'bg-zinc-200 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300',
    };
    return colors[status] || 'bg-zinc-100 text-zinc-800';
};
</script>

<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="md">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md sticky top-0 z-10">
                <h3 class="font-bold text-zinc-900 dark:text-white text-lg">Detalles de Reservación</h3>
                <button @click="$emit('close')" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="overflow-y-auto p-6 space-y-6" v-if="reservation">
                
                <!-- Tarjeta de Info tipo Recibo -->
                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-5 rounded-2xl border border-zinc-100 dark:border-zinc-700">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1">Amenidad</p>
                            <p class="text-lg font-bold text-zinc-900 dark:text-white">{{ reservation.amenity_name }}</p>
                        </div>
                        <span :class="['px-3 py-1 rounded-full text-xs font-bold', getStatusColor(reservation.status)]">
                            {{ reservation.status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400 mb-1">Inicio</p>
                            <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ reservation.start_date }}</p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400 mb-1">Fin</p>
                            <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ reservation.end_date }}</p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400 mb-1">Unidad/Residente</p>
                            <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ reservation.unit_name }}</p>
                        </div>
                        <div>
                            <p class="text-zinc-500 dark:text-zinc-400 mb-1">Asistentes</p>
                            <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ reservation.attendees }} personas</p>
                        </div>
                        <div class="col-span-2 pt-2 flex justify-between items-center border-t border-zinc-200 dark:border-zinc-700 mt-2">
                            <p class="text-zinc-500 dark:text-zinc-400 font-medium">Costo Total</p>
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ formatCurrency(reservation.total_cost) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Edición (Solo Admin) -->
                <form v-if="isAdmin" @submit.prevent="submit" class="space-y-5 bg-white dark:bg-zinc-800 p-5 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                    <h4 class="font-bold text-zinc-900 dark:text-white mb-2">Gestión y Edición</h4>
                    
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Cambiar Estado</label>
                        <select v-model="form.status" class="w-full rounded-xl border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 shadow-sm focus:ring-blue-500 focus:border-blue-500 font-medium text-zinc-800 dark:text-zinc-200">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Aprobada">Aprobada</option>
                            <option value="Completada">Completada</option>
                            <option value="Rechazada">Rechazada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Notas del Administrador</label>
                        <textarea v-model="form.admin_notes" rows="3" class="w-full rounded-xl border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm text-zinc-800 dark:text-zinc-200" placeholder="Motivo de rechazo, notas internas, etc."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <SecondaryButton type="button" @click="$emit('close')">Cancelar</SecondaryButton>
                        <PrimaryButton :disabled="form.processing" class="bg-blue-600 hover:bg-blue-500">
                            Guardar Cambios
                        </PrimaryButton>
                    </div>
                </form>

                <!-- Vista de Notas (Para Residentes) -->
                <div v-else-if="reservation.admin_notes" class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800">
                    <p class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider mb-1">Mensaje del Administrador</p>
                    <p class="text-sm text-blue-900 dark:text-blue-200">{{ reservation.admin_notes }}</p>
                </div>

            </div>
        </div>
    </Modal>
</template>
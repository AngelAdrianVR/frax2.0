<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    reservation: Object
});

const form = useForm({
    status: props.reservation.status,
    admin_notes: props.reservation.admin_notes || ''
});

const submit = () => {
    form.patch(route('reservations.update', props.reservation.id), {
        preserveScroll: true,
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
};

const getStatusColor = (status) => {
    const colors = {
        'Pendiente': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
        'Aprobada': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800',
        'Completada': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800',
        'Rechazada': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800',
        'Cancelada': 'bg-zinc-200 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-600',
    };
    return colors[status] || 'bg-zinc-100 text-zinc-800 border-zinc-200';
};
</script>

<template>
    <AppLayout title="Editar Reservación">
        <Head title="Editar Reservación" />

        <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-8 transition-colors duration-300">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Encabezado -->
                <div class="mb-6">
                    <Link :href="route('reservations.index')" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition flex items-center gap-1 mb-2">
                        <span>&larr;</span> Volver a Reservaciones
                    </Link>
                    <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Gestionar Reservación</h2>
                    <p class="text-zinc-500 dark:text-zinc-400 mt-1">Folio #{{ reservation.id }} - {{ reservation.amenity?.name }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Tarjeta de Información Estilo Recibo -->
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white dark:bg-zinc-800 p-6 rounded-3xl shadow-sm border border-zinc-200 dark:border-zinc-700">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-4 border-b border-zinc-100 dark:border-zinc-700 pb-2">Detalles del Evento</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Estado Actual</p>
                                    <span :class="['px-3 py-1 mt-1 rounded-full text-xs font-bold border inline-block', getStatusColor(reservation.status)]">
                                        {{ reservation.status }}
                                    </span>
                                </div>

                                <div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Unidad / Residente</p>
                                    <p class="font-bold text-zinc-900 dark:text-zinc-100">{{ reservation.private_unit?.name || 'N/A' }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Asistentes</p>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ reservation.attendees_amount }} personas</p>
                                </div>

                                <div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Fecha y Hora</p>
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ reservation.start_date_time }}</p>
                                    <p class="text-sm text-zinc-500">al {{ reservation.end_date_time }}</p>
                                </div>

                                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-700">
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Costo Total</p>
                                    <p class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ formatCurrency(reservation.total_cost) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Edición -->
                    <div class="md:col-span-2">
                        <div class="bg-white dark:bg-zinc-800 p-6 sm:p-8 rounded-3xl shadow-sm border border-zinc-200 dark:border-zinc-700">
                            
                            <div class="mb-6 border-b border-zinc-100 dark:border-zinc-700 pb-4">
                                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Actualización de Estado</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Modifica el estatus de la reservación y agrega notas para el residente.</p>
                            </div>

                            <form @submit.prevent="submit" class="space-y-6">
                                
                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Estado de la Reservación</label>
                                    <select v-model="form.status" class="w-full rounded-xl border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 shadow-sm focus:ring-blue-500 focus:border-blue-500 font-medium text-zinc-900 dark:text-white p-3">
                                        <option value="Pendiente">Pendiente (En revisión)</option>
                                        <option value="Aprobada">Aprobada (Lista para el evento)</option>
                                        <option value="Completada">Completada (Evento finalizado)</option>
                                        <option value="Rechazada">Rechazada</option>
                                        <option value="Cancelada">Cancelada (Por el usuario o admin)</option>
                                    </select>
                                    <span class="text-xs text-red-500 mt-1 block" v-if="form.errors.status">{{ form.errors.status }}</span>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Notas del Administrador</label>
                                    <textarea v-model="form.admin_notes" rows="4" class="w-full rounded-xl border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm text-zinc-900 dark:text-white p-3" placeholder="Ej. El pago no ha sido validado, el aforo excede el límite permitido, etc."></textarea>
                                    <p class="text-xs text-zinc-500 mt-2">Estas notas serán visibles para el residente en su portal.</p>
                                    <span class="text-xs text-red-500 mt-1 block" v-if="form.errors.admin_notes">{{ form.errors.admin_notes }}</span>
                                </div>

                                <div class="flex items-center justify-end gap-4 pt-6 border-t border-zinc-100 dark:border-zinc-700">
                                    <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
                                        <p v-if="form.recentlySuccessful" class="text-sm font-bold text-green-600">¡Guardado exitosamente!</p>
                                    </Transition>
                                    
                                    <PrimaryButton :disabled="form.processing" class="bg-blue-600 hover:bg-blue-500 px-8 py-3 text-base">
                                        Guardar Cambios
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
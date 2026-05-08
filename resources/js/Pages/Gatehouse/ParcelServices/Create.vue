<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// Se esperan las unidades privadas para que el guardia seleccione a qué casa va
defineProps({
    privateUnits: Array 
});

const form = useForm({
    name: '',
    tracking_number: '',
    private_unit_id: '',
    status: 'En Caseta' // Estatus inicial por defecto
});

const submit = () => {
    form.post(route('parcel-services.store'));
};
</script>

<template>
    <AppLayout title="Registrar Paquete">
        <div class="py-12 bg-[#F2F2F7] dark:bg-zinc-900 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6">
                    <Link :href="route('parcel-services.index')" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center">
                        <i class="pi pi-angle-left mr-1"></i> Volver a paquetería
                    </Link>
                </div>

                <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-3xl overflow-hidden border border-zinc-200 dark:border-zinc-700/50">
                    <div class="px-8 py-6 border-b border-zinc-100 dark:border-zinc-700/50 flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <i class="pi pi-box text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Registrar Nuevo Paquete</h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Ingresa los datos del paquete recibido en caseta.</p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="p-8 space-y-6">
                        <!-- Estatus -->
                        <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Estatus Actual</label>
                                <select v-model="form.status" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition-colors" required>
                                    <option value="En Caseta">En Caseta</option>
                                    <option value="Recibido">Recibido</option>
                                    <option value="Entregado">Entregado al Residente</option>
                                    <option value="Devuelto">Devuelto / Regresado</option>
                                </select>
                                <p v-if="form.errors.status" class="mt-2 text-sm text-rose-500">{{ form.errors.status }}</p>
                            </div>

                        <!-- Destino -->
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Unidad / Casa Destino</label>
                            <select v-model="form.private_unit_id" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                                <option value="" disabled>Selecciona la casa destino...</option>
                                <option v-for="unit in privateUnits" :key="unit.id" :value="unit.id">
                                    {{ unit.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.private_unit_id" class="mt-2 text-sm text-rose-500">{{ form.errors.private_unit_id }}</p>
                        </div>

                        <!-- Detalles del paquete -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Mensajería / Descripción</label>
                                <input v-model="form.name" type="text" placeholder="Ej. Amazon, DHL, Sobre Manila" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition-colors" required />
                                <p v-if="form.errors.name" class="mt-2 text-sm text-rose-500">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Número de Guía (Opcional)</label>
                                <input v-model="form.tracking_number" type="text" placeholder="Ej. TBA123456789" class="w-full bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 font-mono transition-colors" />
                                <p v-if="form.errors.tracking_number" class="mt-2 text-sm text-rose-500">{{ form.errors.tracking_number }}</p>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-zinc-100 dark:border-zinc-700/50 flex justify-end gap-3">
                            <Link :href="route('parcel-services.index')" class="px-6 py-3 text-zinc-700 dark:text-zinc-300 font-medium hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-xl transition-colors">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 shadow-sm transition-colors disabled:opacity-50 flex items-center">
                                <i v-if="form.processing" class="pi pi-spinner pi-spin mr-2"></i>
                                Guardar Paquete
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
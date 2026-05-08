<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    patrol: Object,
    guards: Array
});

const form = useForm({
    user_id: props.patrol.user_id,
    start_time: props.patrol.start_time,
    end_time: props.patrol.end_time,
    status: props.patrol.status,
    scanned_points: props.patrol.scanned_points
});

const submit = () => {
    form.put(route('patrols.update', props.patrol.id));
};
</script>

<template>
    <AppLayout title="Editar Rondín">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Botón de regreso -->
                <div class="mb-6 px-4 sm:px-0">
                    <Link :href="route('patrols.index')" class="inline-flex items-center text-sm font-medium text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 transition-colors">
                        <i class="pi pi-arrow-left mr-2"></i> Volver a la bitácora
                    </Link>
                </div>

                <!-- Tarjeta del Formulario -->
                <div class="bg-white dark:bg-zinc-800 shadow-sm border border-zinc-200 dark:border-zinc-700 sm:rounded-3xl overflow-hidden">
                    <div class="px-6 py-8 sm:p-10">
                        <div class="mb-8">
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Editar Rondín</h2>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Actualiza la información del recorrido seleccionado.</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <!-- Guardia -->
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Guardia Asignado *</label>
                                <select id="user_id" v-model="form.user_id" class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" required>
                                    <option value="" disabled>Selecciona un guardia...</option>
                                    <option v-for="guard in guards" :key="guard.id" :value="guard.id">
                                        {{ guard.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.user_id" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.user_id }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Inicio -->
                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Fecha y Hora de Inicio *</label>
                                    <input type="datetime-local" id="start_time" v-model="form.start_time" class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" required>
                                    <div v-if="form.errors.start_time" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.start_time }}</div>
                                </div>

                                <!-- Fin -->
                                <div>
                                    <label for="end_time" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Fecha y Hora de Fin</label>
                                    <input type="datetime-local" id="end_time" v-model="form.end_time" class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                    <p class="text-xs text-zinc-500 mt-1">Dejar vacío si el rondín aún está en curso.</p>
                                    <div v-if="form.errors.end_time" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.end_time }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Estatus -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Estatus del Rondín *</label>
                                    <select id="status" v-model="form.status" class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" required>
                                        <option value="Activo">Activo (En curso)</option>
                                        <option value="Terminado">Terminado (Sin Novedad)</option>
                                        <option value="Incidente">Incidente (Atención requerida)</option>
                                    </select>
                                    <div v-if="form.errors.status" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.status }}</div>
                                </div>

                                <!-- Puntos Escaneados -->
                                <div>
                                    <label for="scanned_points" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Puntos Escaneados</label>
                                    <input type="number" id="scanned_points" v-model="form.scanned_points" min="0" class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                    <div v-if="form.errors.scanned_points" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.scanned_points }}</div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="pt-6 border-t border-zinc-200 dark:border-zinc-700 flex justify-end gap-3">
                                <Link :href="route('patrols.index')" class="px-5 py-2.5 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-xl text-zinc-700 dark:text-zinc-300 font-medium text-sm hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                                    Cancelar
                                </Link>
                                <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium text-sm transition-colors shadow-sm disabled:opacity-50">
                                    Guardar Cambios
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
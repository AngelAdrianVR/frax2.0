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
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Botón de regreso -->
                <div class="mb-6 px-4 sm:px-0">
                    <Link :href="route('patrols.index')" class="inline-flex items-center text-sm font-medium text-zinc-400 hover:text-zinc-200 transition-colors">
                        <i class="pi pi-arrow-left mr-2"></i> Volver a la bitácora
                    </Link>
                </div>

                <!-- Tarjeta del Formulario -->
                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    <div class="px-6 py-8 sm:p-10">
                        <div class="mb-8">
                            <h2 class="text-lg font-medium text-zinc-100 tracking-tight">Editar Rondín</h2>
                            <p class="mt-1 text-sm text-zinc-400">Actualiza la información del recorrido seleccionado.</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <!-- Guardia -->
                            <div class="flex flex-col gap-1.5">
                                <label for="user_id" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Guardia Asignado *</label>
                                <select id="user_id" v-model="form.user_id" class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" required>
                                    <option value="" disabled>Selecciona un guardia...</option>
                                    <option v-for="guard in guards" :key="guard.id" :value="guard.id">
                                        {{ guard.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.user_id" class="text-sm text-red-400">{{ form.errors.user_id }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Inicio -->
                                <div class="flex flex-col gap-1.5">
                                    <label for="start_time" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Fecha y Hora de Inicio *</label>
                                    <input type="datetime-local" id="start_time" v-model="form.start_time" class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" required>
                                    <p v-if="form.errors.start_time" class="text-sm text-red-400">{{ form.errors.start_time }}</p>
                                </div>

                                <!-- Fin -->
                                <div class="flex flex-col gap-1.5">
                                    <label for="end_time" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Fecha y Hora de Fin</label>
                                    <input type="datetime-local" id="end_time" v-model="form.end_time" class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600">
                                    <p class="text-xs text-zinc-500 mt-0.5">Dejar vacío si el rondín aún está en curso.</p>
                                    <p v-if="form.errors.end_time" class="text-sm text-red-400">{{ form.errors.end_time }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Estatus -->
                                <div class="flex flex-col gap-1.5">
                                    <label for="status" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Estatus del Rondín *</label>
                                    <select id="status" v-model="form.status" class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" required>
                                        <option value="Activo">Activo (En curso)</option>
                                        <option value="Terminado">Terminado (Sin Novedad)</option>
                                        <option value="Incidente">Incidente (Atención requerida)</option>
                                    </select>
                                    <p v-if="form.errors.status" class="text-sm text-red-400">{{ form.errors.status }}</p>
                                </div>

                                <!-- Puntos Escaneados -->
                                <div class="flex flex-col gap-1.5">
                                    <label for="scanned_points" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Puntos Escaneados</label>
                                    <input type="number" id="scanned_points" v-model="form.scanned_points" min="0" class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600">
                                    <p v-if="form.errors.scanned_points" class="text-sm text-red-400">{{ form.errors.scanned_points }}</p>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-800/60">
                                <Link :href="route('patrols.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                                    Cancelar
                                </Link>
                                <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50">
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
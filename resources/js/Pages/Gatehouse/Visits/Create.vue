<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    privateUnits: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    name: '',
    reason: '',
    access_type: 'Peatonal',
    expiration_date: '',
    private_unit_id: '',
});

const submit = () => {
    form.post(route('visits.store'));
};
</script>

<template>
    <AppLayout title="Nueva Visita">
        <div class="py-8 md:py-12 bg-[#F2F2F7] dark:bg-zinc-900 min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado -->
                <div class="mb-6 flex items-center justify-between px-4 sm:px-0">
                    <div class="flex items-center gap-3">
                        <Link :href="route('visits.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-200/50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 transition-colors">
                            <i class="pi pi-arrow-left text-sm"></i>
                        </Link>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Nueva Invitación</h1>
                    </div>
                </div>

                <!-- Formulario Estilo iOS -->
                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">
                    
                    <!-- Tarjeta de Datos Principales -->
                    <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-700/50">
                        <div class="p-6 space-y-5">
                            
                            <!-- Nombre -->
                            <div>
                                <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2 ml-1">Visitante</label>
                                <input v-model="form.name" type="text" placeholder="Nombre completo" required
                                    class="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white font-medium" />
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1 ml-1">{{ form.errors.name }}</div>
                            </div>

                            <!-- Motivo -->
                            <div>
                                <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2 ml-1">Motivo (Opcional)</label>
                                <textarea v-model="form.reason" rows="2" placeholder="Ej. Fiesta, Entrega, Familiar..."
                                    class="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white resize-none font-medium"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Detalles Logísticos -->
                    <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-700/50">
                        <div class="p-6 space-y-5">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Tipo de Acceso -->
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2 ml-1">Tipo de Acceso</label>
                                    <div class="relative">
                                        <select v-model="form.access_type" 
                                            class="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white font-medium appearance-none">
                                            <option value="Peatonal">🚶‍♂️ Peatonal</option>
                                            <option value="Vehicular">🚗 Vehicular</option>
                                        </select>
                                        <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-400 pointer-events-none text-xs"></i>
                                    </div>
                                </div>

                                <!-- Fecha de Expiración -->
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2 ml-1">Válido hasta</label>
                                    <input v-model="form.expiration_date" type="datetime-local" 
                                        class="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white font-medium" />
                                </div>
                            </div>

                            <!-- Selector de Unidad (Solo Admin) -->
                            <div v-if="privateUnits.length > 0" class="pt-2">
                                <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2 ml-1">Asignar a Propiedad</label>
                                <div class="relative">
                                    <select v-model="form.private_unit_id" required
                                        class="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white font-medium appearance-none">
                                        <option value="" disabled>Selecciona una casa/lote...</option>
                                        <option v-for="unit in privateUnits" :key="unit.id" :value="unit.id">
                                            🏠 {{ unit.name }}
                                        </option>
                                    </select>
                                    <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-400 pointer-events-none text-xs"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-end gap-3">
                        <Link :href="route('visits.index')" class="w-full sm:w-auto px-6 py-3.5 text-sm font-bold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-2xl transition-colors text-center">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="w-full sm:w-auto px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-2xl shadow-lg shadow-indigo-500/30 transition-all disabled:opacity-50 flex justify-center items-center gap-2">
                            <i v-if="form.processing" class="pi pi-spinner pi-spin"></i>
                            <span v-if="form.processing">Generando...</span>
                            <span v-else>Crear Pase de Acceso</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
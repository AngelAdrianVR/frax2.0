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
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado -->
                <div class="mb-6 flex items-center gap-3 px-4 sm:px-0">
                    <Link :href="route('visits.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left text-sm"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight">Nueva Invitación</h1>
                        <p class="text-sm text-zinc-400">Registra una visita para generar un pase de acceso.</p>
                    </div>
                </div>

                <!-- Formulario -->
                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">
                    
                    <!-- Tarjeta de Datos -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="p-6 space-y-5">
                            
                            <!-- Nombre -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Visitante</label>
                                <input v-model="form.name" type="text" placeholder="Nombre completo" required
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                            </div>

                            <!-- Motivo -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Motivo (Opcional)</label>
                                <textarea v-model="form.reason" rows="2" placeholder="Ej. Fiesta, Entrega, Familiar..."
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Detalles Logísticos -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="p-6 space-y-5">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Tipo de Acceso -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Tipo de Acceso</label>
                                    <div class="relative">
                                        <select v-model="form.access_type" 
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                            <option value="Peatonal">🚶‍♂️ Peatonal</option>
                                            <option value="Vehicular">🚗 Vehicular</option>
                                        </select>
                                        <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                    </div>
                                </div>

                                <!-- Fecha de Expiración -->
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Válido hasta</label>
                                    <input v-model="form.expiration_date" type="datetime-local" 
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                </div>
                            </div>

                            <!-- Selector de Unidad (Solo Admin) -->
                            <div v-if="privateUnits.length > 0" class="flex flex-col gap-1.5 pt-1">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Asignar a Propiedad</label>
                                <div class="relative">
                                    <select v-model="form.private_unit_id" required
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                        <option value="" disabled>Selecciona una casa/lote...</option>
                                        <option v-for="unit in privateUnits" :key="unit.id" :value="unit.id">
                                            🏠 {{ unit.name }}
                                        </option>
                                    </select>
                                    <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end gap-3 pt-2">
                        <Link :href="route('visits.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm text-center">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50 flex items-center gap-2">
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
<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    visit: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: props.visit.name,
    reason: props.visit.reason,
    access_type: props.visit.access_type,
    expiration_date: props.visit.expiration_date ? props.visit.expiration_date.substring(0, 16) : '',
    status: props.visit.status,
});

const submit = () => {
    form.put(route('visits.update', props.visit.id));
};
</script>

<template>
    <AppLayout title="Editar Visita">
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado -->
                <div class="mb-6 flex items-center gap-3 px-4 sm:px-0">
                    <Link :href="route('visits.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left text-sm"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight">Editar Invitación</h1>
                        <p class="text-sm text-zinc-400">Modifica los datos del pase de acceso.</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">
                    
                    <!-- Tarjeta de Estatus -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="p-6">
                            <label class="block text-xs font-medium text-zinc-400 uppercase tracking-wider mb-3">Estatus del Pase</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-200"
                                    :class="form.status === 'Pendiente' ? 'border-amber-500/60 bg-amber-500/10' : 'border-zinc-700/40 hover:bg-zinc-800/50'">
                                    <input type="radio" v-model="form.status" value="Pendiente" class="sr-only" />
                                    <i class="pi pi-clock text-amber-400 mb-1"></i>
                                    <span class="text-xs font-medium text-zinc-300">Pendiente</span>
                                </label>
                                <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-200"
                                    :class="form.status === 'Ingresado' ? 'border-green-500/60 bg-green-500/10' : 'border-zinc-700/40 hover:bg-zinc-800/50'">
                                    <input type="radio" v-model="form.status" value="Ingresado" class="sr-only" />
                                    <i class="pi pi-check-circle text-green-400 mb-1"></i>
                                    <span class="text-xs font-medium text-zinc-300">Ingresado</span>
                                </label>
                                <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-200"
                                    :class="form.status === 'Cancelado' ? 'border-red-500/60 bg-red-500/10' : 'border-zinc-700/40 hover:bg-zinc-800/50'">
                                    <input type="radio" v-model="form.status" value="Cancelado" class="sr-only" />
                                    <i class="pi pi-times-circle text-red-400 mb-1"></i>
                                    <span class="text-xs font-medium text-zinc-300">Cancelado</span>
                                </label>
                                <label class="relative flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer transition-all duration-200"
                                    :class="form.status === 'Expirado' ? 'border-zinc-500/60 bg-zinc-500/10' : 'border-zinc-700/40 hover:bg-zinc-800/50'">
                                    <input type="radio" v-model="form.status" value="Expirado" class="sr-only" />
                                    <i class="pi pi-ban text-zinc-400 mb-1"></i>
                                    <span class="text-xs font-medium text-zinc-300">Expirado</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Datos -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="p-6 space-y-5">
                            
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Visitante</label>
                                <input v-model="form.name" type="text" required
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Válido hasta</label>
                                    <input v-model="form.expiration_date" type="datetime-local" 
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                </div>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Motivo</label>
                                <textarea v-model="form.reason" rows="2"
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <Link :href="route('visits.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm text-center">
                            Descartar
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50 flex items-center gap-2">
                            <i v-if="form.processing" class="pi pi-spinner pi-spin"></i>
                            <span v-else>Guardar Cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
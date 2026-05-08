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
        <div class="py-8 md:py-12 bg-[#F2F2F7] dark:bg-zinc-900 min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado -->
                <div class="mb-6 flex items-center justify-between px-4 sm:px-0">
                    <div class="flex items-center gap-3">
                        <Link :href="route('visits.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-200/50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 transition-colors">
                            <i class="pi pi-arrow-left text-sm"></i>
                        </Link>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Editar Invitación</h1>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">
                    
                    <!-- Tarjeta de Estatus -->
                    <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-700/50">
                        <div class="p-6">
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-3 ml-1">Estatus del Pase</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="relative flex flex-col items-center justify-center p-3 border-2 rounded-2xl cursor-pointer transition-all"
                                    :class="form.status === 'Pendiente' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800'">
                                    <input type="radio" v-model="form.status" value="Pendiente" class="sr-only" />
                                    <i class="pi pi-clock text-amber-500 mb-1"></i>
                                    <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Pendiente</span>
                                </label>
                                <label class="relative flex flex-col items-center justify-center p-3 border-2 rounded-2xl cursor-pointer transition-all"
                                    :class="form.status === 'Ingresado' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800'">
                                    <input type="radio" v-model="form.status" value="Ingresado" class="sr-only" />
                                    <i class="pi pi-check-circle text-green-500 mb-1"></i>
                                    <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Ingresado</span>
                                </label>
                                <label class="relative flex flex-col items-center justify-center p-3 border-2 rounded-2xl cursor-pointer transition-all"
                                    :class="form.status === 'Cancelado' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800'">
                                    <input type="radio" v-model="form.status" value="Cancelado" class="sr-only" />
                                    <i class="pi pi-times-circle text-red-500 mb-1"></i>
                                    <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Cancelado</span>
                                </label>
                                <label class="relative flex flex-col items-center justify-center p-3 border-2 rounded-2xl cursor-pointer transition-all"
                                    :class="form.status === 'Expirado' ? 'border-zinc-500 bg-zinc-100 dark:bg-zinc-800' : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800'">
                                    <input type="radio" v-model="form.status" value="Expirado" class="sr-only" />
                                    <i class="pi pi-ban text-zinc-500 mb-1"></i>
                                    <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Expirado</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Datos -->
                    <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-700/50">
                        <div class="p-6 space-y-5">
                            
                            <div>
                                <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2 ml-1">Visitante</label>
                                <input v-model="form.name" type="text" required
                                    class="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white font-medium" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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
                                <div>
                                    <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2 ml-1">Válido hasta</label>
                                    <input v-model="form.expiration_date" type="datetime-local" 
                                        class="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white font-medium" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2 ml-1">Motivo</label>
                                <textarea v-model="form.reason" rows="2"
                                    class="w-full px-4 py-3 bg-zinc-50 dark:bg-zinc-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all dark:text-white resize-none font-medium"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-end gap-3">
                        <Link :href="route('visits.index')" class="w-full sm:w-auto px-6 py-3.5 text-sm font-bold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-2xl transition-colors text-center">
                            Descartar
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="w-full sm:w-auto px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-2xl shadow-lg transition-all flex justify-center items-center gap-2">
                            <i v-if="form.processing" class="pi pi-spinner pi-spin"></i>
                            <span v-else>Guardar Cambios</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
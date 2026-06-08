<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    parcel: Object,
    privateUnits: Array
});

const form = useForm({
    name: props.parcel.name || props.parcel.courier || '',
    tracking_number: props.parcel.tracking_number || '',
    private_unit_id: props.parcel.private_unit_id || '',
    status: props.parcel.status || 'En Caseta'
});

const submit = () => {
    form.put(route('parcel-services.update', props.parcel.id));
};
</script>

<template>
    <AppLayout title="Editar Paquete">
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6">
                    <Link :href="route('parcel-services.index')" class="text-sm text-zinc-400 hover:text-zinc-200 transition-colors flex items-center">
                        <i class="pi pi-angle-left mr-1"></i> Volver a paquetería
                    </Link>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-800/60 flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#0E63B1]/10 rounded-xl flex items-center justify-center text-[#0E63B1]">
                            <i class="pi pi-pencil text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-medium text-zinc-100 tracking-tight">Editar Paquete</h2>
                            <p class="text-sm text-zinc-400">Modifica los detalles o el estatus del paquete.</p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="p-6 sm:p-8 space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Estatus Actual</label>
                                <select v-model="form.status" class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" required>
                                    <option value="En Caseta">En Caseta</option>
                                    <option value="Recibido">Recibido</option>
                                    <option value="Entregado">Entregado al Residente</option>
                                    <option value="Devuelto">Devuelto / Regresado</option>
                                </select>
                                <p v-if="form.errors.status" class="text-sm text-red-400">{{ form.errors.status }}</p>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Unidad / Casa Destino</label>
                                <select v-model="form.private_unit_id" class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" required>
                                    <option v-for="unit in privateUnits" :key="unit.id" :value="unit.id">
                                        {{ unit.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.private_unit_id" class="text-sm text-red-400">{{ form.errors.private_unit_id }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Mensajería / Descripción</label>
                                <input v-model="form.name" type="text" class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" required />
                                <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Número de Guía</label>
                                <input v-model="form.tracking_number" type="text" class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 font-mono" />
                                <p v-if="form.errors.tracking_number" class="text-sm text-red-400">{{ form.errors.tracking_number }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-800/60">
                            <Link :href="route('parcel-services.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50 flex items-center">
                                <i v-if="form.processing" class="pi pi-spinner pi-spin mr-2"></i>
                                Actualizar Cambios
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
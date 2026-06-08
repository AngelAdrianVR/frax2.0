<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    visitor: Object,
    privateUnits: Array,
});

const form = useForm({
    alias: props.visitor.alias || '',
    name: props.visitor.name || '',
    identification: props.visitor.identification || '',
    default_reason: props.visitor.default_reason || '',
    default_access_type: props.visitor.default_access_type || 'Peatonal',
    default_plate: props.visitor.default_plate || '',
    private_unit_id: props.visitor.private_unit_id || '',
});

const submit = () => {
    form.put(route('frequent-visitors.update', props.visitor.id));
};
</script>

<template>
    <AppLayout title="Editar Visitante Frecuente">
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

                <div class="mb-6 flex items-center gap-3 px-4 sm:px-0">
                    <Link :href="route('frequent-visitors.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left text-sm"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight">Editar Visitante</h1>
                        <p class="text-sm text-zinc-400">Actualiza la información del visitante frecuente.</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">

                    <!-- Identidad -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Identidad</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Nombre Completo *</label>
                                <input v-model="form.name" type="text" placeholder="Ej. Juan Pérez"
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" required />
                                <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Alias</label>
                                    <input v-model="form.alias" type="text" placeholder="Ej. Jardinero"
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acceso -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Acceso</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Motivo Recurrente</label>
                                <input v-model="form.default_reason" type="text" placeholder="Motivo..."
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Tipo *</label>
                                    <div class="relative">
                                        <select v-model="form.default_access_type"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                            <option value="Peatonal">🚶‍♂️ Peatonal</option>
                                            <option value="Vehicular">🚗 Vehicular</option>
                                        </select>
                                        <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                    </div>
                                </div>
                                <div v-if="form.default_access_type === 'Vehicular'" class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Placa</label>
                                    <input v-model="form.default_plate" type="text" placeholder="ABC-1234"
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 font-mono uppercase" />
                                </div>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Propiedad *</label>
                                <div class="relative">
                                    <select v-model="form.private_unit_id" required
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                        <option value="" disabled>Selecciona...</option>
                                        <option v-for="unit in privateUnits" :key="unit.id" :value="unit.id">
                                            🏠 {{ unit.lot_number || unit.id }}
                                        </option>
                                    </select>
                                    <i class="pi pi-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-zinc-500 pointer-events-none text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 pt-2">
                        <Link :href="route('frequent-visitors.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm text-center">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50 flex items-center gap-2">
                            <i v-if="form.processing" class="pi pi-spinner pi-spin"></i>
                            <span v-if="form.processing">Guardando...</span>
                            <span v-else>Actualizar Visitante</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

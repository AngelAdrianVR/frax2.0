<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    base_amount: '',
    recurrence_type: 'Mensual',
    slow_payers_apply: false,
});

const recurrences = ['Semanal', 'Quincenal', 'Mensual', 'Bimestral', 'Anual', 'Pago unico'];

const submit = () => {
    form.post(route('billing-concepts.store'));
};
</script>

<template>
    <AppLayout title="Nuevo Concepto de Cobro">
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 flex items-center gap-4">
                    <Link :href="route('billing-concepts.index')" class="w-10 h-10 bg-zinc-800 border border-zinc-700/50 rounded-full flex items-center justify-center text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight">Crear Concepto</h1>
                        <p class="text-sm text-zinc-400">Define una nueva cuota para tu fraccionamiento.</p>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    <form @submit.prevent="submit" class="p-8">
                        <div class="space-y-6">
                            
                            <!-- Nombre -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Nombre del concepto</label>
                                <input v-model="form.name" type="text" placeholder="Ej. Cuota de Mantenimiento" required
                                    class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                            </div>

                            <!-- Monto y Recurrencia -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Monto Base (MXN)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-zinc-500 font-medium">$</span>
                                        </div>
                                        <input v-model="form.base_amount" type="number" step="0.01" min="0" placeholder="0.00" required
                                            class="w-full pl-8 bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                    </div>
                                    <p v-if="form.errors.base_amount" class="text-sm text-red-400">{{ form.errors.base_amount }}</p>
                                </div>

                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Frecuencia de cobro</label>
                                    <select v-model="form.recurrence_type" required
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 appearance-none">
                                        <option v-for="type in recurrences" :key="type" :value="type">{{ type }}</option>
                                    </select>
                                    <p v-if="form.errors.recurrence_type" class="text-sm text-red-400">{{ form.errors.recurrence_type }}</p>
                                </div>
                            </div>

                            <!-- Opciones Adicionales -->
                            <div class="pt-4 border-t border-zinc-800/60">
                                <label class="flex items-center gap-4 cursor-pointer p-4 rounded-xl border border-zinc-700/40 bg-zinc-800/30 hover:bg-zinc-800/50 transition-colors">
                                    <div class="relative flex items-center">
                                        <input v-model="form.slow_payers_apply" type="checkbox" class="sr-only peer" />
                                        <div class="w-11 h-6 bg-zinc-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0E63B1]"></div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-zinc-200 text-sm">Aplica recargos por morosidad</p>
                                        <p class="text-xs text-zinc-400 mt-0.5">Si se activa, el sistema podrá sumar multas automáticas si el residente se atrasa en este concepto.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="mt-8 pt-4 border-t border-zinc-800/60 flex justify-end gap-3">
                            <Link :href="route('billing-concepts.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50">
                                {{ form.processing ? 'Guardando...' : 'Crear Concepto' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
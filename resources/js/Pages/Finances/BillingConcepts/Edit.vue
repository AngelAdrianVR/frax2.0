<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    concept: Object
});

const form = useForm({
    name: props.concept.name,
    base_amount: props.concept.base_amount,
    recurrence_type: props.concept.recurrence_type,
    slow_payers_apply: Boolean(props.concept.slow_payers_apply),
});

const recurrences = ['Semanal', 'Quincenal', 'Mensual', 'Bimestral', 'Anual', 'Pago unico'];

const submit = () => {
    form.put(route('billing-concepts.update', props.concept.id));
};
</script>

<template>
    <AppLayout title="Editar Concepto">
        <div class="py-10">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 flex items-center gap-4">
                    <Link :href="route('billing-concepts.index')" class="w-10 h-10 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-full flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                        <i class="pi pi-arrow-left"></i>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Editar Concepto</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Modifica los valores de esta cuota.</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                    <form @submit.prevent="submit" class="p-8">
                        <div class="space-y-6">
                            
                            <!-- Nombre -->
                            <div>
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Nombre del concepto</label>
                                <input v-model="form.name" type="text" required
                                    class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <!-- Monto y Recurrencia -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Monto Base (MXN)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-zinc-500 dark:text-zinc-400 font-medium">$</span>
                                        </div>
                                        <input v-model="form.base_amount" type="number" step="0.01" min="0" required
                                            class="w-full pl-8 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
                                    </div>
                                    <p v-if="form.errors.base_amount" class="mt-1 text-sm text-red-500">{{ form.errors.base_amount }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Frecuencia de cobro</label>
                                    <select v-model="form.recurrence_type" required
                                        class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all appearance-none">
                                        <option v-for="type in recurrences" :key="type" :value="type">{{ type }}</option>
                                    </select>
                                    <p v-if="form.errors.recurrence_type" class="mt-1 text-sm text-red-500">{{ form.errors.recurrence_type }}</p>
                                </div>
                            </div>

                            <!-- Opciones Adicionales -->
                            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
                                <label class="flex items-center gap-4 cursor-pointer p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/30 hover:bg-zinc-100 dark:hover:bg-zinc-800/50 transition-colors">
                                    <div class="relative flex items-center">
                                        <input v-model="form.slow_payers_apply" type="checkbox" class="sr-only peer" />
                                        <div class="w-11 h-6 bg-zinc-300 dark:bg-zinc-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-zinc-900 dark:text-white text-sm">Aplica recargos por morosidad</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Si se activa, el sistema podrá sumar multas automáticas si el residente se atrasa en este concepto.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-3">
                            <Link :href="route('billing-concepts.index')" class="px-6 py-2.5 rounded-xl font-semibold text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing" class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold px-6 py-2.5 rounded-xl shadow-sm hover:opacity-90 transition-opacity disabled:opacity-50">
                                {{ form.processing ? 'Guardando...' : 'Actualizar Concepto' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
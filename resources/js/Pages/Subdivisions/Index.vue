<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    subdivision: Object
});

const form = useForm({
    name: props.subdivision?.name || '',
    tax_id: props.subdivision?.tax_id || '',
    bank_account: props.subdivision?.bank_account || '',
    bank_name: props.subdivision?.bank_name || '',
    // Agrega aquí otros campos según las columnas de tu tabla
});

const submit = () => {
    form.put(route('subdivisions.update', props.subdivision.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Manejar éxito (ej. mostrar notificación)
        }
    });
};
</script>

<template>
    <AppLayout title="Datos del Coto">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 px-4 sm:px-0">
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Datos del Coto</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Información general y datos de cobro del fraccionamiento.</p>
                </div>

                <div v-if="!subdivision" class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <i class="pi pi-building text-4xl text-zinc-400 mb-3"></i>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Fraccionamiento no encontrado</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Contacta al administrador del sistema.</p>
                </div>

                <div v-else class="bg-white dark:bg-zinc-800 shadow-xl sm:rounded-lg overflow-hidden p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Sección de Datos Generales -->
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-zinc-900 dark:text-white mb-4">Datos Generales</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Nombre del Coto</label>
                                    <input type="text" id="name" v-model="form.name" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="tax_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">RFC / Identificación Fiscal</label>
                                    <input type="text" id="tax_id" v-model="form.tax_id" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <hr class="border-zinc-200 dark:border-zinc-700">

                        <!-- Sección de Datos de Cobro -->
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-zinc-900 dark:text-white mb-4">Datos de Cobro</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="bank_name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Banco</label>
                                    <input type="text" id="bank_name" v-model="form.bank_name" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                                <div>
                                    <label for="bank_account" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Cuenta CLABE / Número de Cuenta</label>
                                    <input type="text" id="bank_account" v-model="form.bank_account" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <!-- Botón de Guardar -->
                        <div class="flex justify-end pt-4">
                            <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition-colors">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
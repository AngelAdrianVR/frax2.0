<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    checkpoint: Object
});

const form = useForm({
    name: props.checkpoint.name,
    coordinates: props.checkpoint.coordinates || '',
    tag_nfc_id: props.checkpoint.tag_nfc_id || '',
});

const submit = () => {
    form.put(route('checkpoints.update', props.checkpoint.id));
};
</script>

<template>
    <AppLayout title="Editar Punto de Control">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 flex items-center gap-4 px-4 sm:px-0">
                    <Link :href="route('checkpoints.index')" class="w-10 h-10 bg-white dark:bg-zinc-800 rounded-full flex items-center justify-center shadow-sm border border-zinc-200 dark:border-zinc-700 text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                        <i class="pi pi-arrow-left"></i>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Editar Punto de Control</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Actualiza la información de esta ubicación.</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 shadow-sm rounded-3xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <form @submit.prevent="submit" class="p-6 sm:p-8 space-y-6">
                        
                        <!-- Nombre -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Nombre de la Ubicación</label>
                            <input v-model="form.name" type="text" id="name" 
                                class="w-full rounded-xl border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors px-4 py-3" required />
                            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Coordenadas -->
                            <div>
                                <label for="coordinates" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Coordenadas GPS (Opcional)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="pi pi-map text-zinc-400"></i>
                                    </div>
                                    <input v-model="form.coordinates" type="text" id="coordinates" 
                                        class="w-full pl-11 rounded-xl border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors py-3" />
                                </div>
                                <p v-if="form.errors.coordinates" class="mt-2 text-sm text-red-600">{{ form.errors.coordinates }}</p>
                            </div>

                            <!-- Tag NFC -->
                            <div>
                                <label for="tag_nfc_id" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">ID del Tag NFC / Código QR</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="pi pi-qrcode text-zinc-400"></i>
                                    </div>
                                    <input v-model="form.tag_nfc_id" type="text" id="tag_nfc_id" 
                                        class="w-full pl-11 rounded-xl border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors py-3 font-mono" />
                                </div>
                                <p v-if="form.errors.tag_nfc_id" class="mt-2 text-sm text-red-600">{{ form.errors.tag_nfc_id }}</p>
                            </div>
                        </div>

                        <div class="pt-6 flex justify-end gap-3 border-t border-zinc-100 dark:border-zinc-700">
                            <Link :href="route('checkpoints.index')" class="px-6 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing" class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-colors disabled:opacity-50 flex items-center">
                                <i class="pi pi-check-circle mr-2"></i> Actualizar Punto
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
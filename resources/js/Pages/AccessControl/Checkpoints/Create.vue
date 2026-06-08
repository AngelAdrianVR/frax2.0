<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    coordinates: '',
    tag_nfc_id: '',
});

const submit = () => {
    form.post(route('checkpoints.store'));
};
</script>

<template>
    <AppLayout title="Crear Punto de Control">
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 flex items-center gap-4 px-4 sm:px-0">
                    <Link :href="route('checkpoints.index')" class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center border border-zinc-700/50 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight">Nuevo Punto de Control</h1>
                        <p class="text-sm text-zinc-400">Registra un nuevo punto físico para los rondines.</p>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    <form @submit.prevent="submit" class="p-6 sm:p-8 space-y-6">
                        
                        <!-- Nombre -->
                        <div class="flex flex-col gap-1.5">
                            <label for="name" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Nombre de la Ubicación</label>
                            <input v-model="form.name" type="text" id="name" placeholder="Ej. Casa Club Norte, Alberca, Caseta Principal..." 
                                class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" required />
                            <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Coordenadas -->
                            <div class="flex flex-col gap-1.5">
                                <label for="coordinates" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Coordenadas GPS (Opcional)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="pi pi-map text-zinc-500"></i>
                                    </div>
                                    <input v-model="form.coordinates" type="text" id="coordinates" placeholder="20.659698, -103.349609" 
                                        class="w-full pl-11 bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                </div>
                                <p v-if="form.errors.coordinates" class="text-sm text-red-400">{{ form.errors.coordinates }}</p>
                            </div>

                            <!-- Tag NFC -->
                            <div class="flex flex-col gap-1.5">
                                <label for="tag_nfc_id" class="text-xs font-medium text-zinc-400 uppercase tracking-wider">ID del Tag NFC / Código QR</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="pi pi-qrcode text-zinc-500"></i>
                                    </div>
                                    <input v-model="form.tag_nfc_id" type="text" id="tag_nfc_id" placeholder="Ej. TAG-8839201" 
                                        class="w-full pl-11 bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 font-mono" />
                                </div>
                                <p class="text-xs text-zinc-500">Identificador único que el guardia escaneará.</p>
                                <p v-if="form.errors.tag_nfc_id" class="text-sm text-red-400">{{ form.errors.tag_nfc_id }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-800/60">
                            <Link :href="route('checkpoints.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50 flex items-center">
                                <i class="pi pi-save mr-2"></i> Guardar Punto
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
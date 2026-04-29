<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    checkpoints: Object
});
</script>

<template>
    <AppLayout title="Puntos de Control">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-6 px-4 sm:px-0">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Puntos de Control (Checkpoints)</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Administración de etiquetas NFC/QR para guardias.</p>
                    </div>
                    <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md text-sm font-medium transition-colors">
                        + Nuevo Punto
                    </button>
                </div>

                <div v-if="checkpoints.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <i class="pi pi-map-marker text-4xl text-zinc-400 mb-3"></i>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Sin puntos de control</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Configura las ubicaciones que los guardias deben escanear.</p>
                </div>

                <div v-else class="bg-white dark:bg-zinc-800 shadow-xl sm:rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Nombre de Ubicación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Coordenadas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Tag NFC / QR</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            <tr v-for="cp in checkpoints.data" :key="cp.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-zinc-900 dark:text-white">
                                    {{ cp.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    <i class="pi pi-compass mr-1"></i> {{ cp.coordinates }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 dark:text-indigo-400 font-mono">
                                    {{ cp.tag_nfc_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Editar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div v-if="checkpoints.links && checkpoints.links.length > 3" class="flex justify-center mt-6">
                    <div class="flex gap-1">
                        <Link v-for="(link, k) in checkpoints.links" :key="k" :href="link.url" v-html="link.label" class="px-3 py-1 border rounded text-sm transition-colors" :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-300 dark:border-zinc-600'" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
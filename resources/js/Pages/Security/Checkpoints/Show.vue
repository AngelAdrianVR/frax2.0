<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import moment from 'moment';

defineProps({
    checkpoint: Object
});
</script>

<template>
    <AppLayout title="Detalles del Punto">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado -->
                <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-0">
                    <div class="flex items-center gap-4">
                        <Link :href="route('checkpoints.index')" class="w-10 h-10 bg-white dark:bg-zinc-800 rounded-full flex items-center justify-center shadow-sm border border-zinc-200 dark:border-zinc-700 text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                            <i class="pi pi-arrow-left"></i>
                        </Link>
                        <div>
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Detalle del Punto</h1>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Información e historial de escaneos.</p>
                        </div>
                    </div>
                    <Link :href="route('checkpoints.edit', checkpoint.id)" class="px-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl shadow-sm text-sm font-medium transition-colors inline-flex items-center">
                        <i class="pi pi-pencil mr-2 text-zinc-400"></i> Editar Información
                    </Link>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Tarjeta de Detalles -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white dark:bg-zinc-800 rounded-3xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                    <i class="pi pi-map-marker text-2xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white">{{ checkpoint.name }}</h2>
                                    <p class="text-sm text-zinc-500">Punto de Control Físico</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                                    <span class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1">ID Tag NFC / QR</span>
                                    <div class="flex items-center text-zinc-900 dark:text-white font-mono font-medium">
                                        <i class="pi pi-qrcode mr-2 text-zinc-400"></i>
                                        {{ checkpoint.tag_nfc_id || 'No asignado' }}
                                    </div>
                                </div>

                                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                                    <span class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-1">Coordenadas GPS</span>
                                    <div class="flex items-center text-zinc-900 dark:text-white text-sm">
                                        <i class="pi pi-compass mr-2 text-zinc-400"></i>
                                        {{ checkpoint.coordinates || 'No definidas' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Escaneos -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-zinc-800 rounded-3xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden h-full">
                            <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 flex justify-between items-center">
                                <h3 class="font-bold text-zinc-900 dark:text-white">Últimos Escaneos Registrados</h3>
                                <span class="text-xs font-medium bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 px-2 py-1 rounded-md">10 más recientes</span>
                            </div>
                            
                            <div class="p-6">
                                <!-- Si no hay logs -->
                                <div v-if="!checkpoint.logs || checkpoint.logs.length === 0" class="text-center py-12">
                                    <div class="w-16 h-16 bg-zinc-50 dark:bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-zinc-100 dark:border-zinc-800">
                                        <i class="pi pi-history text-2xl text-zinc-300 dark:text-zinc-600"></i>
                                    </div>
                                    <h4 class="text-zinc-900 dark:text-white font-medium">Sin registros recientes</h4>
                                    <p class="text-sm text-zinc-500 mt-1">Los guardias aún no han escaneado este punto.</p>
                                </div>

                                <!-- Lista de logs (Asumiendo que checkpoint.logs existe) -->
                                <ul v-else class="space-y-4">
                                    <li v-for="log in checkpoint.logs" :key="log.id" class="flex items-start gap-4 p-4 rounded-2xl hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition-colors border border-transparent hover:border-zinc-100 dark:hover:border-zinc-700">
                                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                                            <i class="pi pi-check text-emerald-600 dark:text-emerald-400"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-zinc-900 dark:text-white truncate">
                                                Escaneado exitosamente
                                            </p>
                                            <p class="text-xs text-zinc-500 mt-0.5">
                                                Guardia ID: {{ log.user_id || 'Desconocido' }}
                                            </p>
                                        </div>
                                        <div class="text-right whitespace-nowrap">
                                            <span class="text-xs font-medium text-zinc-500 block">
                                                {{ moment(log.created_at).format('DD/MM/YYYY') }}
                                            </span>
                                            <span class="text-xs text-zinc-400">
                                                {{ moment(log.created_at).format('HH:mm') }} hrs
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

const props = defineProps({
    parcel: Object
});

const toast = useToast();

const getStatusColor = (status) => {
    switch(status) {
        case 'Entregado': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400';
        case 'En Caseta': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'Recibido': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'Devuelto': return 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400';
        case 'Regresado': return 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400';
        default: return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300';
    }
};

const markAsDelivered = () => {
    router.put(route('parcel-services.update', props.parcel.id), {
        status: 'Entregado',
        delivery_date: new Date().toISOString()
    }, {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Entregado', detail: 'Paquete marcado como entregado al residente.', life: 3000 });
        }
    });
};
</script>

<template>
    <AppLayout title="Detalle de Paquete">
        <Toast />
        <div class="py-12 bg-[#F2F2F7] dark:bg-zinc-900 min-h-screen flex items-center justify-center p-4">
            
            <div class="max-w-md w-full">
                <div class="mb-6">
                    <Link :href="route('parcel-services.index')" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline flex items-center">
                        <i class="pi pi-angle-left mr-1"></i> Volver a paquetería
                    </Link>
                </div>

                <!-- Tarjeta Estilo Ticket / Wallet Pass -->
                <div class="bg-white dark:bg-zinc-800 rounded-[2rem] shadow-xl overflow-hidden relative">
                    
                    <!-- Header del Ticket -->
                    <div class="bg-indigo-600 px-8 py-10 text-center relative overflow-hidden">
                        <!-- Círculos de fondo para diseño -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                        
                        <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-4 border border-white/30 shadow-inner">
                            <i class="pi pi-box text-4xl text-white"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">{{ parcel.courier || parcel.name }}</h2>
                        <p class="text-indigo-100 mt-1 font-mono tracking-widest text-sm">{{ parcel.tracking_number || 'SIN GUÍA' }}</p>
                    </div>

                    <!-- Separador dentado -->
                    <div class="relative flex justify-between items-center bg-white dark:bg-zinc-800 h-8 -my-4 z-10">
                        <div class="w-8 h-8 bg-[#F2F2F7] dark:bg-zinc-900 rounded-full -ml-4 shadow-inner"></div>
                        <div class="border-t-2 border-dashed border-zinc-200 dark:border-zinc-700 flex-grow mx-4"></div>
                        <div class="w-8 h-8 bg-[#F2F2F7] dark:bg-zinc-900 rounded-full -mr-4 shadow-inner"></div>
                    </div>

                    <!-- Contenido del Ticket -->
                    <div class="px-8 pt-8 pb-8 space-y-6">
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Estatus Actual</span>
                            <span class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider" :class="getStatusColor(parcel.status)">
                                {{ parcel.status }}
                            </span>
                        </div>

                        <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl p-4 border border-zinc-100 dark:border-zinc-700/50">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase font-semibold mb-1">Destino</p>
                            <p class="text-lg font-bold text-zinc-900 dark:text-white">{{ parcel.private_unit?.name || 'Casa No Asignada' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-semibold mb-1">Fecha Recepción</p>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ parcel.created_at ? new Date(parcel.created_at).toLocaleDateString() : 'N/A' }}</p>
                                <p class="text-xs text-zinc-500">{{ parcel.created_at ? new Date(parcel.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-semibold mb-1">Fecha Entrega</p>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ parcel.delivery_date ? new Date(parcel.delivery_date).toLocaleDateString() : '--/--/----' }}</p>
                                <p class="text-xs text-zinc-500">{{ parcel.delivery_date ? new Date(parcel.delivery_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '' }}</p>
                            </div>
                        </div>

                        <div v-if="parcel.received_by" class="border-t border-zinc-100 dark:border-zinc-700/50 pt-4">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-semibold">Recibido en caseta por:</p>
                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200 mt-1 flex items-center">
                                <i class="pi pi-user text-zinc-400 mr-2 text-xs"></i> {{ parcel.received_by.name }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botón de Acción Principal -->
                <div class="mt-6" v-if="parcel.status === 'En Caseta' || parcel.status === 'Recibido'">
                    <button @click="markAsDelivered" class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 transition-all flex justify-center items-center text-lg">
                        <i class="pi pi-check-circle mr-2"></i>
                        Marcar como Entregado
                    </button>
                    <p class="text-center text-xs text-zinc-500 mt-3">Presiona al entregar el paquete al residente.</p>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
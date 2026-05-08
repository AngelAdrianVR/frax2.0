<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    visit: {
        type: Object,
        required: true
    }
});

const isCopied = ref(false);

const getStatusColor = (status) => {
    switch(status) {
        case 'Ingresado': return 'bg-green-500 text-white';
        case 'Pendiente': return 'bg-amber-500 text-white';
        case 'Cancelado': return 'bg-red-500 text-white';
        default: return 'bg-zinc-500 text-white';
    }
};

const copyLink = () => {
    // Simulación de copiar al portapapeles
    navigator.clipboard.writeText(`https://tudominio.com/pase/${props.visit.qr_code}`);
    isCopied.value = true;
    setTimeout(() => isCopied.value = false, 2000);
};
</script>

<template>
    <AppLayout title="Detalle de Visita">
        <div class="py-8 md:py-12 bg-[#F2F2F7] dark:bg-zinc-900 min-h-screen flex items-center justify-center">
            <div class="w-full max-w-md mx-auto sm:px-6 lg:px-8 px-4">
                
                <!-- Botón de regreso flotante -->
                <div class="mb-6">
                    <Link :href="route('visits.index')" class="inline-flex items-center gap-2 text-sm font-bold text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                        <i class="pi pi-arrow-left"></i> Volver a la Bitácora
                    </Link>
                </div>

                <!-- DIGITAL PASS (Apple Wallet Style) -->
                <div class="relative bg-white dark:bg-zinc-800 shadow-2xl rounded-[2.5rem] overflow-hidden border border-zinc-100 dark:border-zinc-700/50">
                    
                    <!-- Header -->
                    <div class="bg-indigo-600 p-8 text-center text-white relative">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                            <i class="pi pi-id-card text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-black tracking-tight mb-1">Pase de Acceso</h2>
                        <p class="text-indigo-200 text-sm font-medium">{{ visit.private_unit_name || 'Mi Domicilio' }}</p>
                        
                        <!-- Semi-circles for the "ticket" look -->
                        <div class="absolute -bottom-4 -left-4 w-8 h-8 bg-[#F2F2F7] dark:bg-zinc-900 rounded-full"></div>
                        <div class="absolute -bottom-4 -right-4 w-8 h-8 bg-[#F2F2F7] dark:bg-zinc-900 rounded-full"></div>
                    </div>

                    <!-- Dotted Line -->
                    <div class="px-8 flex justify-center -mt-[1px]">
                        <div class="w-full border-t-2 border-dashed border-zinc-200 dark:border-zinc-700"></div>
                    </div>

                    <div class="p-8 pt-10 relative">
                        
                        <!-- Status Badge Absoluto -->
                        <div class="absolute top-4 right-6">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm" :class="getStatusColor(visit.status)">
                                {{ visit.status }}
                            </span>
                        </div>

                        <!-- QR Placeholder Elegante -->
                        <div class="flex flex-col items-center justify-center mb-8">
                            <div class="p-4 bg-white dark:bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.08)]">
                                <!-- En un entorno real aquí iría un componente vue-qrcode o una imagen generada -->
                                <div class="w-48 h-48 bg-zinc-50 border-2 border-zinc-100 rounded-2xl flex items-center justify-center text-zinc-400">
                                    <div class="text-center">
                                        <i class="pi pi-qrcode text-6xl mb-2 text-zinc-900"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-zinc-400 mt-4 uppercase font-bold tracking-widest">Escanear en Caseta</p>
                        </div>

                        <!-- Info Grid -->
                        <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-2xl p-5 space-y-4">
                            <div>
                                <p class="text-[10px] uppercase text-zinc-400 font-bold mb-0.5">Visitante</p>
                                <p class="text-lg font-black text-zinc-900 dark:text-white leading-tight">{{ visit.name }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-3 border-t border-zinc-200 dark:border-zinc-700/50">
                                <div>
                                    <p class="text-[10px] uppercase text-zinc-400 font-bold mb-0.5">Acceso</p>
                                    <p class="text-sm font-bold text-zinc-700 dark:text-zinc-200">
                                        <i :class="visit.access_type === 'Vehicular' ? 'pi pi-car' : 'pi pi-user'" class="mr-1 text-zinc-400"></i>
                                        {{ visit.access_type }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase text-zinc-400 font-bold mb-0.5">Válido hasta</p>
                                    <p class="text-sm font-bold text-zinc-700 dark:text-zinc-200">{{ visit.expiration_date }}</p>
                                </div>
                            </div>
                            
                            <div v-if="visit.reason" class="pt-3 border-t border-zinc-200 dark:border-zinc-700/50">
                                <p class="text-[10px] uppercase text-zinc-400 font-bold mb-0.5">Motivo</p>
                                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ visit.reason }}</p>
                            </div>
                        </div>

                        <!-- Botón de Compartir -->
                        <div class="mt-8">
                            <button @click="copyLink" 
                                class="w-full py-4 rounded-2xl font-bold text-sm shadow-xl transition-all flex items-center justify-center gap-2"
                                :class="isCopied ? 'bg-green-500 text-white shadow-green-500/30' : 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-zinc-900/20'">
                                <i :class="isCopied ? 'pi pi-check' : 'pi pi-share-alt'"></i> 
                                {{ isCopied ? '¡Enlace Copiado!' : 'Compartir Invitación' }}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
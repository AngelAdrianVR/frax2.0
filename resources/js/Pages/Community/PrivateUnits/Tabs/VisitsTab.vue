<script setup>
const props = defineProps({
    unit: Object
});
</script>

<template>
    <div class="space-y-4 animate-fade-in">
        <div class="flex justify-between items-end ml-4 mb-2 pr-4">
            <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider">Últimos Accesos y Visitas</h2>
        </div>
        
        <div v-if="!unit.visits || unit.visits.length === 0" class="text-center py-10 bg-white dark:bg-[#1C1C1E] rounded-[24px] border border-black/5 dark:border-white/5 shadow-sm">
             <i class="pi pi-id-card text-4xl text-gray-300 dark:text-gray-600 mb-3 block"></i>
             <p class="text-gray-500 text-sm">No hay historial de visitas reciente.</p>
        </div>

        <div v-else class="bg-white dark:bg-[#1C1C1E] rounded-[24px] shadow-sm border border-black/5 dark:border-white/5 divide-y divide-gray-100 dark:divide-zinc-800">
            <div v-for="visit in unit.visits" :key="visit.id" class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-[#2C2C2E]/50 transition">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 dark:bg-[#2C2C2E] text-gray-500">
                        <i class="pi" :class="visit.access_type === 'Vehicular' ? 'pi-car' : 'pi-user'"></i>
                    </div>
                    <div>
                        <p class="text-[15px] font-bold text-gray-900 dark:text-white">{{ visit.name }}</p>
                        <p class="text-[12px] text-gray-500">{{ visit.reason || 'Visita General' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="block text-[12px] font-bold" 
                        :class="visit.status === 'Ingresado' ? 'text-green-600' : (visit.status === 'Pendiente' ? 'text-amber-600' : 'text-gray-400')">
                        {{ visit.status }}
                    </span>
                    <span class="text-[10px] text-gray-400 block mt-1">
                        {{ visit.date_of_use || visit.created_at }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
<template>
    <div class="space-y-4">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Últimos Accesos y Visitas</h3>
        
        <div v-if="!unit.visits || unit.visits.length === 0" class="text-center py-8 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300">
             <i class="pi pi-id-card text-3xl text-gray-300 mb-2"></i>
             <p class="text-gray-500 text-sm">No hay historial de visitas reciente.</p>
        </div>

        <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
            <div v-for="visit in unit.visits" :key="visit.id" class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-500">
                        <i class="pi" :class="visit.access_type === 'Vehicular' ? 'pi-car' : 'pi-user'"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ visit.name }}</p>
                        <p class="text-xs text-gray-500">{{ visit.reason || 'Visita General' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="block text-xs font-bold" 
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

<script>
export default {
    props: ['unit']
}
</script>
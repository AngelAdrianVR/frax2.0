<template>
    <div class="space-y-6">
        <!-- Card Detalles Técnicos -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Detalles de la Propiedad</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                        <i class="pi pi-map-marker text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Dirección Completa</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ unit.unit_street }} {{ unit.exterior_number }} {{ unit.int_number ? 'Int ' + unit.int_number : '' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                        <i class="pi pi-home text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Dimensiones</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ unit.square_meters }} m²
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg text-purple-600 dark:text-purple-400">
                        <i class="pi pi-hashtag text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Lote Catastral</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ unit.lot_number }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="p-2 rounded-lg" :class="unit.access_block ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'">
                        <i class="pi" :class="unit.access_block ? 'pi-lock' : 'pi-lock-open'"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Control de Acceso</p>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ unit.access_block ? 'Bloqueado Manualmente' : 'Acceso Permitido' }}
                        </p>
                    </div>
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
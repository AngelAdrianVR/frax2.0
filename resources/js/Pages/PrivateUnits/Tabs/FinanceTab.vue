<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 dark:text-gray-200 text-sm">Historial Reciente</h3>
            <span class="text-xs text-gray-500">Últimos 10 movimientos</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800 border-b dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3">Concepto</th>
                        <th class="px-6 py-3">Vencimiento</th>
                        <th class="px-6 py-3">Monto</th>
                        <th class="px-6 py-3 text-right">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr v-for="fee in unit.generated_fees" :key="fee.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
                            {{ fee.payment_reference }}
                        </td>
                        <td class="px-6 py-3 text-gray-500">
                            {{ fee.expiration_date }}
                        </td>
                        <td class="px-6 py-3 font-bold text-gray-700 dark:text-gray-300">
                            ${{ fee.total_amount }}
                        </td>
                        <td class="px-6 py-3 text-right">
                            <span class="px-2 py-1 rounded text-xs font-bold"
                                :class="{
                                    'bg-green-100 text-green-700': fee.status === 'Pagado',
                                    'bg-red-100 text-red-700': fee.status === 'Atrasada',
                                    'bg-yellow-100 text-yellow-700': fee.status === 'Pendiente' || fee.status === 'Parcial'
                                }">
                                {{ fee.status }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="!unit.generated_fees || unit.generated_fees.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">
                            No hay registros financieros recientes.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: ['unit']
}
</script>
<script>
export default {
    name: 'PricingSection',
    data() {
        return {
            billingCycle: 'monthly' // 'monthly' | 'yearly'
        }
    },
    computed: {
        isMonthly() {
            return this.billingCycle === 'monthly';
        },
        prices() {
            return {
                basic: this.isMonthly ? '99' : '990',
                standard: this.isMonthly ? '249' : '2,490',
                premium: this.isMonthly ? '499' : '4,990',
                period: this.isMonthly ? '/por mes' : '/por año',
                savings: !this.isMonthly
            }
        }
    }
}
</script>

<template>
    <div id="planes" class="bg-gray-50 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            
            <!-- Header Pricing -->
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                <div>
                    <h2 class="text-sm font-bold tracking-widest text-blue-600 uppercase mb-2">Planes Flexibles</h2>
                    <p class="text-4xl md:text-5xl font-bold text-gray-900">Nuestros Planes de Precios.</p>
                </div>
                <div class="flex items-center gap-4">
                    <p class="text-gray-500 max-w-xs text-sm md:text-right">
                        Selecciona el plan que mejor se adapte al tamaño de tu comunidad.
                    </p>
                    <!-- Toggle Mensual/Anual -->
                    <div class="bg-[#0B0F19] p-1 rounded-full flex items-center relative">
                        <button 
                            @click="billingCycle = 'monthly'"
                            class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 relative z-10"
                            :class="isMonthly ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                        >
                            Mensual
                        </button>
                        <button 
                            @click="billingCycle = 'yearly'"
                            class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 relative z-10"
                            :class="!isMonthly ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                        >
                            Anual <span v-if="!isMonthly" class="text-xs ml-1 bg-green-500 text-white px-1 rounded">-20%</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cards Container -->
            <div class="bg-[#0B0F19] rounded-[2.5rem] p-8 md:p-12 transition-all duration-500">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Card 1 -->
                    <div class="bg-transparent border border-gray-800 rounded-3xl p-8 flex flex-col transition-transform hover:-translate-y-1 duration-300">
                        <h3 class="text-2xl font-bold text-white mb-4">Plan Básico</h3>
                        <div class="flex items-baseline mb-4">
                            <span class="text-5xl font-bold text-white">${{ prices.basic }}</span>
                            <span class="text-gray-400 ml-2">{{ prices.period }}</span>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed mb-8">
                            Ideal para pequeños condominios que inician su transformación digital.
                        </p>
                        <div class="mt-auto">
                            <ul class="space-y-4 mb-8">
                                <li class="flex items-center gap-3 text-gray-300 text-sm"><i class="pi pi-angle-double-right text-blue-500"></i> Gestión de Residentes</li>
                                <li class="flex items-center gap-3 text-gray-300 text-sm"><i class="pi pi-angle-double-right text-blue-500"></i> Control de Ingresos/Egresos</li>
                            </ul>
                            <button class="w-full py-4 rounded-full bg-blue-600/20 text-white font-bold border border-blue-600/30 hover:bg-blue-600 transition-colors flex items-center justify-center gap-2 group">
                                Elegir Plan <i class="pi pi-arrow-up-right text-sm group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-transparent border border-gray-800 rounded-3xl p-8 flex flex-col transition-transform hover:-translate-y-1 duration-300">
                        <h3 class="text-2xl font-bold text-white mb-4">Plan Estándar</h3>
                        <div class="flex items-baseline mb-4">
                            <span class="text-5xl font-bold text-white">${{ prices.standard }}</span>
                            <span class="text-gray-400 ml-2">{{ prices.period }}</span>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed mb-8">
                            Para comunidades en crecimiento que requieren control de accesos.
                        </p>
                        <div class="mt-auto">
                            <ul class="space-y-4 mb-8">
                                <li class="flex items-center gap-3 text-gray-300 text-sm"><i class="pi pi-angle-double-right text-blue-500"></i> App Móvil para Residentes</li>
                                <li class="flex items-center gap-3 text-gray-300 text-sm"><i class="pi pi-angle-double-right text-blue-500"></i> Generación de códigos QR</li>
                            </ul>
                            <button class="w-full py-4 rounded-full bg-blue-600/20 text-white font-bold border border-blue-600/30 hover:bg-blue-600 transition-colors flex items-center justify-center gap-2 group">
                                Elegir Plan <i class="pi pi-arrow-up-right text-sm group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-3xl p-8 flex flex-col transform lg:-translate-y-4 shadow-2xl shadow-blue-900/20 transition-transform hover:scale-[1.02] duration-300">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Plan Premium</h3>
                        <div class="flex items-baseline mb-4">
                            <span class="text-5xl font-bold text-gray-900">${{ prices.premium }}</span>
                            <span class="text-gray-500 ml-2">{{ prices.period }}</span>
                        </div>
                        <p class="text-gray-500 text-sm leading-relaxed mb-8">
                            La solución definitiva con integraciones de hardware y soporte VIP.
                        </p>
                        <div class="mt-auto">
                            <ul class="space-y-4 mb-8">
                                <li class="flex items-center gap-3 text-gray-600 text-sm"><i class="pi pi-angle-double-right text-blue-600"></i> Integración LPR (Placas)</li>
                                <li class="flex items-center gap-3 text-gray-600 text-sm"><i class="pi pi-angle-double-right text-blue-600"></i> Pagos en línea integrados</li>
                                <li class="flex items-center gap-3 text-gray-600 text-sm"><i class="pi pi-angle-double-right text-blue-600"></i> Usuarios Ilimitados</li>
                            </ul>
                            <button class="w-full py-4 rounded-full bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 shadow-lg shadow-blue-600/30 group">
                                Elegir Plan <i class="pi pi-arrow-up-right text-sm group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
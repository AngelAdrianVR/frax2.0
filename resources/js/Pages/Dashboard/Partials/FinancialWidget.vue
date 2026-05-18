<script setup>
const props = defineProps({
    data: Object
});

// Formatear a moneda
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
};
</script>

<template>
    <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 dark:from-[#2C2C2E] dark:to-[#1C1C1E] rounded-[24px] p-6 shadow-md text-white relative overflow-hidden">
        <!-- Decoración de fondo -->
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
        
        <div class="flex items-center justify-between mb-6 relative z-10">
            <h3 class="text-lg font-medium text-zinc-100">Salud Financiera</h3>
            <i class="pi pi-wallet text-zinc-300 text-xl"></i>
        </div>

        <div class="mb-6 relative z-10">
            <p class="text-sm text-zinc-400 mb-1">Deuda Total Acumulada</p>
            <h2 class="text-4xl font-bold tracking-tight text-white">{{ formatCurrency(data.total_debt) }}</h2>
        </div>

        <div class="grid grid-cols-2 gap-4 relative z-10">
            <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-2 h-2 rounded-full bg-green-400"></div>
                    <span class="text-xs text-zinc-300">Cobranza</span>
                </div>
                <p class="text-xl font-semibold">{{ data.payment_rate }}%</p>
            </div>
            
            <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-2 h-2 rounded-full bg-red-400"></div>
                    <span class="text-xs text-zinc-300">Morosos</span>
                </div>
                <p class="text-xl font-semibold">{{ data.slow_payers }} <span class="text-sm font-normal text-zinc-400">casas</span></p>
            </div>
        </div>
    </div>
</template>
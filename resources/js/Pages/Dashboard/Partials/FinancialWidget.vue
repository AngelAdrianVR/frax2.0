<script setup>
const props = defineProps({ data: Object });

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <div class="relative bg-zinc-900 dark:bg-zinc-800 rounded-2xl p-5 text-white overflow-hidden shadow-[0_0_0_0.5px_rgba(0,0,0,0.1),0_4px_24px_rgba(0,0,0,0.08)]">
        <!-- Decorative gradient blobs -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-bl from-white/[0.06] to-transparent rounded-full blur-3xl"></div>
        <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-gradient-to-tr from-white/[0.04] to-transparent rounded-full blur-2xl"></div>

        <div class="relative z-10">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-[13px] font-semibold text-zinc-200">Salud Financiera</h3>
                    <p class="text-[11px] text-zinc-400 mt-0.5">Deuda acumulada</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-white/[0.08] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-zinc-300"><path d="M12 7.5a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"/><path fill-rule="evenodd" d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3h17.25c1.035 0 1.875.84 1.875 1.875v9.75c0 1.036-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 011.5 14.625v-9.75zM8.25 9.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM18.75 9a.75.75 0 00-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 00.75-.75V9.75a.75.75 0 00-.75-.75h-.008zM4.5 9.75A.75.75 0 015.25 9h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V9.75z" clip-rule="evenodd"/><path d="M2.25 18a.75.75 0 000 1.5c5.4 0 10.63.722 15.6 2.075 1.19.324 2.4-.558 2.4-1.82V18.75a.75.75 0 00-.75-.75H2.25z"/></svg>
                </div>
            </div>

            <div class="mb-5">
                <p class="text-3xl font-bold tracking-tight text-white tabular-nums">{{ formatCurrency(data.total_debt) }}</p>
                <p class="text-[11px] text-zinc-400 mt-1">MXN · Total acumulado</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white/[0.06] rounded-xl p-3 backdrop-blur-sm">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-[0.05em]">Cobranza</span>
                    </div>
                    <p class="text-xl font-bold text-white tabular-nums">{{ data.payment_rate }}<span class="text-sm font-medium text-zinc-400">%</span></p>
                </div>
                <div class="bg-white/[0.06] rounded-xl p-3 backdrop-blur-sm">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                        <span class="text-[10px] font-semibold text-zinc-400 uppercase tracking-[0.05em]">Morosos</span>
                    </div>
                    <p class="text-xl font-bold text-white tabular-nums">{{ data.slow_payers }} <span class="text-sm font-medium text-zinc-400">casas</span></p>
                </div>
            </div>
        </div>
    </div>
</template>
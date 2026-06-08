<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import OccupancyWidget from '@/Pages/Dashboard/Partials/OccupancyWidget.vue';
import ResourcesWidget from '@/Pages/Dashboard/Partials/ResourcesWidget.vue';
import FinancialWidget from '@/Pages/Dashboard/Partials/FinancialWidget.vue';
import QuickActionsWidget from '@/Pages/Dashboard/Partials/QuickActionsWidget.vue';
import NoticeBoardWidget from '@/Pages/Dashboard/Partials/NoticeBoardWidget.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            units: { total: 120, occupied: 105 },
            residents: { total: 340, active: 310 },
            resources: { vehicles: 185, pets: 60, tags: 250 },
            finances: { payment_rate: 85, total_debt: 24500, slow_payers: 12 },
            notices: []
        })
    }
});

const userName = page.props.auth?.user?.name?.split(' ')[0] || '';
const currentTime = new Date().getHours();
const greeting = currentTime < 12 ? 'Buenos días' : currentTime < 18 ? 'Buenas tardes' : 'Buenas noches';
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="max-w-7xl mx-auto space-y-5">
            
            <!-- ═══ WELCOME HERO ═══ -->
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em] mb-1">
                        {{ new Date().toLocaleDateString('es-MX', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                    </p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">
                        {{ greeting }}, <span class="text-zinc-500 dark:text-zinc-400">{{ userName }}</span>
                    </h1>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-[12px] font-medium text-emerald-700 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Sistema activo
                    </span>
                </div>
            </div>

            <!-- ═══ KPI ROW ═══ -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- KPI Card 1: Ocupación -->
                <div class="group relative bg-white dark:bg-zinc-900 rounded-2xl p-5 border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)] hover:shadow-[0_0_0_0.5px_rgba(0,0,0,0.06),0_4px_16px_rgba(0,0,0,0.04)] transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-zinc-100/80 dark:from-zinc-800/50 to-transparent rounded-bl-[40px] -mr-2 -mt-2"></div>
                    <div class="relative">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded-[10px] bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 text-zinc-500 dark:text-zinc-400"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"/><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"/></svg>
                            </div>
                            <p class="text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em]">Ocupación</p>
                        </div>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight tabular-nums">{{ Math.round((stats.units.occupied / stats.units.total) * 100) }}<span class="text-lg text-zinc-400 dark:text-zinc-500 font-medium">%</span></p>
                        <p class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-1">{{ stats.units.occupied }} de {{ stats.units.total }} unidades</p>
                    </div>
                </div>

                <!-- KPI Card 2: Residentes -->
                <div class="group relative bg-white dark:bg-zinc-900 rounded-2xl p-5 border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)] hover:shadow-[0_0_0_0.5px_rgba(0,0,0,0.06),0_4px_16px_rgba(0,0,0,0.04)] transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-blue-50 dark:from-blue-500/5 to-transparent rounded-bl-[40px] -mr-2 -mt-2"></div>
                    <div class="relative">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded-[10px] bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 text-blue-500"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" /></svg>
                            </div>
                            <p class="text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em]">Residentes</p>
                        </div>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight tabular-nums">{{ stats.residents.total }}</p>
                        <p class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-1">{{ stats.residents.active }} activos</p>
                    </div>
                </div>

                <!-- KPI Card 3: Cobranza -->
                <div class="group relative bg-white dark:bg-zinc-900 rounded-2xl p-5 border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)] hover:shadow-[0_0_0_0.5px_rgba(0,0,0,0.06),0_4px_16px_rgba(0,0,0,0.04)] transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-emerald-50 dark:from-emerald-500/5 to-transparent rounded-bl-[40px] -mr-2 -mt-2"></div>
                    <div class="relative">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded-[10px] bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 text-emerald-500"><path d="M10.464 8.746c.227-.18.497-.311.786-.394v2.795a2.252 2.252 0 01-.786-.393c-.394-.313-.546-.681-.546-1.004 0-.323.152-.691.546-1.004zM12.75 15.662v-2.824c.347.085.664.228.921.421.427.32.579.686.579.991 0 .305-.152.671-.579.991a2.534 2.534 0 01-.921.42z"/><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v.816a3.836 3.836 0 00-1.72.756c-.712.566-1.112 1.35-1.112 2.178 0 .829.4 1.612 1.113 2.178.502.4 1.102.647 1.719.756v2.978a2.536 2.536 0 01-.921-.421l-.879-.66a.75.75 0 00-.9 1.2l.879.66c.533.4 1.169.645 1.821.75V18a.75.75 0 001.5 0v-.81a4.124 4.124 0 001.821-.749c.745-.559 1.179-1.344 1.179-2.191 0-.847-.434-1.632-1.179-2.191a4.122 4.122 0 00-1.821-.75V8.354c.29.082.559.213.786.393l.415.33a.75.75 0 00.933-1.175l-.415-.33a3.836 3.836 0 00-1.719-.755V6z" clip-rule="evenodd" /></svg>
                            </div>
                            <p class="text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em]">Cobranza</p>
                        </div>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight tabular-nums">{{ stats.finances.payment_rate }}<span class="text-lg text-zinc-400 dark:text-zinc-500 font-medium">%</span></p>
                        <p class="text-[12px] text-emerald-600 dark:text-emerald-400 mt-1">{{ stats.finances.slow_payers }} morosos</p>
                    </div>
                </div>

                <!-- KPI Card 4: Accesos -->
                <div class="group relative bg-white dark:bg-zinc-900 rounded-2xl p-5 border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)] hover:shadow-[0_0_0_0.5px_rgba(0,0,0,0.06),0_4px_16px_rgba(0,0,0,0.04)] transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-violet-50 dark:from-violet-500/5 to-transparent rounded-bl-[40px] -mr-2 -mt-2"></div>
                    <div class="relative">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded-[10px] bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 text-violet-500"><path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd" /></svg>
                            </div>
                            <p class="text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em]">Tags Activos</p>
                        </div>
                        <p class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight tabular-nums">{{ stats.resources.tags }}</p>
                        <p class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-1">{{ stats.resources.vehicles }} vehículos</p>
                    </div>
                </div>
            </div>

            <!-- ═══ MAIN GRID ═══ -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                
                <!-- Columna 1: Ocupación + Recursos -->
                <div class="flex flex-col gap-5">
                    <OccupancyWidget :data="stats.units" :residents="stats.residents" />
                    <ResourcesWidget :data="stats.resources" />
                </div>

                <!-- Columna 2: Finanzas + Quick Actions -->
                <div class="flex flex-col gap-5">
                    <FinancialWidget :data="stats.finances" />
                    <QuickActionsWidget />
                </div>

                <!-- Columna 3: Notice Board -->
                <div class="flex flex-col gap-5">
                    <NoticeBoardWidget :notices="stats.notices" />
                </div>

            </div>
        </div>
    </AppLayout>
</template>
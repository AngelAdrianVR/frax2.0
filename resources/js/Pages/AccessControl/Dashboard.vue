<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineProps({
    stats: Object,
    visitasPendientes: Array,
    paquetesEnCaseta: Array,
    rondinesActivos: Array,
    incidenciasAbiertas: Array,
    ultimosAccesos: Array,
});

const activeTab = ref('overview');

const tabs = [
    { key: 'overview', label: 'Resumen', icon: 'pi pi-home' },
    { key: 'visits', label: 'Visitas', icon: 'pi pi-users' },
    { key: 'parcels', label: 'Paquetería', icon: 'pi pi-box' },
    { key: 'patrols', label: 'Rondines', icon: 'pi pi-shield' },
    { key: 'incidents', label: 'Incidencias', icon: 'pi pi-exclamation-triangle' },
];

const statCards = computed(() => [
    { label: 'Visitas Pendientes', value: '{stats.visitasPendientes}', icon: 'pi pi-users', color: 'amber', route: 'visits.index' },
    { label: 'Paquetes en Caseta', value: '{stats.paquetesEnCaseta}', icon: 'pi pi-box', color: 'blue', route: 'parcel-services.index' },
    { label: 'Rondines Activos', value: '{stats.rondinesActivos}', icon: 'pi pi-shield', color: 'green', route: 'patrols.index' },
    { label: 'Incidencias Abiertas', value: '{stats.incidenciasAbiertas}', icon: 'pi pi-exclamation-triangle', color: 'red', route: 'incidents.index' },
]);

const getMovementColor = (mov) => mov === 'Entrada' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400';
const getMovementBg = (mov) => mov === 'Entrada' ? 'bg-emerald-50 dark:bg-emerald-500/10' : 'bg-rose-50 dark:bg-rose-500/10';

const getSeverityClass = (severity) => ({
    'Baja': 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
    'Media': 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
    'Alta': 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400',
    'Critica': 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
}[severity] || 'bg-zinc-100 text-zinc-700');
</script>

<template>
    <AppLayout title="Caseta y Accesos">
        <div class="py-6 md:py-10 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 px-4 sm:px-0 gap-3">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">
                            🛡️ Caseta y Accesos
                        </h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Panel unificado de control de accesos, paquetería, rondines e incidencias.
                        </p>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <Link :href="route('visits.create')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition flex items-center gap-2">
                            <i class="pi pi-plus text-xs"></i> Nueva Visita
                        </Link>
                        <Link :href="route('parcel-services.create')" class="px-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 text-sm font-semibold rounded-xl shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-700 transition flex items-center gap-2">
                            <i class="pi pi-box text-xs"></i> Registrar Paquete
                        </Link>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 px-4 sm:px-0">
                    <Link v-for="card in statCards" :key="card.label" :href="route(card.route)"
                        class="bg-white dark:bg-zinc-900 rounded-2xl p-5 shadow-sm border border-zinc-100 dark:border-zinc-900/50 hover:shadow-md transition-all group">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ card.label }}</span>
                            <div :class="`w-9 h-9 rounded-xl bg-${card.color}-50 dark:bg-${card.color}-500/10 flex items-center justify-center`">
                                <i :class="[card.icon, `text-${card.color}-600 dark:text-${card.color}-400`]"></i>
                            </div>
                        </div>
                        <span class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">{{ stats[Object.keys(stats).find(k => k.toLowerCase().includes(card.label.toLowerCase().split(' ')[0])) || card.label.toLowerCase().replace(' ', '')] || 0 }}</span>
                        <div class="mt-1 text-xs text-zinc-400 group-hover:text-indigo-500 transition-colors">
                            Ver todos <i class="pi pi-arrow-right text-[10px] ml-1"></i>
                        </div>
                    </Link>
                </div>

                <!-- Accesos de Hoy -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 px-4 sm:px-0 mb-8">
                    <!-- Entradas/Salidas -->
                    <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-zinc-100 dark:border-zinc-900/50 overflow-hidden">
                        <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-900/50 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Accesos Recientes</h2>
                            <Link :href="route('access-logs.index')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">Ver bitácora →</Link>
                        </div>
                        <div class="divide-y divide-zinc-50 dark:divide-zinc-700/30 max-h-[480px] overflow-y-auto">
                            <div v-for="log in ultimosAccesos" :key="log.id" class="px-6 py-3.5 flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-700/20 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div :class="['w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold shrink-0', getMovementBg(log.movimiento)]">
                                        <i :class="log.movimiento === 'Entrada' ? 'pi pi-sign-in' : 'pi pi-sign-out'"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ log.identificador }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ log.unidad }} · {{ log.metodo }}</p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0 ml-4">
                                    <span :class="['text-xs font-bold', getMovementColor(log.movimiento)]">{{ log.movimiento }}</span>
                                    <p class="text-[11px] text-zinc-400 mt-0.5">{{ log.fechaHora }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen Rápido -->
                    <div class="space-y-4">
                        <!-- Rondines Activos -->
                        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-900/50 p-5">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                                <i class="pi pi-shield text-green-500"></i> Rondines Activos
                            </h3>
                            <div v-if="rondinesActivos.length === 0" class="text-center py-3">
                                <p class="text-xs text-zinc-400">Sin rondines activos</p>
                            </div>
                            <div v-for="r in rondinesActivos" :key="r.id" class="flex justify-between items-center py-2 border-b border-zinc-50 dark:border-zinc-700/30 last:border-0">
                                <div>
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ r.guardia }}</p>
                                    <p class="text-xs text-zinc-500">{{ r.puntos }} pts · Inicio {{ r.inicio }}</p>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            </div>
                        </div>

                        <!-- Incidencias Prioritarias -->
                        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-900/50 p-5">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white mb-3 flex items-center gap-2">
                                <i class="pi pi-exclamation-triangle text-red-500"></i> Incidencias Abiertas
                            </h3>
                            <div v-if="incidenciasAbiertas.length === 0" class="text-center py-3">
                                <p class="text-xs text-zinc-400">Sin incidencias pendientes</p>
                            </div>
                            <div v-for="inc in incidenciasAbiertas.slice(0, 5)" :key="inc.id" class="py-2 border-b border-zinc-50 dark:border-zinc-700/30 last:border-0">
                                <div class="flex items-center gap-2">
                                    <span :class="['px-2 py-0.5 text-[10px] font-bold rounded-full', getSeverityClass(inc.severidad)]">{{ inc.severidad }}</span>
                                    <span class="text-xs text-zinc-500">{{ inc.tipo }}</span>
                                </div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white mt-1 truncate">{{ inc.titulo }}</p>
                                <p class="text-[11px] text-zinc-400">{{ inc.guardia }} · {{ inc.hace }}</p>
                            </div>
                            <Link :href="route('incidents.index')" class="block text-center text-xs font-semibold text-indigo-600 mt-3 hover:text-indigo-500">Ver todas →</Link>
                        </div>
                    </div>
                </div>

                <!-- Secciones Rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-4 sm:px-0">
                    <!-- Visitas Pendientes -->
                    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-900/50 p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Visitas Pendientes Hoy</h3>
                            <Link :href="route('visits.index')" class="text-xs text-indigo-600 font-semibold">Todas →</Link>
                        </div>
                        <div v-if="visitasPendientes.length === 0" class="text-center py-6">
                            <i class="pi pi-users text-3xl text-zinc-300 dark:text-zinc-600 mb-2"></i>
                            <p class="text-sm text-zinc-500">Sin visitas pendientes</p>
                        </div>
                        <div v-for="v in visitasPendientes.slice(0, 5)" :key="v.id" class="flex items-center justify-between py-3 border-b border-zinc-50 dark:border-zinc-900/30 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 font-bold text-sm">
                                    {{ v.nombre.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ v.nombre }}</p>
                                    <p class="text-xs text-zinc-500">{{ v.unidad }} · {{ v.tipoAcceso }}</p>
                                </div>
                            </div>
                            <Link :href="route('visits.show', v.id)" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 px-2 py-1 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg">
                                Ver QR
                            </Link>
                        </div>
                    </div>

                    <!-- Paquetes en Caseta -->
                    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-900/50 p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Paquetes en Caseta</h3>
                            <Link :href="route('parcel-services.index')" class="text-xs text-indigo-600 font-semibold">Todos →</Link>
                        </div>
                        <div v-if="paquetesEnCaseta.length === 0" class="text-center py-6">
                            <i class="pi pi-box text-3xl text-zinc-300 dark:text-zinc-600 mb-2"></i>
                            <p class="text-sm text-zinc-500">Sin paquetes pendientes</p>
                        </div>
                        <div v-for="p in paquetesEnCaseta.slice(0, 5)" :key="p.id" class="flex items-center justify-between py-3 border-b border-zinc-50 dark:border-zinc-900/30 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600">
                                    <i class="pi pi-box text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ p.mensajeria }}</p>
                                    <p class="text-xs text-zinc-500">{{ p.unidad }} · {{ p.diasEnCaseta }} días</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-2.5 py-1 rounded-full">
                                {{ p.status }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

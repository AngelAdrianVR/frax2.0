<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

// Importaciones con rutas absolutas usando el alias @/ para evitar errores
import OccupancyWidget from '@/Pages/Dashboard/Partials/OccupancyWidget.vue';
import ResourcesWidget from '@/Pages/Dashboard/Partials/ResourcesWidget.vue';
import FinancialWidget from '@/Pages/Dashboard/Partials/FinancialWidget.vue';
import QuickActionsWidget from '@/Pages/Dashboard/Partials/QuickActionsWidget.vue';
import NoticeBoardWidget from '@/Pages/Dashboard/Partials/NoticeBoardWidget.vue';

// Aquí recibimos los datos desde el DashboardController (que crearemos después)
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
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            
            <!-- Encabezado de Bienvenida -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white tracking-tight">
                    Resumen de la Comunidad
                </h1>
                <p class="text-zinc-500 dark:text-zinc-400 mt-1">
                    Aquí tienes el panorama general de tu fraccionamiento al día de hoy.
                </p>
            </div>

            <!-- Grid Principal -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Columna Izquierda: Ocupación y Recursos -->
                <div class="flex flex-col gap-6">
                    <OccupancyWidget :data="stats.units" :residents="stats.residents" />
                    <ResourcesWidget :data="stats.resources" />
                </div>

                <!-- Columna Central: Finanzas (Módulo de Unidades) -->
                <div class="flex flex-col gap-6">
                    <FinancialWidget :data="stats.finances" />
                    <QuickActionsWidget />
                </div>

                <!-- Columna Derecha: Muro de Avisos y Actividad -->
                <div class="flex flex-col gap-6">
                    <NoticeBoardWidget :notices="stats.notices" />
                </div>

            </div>
        </div>
    </AppLayout>
</template>
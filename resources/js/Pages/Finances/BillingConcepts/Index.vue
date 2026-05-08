<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';

const props = defineProps({
    concepts: Array
});

const toast = useToast();
const confirm = useConfirm();

const formatCurrency = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val);

const deleteConcept = (id) => {
    confirm.require({
        message: '¿Estás seguro de que deseas eliminar este concepto de cobro? Esto no afectará los cobros ya generados, pero no se podrá usar para nuevos cobros.',
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Sí, eliminar',
        rejectLabel: 'Cancelar',
        accept: () => {
            router.delete(route('billing-concepts.destroy', id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Eliminado', detail: 'Concepto eliminado correctamente', life: 3000 });
                }
            });
        }
    });
};
</script>

<template>
    <AppLayout title="Conceptos de Cobro">
        <Toast />
        <ConfirmDialog />

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Header Estilo iOS -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 px-4 sm:px-0 gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Conceptos de Cobro</h1>
                        <p class="text-zinc-500 dark:text-zinc-400 mt-1">Configuración del catálogo de cuotas y mantenimiento.</p>
                    </div>
                    <Link :href="route('billing-concepts.create')" class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 font-semibold py-2.5 px-5 rounded-xl shadow-sm transition-all text-sm flex items-center gap-2">
                        <i class="pi pi-plus"></i> Nuevo Concepto
                    </Link>
                </div>

                <!-- Grid de Tarjetas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="concept in concepts" :key="concept.id" class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6 flex flex-col justify-between transition-all hover:shadow-md relative overflow-hidden">
                        
                        <!-- Barra de Acento -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>

                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-bold text-zinc-800 dark:text-white leading-tight pr-4">{{ concept.name }}</h3>
                                <span class="text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 px-2.5 py-1 rounded-full whitespace-nowrap">
                                    {{ concept.recurrence_type }}
                                </span>
                            </div>

                            <div class="mb-5 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl p-4">
                                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Monto Base</p>
                                <p class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">{{ formatCurrency(concept.base_amount) }}</p>
                            </div>

                            <div class="flex items-center text-sm font-medium">
                                <span v-if="concept.slow_payers_apply" class="flex items-center text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-400/10 px-3 py-1.5 rounded-lg">
                                    <i class="pi pi-exclamation-circle mr-2 text-xs"></i> Aplica Morosidad
                                </span>
                                <span v-else class="flex items-center text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-400/10 px-3 py-1.5 rounded-lg">
                                    <i class="pi pi-check-circle mr-2 text-xs"></i> Sin recargos
                                </span>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-2">
                            <Link :href="route('billing-concepts.edit', concept.id)" class="text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                <i class="pi pi-pencil"></i>
                            </Link>
                            <button @click="deleteConcept(concept.id)" class="text-zinc-500 hover:text-red-600 dark:hover:text-red-400 p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                                <i class="pi pi-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Estado Vacío -->
                    <div v-if="concepts.length === 0" class="col-span-full bg-white dark:bg-zinc-900 rounded-3xl border border-dashed border-zinc-300 dark:border-zinc-700 p-12 text-center">
                        <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="pi pi-receipt text-2xl text-zinc-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">No hay conceptos de cobro</h3>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-6 max-w-md mx-auto">Comienza agregando las cuotas de mantenimiento, pagos extraordinarios o rentas de amenidades.</p>
                        <Link :href="route('billing-concepts.create')" class="bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-semibold py-2.5 px-6 rounded-xl shadow-sm hover:opacity-90 transition-opacity inline-flex items-center">
                            Crear mi primer concepto
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
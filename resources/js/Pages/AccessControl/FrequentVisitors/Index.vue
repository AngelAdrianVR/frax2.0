<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';

defineProps({
    visitors: Object,
    filters: Object,
});

const searchQuery = ref('');

const confirm = useConfirm();
const toast = useToast();

const getAccessIcon = (tipo) => tipo === 'Vehicular' ? 'pi pi-car' : 'pi pi-user';
const getAccessLabel = (tipo) => tipo === 'Vehicular' ? 'Vehicular' : 'Peatonal';
const getAccessColor = (tipo) => tipo === 'Vehicular'
    ? 'bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-400'
    : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400';

const deleteVisitor = (id) => {
    confirm.require({
        message: '¿Estás seguro de eliminar este visitante frecuente? Ya no aparecerá en la lista de accesos rápidos.',
        header: 'Eliminar Visitante Frecuente',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Sí, eliminar',
        rejectLabel: 'Cancelar',
        accept: () => {
            router.delete(route('frequent-visitors.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Eliminado', detail: 'Visitante frecuente eliminado.', life: 3000 });
                }
            });
        }
    });
};
</script>

<template>
    <AppLayout title="Visitantes Frecuentes">
        <Toast />
        <ConfirmDialog />

        <div class="py-8 md:py-12 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 px-4 sm:px-0 gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Visitantes Frecuentes</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Catálogo de proveedores, trabajadores y visitas recurrentes con acceso permanente.</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <i class="pi pi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-zinc-400"></i>
                            <input v-model="searchQuery" type="text" placeholder="Buscar por nombre o alias..."
                                class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-zinc-800 border-none rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-indigo-500 dark:text-white transition-shadow" />
                        </div>
                        <Link :href="route('frequent-visitors.create')" class="shrink-0 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md text-sm font-semibold transition-all flex items-center gap-2">
                            <i class="pi pi-plus text-xs"></i> <span class="hidden sm:inline">Nuevo Visitante</span>
                        </Link>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="visitors.data.length === 0" class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm p-12 text-center border border-zinc-100 dark:border-zinc-900/50 mx-4 sm:mx-0">
                    <div class="w-20 h-20 bg-zinc-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="pi pi-id-card text-4xl text-zinc-300 dark:text-zinc-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Sin visitantes frecuentes</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 mb-6 max-w-md mx-auto">
                        Registra jardineros, repartidores, empleadas domésticas o proveedores de obra para agilizar su acceso diario sin generar invitaciones cada vez.
                    </p>
                    <Link :href="route('frequent-visitors.create')" class="px-6 py-2.5 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl font-semibold text-sm transition-colors inline-flex items-center gap-2">
                        <i class="pi pi-plus text-xs"></i> Registrar primer visitante
                    </Link>
                </div>

                <!-- Desktop Table -->
                <div v-else class="bg-white dark:bg-zinc-900 shadow-sm sm:rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-900/50">
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-700/50">
                            <thead class="bg-zinc-50/50 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Visitante</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Razón / Motivo</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acceso</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Unidad</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                                <tr v-for="visitor in visitors.data" :key="visitor.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/20 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-sm">
                                                {{ visitor.nombre.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ visitor.nombre }}</div>
                                                <div v-if="visitor.placa" class="text-xs text-zinc-500 font-mono">{{ visitor.placa }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-zinc-600 dark:text-zinc-300 line-clamp-1 max-w-[200px]">{{ visitor.razon || 'Sin motivo definido' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold rounded-full', getAccessColor(visitor.tipoAcceso)]">
                                            <i :class="getAccessIcon(visitor.tipoAcceso)"></i> {{ getAccessLabel(visitor.tipoAcceso) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        🏠 {{ visitor.unidad }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <Link :href="route('frequent-visitors.edit', visitor.id)" class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:bg-amber-100 hover:text-amber-600 transition-colors">
                                                <i class="pi pi-pencil text-xs"></i>
                                            </Link>
                                            <button @click="deleteVisitor(visitor.id)" class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:bg-red-100 hover:text-red-600 transition-colors">
                                                <i class="pi pi-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden divide-y divide-zinc-100 dark:divide-zinc-700/50">
                        <div v-for="visitor in visitors.data" :key="visitor.id" class="p-5 hover:bg-zinc-50 dark:hover:bg-zinc-700/20 active:bg-zinc-100 transition-colors">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ visitor.nombre.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white leading-tight">{{ visitor.nombre }}</h4>
                                        <span :class="['inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full mt-1', getAccessColor(visitor.tipoAcceso)]">
                                            <i :class="getAccessIcon(visitor.tipoAcceso)"></i> {{ getAccessLabel(visitor.tipoAcceso) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 mb-1">{{ visitor.razon || 'Sin motivo' }}</p>
                            <div class="flex justify-between items-center pt-3 border-t border-zinc-100 dark:border-zinc-700/50">
                                <span class="text-xs text-zinc-500 font-medium">🏠 {{ visitor.unidad }}</span>
                                <div class="flex gap-2">
                                    <Link :href="route('frequent-visitors.edit', visitor.id)" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-700 rounded-lg text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                                        Editar
                                    </Link>
                                    <button @click="deleteVisitor(visitor.id)" class="px-3 py-1.5 bg-red-50 dark:bg-red-500/10 rounded-lg text-xs font-semibold text-red-600 dark:text-red-400">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="visitors.links && visitors.links.length > 3" class="flex justify-center mt-8">
                    <div class="flex gap-1.5 bg-white dark:bg-zinc-800 p-1.5 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-700/50">
                        <Link v-for="(link, k) in visitors.links" :key="k" :href="link.url" v-html="link.label"
                            class="min-w-[32px] h-8 flex items-center justify-center rounded-xl text-sm font-medium transition-colors px-2"
                            :class="link.active ? 'bg-indigo-600 text-white shadow-md' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700'" />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

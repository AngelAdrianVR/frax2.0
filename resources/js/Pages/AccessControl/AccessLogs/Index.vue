<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AccessLogForm from '@/Pages/AccessControl/AccessLogs/Partials/AccessLogForm.vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    logs: Object,
    filters: Object,
    privateUnits: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
});

const confirm = useConfirm();
const toast = useToast();

// ── Filtros ──
const searchQuery = ref(props.filters?.search || '');
const selectedMovement = ref(props.filters?.movement_type || '');
const selectedCategory = ref(props.filters?.access_category || '');
const soloDentro = ref(props.filters?.solo_dentro || false);

const applyFilters = () => {
    router.get(route('access-logs.index'), {
        search: searchQuery.value || undefined,
        movement_type: selectedMovement.value || undefined,
        access_category: selectedCategory.value || undefined,
        solo_dentro: soloDentro.value || undefined,
    }, { preserveState: true, preserveScroll: true, replace: true });
};

let filterTimeout;
watch([searchQuery, selectedMovement, selectedCategory, soloDentro], () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(applyFilters, 300);
});

// ── Modal ──
const showModal = ref(false);
const editingLog = ref(null);

const openCreateModal = () => {
    editingLog.value = null;
    showModal.value = true;
};

const openEditModal = (log) => {
    editingLog.value = log;
    showModal.value = true;
};

const onFormSubmitted = () => {
    showModal.value = false;
    toast.add({ severity: 'success', summary: editingLog.value ? 'Actualizado' : 'Registrado', detail: editingLog.value ? 'Registro modificado correctamente.' : 'Entrada registrada en la bitácora.', life: 3000 });
};

const onFormCancel = () => {
    showModal.value = false;
};

// ── Checkout ──
const doCheckout = (log) => {
    confirm.require({
        message: `¿Confirmas la salida de "${log.identificador}"?`,
        header: 'Registrar Salida',
        icon: 'pi pi-sign-out',
        acceptLabel: 'Sí, registrar',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.post(route('access-logs.checkout', log.id), {}, {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Salida registrada', detail: `${log.identificador} ha salido del fraccionamiento.`, life: 3000 });
                },
            });
        },
    });
};

// ── Eliminar ──
const confirmDelete = (log) => {
    confirm.require({
        message: `¿Eliminar el registro de "${log.identificador}"?`,
        header: 'Eliminar Registro',
        icon: 'pi pi-trash',
        acceptLabel: 'Eliminar',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('access-logs.destroy', log.id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Eliminado', detail: 'Registro eliminado.', life: 3000 });
                },
            });
        },
    });
};

// ── Badges ──
const movementBadge = (mov) => mov === 'Entrada'
    ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-500/20'
    : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200/50 dark:border-rose-500/20';

const categoryBadge = (cat) => {
    const map = {
        Visita: 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400',
        Proveedor: 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400',
        Contratista: 'bg-violet-50 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400',
        Conductor: 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400',
        Delivery: 'bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400',
        Otro: 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400',
    };
    return map[cat] || map.Otro;
};

const movementOptions = [
    { label: 'Todos', value: '' },
    { label: 'Entrada', value: 'Entrada' },
    { label: 'Salida', value: 'Salida' },
];

const categoryFilterOptions = [
    { label: 'Todas', value: '' },
    ...props.categories.map(c => ({ label: c, value: c })),
];

const methodIcon = (m) => ({ QR: 'pi pi-qrcode', RFID: 'pi pi-wifi', Manual: 'pi pi-user-edit', Biometrico: 'pi pi-fingerprint' }[m] || 'pi pi-circle');
const formatPlate = (p) => p ? p.toUpperCase() : '';
</script>

<template>
    <AppLayout title="Bitácora de Accesos">
        <Head title="Bitácora de Accesos" />

        <div class="max-w-7xl mx-auto space-y-5">
            
            <!-- ═══ HEADER ═══ -->
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <p class="text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em] mb-1">Accesos y Seguridad</p>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Bitácora de Accesos</h1>
                    <p class="text-[13px] text-zinc-500 dark:text-zinc-400 mt-0.5">Registro universal de entradas y salidas del fraccionamiento.</p>
                </div>
                <Button 
                    @click="openCreateModal"
                    icon="pi pi-plus" 
                    label="Nuevo Acceso" 
                    class="p-button-sm !rounded-xl !shadow-none !bg-zinc-900 dark:!bg-white !text-white dark:!text-zinc-900 !border-0 hover:!bg-zinc-800 dark:hover:!bg-zinc-200 !px-4 !py-2.5 !text-[13px] !font-semibold"
                />
            </div>

            <!-- ═══ FILTERS BAR ═══ -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-3 border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)]">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">
                            <i class="pi pi-search text-sm"></i>
                        </span>
                        <InputText 
                            v-model="searchQuery" 
                            placeholder="Buscar por nombre, placa, identificación..." 
                            class="w-full !pl-9 !py-2 !text-[13px] !rounded-xl !bg-zinc-50 dark:!bg-zinc-800 !border-zinc-200 dark:!border-zinc-700 !text-zinc-800 dark:!text-zinc-200"
                        />
                    </div>
                    <Select 
                        v-model="selectedMovement" 
                        :options="movementOptions" 
                        optionLabel="label" optionValue="value"
                        placeholder="Movimiento" 
                        class="w-full sm:w-36 !text-[13px] !rounded-xl" 
                    />
                    <Select 
                        v-model="selectedCategory" 
                        :options="categoryFilterOptions" 
                        optionLabel="label" optionValue="value"
                        placeholder="Categoría" 
                        class="w-full sm:w-40 !text-[13px] !rounded-xl" 
                    />
                    <button 
                        @click="soloDentro = !soloDentro"
                        :class="[
                            'flex items-center gap-2 px-3 py-2 rounded-xl text-[13px] font-medium border transition-all flex-shrink-0',
                            soloDentro 
                                ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400' 
                                : 'bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700'
                        ]"
                    >
                        <span :class="['w-1.5 h-1.5 rounded-full', soloDentro ? 'bg-emerald-500' : 'bg-zinc-300 dark:bg-zinc-600']"></span>
                        Dentro
                    </button>
                </div>
            </div>

            <!-- ═══ EMPTY STATE ═══ -->
            <div v-if="logs.data.length === 0" class="bg-white dark:bg-zinc-900 rounded-2xl p-16 text-center border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)]">
                <div class="w-16 h-16 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-4">
                    <i class="pi pi-shield text-2xl text-zinc-300 dark:text-zinc-600"></i>
                </div>
                <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-100">Sin registros de acceso</h3>
                <p class="text-[13px] text-zinc-500 dark:text-zinc-400 mt-1 mb-6">Aún no se ha registrado ninguna entrada o salida.</p>
                <Button @click="openCreateModal" label="Registrar primer acceso" icon="pi pi-plus" class="p-button-sm !rounded-xl !text-[13px]" />
            </div>

            <!-- ═══ DESKTOP TABLE ═══ -->
            <div v-else class="bg-white dark:bg-zinc-900 rounded-2xl border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800/50">
                        <thead>
                            <tr class="bg-zinc-50/50 dark:bg-zinc-800/30">
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em]">Visitante</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em]">Movimiento</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em]">Categoría</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em]">Unidad</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em]">Fecha / Hora</th>
                                <th class="px-5 py-3.5 text-right text-[11px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em]">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                            <tr v-for="log in logs.data" :key="log.id" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors group">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div :class="['w-8 h-8 rounded-[10px] flex items-center justify-center flex-shrink-0', log.esVehicular ? 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400']">
                                            <i :class="[log.esVehicular ? 'pi pi-car' : 'pi pi-user', 'text-sm']"></i>
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-semibold text-zinc-800 dark:text-zinc-200 truncate max-w-[180px]">{{ log.identificador }}</p>
                                            <p v-if="log.vehicle_plate" class="text-[11px] font-mono font-medium text-zinc-400 dark:text-zinc-500">{{ formatPlate(log.vehicle_plate) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span :class="['inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg', movementBadge(log.movimiento)]">
                                        <span :class="['w-1.5 h-1.5 rounded-full', log.movimiento === 'Entrada' ? 'bg-emerald-500' : 'bg-rose-500']"></span>
                                        {{ log.movimiento }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span v-if="log.access_category" :class="['inline-flex px-2 py-0.5 text-[11px] font-medium rounded-md', categoryBadge(log.access_category)]">{{ log.categoryLabel }}</span>
                                    <span v-else class="text-[12px] text-zinc-400">—</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <Link v-if="log.private_unit_id" :href="route('admin.private-units.show', log.private_unit_id)" class="text-[13px] font-medium text-blue-600 dark:text-blue-400 hover:underline">#{{ log.unidad }}</Link>
                                    <span v-else class="text-[12px] text-zinc-400">—</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div>
                                        <p class="text-[13px] font-medium text-zinc-700 dark:text-zinc-300 font-mono">{{ log.fechaHora }}</p>
                                        <p v-if="log.exit_time" class="text-[11px] text-zinc-400 dark:text-zinc-500 font-mono">→ {{ log.exit_time }}</p>
                                        <p v-if="log.sigueDentro" class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">Activo</p>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button v-if="log.sigueDentro" @click="doCheckout(log)" v-tooltip.top="'Registrar Salida'" class="w-7 h-7 flex items-center justify-center rounded-lg text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-all">
                                            <i class="pi pi-sign-out text-sm"></i>
                                        </button>
                                        <Link :href="route('access-logs.show', log.id)" v-tooltip.top="'Ver detalle'" class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all">
                                            <i class="pi pi-eye text-sm"></i>
                                        </Link>
                                        <button @click="openEditModal(log)" v-tooltip.top="'Editar'" class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-all">
                                            <i class="pi pi-pencil text-sm"></i>
                                        </button>
                                        <button @click="confirmDelete(log)" v-tooltip.top="'Eliminar'" class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all">
                                            <i class="pi pi-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ═══ MOBILE CARDS ═══ -->
                <div class="md:hidden divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    <div v-for="log in logs.data" :key="log.id" class="p-4 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div :class="['w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0', log.esVehicular ? 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500']">
                                    <i :class="[log.esVehicular ? 'pi pi-car' : 'pi pi-user', 'text-sm']"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-semibold text-zinc-800 dark:text-zinc-200 truncate">{{ log.identificador }}</p>
                                    <p v-if="log.vehicle_plate" class="text-[11px] font-mono text-zinc-400 dark:text-zinc-500">{{ formatPlate(log.vehicle_plate) }}</p>
                                </div>
                            </div>
                            <span :class="['inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold rounded-lg flex-shrink-0', movementBadge(log.movimiento)]">{{ log.movimiento }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span v-if="log.access_category" :class="['inline-flex px-2 py-0.5 text-[10px] font-medium rounded-md', categoryBadge(log.access_category)]">{{ log.categoryLabel }}</span>
                            <span class="inline-flex items-center gap-1 text-[11px] text-zinc-500 dark:text-zinc-400"><i :class="[methodIcon(log.metodo), 'text-xs']"></i> {{ log.metodo }}</span>
                            <span v-if="log.sigueDentro" class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Dentro</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800/50">
                            <div>
                                <p class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400">{{ log.fechaHora }}</p>
                                <p v-if="log.unidad !== 'N/A'" class="text-[11px] text-zinc-400 dark:text-zinc-500">Unidad #{{ log.unidad }}</p>
                            </div>
                            <div class="flex items-center gap-0.5">
                                <button v-if="log.sigueDentro" @click="doCheckout(log)" class="w-7 h-7 flex items-center justify-center rounded-lg text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-500/10"><i class="pi pi-sign-out text-sm"></i></button>
                                <Link :href="route('access-logs.show', log.id)" class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400"><i class="pi pi-eye text-sm"></i></Link>
                                <button @click="openEditModal(log)" class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400"><i class="pi pi-pencil text-sm"></i></button>
                                <button @click="confirmDelete(log)" class="w-7 h-7 flex items-center justify-center rounded-lg text-zinc-400"><i class="pi pi-trash text-sm"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ PAGINATION ═══ -->
            <div v-if="logs.links && logs.links.length > 3" class="flex justify-center">
                <div class="flex gap-1 bg-white dark:bg-zinc-900 p-1.5 rounded-2xl border border-black/[0.03] dark:border-white/[0.04] shadow-sm">
                    <Link v-for="(link, k) in logs.links" :key="k" :href="link.url" v-html="link.label"
                        class="min-w-[34px] h-8 flex items-center justify-center rounded-xl text-[13px] font-medium transition-all px-2"
                        :class="link.active ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'" />
                </div>
            </div>

            <!-- ═══ MODAL: CREAR / EDITAR ACCESO ═══ -->
            <Dialog 
                v-model:visible="showModal" 
                :header="editingLog ? 'Editar Registro de Acceso' : 'Nuevo Registro de Acceso'"
                :modal="true"
                :style="{ width: '680px' }"
                :pt="{
                    root: { class: '!rounded-2xl !shadow-[0_20px_60px_rgba(0,0,0,0.12)] dark:!bg-zinc-900' },
                    header: { class: '!px-6 !pt-6 !pb-3 !text-base !font-bold !text-zinc-800 dark:!text-zinc-100 !bg-transparent' },
                    content: { class: '!px-6 !pb-6 !bg-transparent' },
                    mask: { class: '!bg-black/20 !backdrop-blur-sm' }
                }"
            >
                <AccessLogForm
                    :accessLog="editingLog"
                    :privateUnits="privateUnits"
                    :categories="categories"
                    @submitted="onFormSubmitted"
                    @cancel="onFormCancel"
                />
            </Dialog>

            <ConfirmDialog :pt="{ root: { class: '!rounded-2xl !shadow-[0_20px_60px_rgba(0,0,0,0.12)]' }, acceptButton: { class: '!rounded-xl !text-[13px] !font-semibold' }, rejectButton: { class: '!rounded-xl !text-[13px] !font-medium' } }" />
            <Toast position="bottom-right" :pt="{ root: { class: '!rounded-2xl' }, message: { class: '!rounded-xl' } }" />
        </div>
    </AppLayout>
</template>

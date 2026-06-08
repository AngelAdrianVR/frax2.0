<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AccessLogForm from '@/Pages/AccessControl/AccessLogs/Partials/AccessLogForm.vue';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import Button from 'primevue/button';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    accessLog: Object,
    privateUnits: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
});

const confirm = useConfirm();
const toast = useToast();

const showEditModal = ref(false);

const doCheckout = () => {
    confirm.require({
        message: `¿Confirmas la salida de "${props.accessLog.identificador}"?`,
        header: 'Registrar Salida',
        icon: 'pi pi-sign-out',
        acceptLabel: 'Sí, registrar',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.post(route('access-logs.checkout', props.accessLog.id), {}, {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Salida registrada', detail: `${props.accessLog.identificador} ha salido.`, life: 3000 });
                    // Recargar para reflejar el cambio de estado
                    router.reload();
                },
            });
        },
    });
};

const onFormSubmitted = () => {
    showEditModal.value = false;
    toast.add({ severity: 'success', summary: 'Actualizado', detail: 'Registro modificado correctamente.', life: 3000 });
    router.reload();
};

const confirmDelete = () => {
    confirm.require({
        message: `¿Eliminar el registro de "${props.accessLog.identificador}"?`,
        header: 'Eliminar Registro',
        icon: 'pi pi-trash',
        acceptLabel: 'Eliminar',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('access-logs.destroy', props.accessLog.id), {
                onSuccess: () => router.visit(route('access-logs.index')),
            });
        },
    });
};

const movementBadge = props.accessLog.movimiento === 'Entrada'
    ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-500/20'
    : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200/50 dark:border-rose-500/20';

const categoryBadge = () => {
    const map = {
        Visita: 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400',
        Proveedor: 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400',
        Contratista: 'bg-violet-50 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400',
        Conductor: 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-700 dark:text-cyan-400',
        Delivery: 'bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400',
        Otro: 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400',
    };
    return map[props.accessLog.access_category] || map.Otro;
};
</script>

<template>
    <AppLayout title="Detalle de Acceso">
        <Head title="Detalle de Acceso" />

        <div class="max-w-3xl mx-auto space-y-5">
            
            <!-- Back -->
            <Link :href="route('access-logs.index')" class="inline-flex items-center gap-1.5 text-[13px] font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors">
                <i class="pi pi-arrow-left text-xs"></i> Volver a la bitácora
            </Link>

            <!-- Main Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_4px_16px_rgba(0,0,0,0.03)] overflow-hidden">
                
                <!-- Header -->
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800/50">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0', accessLog.esVehicular ? 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500']">
                                <i :class="[accessLog.esVehicular ? 'pi pi-car' : 'pi pi-user', 'text-xl']"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-zinc-900 dark:text-white">{{ accessLog.identificador }}</h1>
                                <div class="flex items-center gap-2 mt-1">
                                    <span :class="['inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-semibold rounded-lg border', movementBadge]">
                                        <span :class="['w-1.5 h-1.5 rounded-full', accessLog.movimiento === 'Entrada' ? 'bg-emerald-500' : 'bg-rose-500']"></span>
                                        {{ accessLog.movimiento }}
                                    </span>
                                    <span v-if="accessLog.access_category" :class="['inline-flex px-2 py-0.5 text-[11px] font-medium rounded-md', categoryBadge()]">
                                        {{ accessLog.categoryLabel }}
                                    </span>
                                    <span v-if="accessLog.sigueDentro" class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Dentro
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <Button 
                                v-if="accessLog.sigueDentro" 
                                @click="doCheckout" 
                                icon="pi pi-sign-out" 
                                label="Registrar Salida" 
                                class="!rounded-xl !text-[13px] !font-semibold !bg-emerald-500 !border-emerald-500 hover:!bg-emerald-600 !px-4"
                            />
                            <button @click="showEditModal = true" class="w-8 h-8 flex items-center justify-center rounded-xl text-zinc-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-all">
                                <i class="pi pi-pencil text-sm"></i>
                            </button>
                            <button @click="confirmDelete" class="w-8 h-8 flex items-center justify-center rounded-xl text-zinc-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all">
                                <i class="pi pi-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Visitante -->
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em] mb-2">Visitante</p>
                        <p class="text-[14px] font-semibold text-zinc-800 dark:text-zinc-200">{{ accessLog.visitor_name || accessLog.identificador }}</p>
                        <p v-if="accessLog.visitor_company" class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-1">{{ accessLog.visitor_company }}</p>
                        <p v-if="accessLog.visitor_identification" class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-1 font-mono">ID: {{ accessLog.visitor_identification }}</p>
                    </div>

                    <!-- Unidad -->
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em] mb-2">Unidad de Destino</p>
                        <Link v-if="accessLog.private_unit_id" :href="route('admin.private-units.show', accessLog.private_unit_id)" class="text-[14px] font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            #{{ accessLog.unidad }}
                        </Link>
                        <span v-else class="text-[14px] font-semibold text-zinc-400">No asignada</span>
                        <p v-if="accessLog.unidadCalle" class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-1">{{ accessLog.unidadCalle }}</p>
                    </div>

                    <!-- Vehículo -->
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em] mb-2">Vehículo</p>
                        <template v-if="accessLog.vehicle_plate">
                            <p class="text-[14px] font-bold font-mono text-zinc-800 dark:text-zinc-200">{{ accessLog.vehicle_plate.toUpperCase() }}</p>
                            <p class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-1">
                                {{ accessLog.vehicle_brand || '—' }} · {{ accessLog.vehicle_color || '—' }}
                            </p>
                        </template>
                        <p v-else class="text-[14px] text-zinc-400">Sin vehículo</p>
                    </div>

                    <!-- Método -->
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em] mb-2">Verificación</p>
                        <p class="text-[14px] font-semibold text-zinc-800 dark:text-zinc-200">{{ accessLog.metodo }}</p>
                        <p class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-1">{{ accessLog.access_category || '—' }}</p>
                    </div>

                    <!-- Entrada -->
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em] mb-2">Fecha de Entrada</p>
                        <p class="text-[14px] font-semibold font-mono text-zinc-800 dark:text-zinc-200">{{ accessLog.fechaHora }}</p>
                    </div>

                    <!-- Salida / Estancia -->
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em] mb-2">
                            {{ accessLog.exit_time ? 'Fecha de Salida' : 'Estado' }}
                        </p>
                        <template v-if="accessLog.exit_time">
                            <p class="text-[14px] font-semibold font-mono text-zinc-800 dark:text-zinc-200">{{ accessLog.exit_time }}</p>
                            <p v-if="accessLog.estancia" class="text-[12px] text-zinc-500 dark:text-zinc-400 mt-1">Duración: {{ accessLog.estancia }}</p>
                        </template>
                        <p v-else class="text-[14px] font-semibold text-emerald-600 dark:text-emerald-400">Aún dentro</p>
                    </div>
                </div>

                <!-- Notas -->
                <div v-if="accessLog.notas" class="px-6 pb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl p-4">
                        <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.06em] mb-2">Notas</p>
                        <p class="text-[13px] text-zinc-600 dark:text-zinc-300 leading-relaxed">{{ accessLog.notas }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ EDIT MODAL ═══ -->
        <Dialog 
            v-model:visible="showEditModal" 
            header="Editar Registro de Acceso"
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
                :accessLog="accessLog"
                :privateUnits="privateUnits"
                :categories="categories"
                @submitted="onFormSubmitted"
                @cancel="showEditModal = false"
            />
        </Dialog>

        <ConfirmDialog :pt="{ root: { class: '!rounded-2xl !shadow-[0_20px_60px_rgba(0,0,0,0.12)]' }, acceptButton: { class: '!rounded-xl !text-[13px] !font-semibold' }, rejectButton: { class: '!rounded-xl !text-[13px] !font-medium' } }" />
        <Toast position="bottom-right" :pt="{ root: { class: '!rounded-2xl' }, message: { class: '!rounded-xl' } }" />
    </AppLayout>
</template>
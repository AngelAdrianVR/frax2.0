<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

const props = defineProps({
    privateUnits: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    alias: '',
    name: '',
    identification: '',
    default_reason: '',
    default_access_type: 'Peatonal',
    default_plate: '',
    private_unit_id: '',
});

const accessTypeOptions = [
    { label: '🚶‍♂️ Peatonal', value: 'Peatonal' },
    { label: '🚗 Vehicular', value: 'Vehicular' },
];

const unitOptions = computed(() =>
    props.privateUnits.map(u => ({
        label: `🏠 ${u.lot_number || u.id}`,
        value: u.id,
    }))
);

const submit = () => {
    form.post(route('frequent-visitors.store'), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AppLayout title="Nuevo Visitante Frecuente">
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

                <!-- Encabezado -->
                <div class="mb-6 flex items-center gap-3 px-4 sm:px-0">
                    <Link :href="route('frequent-visitors.index')" class="w-8 h-8 flex items-center justify-center rounded-full bg-zinc-800 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left text-sm"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight m-0">Nuevo visitante frecuente</h1>
                        <p class="text-sm text-zinc-400">Registra un visitante recurrente con acceso rápido.</p>
                    </div>
                </div>

                <!-- Formulario -->
                <form @submit.prevent="submit" class="space-y-6 px-4 sm:px-0">

                    <!-- Tarjeta: Identidad -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 tracking-wider">Identidad del visitante</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Nombre completo *</label>
                                <InputText
                                    v-model="form.name"
                                    placeholder="Ej. Juan Pérez"
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                    :class="{ 'p-invalid': form.errors.name }"
                                />
                                <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Alias (opcional)</label>
                                    <InputText
                                        v-model="form.alias"
                                        placeholder="Ej. Jardinero, Mamá"
                                        class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                    />
                                    <p class="text-xs text-zinc-500">Nombre corto para identificar rápido en caseta.</p>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Identificación</label>
                                    <InputText
                                        v-model="form.identification"
                                        placeholder="INE, Pasaporte, Licencia..."
                                        class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Acceso -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-800/60">
                            <h2 class="text-xs font-medium text-zinc-400 tracking-wider">Detalles de acceso</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Motivo de visita recurrente</label>
                                <InputText
                                    v-model="form.default_reason"
                                    placeholder="Ej. Mantenimiento de jardín, Limpieza, Paquetería..."
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Tipo de acceso *</label>
                                    <Select
                                        v-model="form.default_access_type"
                                        :options="accessTypeOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Selecciona tipo"
                                        class="w-full !rounded-xl !text-[13px]"
                                        pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    />
                                </div>

                                <div v-if="form.default_access_type === 'Vehicular'" class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400">Placa del vehículo</label>
                                    <InputText
                                        v-model="form.default_plate"
                                        placeholder="Ej. ABC-1234"
                                        class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500 !font-mono"
                                        style="text-transform:uppercase"
                                    />
                                </div>
                            </div>

                            <!-- Unidad -->
                            <div class="flex flex-col gap-1.5 pt-1">
                                <label class="text-xs font-medium text-zinc-400">Asignar a propiedad *</label>
                                <Select
                                    v-model="form.private_unit_id"
                                    :options="unitOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Selecciona una casa / lote / cajón..."
                                    class="w-full !rounded-xl !text-[13px]"
                                    :class="{ 'p-invalid': form.errors.private_unit_id }"
                                    pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    filter
                                />
                                <p v-if="form.errors.private_unit_id" class="text-sm text-red-400">{{ form.errors.private_unit_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 pt-2">
                        <Link :href="route('frequent-visitors.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm text-center">
                            Cancelar
                        </Link>
                        <Button
                            type="submit"
                            :label="form.processing ? 'Guardando...' : 'Registrar visitante'"
                            :loading="form.processing"
                            class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5"
                        />
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

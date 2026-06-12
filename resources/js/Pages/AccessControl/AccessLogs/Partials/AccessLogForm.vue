<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';

const props = defineProps({
    accessLog: { type: Object, default: null },
    privateUnits: { type: Array, required: true },
    categories: { type: Array, required: true },
});

const emit = defineEmits(['submitted', 'cancel']);

const isEditing = computed(() => !!props.accessLog);

const unitOptions = computed(() =>
    props.privateUnits.map(u => ({
        label: `#${u.lot_number} — ${u.unit_street || ''}`,
        value: u.id,
    }))
);

const categoryOptions = computed(() =>
    props.categories.map(c => ({ label: c, value: c }))
);

const methodOptions = [
    { label: 'Manual', value: 'Manual' },
    { label: 'QR', value: 'QR' },
    { label: 'RFID', value: 'RFID' },
    { label: 'Biométrico', value: 'Biometrico' },
];

const form = useForm({
    visitor_name: props.accessLog?.visitor_name || '',
    visitor_company: props.accessLog?.visitor_company || '',
    visitor_identification: props.accessLog?.visitor_identification || '',
    vehicle_plate: props.accessLog?.vehicle_plate || '',
    vehicle_brand: props.accessLog?.vehicle_brand || '',
    vehicle_color: props.accessLog?.vehicle_color || '',
    access_category: props.accessLog?.access_category || 'Visita',
    verification_method: props.accessLog?.metodo || 'Manual',
    notes: props.accessLog?.notas || '',
    private_unit_id: props.accessLog?.private_unit_id || '',
});

const inputClass = 'w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500 focus:!border-zinc-500 focus:!ring-1 focus:!ring-zinc-600';

const submit = () => {
    if (isEditing.value) {
        form.put(route('access-logs.update', props.accessLog.id), {
            preserveScroll: true,
            onSuccess: () => emit('submitted'),
            onError: () => {},
        });
    } else {
        form.post(route('access-logs.store'), {
            preserveScroll: true,
            onSuccess: () => emit('submitted'),
            onError: () => {},
        });
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-4">
        <!-- Fila 1: Categoría + Método -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-zinc-400 mb-1.5">Tipo de acceso *</label>
                <Select
                    v-model="form.access_category"
                    :options="categoryOptions"
                    optionLabel="label" optionValue="value"
                    placeholder="Categoría"
                    class="w-full !rounded-xl !text-[13px]"
                    :class="{ 'p-invalid': form.errors.access_category }"
                    pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                />
                <p v-if="form.errors.access_category" class="text-[11px] text-red-500 mt-1">{{ form.errors.access_category }}</p>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-zinc-400 mb-1.5">Verificación *</label>
                <Select
                    v-model="form.verification_method"
                    :options="methodOptions"
                    optionLabel="label" optionValue="value"
                    class="w-full !rounded-xl !text-[13px]"
                    pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                />
            </div>
        </div>

        <!-- Fila 2: Nombre -->
        <div>
            <label class="block text-[12px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1.5">Nombre del visitante *</label>
            <InputText v-model="form.visitor_name" placeholder="Ej. Juan Pérez" :class="[inputClass, { 'p-invalid': form.errors.visitor_name }]" />
            <p v-if="form.errors.visitor_name" class="text-[11px] text-red-500 mt-1">{{ form.errors.visitor_name }}</p>
        </div>

        <!-- Fila 3: Empresa + Identificación -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1.5">Empresa</label>
                <InputText v-model="form.visitor_company" placeholder="Opcional" :class="inputClass" />
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1.5">Identificación</label>
                <InputText v-model="form.visitor_identification" placeholder="INE / ID" :class="inputClass" />
            </div>
        </div>

        <!-- Fila 4: Vehículo (3 columnas) -->
        <div class="bg-zinc-800/40 rounded-xl p-4 space-y-3 border border-zinc-700/40">
            <div class="flex items-center gap-2">
                <i class="pi pi-car text-zinc-500 text-sm"></i>
                <p class="text-[11px] font-semibold text-zinc-500">Vehículo (opcional)</p>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-medium text-zinc-400 mb-1">Placas</label>
                    <InputText v-model="form.vehicle_plate" placeholder="ABC-123" :class="inputClass + ' !font-mono'" style="text-transform:uppercase" />
                </div>
                <div>
                    <label class="block text-[11px] font-medium text-zinc-400 mb-1">Marca</label>
                    <InputText v-model="form.vehicle_brand" placeholder="Ej. Toyota" :class="inputClass" />
                </div>
                <div>
                    <label class="block text-[11px] font-medium text-zinc-400 mb-1">Color</label>
                    <InputText v-model="form.vehicle_color" placeholder="Ej. Blanco" :class="inputClass" />
                </div>
            </div>
        </div>

        <!-- Fila 5: Unidad + Notas -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-zinc-400 mb-1.5">Unidad de destino *</label>
                <Select
                    v-model="form.private_unit_id"
                    :options="unitOptions"
                    optionLabel="label" optionValue="value"
                    placeholder="Buscar unidad..."
                    class="w-full !rounded-xl !text-[13px]"
                    :class="{ 'p-invalid': form.errors.private_unit_id }"
                    pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                    filter
                />
                <p v-if="form.errors.private_unit_id" class="text-[11px] text-red-400 mt-1">{{ form.errors.private_unit_id }}</p>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-zinc-400 mb-1.5">Notas</label>
                <Textarea v-model="form.notes" placeholder="Observaciones..." rows="2" :class="inputClass" autoResize />
            </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 pt-3 border-t border-zinc-800/60">
            <Button
                type="button"
                label="Cancelar"
                @click="$emit('cancel')"
                class="!rounded-xl !text-[13px] !font-medium !bg-zinc-800/50 !text-zinc-300 !border !border-zinc-700/50 hover:!bg-zinc-800 !px-5 !py-2.5"
            />
            <Button
                type="submit"
                :label="isEditing ? 'Guardar Cambios' : 'Registrar Entrada'"
                :icon="isEditing ? 'pi pi-check' : 'pi pi-sign-in'"
                :loading="form.processing"
                class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5"
            />
        </div>
    </form>
</template>

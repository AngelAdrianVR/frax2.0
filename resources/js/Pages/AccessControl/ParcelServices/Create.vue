<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

const props = defineProps({
    privateUnits: Array
});

const form = useForm({
    name: '',
    tracking_number: '',
    private_unit_id: '',
    status: 'En Caseta'
});

const statusOptions = [
    { label: 'En caseta', value: 'En Caseta' },
    { label: 'Recibido', value: 'Recibido' },
    { label: 'Entregado al residente', value: 'Entregado' },
    { label: 'Devuelto / Regresado', value: 'Devuelto' },
];

const unitOptions = computed(() =>
    props.privateUnits.map(u => ({
        label: u.name || `#${u.lot_number}`,
        value: u.id,
    }))
);

const submit = () => {
    form.post(route('parcel-services.store'), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AppLayout title="Registrar Paquete">
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6">
                    <Link :href="route('parcel-services.index')" class="text-sm text-zinc-400 hover:text-zinc-200 transition-colors flex items-center">
                        <i class="pi pi-angle-left mr-1"></i> Volver a paquetería
                    </Link>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-800/60 flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#0E63B1]/10 rounded-xl flex items-center justify-center text-[#0E63B1]">
                            <i class="pi pi-box text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-medium text-zinc-100 tracking-tight m-0">Registrar nuevo paquete</h2>
                            <p class="text-sm text-zinc-400">Ingresa los datos del paquete recibido en caseta.</p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="p-6 sm:p-8 space-y-6">
                        <!-- Estatus -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-medium text-zinc-400">Estatus actual</label>
                            <Select
                                v-model="form.status"
                                :options="statusOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecciona estatus"
                                class="w-full !rounded-xl !text-[13px]"
                                :class="{ 'p-invalid': form.errors.status }"
                                pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                            />
                            <p v-if="form.errors.status" class="text-sm text-red-400">{{ form.errors.status }}</p>
                        </div>

                        <!-- Destino -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-medium text-zinc-400">Unidad / Casa destino</label>
                            <Select
                                v-model="form.private_unit_id"
                                :options="unitOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecciona la casa destino..."
                                class="w-full !rounded-xl !text-[13px]"
                                :class="{ 'p-invalid': form.errors.private_unit_id }"
                                pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                filter
                            />
                            <p v-if="form.errors.private_unit_id" class="text-sm text-red-400">{{ form.errors.private_unit_id }}</p>
                        </div>

                        <!-- Detalles del paquete -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Mensajería / Descripción</label>
                                <InputText
                                    v-model="form.name"
                                    placeholder="Ej. Amazon, DHL, Sobre Manila"
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500"
                                    :class="{ 'p-invalid': form.errors.name }"
                                />
                                <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-medium text-zinc-400">Número de guía (opcional)</label>
                                <InputText
                                    v-model="form.tracking_number"
                                    placeholder="Ej. TBA123456789"
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 placeholder:!text-zinc-500 !font-mono"
                                />
                                <p v-if="form.errors.tracking_number" class="text-sm text-red-400">{{ form.errors.tracking_number }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-800/60">
                            <Link :href="route('parcel-services.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                                Cancelar
                            </Link>
                            <Button
                                type="submit"
                                label="Guardar paquete"
                                :loading="form.processing"
                                class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5"
                            />
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
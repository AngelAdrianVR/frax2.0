<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

const props = defineProps({
    guards: Array
});

const form = useForm({
    user_id: '',
    start_time: '',
    end_time: '',
    status: 'Activo',
    scanned_points: 0
});

const guardOptions = computed(() =>
    props.guards.map(g => ({ label: g.name, value: g.id }))
);

const statusOptions = [
    { label: 'Activo (En curso)', value: 'Activo' },
    { label: 'Terminado (Sin novedad)', value: 'Terminado' },
    { label: 'Incidente (Atención requerida)', value: 'Incidente' },
];

const submit = () => {
    form.post(route('patrols.store'), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AppLayout title="Registrar Rondín">
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Botón de regreso -->
                <div class="mb-6 px-4 sm:px-0">
                    <Link :href="route('patrols.index')" class="inline-flex items-center text-sm font-medium text-zinc-400 hover:text-zinc-200 transition-colors">
                        <i class="pi pi-arrow-left mr-2"></i> Volver a la bitácora
                    </Link>
                </div>

                <!-- Tarjeta del Formulario -->
                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    <div class="px-6 py-8 sm:p-10">
                        <div class="mb-8">
                            <h2 class="text-lg font-medium text-zinc-100 tracking-tight m-0">Detalles del rondín</h2>
                            <p class="mt-1 text-sm text-zinc-400">Ingresa la información del recorrido de seguridad manual.</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <!-- Guardia -->
                            <div class="flex flex-col gap-1.5">
                                <label for="user_id" class="text-xs font-medium text-zinc-400">Guardia asignado *</label>
                                <Select
                                    id="user_id"
                                    v-model="form.user_id"
                                    :options="guardOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Selecciona un guardia..."
                                    class="w-full !rounded-xl !text-[13px]"
                                    :class="{ 'p-invalid': form.errors.user_id }"
                                    pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                />
                                <p v-if="form.errors.user_id" class="text-sm text-red-400">{{ form.errors.user_id }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Inicio -->
                                <div class="flex flex-col gap-1.5">
                                    <label for="start_time" class="text-xs font-medium text-zinc-400">Fecha y hora de inicio *</label>
                                    <InputText id="start_time" v-model="form.start_time" type="datetime-local" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                    <p v-if="form.errors.start_time" class="text-sm text-red-400">{{ form.errors.start_time }}</p>
                                </div>

                                <!-- Fin -->
                                <div class="flex flex-col gap-1.5">
                                    <label for="end_time" class="text-xs font-medium text-zinc-400">Fecha y hora de fin</label>
                                    <InputText id="end_time" v-model="form.end_time" type="datetime-local" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                    <p class="text-xs text-zinc-500 mt-0.5">Dejar vacío si el rondín aún está en curso.</p>
                                    <p v-if="form.errors.end_time" class="text-sm text-red-400">{{ form.errors.end_time }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Estatus -->
                                <div class="flex flex-col gap-1.5">
                                    <label for="status" class="text-xs font-medium text-zinc-400">Estatus del rondín *</label>
                                    <Select
                                        id="status"
                                        v-model="form.status"
                                        :options="statusOptions"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Selecciona estatus"
                                        class="w-full !rounded-xl !text-[13px]"
                                        pt:root:class="!bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                    />
                                    <p v-if="form.errors.status" class="text-sm text-red-400">{{ form.errors.status }}</p>
                                </div>

                                <!-- Puntos Escaneados -->
                                <div class="flex flex-col gap-1.5">
                                    <label for="scanned_points" class="text-xs font-medium text-zinc-400">Puntos escaneados</label>
                                    <InputText id="scanned_points" v-model="form.scanned_points" type="number" min="0" class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100" />
                                    <p v-if="form.errors.scanned_points" class="text-sm text-red-400">{{ form.errors.scanned_points }}</p>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-800/60">
                                <Link :href="route('patrols.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                                    Cancelar
                                </Link>
                                <Button
                                    type="submit"
                                    label="Registrar rondín"
                                    :loading="form.processing"
                                    class="!rounded-xl !text-[13px] !font-medium !bg-[#0E63B1] !border-[#0E63B1] !text-white hover:!bg-[#0c5599] !px-5 !py-2.5"
                                />
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
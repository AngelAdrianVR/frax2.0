<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

const props = defineProps({
    checkpoint: Object
});

const form = useForm({
    name: props.checkpoint.name,
    coordinates: props.checkpoint.coordinates || '',
    tag_nfc_id: props.checkpoint.tag_nfc_id || '',
});

const submit = () => {
    form.put(route('checkpoints.update', props.checkpoint.id), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AppLayout title="Editar Punto de Control">
        <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-6 flex items-center gap-4 px-4 sm:px-0">
                    <Link :href="route('checkpoints.index')" class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center border border-zinc-700/50 text-zinc-400 hover:text-zinc-100 transition-colors">
                        <i class="pi pi-arrow-left"></i>
                    </Link>
                    <div>
                        <h1 class="text-lg font-medium text-zinc-100 tracking-tight m-0">Editar punto de control</h1>
                        <p class="text-sm text-zinc-400">Actualiza la información de esta ubicación.</p>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                    <form @submit.prevent="submit" class="p-6 sm:p-8 space-y-6">
                        
                        <!-- Nombre -->
                        <div class="flex flex-col gap-1.5">
                            <label for="name" class="text-xs font-medium text-zinc-400">Nombre de la ubicación</label>
                            <InputText
                                id="name"
                                v-model="form.name"
                                class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                :class="{ 'p-invalid': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="text-sm text-red-400">{{ form.errors.name }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Coordenadas -->
                            <div class="flex flex-col gap-1.5">
                                <label for="coordinates" class="text-xs font-medium text-zinc-400">Coordenadas GPS (opcional)</label>
                                <InputText
                                    id="coordinates"
                                    v-model="form.coordinates"
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100"
                                />
                                <p v-if="form.errors.coordinates" class="text-sm text-red-400">{{ form.errors.coordinates }}</p>
                            </div>

                            <!-- Tag NFC -->
                            <div class="flex flex-col gap-1.5">
                                <label for="tag_nfc_id" class="text-xs font-medium text-zinc-400">ID del Tag NFC / Código QR</label>
                                <InputText
                                    id="tag_nfc_id"
                                    v-model="form.tag_nfc_id"
                                    class="w-full !rounded-xl !text-[13px] !bg-zinc-800 !border-zinc-700/40 !text-zinc-100 !font-mono"
                                />
                                <p v-if="form.errors.tag_nfc_id" class="text-sm text-red-400">{{ form.errors.tag_nfc_id }}</p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-zinc-800/60">
                            <Link :href="route('checkpoints.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                                Cancelar
                            </Link>
                            <Button
                                type="submit"
                                label="Actualizar punto"
                                icon="pi pi-check-circle"
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
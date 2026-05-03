<script setup>
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    description: '',
    reservation_cost: 0,
    capacity: 10,
    mode: 'Exclusivo',
    photo: null,
    rules: [],
    // Estructura del Schedule
    availability_schedule: {
        monday: { active: true, start: '09:00', end: '22:00' },
        tuesday: { active: true, start: '09:00', end: '22:00' },
        wednesday: { active: true, start: '09:00', end: '22:00' },
        thursday: { active: true, start: '09:00', end: '22:00' },
        friday: { active: true, start: '09:00', end: '23:00' },
        saturday: { active: true, start: '10:00', end: '23:00' },
        sunday: { active: true, start: '10:00', end: '20:00' },
    }
});

const newRule = ref('');
const previewImage = ref(null);
const days = [
    { key: 'monday', label: 'Lunes' },
    { key: 'tuesday', label: 'Martes' },
    { key: 'wednesday', label: 'Miércoles' },
    { key: 'thursday', label: 'Jueves' },
    { key: 'friday', label: 'Viernes' },
    { key: 'saturday', label: 'Sábado' },
    { key: 'sunday', label: 'Domingo' },
];

const handlePhotoUpload = (e) => {
    const file = e.target.files[0];
    form.photo = file;
    previewImage.value = URL.createObjectURL(file);
};

const addRule = () => {
    if (newRule.value.trim()) {
        form.rules.push(newRule.value.trim());
        newRule.value = '';
    }
};

const removeRule = (index) => {
    form.rules.splice(index, 1);
};

const submit = () => {
    form.post(route('amenities.store'));
};
</script>

<template>
    <AppLayout title="Nueva Amenidad">
        <Head title="Crear Amenidad" />

        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-zinc-800 rounded-3xl shadow-xl overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Nueva Amenidad</h2>
                            <Link :href="route('amenities.index')" class="text-sm text-zinc-500 hover:text-zinc-700"> Cancelar</Link>
                        </div>

                        <form @submit.prevent="submit" class="space-y-8">
                            
                            <!-- Sección 1: Detalles Básicos -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div>
                                        <InputLabel value="Nombre de la Amenidad" />
                                        <TextInput v-model="form.name" class="w-full mt-1" placeholder="Ej. Alberca Central" required />
                                    </div>
                                    <div>
                                        <InputLabel value="Descripción" />
                                        <textarea v-model="form.description" class="w-full mt-1 border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 rounded-md shadow-sm" rows="4"></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel value="Costo Reserva ($)" />
                                            <TextInput type="number" step="0.01" v-model="form.reservation_cost" class="w-full mt-1" />
                                        </div>
                                        <div>
                                            <InputLabel value="Aforo Máximo" />
                                            <TextInput type="number" v-model="form.capacity" class="w-full mt-1" />
                                        </div>
                                    </div>
                                    <div>
                                        <InputLabel value="Modo de Uso" />
                                        <select v-model="form.mode" class="w-full mt-1 border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 rounded-md shadow-sm">
                                            <option value="Exclusivo">Exclusivo (Privado por reserva)</option>
                                            <option value="Compartido">Compartido (Múltiples usuarios)</option>
                                        </select>
                                        <p class="text-xs text-zinc-500 mt-1">
                                            {{ form.mode === 'Exclusivo' ? 'Solo una reserva activa a la vez.' : 'Múltiples vecinos pueden reservar a la misma hora hasta llenar aforo.' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Foto Upload -->
                                <div class="flex flex-col">
                                    <InputLabel value="Fotografía de Portada" class="mb-2" />
                                    <div class="flex-grow border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-2xl flex items-center justify-center bg-zinc-50 dark:bg-zinc-700/50 relative overflow-hidden group min-h-[250px]">
                                        <img v-if="previewImage" :src="previewImage" class="absolute inset-0 w-full h-full object-cover">
                                        <div class="text-center p-6 relative z-10 transition-opacity" :class="previewImage ? 'opacity-0 group-hover:opacity-100 bg-black/40 w-full h-full flex items-center justify-center' : ''">
                                            <div class="space-y-1">
                                                <svg class="mx-auto h-12 w-12 text-zinc-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                <div class="flex text-sm text-zinc-600 dark:text-zinc-300 justify-center">
                                                    <label class="relative cursor-pointer bg-white dark:bg-zinc-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-2">
                                                        <span>Subir archivo</span>
                                                        <input type="file" class="sr-only" @change="handlePhotoUpload" accept="image/*">
                                                    </label>
                                                </div>
                                                <p class="text-xs text-zinc-500">PNG, JPG, GIF hasta 5MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-zinc-200 dark:border-zinc-700">

                            <!-- Sección 2: Horarios de Disponibilidad (Schedule) -->
                            <div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Horario de Disponibilidad</h3>
                                <div class="bg-zinc-50 dark:bg-zinc-700/30 rounded-xl p-4 space-y-3">
                                    <div v-for="day in days" :key="day.key" class="flex items-center gap-4 py-2 border-b last:border-0 border-zinc-200 dark:border-zinc-700">
                                        <div class="w-32 flex items-center gap-2">
                                            <input type="checkbox" v-model="form.availability_schedule[day.key].active" class="rounded text-blue-600 focus:ring-blue-500">
                                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ day.label }}</span>
                                        </div>
                                        <div class="flex-1 flex gap-4 items-center" :class="{ 'opacity-50 pointer-events-none': !form.availability_schedule[day.key].active }">
                                            <input type="time" v-model="form.availability_schedule[day.key].start" class="rounded-md border-zinc-300 text-sm">
                                            <span class="text-zinc-400">a</span>
                                            <input type="time" v-model="form.availability_schedule[day.key].end" class="rounded-md border-zinc-300 text-sm">
                                        </div>
                                        <div class="text-xs text-zinc-500 w-24 text-right">
                                            {{ form.availability_schedule[day.key].active ? 'Abierto' : 'Cerrado' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-zinc-200 dark:border-zinc-700">

                            <!-- Sección 3: Reglas -->
                            <div>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Reglas del Área</h3>
                                <div class="flex gap-2 mb-4">
                                    <TextInput v-model="newRule" placeholder="Ej. Prohibido ingerir alcohol" class="flex-1" @keyup.enter="addRule" />
                                    <SecondaryButton @click="addRule" type="button">Agregar</SecondaryButton>
                                </div>
                                <ul class="space-y-2">
                                    <li v-for="(rule, idx) in form.rules" :key="idx" class="flex justify-between items-center bg-zinc-100 dark:bg-zinc-700 px-4 py-2 rounded-lg">
                                        <span>{{ rule }}</span>
                                        <button type="button" @click="removeRule(idx)" class="text-red-500 hover:text-red-700">✕</button>
                                    </li>
                                </ul>
                            </div>

                            <div class="flex justify-end pt-6">
                                <PrimaryButton :disabled="form.processing" class="px-8 py-3 text-lg">
                                    Crear Amenidad
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
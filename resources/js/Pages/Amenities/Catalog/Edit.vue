<script setup>
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps(['amenity']);

const form = useForm({
    _method: 'PUT',
    name: props.amenity.name,
    description: props.amenity.description || '',
    reservation_cost: props.amenity.reservation_cost,
    capacity: props.amenity.capacity,
    mode: props.amenity.mode,
    photo: null, 
    rules: props.amenity.rules || [],
    availability_schedule: props.amenity.availability_schedule || {
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
const previewImage = ref(props.amenity.photo_url);

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
    if (file) {
        form.photo = file;
        previewImage.value = URL.createObjectURL(file);
    }
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
    form.post(route('amenities.update', props.amenity.id));
};
</script>

<template>
    <AppLayout title="Editar Amenidad">
        <Head title="Editar Amenidad" />

        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen transition-colors duration-300">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Encabezado -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Editar Amenidad</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Actualiza los detalles, costos y horarios de esta instalación.</p>
                    </div>
                    <Link :href="route('amenities.index')" class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm font-semibold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition shadow-sm">
                        Volver
                    </Link>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- Tarjeta 1: Detalles Básicos -->
                    <div class="bg-white dark:bg-zinc-800 rounded-3xl shadow-sm border border-zinc-100 dark:border-zinc-700 overflow-hidden">
                        <div class="border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 px-8 py-5 flex items-center gap-3">
                            <span class="p-2 bg-blue-100 text-blue-600 rounded-lg">📋</span>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Detalles Generales</h3>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <InputLabel value="Nombre de la Amenidad" class="text-zinc-700 dark:text-zinc-300 font-semibold mb-1" />
                                    <TextInput v-model="form.name" class="w-full bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-blue-500" placeholder="Ej. Alberca Central, Casa Club..." required />
                                </div>
                                <div>
                                    <InputLabel value="Descripción" class="text-zinc-700 dark:text-zinc-300 font-semibold mb-1" />
                                    <textarea v-model="form.description" class="w-full bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-blue-500 shadow-sm" rows="4" placeholder="Describe brevemente las instalaciones..."></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-700">
                                        <InputLabel value="Costo Reserva ($)" class="text-zinc-600 dark:text-zinc-400 mb-2" />
                                        <TextInput type="number" step="0.01" v-model="form.reservation_cost" class="w-full border-zinc-200 dark:border-zinc-700 rounded-lg text-lg font-bold text-blue-600" />
                                    </div>
                                    <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-700">
                                        <InputLabel value="Aforo Máximo" class="text-zinc-600 dark:text-zinc-400 mb-2" />
                                        <TextInput type="number" v-model="form.capacity" class="w-full border-zinc-200 dark:border-zinc-700 rounded-lg text-lg font-bold" />
                                    </div>
                                </div>
                                <div>
                                    <InputLabel value="Modo de Uso" class="text-zinc-700 dark:text-zinc-300 font-semibold mb-1" />
                                    <select v-model="form.mode" class="w-full bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-blue-500 shadow-sm font-medium">
                                        <option value="Exclusivo">Exclusivo (Privado por reserva)</option>
                                        <option value="Compartido">Compartido (Múltiples usuarios)</option>
                                    </select>
                                    <p class="text-xs text-zinc-500 mt-2 ml-1">
                                        <span class="font-bold text-blue-500">💡 Tip:</span> 
                                        {{ form.mode === 'Exclusivo' ? 'Solo una familia puede reservar en un horario.' : 'Varias familias pueden entrar a la misma hora hasta llenar aforo.' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Foto Upload -->
                            <div class="flex flex-col h-full">
                                <InputLabel value="Fotografía de Portada" class="text-zinc-700 dark:text-zinc-300 font-semibold mb-2" />
                                <div class="flex-grow border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-3xl flex items-center justify-center bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/50 dark:hover:bg-zinc-800 transition relative overflow-hidden group min-h-[300px]">
                                    <img v-if="previewImage" :src="previewImage" class="absolute inset-0 w-full h-full object-cover">
                                    <div class="text-center p-6 relative z-10 transition-opacity" :class="previewImage ? 'opacity-0 group-hover:opacity-100 bg-zinc-900/60 backdrop-blur-sm w-full h-full flex flex-col items-center justify-center rounded-3xl' : ''">
                                        <div class="space-y-3">
                                            <div class="w-16 h-16 mx-auto bg-blue-100 text-blue-500 rounded-full flex items-center justify-center">
                                                <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                            </div>
                                            <div class="flex text-sm text-zinc-600 dark:text-zinc-300 justify-center">
                                                <label class="relative cursor-pointer bg-white dark:bg-zinc-700 px-4 py-2 rounded-xl shadow-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-500 transition">
                                                    <span>Cambiar fotografía</span>
                                                    <input type="file" class="sr-only" @change="handlePhotoUpload" accept="image/*">
                                                </label>
                                            </div>
                                            <p class="text-xs font-medium text-zinc-400" :class="previewImage ? 'text-zinc-200' : ''">Formatos: PNG, JPG (Máx. 5MB)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 2: Horarios -->
                    <div class="bg-white dark:bg-zinc-800 rounded-3xl shadow-sm border border-zinc-100 dark:border-zinc-700 overflow-hidden">
                        <div class="border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 px-8 py-5 flex items-center gap-3">
                            <span class="p-2 bg-orange-100 text-orange-600 rounded-lg">🕒</span>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Horarios de Disponibilidad</h3>
                        </div>
                        <div class="p-4 sm:p-8">
                            <div class="bg-zinc-50 dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-700 overflow-hidden">
                                <div v-for="day in days" :key="day.key" class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 border-b last:border-0 border-zinc-200 dark:border-zinc-700 hover:bg-white dark:hover:bg-zinc-800/80 transition">
                                    
                                    <div class="w-full sm:w-40 flex items-center gap-3">
                                        <input type="checkbox" v-model="form.availability_schedule[day.key].active" class="w-5 h-5 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 transition">
                                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ day.label }}</span>
                                    </div>
                                    
                                    <div class="flex-1 flex gap-2 sm:gap-4 items-center" :class="{ 'opacity-40 grayscale pointer-events-none': !form.availability_schedule[day.key].active }">
                                        <input type="time" v-model="form.availability_schedule[day.key].start" class="w-full sm:w-auto rounded-xl border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 text-sm focus:ring-blue-500 font-medium">
                                        <span class="text-zinc-400 font-medium">a</span>
                                        <input type="time" v-model="form.availability_schedule[day.key].end" class="w-full sm:w-auto rounded-xl border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 text-sm focus:ring-blue-500 font-medium">
                                    </div>

                                    <div class="w-28 text-right hidden sm:block">
                                        <span v-if="form.availability_schedule[day.key].active" class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-bold rounded-full">Abierto</span>
                                        <span v-else class="px-3 py-1 bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400 text-xs font-bold rounded-full">Cerrado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 3: Reglas -->
                    <div class="bg-white dark:bg-zinc-800 rounded-3xl shadow-sm border border-zinc-100 dark:border-zinc-700 overflow-hidden">
                        <div class="border-b border-zinc-100 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 px-8 py-5 flex items-center gap-3">
                            <span class="p-2 bg-red-100 text-red-600 rounded-lg">📜</span>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Reglamento Interno</h3>
                        </div>
                        <div class="p-8">
                            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                                <TextInput v-model="newRule" placeholder="Ej. Prohibido ingresar bebidas alcohólicas..." class="flex-1 bg-zinc-50 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-blue-500" @keyup.enter="addRule" />
                                <button @click="addRule" type="button" class="px-6 py-3 bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-800 rounded-xl font-bold hover:bg-zinc-700 dark:hover:bg-zinc-300 transition shadow-sm whitespace-nowrap">
                                    + Agregar Regla
                                </button>
                            </div>
                            
                            <!-- Lista de Reglas tipo "Chips" -->
                            <div v-if="form.rules.length > 0" class="flex flex-wrap gap-3">
                                <div v-for="(rule, idx) in form.rules" :key="idx" class="flex items-center gap-2 bg-blue-50 border border-blue-100 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800/50 dark:text-blue-300 px-4 py-2 rounded-xl text-sm font-medium shadow-sm transition hover:shadow">
                                    <span>{{ rule }}</span>
                                    <button type="button" @click="removeRule(idx)" class="w-6 h-6 rounded-full flex items-center justify-center text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 hover:text-blue-700 dark:hover:text-white transition focus:outline-none">
                                        ✕
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-center py-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700">
                                <p class="text-zinc-500 dark:text-zinc-400 text-sm">No hay reglas registradas aún. Escribe una arriba y presiona "Agregar".</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botón Flotante de Guardar -->
                    <div class="flex justify-end pt-4 pb-12 sticky bottom-4 z-10">
                        <PrimaryButton :disabled="form.processing" class="px-8 py-4 text-lg bg-blue-600 hover:bg-blue-500 rounded-2xl shadow-xl shadow-blue-600/30 transition-all transform hover:-translate-y-1">
                            Guardar Cambios
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
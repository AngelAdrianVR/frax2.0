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

        <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Encabezado -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-lg font-medium text-zinc-100 tracking-tight">Editar Amenidad</h2>
                        <p class="text-sm text-zinc-400 mt-1">Actualiza los detalles, costos y horarios de esta instalación.</p>
                    </div>
                    <Link :href="route('amenities.index')" class="px-4 py-2.5 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-300 border border-zinc-700/50 font-medium rounded-xl transition-all duration-200 text-sm">
                        Volver
                    </Link>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- Tarjeta 1: Detalles Básicos -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="border-b border-zinc-800/60 px-6 py-4">
                            <h3 class="text-sm font-medium text-zinc-200 tracking-tight">Detalles Generales</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Nombre de la Amenidad</label>
                                    <input v-model="form.name" type="text" placeholder="Ej. Alberca Central, Casa Club..." required
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Descripción</label>
                                    <textarea v-model="form.description" rows="4" placeholder="Describe brevemente las instalaciones..."
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 resize-none"></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-zinc-800/50 p-4 rounded-xl border border-zinc-700/40">
                                        <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2 block">Costo Reserva ($)</label>
                                        <input type="number" step="0.01" v-model="form.reservation_cost"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-lg px-3 py-2 text-lg font-bold transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                    </div>
                                    <div class="bg-zinc-800/50 p-4 rounded-xl border border-zinc-700/40">
                                        <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2 block">Aforo Máximo</label>
                                        <input type="number" v-model="form.capacity"
                                            class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-lg px-3 py-2 text-lg font-bold transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600" />
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Modo de Uso</label>
                                    <select v-model="form.mode"
                                        class="w-full bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600">
                                        <option value="Exclusivo">Exclusivo (Privado por reserva)</option>
                                        <option value="Compartido">Compartido (Múltiples usuarios)</option>
                                    </select>
                                    <p class="text-xs text-zinc-500 mt-1">
                                        <span class="font-medium text-[#0E63B1]">💡 Tip:</span> 
                                        {{ form.mode === 'Exclusivo' ? 'Solo una familia puede reservar en un horario.' : 'Varias familias pueden entrar a la misma hora hasta llenar aforo.' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Foto Upload -->
                            <div class="flex flex-col">
                                <label class="text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Fotografía de Portada</label>
                                <div class="flex-grow border-2 border-dashed border-zinc-700/60 rounded-xl flex items-center justify-center bg-zinc-800/30 hover:bg-zinc-800/50 transition relative overflow-hidden group min-h-[300px]">
                                    <img v-if="previewImage" :src="previewImage" class="absolute inset-0 w-full h-full object-cover">
                                    <div class="text-center p-6 relative z-10 transition-opacity" :class="previewImage ? 'opacity-0 group-hover:opacity-100 bg-zinc-900/70 backdrop-blur-sm w-full h-full flex flex-col items-center justify-center rounded-xl' : ''">
                                        <div class="space-y-3">
                                            <div class="w-16 h-16 mx-auto bg-zinc-800 text-zinc-400 rounded-full flex items-center justify-center">
                                                <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                            </div>
                                            <div class="flex text-sm text-zinc-300 justify-center">
                                                <label class="relative cursor-pointer bg-zinc-700 px-4 py-2 rounded-xl font-medium text-zinc-200 hover:text-white transition">
                                                    <span>Cambiar fotografía</span>
                                                    <input type="file" class="sr-only" @change="handlePhotoUpload" accept="image/*">
                                                </label>
                                            </div>
                                            <p class="text-xs font-medium text-zinc-500" :class="previewImage ? 'text-zinc-300' : ''">Formatos: PNG, JPG (Máx. 5MB)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 2: Horarios -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="border-b border-zinc-800/60 px-6 py-4">
                            <h3 class="text-sm font-medium text-zinc-200 tracking-tight">Horarios de Disponibilidad</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="bg-zinc-800/30 rounded-xl border border-zinc-700/40 overflow-hidden">
                                <div v-for="day in days" :key="day.key" class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 border-b border-zinc-800/60 last:border-0 hover:bg-zinc-800/40 transition">
                                    
                                    <div class="w-full sm:w-40 flex items-center gap-3">
                                        <input type="checkbox" v-model="form.availability_schedule[day.key].active" class="w-5 h-5 rounded border-zinc-600 bg-zinc-700 text-[#0E63B1] focus:ring-[#0E63B1] transition">
                                        <span class="font-medium text-zinc-200">{{ day.label }}</span>
                                    </div>
                                    
                                    <div class="flex-1 flex gap-2 sm:gap-4 items-center" :class="{ 'opacity-30 pointer-events-none': !form.availability_schedule[day.key].active }">
                                        <input type="time" v-model="form.availability_schedule[day.key].start" class="w-full sm:w-auto bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-3 py-2 text-sm transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600">
                                        <span class="text-zinc-500 font-medium">a</span>
                                        <input type="time" v-model="form.availability_schedule[day.key].end" class="w-full sm:w-auto bg-zinc-800 border border-zinc-700/40 text-zinc-100 rounded-xl px-3 py-2 text-sm transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600">
                                    </div>

                                    <div class="w-28 text-right hidden sm:block">
                                        <span v-if="form.availability_schedule[day.key].active" class="px-3 py-1 bg-green-500/10 text-green-400 text-xs font-medium rounded-full border border-green-500/20">Abierto</span>
                                        <span v-else class="px-3 py-1 bg-zinc-800 text-zinc-500 text-xs font-medium rounded-full border border-zinc-700/40">Cerrado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta 3: Reglas -->
                    <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                        <div class="border-b border-zinc-800/60 px-6 py-4">
                            <h3 class="text-sm font-medium text-zinc-200 tracking-tight">Reglamento Interno</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                                <input v-model="newRule" type="text" placeholder="Ej. Prohibido ingresar bebidas alcohólicas..."
                                    class="flex-1 bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600"
                                    @keyup.enter="addRule" />
                                <button @click="addRule" type="button" class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl transition-all duration-200 text-sm whitespace-nowrap">
                                    + Agregar Regla
                                </button>
                            </div>
                            
                            <div v-if="form.rules.length > 0" class="flex flex-wrap gap-3">
                                <div v-for="(rule, idx) in form.rules" :key="idx" class="flex items-center gap-2 bg-zinc-800 border border-zinc-700/40 text-zinc-200 px-4 py-2 rounded-xl text-sm font-medium transition">
                                    <span>{{ rule }}</span>
                                    <button type="button" @click="removeRule(idx)" class="w-6 h-6 rounded-full flex items-center justify-center text-zinc-400 hover:bg-zinc-700 hover:text-zinc-200 transition focus:outline-none">
                                        ✕
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-center py-6 bg-zinc-800/30 rounded-xl border border-dashed border-zinc-700/60">
                                <p class="text-zinc-500 text-sm">No hay reglas registradas aún. Escribe una arriba y presiona "Agregar".</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Guardar -->
                    <div class="flex justify-end pt-2 pb-8">
                        <button type="submit" :disabled="form.processing" class="px-6 py-3 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 disabled:opacity-50">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
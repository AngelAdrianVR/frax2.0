<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const isAdmin = computed(() => {
    const role = page.props.auth.user.role;
    return ['Admin', 'Administrador', 'Empleado'].includes(role);
});

const form = useForm({
    content: '',
    type: 'general',
    is_pinned: false,
    is_multiple_choice: false,
    poll_options: [],
    image: null, // Nuevo campo para la imagen
});

const isPollActive = ref(false);
const imageInput = ref(null);
const imagePreview = ref(null);

// --- Lógica del nuevo menú desplegable iOS ---
const isTypeMenuOpen = ref(false);

const postTypes = {
    general: { label: 'Vecinal', icon: 'pi pi-users', color: 'text-indigo-600 dark:text-indigo-400' },
    announcement: { label: 'Oficial', icon: 'pi pi-megaphone', color: 'text-amber-600 dark:text-amber-500' },
    alert: { label: 'Alerta', icon: 'pi pi-exclamation-triangle', color: 'text-red-600 dark:text-red-500' }
};

const selectType = (type) => {
    form.type = type;
    isTypeMenuOpen.value = false;
};
// ---------------------------------------------

const triggerImageUpload = () => {
    imageInput.value.click();
};

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = null;
    if (imageInput.value) imageInput.value.value = '';
};

const togglePoll = () => {
    isPollActive.value = !isPollActive.value;
    if (isPollActive.value) {
        form.poll_options = ['', ''];
    } else {
        form.poll_options = [];
    }
};

const addPollOption = () => {
    if (form.poll_options.length < 5) form.poll_options.push('');
};

const removePollOption = (index) => {
    form.poll_options.splice(index, 1);
};

const submitPost = () => {
    if (isPollActive.value) {
        form.poll_options = form.poll_options.filter(opt => opt.trim() !== '');
    }

    form.post(route('notice-board.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('content', 'type', 'is_pinned', 'is_multiple_choice', 'poll_options', 'image');
            form.type = 'general';
            isPollActive.value = false;
            removeImage();
        },
    });
};

const getInitials = (name) => {
    if (!name) return '?';
    return name.charAt(0).toUpperCase();
};
</script>

<template>
    <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] p-5 shadow-[0_2px_10px_rgba(0,0,0,0.04)] dark:shadow-none border border-black/5 dark:border-white/5 transition-colors relative z-10">
        <form @submit.prevent="submitPost">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 mt-1">
                    <div class="h-10 w-10 md:h-12 md:w-12 rounded-full border-2 border-white dark:border-zinc-700 shadow-sm flex items-center justify-center bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 font-bold text-lg">
                        {{ getInitials($page.props.auth.user.name) }}
                    </div>
                </div>
                
                <div class="flex-1">
                    <textarea 
                        v-model="form.content"
                        rows="3" 
                        class="w-full rounded-2xl border-none bg-black/5 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 placeholder-gray-500 dark:placeholder-gray-400 resize-none transition-all text-sm md:text-base py-3 px-4" 
                        placeholder="¿Qué quieres compartir con los vecinos?"
                    ></textarea>

                    <!-- PREVISUALIZADOR DE IMAGEN -->
                    <div v-if="imagePreview" class="relative mt-3 inline-block">
                        <img :src="imagePreview" class="h-40 w-auto rounded-xl object-cover border border-gray-200 dark:border-zinc-700 shadow-sm">
                        <button type="button" @click="removeImage" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 shadow-md">
                            <i class="pi pi-times text-[10px]"></i>
                        </button>
                    </div>
                    
                    <!-- MÓDULO DE ENCUESTA -->
                    <div v-if="isPollActive" class="mt-4 bg-gray-50 dark:bg-[#2C2C2E] p-4 rounded-2xl border border-gray-100 dark:border-zinc-700/50">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide flex items-center gap-2">
                                <i class="pi pi-chart-bar"></i> Opciones de Encuesta
                            </span>
                            <button type="button" @click="togglePoll" class="text-gray-400 hover:text-red-500 transition-colors">
                                <i class="pi pi-times text-sm"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-2">
                            <div v-for="(option, index) in form.poll_options" :key="index" class="relative">
                                <input v-model="form.poll_options[index]" type="text" :placeholder="`Opción ${index + 1}`" class="w-full rounded-xl border-gray-200 dark:border-zinc-600 bg-white dark:bg-[#1C1C1E] text-sm py-2.5 pl-3 pr-10 focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white shadow-sm transition-all placeholder-gray-400">
                                <button v-if="form.poll_options.length > 2" @click="removePollOption(index)" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                    <i class="pi pi-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button v-if="form.poll_options.length < 5" type="button" @click="addPollOption" class="mt-3 text-sm text-indigo-600 dark:text-indigo-400 font-semibold hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors flex items-center gap-1">
                            <i class="pi pi-plus text-xs"></i> Añadir opción
                        </button>
                        
                        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-zinc-700/50 flex items-center gap-2">
                            <input type="checkbox" id="is_multiple_choice" v-model="form.is_multiple_choice" class="rounded text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 bg-white w-4 h-4 cursor-pointer">
                            <label for="is_multiple_choice" class="text-xs font-semibold text-gray-700 dark:text-gray-300 cursor-pointer">
                                Permitir selección múltiple
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <!-- BOTÓN IMAGEN -->
                        <input type="file" ref="imageInput" class="hidden" accept="image/*" @change="handleImageUpload">
                        <button type="button" @click="triggerImageUpload" class="bg-gray-50 text-gray-600 dark:bg-[#2C2C2E] dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-100 dark:border-zinc-700 transition-colors text-xs font-semibold">
                            <i class="pi pi-image text-sm"></i> Foto
                        </button>

                        <!-- BOTÓN ENCUESTA -->
                        <button type="button" @click="togglePoll" :class="isPollActive ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400' : 'bg-gray-50 text-gray-600 dark:bg-[#2C2C2E] dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700'" class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-100 dark:border-zinc-700 transition-colors text-xs font-semibold">
                            <i class="pi pi-chart-bar text-sm"></i> Encuesta
                        </button>

                        <div v-if="isAdmin" class="flex items-center gap-3 bg-gray-50 dark:bg-[#2C2C2E] px-3 py-1.5 rounded-xl border border-gray-100 dark:border-zinc-700">
                            <!-- CHECK FIJAR -->
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="is_pinned" v-model="form.is_pinned" class="rounded text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 bg-white w-4 h-4 cursor-pointer">
                                <label for="is_pinned" class="text-xs font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1 cursor-pointer">
                                    <i class="pi pi-thumbtack text-xs"></i> Fijar
                                </label>
                            </div>
                            
                            <div class="w-px h-4 bg-gray-300 dark:bg-zinc-600"></div>
                            
                            <!-- NUEVO SELECTOR TIPO APPLE -->
                            <div class="relative">
                                <!-- Capa invisible para cerrar al hacer clic afuera -->
                                <div v-if="isTypeMenuOpen" @click="isTypeMenuOpen = false" class="fixed inset-0 z-40"></div>
                                
                                <button type="button" @click="isTypeMenuOpen = !isTypeMenuOpen" class="relative z-50 flex items-center gap-1.5 text-xs font-bold px-2 py-1 rounded-lg transition-all hover:bg-black/5 dark:hover:bg-white/5" :class="postTypes[form.type].color">
                                    <i :class="postTypes[form.type].icon" class="text-[10px]"></i>
                                    {{ postTypes[form.type].label }}
                                    <i class="pi pi-chevron-down text-[8px] ml-1 opacity-70"></i>
                                </button>

                                <transition 
                                    enter-active-class="transition ease-out duration-100" 
                                    enter-from-class="transform opacity-0 scale-95" 
                                    enter-to-class="transform opacity-100 scale-100" 
                                    leave-active-class="transition ease-in duration-75" 
                                    leave-from-class="transform opacity-100 scale-100" 
                                    leave-to-class="transform opacity-0 scale-95"
                                >
                                    <div v-if="isTypeMenuOpen" class="absolute bottom-full mb-2 right-0 w-36 bg-white dark:bg-[#3A3A3C] border border-gray-100 dark:border-zinc-700 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_20px_rgba(0,0,0,0.3)] overflow-hidden z-50 py-1.5">
                                        <button type="button" @click="selectType('general')" class="w-full text-left px-3 py-2 text-xs font-semibold hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors flex items-center gap-2" :class="form.type === 'general' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-200'">
                                            <i class="pi pi-users w-4 text-center"></i> Vecinal
                                        </button>
                                        <button type="button" @click="selectType('announcement')" class="w-full text-left px-3 py-2 text-xs font-semibold hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors flex items-center gap-2" :class="form.type === 'announcement' ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-500' : 'text-gray-700 dark:text-gray-200'">
                                            <i class="pi pi-megaphone w-4 text-center"></i> Oficial
                                        </button>
                                        <button type="button" @click="selectType('alert')" class="w-full text-left px-3 py-2 text-xs font-semibold hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors flex items-center gap-2" :class="form.type === 'alert' ? 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500' : 'text-gray-700 dark:text-gray-200'">
                                            <i class="pi pi-exclamation-triangle w-4 text-center"></i> Alerta
                                        </button>
                                    </div>
                                </transition>
                            </div>
                            <!-- FIN NUEVO SELECTOR -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 flex items-center justify-end border-t border-black/5 dark:border-white/5 pt-4">
                <button 
                    type="submit" 
                    :disabled="form.processing || (!form.content.trim() && !form.image)"
                    class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <i v-if="form.processing" class="pi pi-spin pi-spinner mr-2"></i>
                    <i v-else class="pi pi-send mr-2 text-xs"></i>
                    Publicar
                </button>
            </div>
        </form>
    </div>
</template>
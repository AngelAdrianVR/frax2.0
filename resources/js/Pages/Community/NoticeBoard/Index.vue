<script setup>
import { ref, computed } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import CreatePostBox from '@/Components/CreatePostBox.vue'; 

const props = defineProps({
    posts: Object
});

const page = usePage();
const confirm = useConfirm();

const isAdmin = computed(() => {
    const role = page.props.auth.user.role;
    return ['Admin', 'Administrador', 'Empleado'].includes(role);
});

// --- Lógica de Interacciones ---
const toggleLike = (postId) => {
    router.post(route('notice-board.react', postId), {}, { preserveScroll: true });
};

const confirmDelete = (postId) => {
    confirm.require({
        message: '¿Estás seguro de que deseas eliminar esta publicación?',
        header: 'Eliminar Publicación',
        icon: 'pi pi-exclamation-triangle text-red-500',
        acceptLabel: 'Sí, eliminar',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => { router.delete(route('notice-board.destroy', postId), { preserveScroll: true }); }
    });
};

// --- Lógica de Edición ---
const editingPost = ref(null);
const editContent = ref('');
const editPinned = ref(false); 
const editPollClosed = ref(false); 
const processingEdit = ref(false);

const startEdit = (post) => {
    editingPost.value = post.id;
    editContent.value = post.content;
    editPinned.value = post.is_pinned;
    editPollClosed.value = post.is_poll_closed;
};

const cancelEdit = () => {
    editingPost.value = null;
    editContent.value = '';
    editPinned.value = false;
    editPollClosed.value = false;
};

const submitEdit = (postId) => {
    if (!editContent.value.trim()) return;
    processingEdit.value = true;
    
    router.put(route('notice-board.update', postId), {
        content: editContent.value,
        is_pinned: editPinned.value,
        is_poll_closed: editPollClosed.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            editingPost.value = null;
            processingEdit.value = false;
        },
        onError: () => { processingEdit.value = false; }
    });
};

// --- Lógica de Encuestas ---
const submitVote = (optionId) => {
    router.post(route('notice-board.poll.vote', optionId), {}, {
        preserveScroll: true
    });
};

// --- Lógica de Comentarios ---
const activeComments = ref({});
const newComments = ref({});
const processingComments = ref({});

const toggleCommentSection = (postId) => { activeComments.value[postId] = !activeComments.value[postId]; };

const submitComment = (postId) => {
    if (!newComments.value[postId]?.trim()) return;
    processingComments.value[postId] = true;
    router.post(route('notice-board.comment.store', postId), {
        content: newComments.value[postId]
    }, {
        preserveScroll: true,
        onSuccess: () => {
            newComments.value[postId] = '';
            processingComments.value[postId] = false;
            activeComments.value[postId] = true; 
        },
        onError: () => { processingComments.value[postId] = false; }
    });
};

const getInitials = (name) => {
    if (!name) return '?';
    return name.charAt(0).toUpperCase();
};
</script>

<template>
    <AppLayout title="Muro de Avisos">
        <ConfirmDialog></ConfirmDialog>

        <!-- Fondo principal actualizado con zinc-950 -->
        <div class="min-h-screen text-gray-800 dark:text-zinc-100 p-4 sm:p-8 transition-colors duration-300 font-sans tracking-tight">
            
            <div class="max-w-3xl mx-auto space-y-8">
                
                <!-- HERO BANNER (Diseño Atractivo) -->
                <div class="relative overflow-hidden rounded-[32px] bg-gradient-to-br from-indigo-600 via-indigo-500 to-purple-600 p-8 sm:p-10 text-white shadow-lg shadow-indigo-500/20 group">
                    <!-- Elementos decorativos de fondo (Glassmorphism blobs) -->
                    <div class="absolute -top-10 -right-10 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700"></div>
                    <div class="absolute bottom-0 right-20 w-32 h-32 bg-purple-400/20 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-indigo-400/20 rounded-full blur-2xl"></div>
                    
                    <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white mb-2">Muro de Avisos</h1>
                            <p class="text-indigo-100/90 text-sm sm:text-base font-medium max-w-md leading-relaxed">
                                Conecta con tus vecinos, participa en encuestas y entérate de todo lo que sucede en la comunidad.
                            </p>
                        </div>
                        <div class="hidden sm:flex h-16 w-16 bg-white/10 backdrop-blur-md rounded-2xl items-center justify-center border border-white/20 shadow-inner">
                            <i class="pi pi-users text-3xl text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Creador de publicaciones (Se eliminó el z-20 que causaba el problema con el scroll) -->
                <div class="relative">
                    <CreatePostBox />
                </div>

                <!-- FEED DE PUBLICACIONES -->
                <div class="space-y-6">
                    <!-- Empty State Moderno -->
                    <div v-if="posts.data.length === 0" class="bg-white/80 dark:bg-[#1C1C1E]/80 backdrop-blur-xl rounded-[32px] p-12 text-center border border-gray-200/50 dark:border-zinc-800 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none">
                        <div class="mx-auto h-24 w-24 bg-indigo-50 dark:bg-indigo-500/10 rounded-full flex items-center justify-center mb-5">
                            <i class="pi pi-comments text-4xl text-indigo-400 dark:text-indigo-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">El muro está muy tranquilo</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">¡Sé el primero en romper el hielo! Comparte una noticia, haz una pregunta o crea una encuesta para la comunidad.</p>
                    </div>

                    <!-- Tarjetas de Publicaciones -->
                    <div v-for="post in posts.data" :key="post.id" class="bg-white dark:bg-[#1C1C1E] rounded-[32px] p-6 sm:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-gray-100 dark:border-white/5 transition-all duration-300 relative overflow-hidden group/post">
                        
                        <!-- Etiqueta lateral / Borde decorativo -->
                        <div v-if="post.is_pinned" class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-indigo-400 to-indigo-600"></div>
                        <div v-else-if="post.type === 'alert'" class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-red-400 to-red-600"></div>

                        <!-- Badge -->
                        <div v-if="post.is_pinned || post.type !== 'general'" class="mb-5 flex items-center">
                            <span class="inline-flex items-center gap-x-1.5 rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wider shadow-sm"
                                :class="{
                                    'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-500/30': post.is_pinned && post.type !== 'alert',
                                    'bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 border border-amber-100 dark:border-amber-500/30': post.type === 'announcement' && !post.is_pinned,
                                    'bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-400 border border-red-100 dark:border-red-500/30': post.type === 'alert'
                                }">
                                <i :class="post.type === 'alert' ? 'pi pi-exclamation-triangle' : 'pi pi-megaphone'" class="text-[10px]"></i>
                                {{ post.type === 'alert' ? 'Alerta Importante' : (post.is_pinned ? 'Aviso Fijado' : 'Comunicado Oficial') }}
                            </span>
                        </div>

                        <!-- Cabecera de la Publicación -->
                        <div class="flex justify-between items-start">
                            <div class="flex space-x-4">
                                <div class="flex-shrink-0">
                                    <!-- Avatar con Gradiente -->
                                    <div class="h-12 w-12 rounded-full flex items-center justify-center font-bold text-base shadow-sm"
                                        :class="post.type === 'alert' ? 'bg-gradient-to-br from-red-100 to-red-200 text-red-700 dark:from-red-900/40 dark:to-red-800/40 dark:text-red-300' : 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-700 dark:from-zinc-800 dark:to-zinc-700 dark:text-zinc-300'">
                                        {{ getInitials(post.user.name) }}
                                    </div>
                                </div>
                                <div class="pt-1">
                                    <p class="text-base font-bold text-gray-900 dark:text-white tracking-tight">{{ post.user.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium flex items-center gap-1 mt-0.5">
                                        <i class="pi pi-clock text-[10px]"></i>
                                        {{ post.created_at_human || 'Recientemente' }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Botones ocultos cuando se edita -->
                            <div class="flex items-center gap-1 opacity-0 group-hover/post:opacity-100 transition-opacity duration-200" v-if="editingPost !== post.id">
                                <button v-if="$page.props.auth.user.id === post.user_id" @click="startEdit(post)" class="w-9 h-9 rounded-full flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all hover:scale-105">
                                    <i class="pi pi-pencil text-sm"></i>
                                </button>
                                <button v-if="$page.props.auth.user.id === post.user_id || isAdmin" @click="confirmDelete(post.id)" class="w-9 h-9 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all hover:scale-105">
                                    <i class="pi pi-trash text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- EDICIÓN Y TEXTO -->
                        <div v-if="editingPost === post.id" class="mt-5 bg-gray-50 dark:bg-black/20 p-5 rounded-2xl border border-gray-100 dark:border-zinc-800">
                            <textarea 
                                v-model="editContent"
                                rows="3" 
                                class="w-full rounded-xl border-0 bg-white dark:bg-[#1C1C1E] text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 shadow-sm resize-none transition-all text-[15px] py-3 px-4" 
                            ></textarea>
                            
                            <div class="mt-4 flex flex-col gap-3">
                                <div v-if="isAdmin" class="flex items-center gap-2 px-1">
                                    <input type="checkbox" :id="'edit_pinned_' + post.id" v-model="editPinned" class="rounded text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 bg-white w-4 h-4 cursor-pointer transition-colors">
                                    <label :for="'edit_pinned_' + post.id" class="text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer flex items-center gap-1.5 select-none">
                                        <i class="pi pi-thumbtack text-xs text-indigo-500"></i> Mantener publicación fijada
                                    </label>
                                </div>

                                <div v-if="post.poll_options && post.poll_options.length > 0" class="flex items-center gap-2 px-1">
                                    <input type="checkbox" :id="'edit_poll_' + post.id" v-model="editPollClosed" class="rounded text-red-600 focus:ring-red-500 border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 bg-white w-4 h-4 cursor-pointer transition-colors">
                                    <label :for="'edit_poll_' + post.id" class="text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer flex items-center gap-1.5 select-none">
                                        <i class="pi pi-lock text-xs text-red-500"></i> Cerrar encuesta (Congelar votos)
                                    </label>
                                </div>
                            </div>

                            <div class="mt-5 flex justify-end gap-3">
                                <button @click="cancelEdit" class="px-5 py-2 rounded-xl text-sm font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-[#2C2C2E] transition-colors">Cancelar</button>
                                <button @click="submitEdit(post.id)" :disabled="processingEdit" class="px-5 py-2 rounded-xl text-sm font-bold bg-indigo-600 text-white hover:bg-indigo-700 hover:shadow-md hover:shadow-indigo-500/20 transition-all flex items-center gap-2 disabled:opacity-70">
                                    <i v-if="processingEdit" class="pi pi-spin pi-spinner text-xs"></i>
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                        <div v-else class="mt-5">
                            <!-- Texto de la publicación -->
                            <div class="text-gray-800 dark:text-gray-200 text-[15px] sm:text-base leading-relaxed whitespace-pre-wrap">{{ post.content }}</div>
                            
                            <!-- Imagen si existe -->
                            <div v-if="post.image_url" class="mt-5 rounded-2xl overflow-hidden border border-gray-100 dark:border-zinc-800 shadow-sm flex justify-center bg-gray-50 dark:bg-black/40">
                                <img :src="post.image_url" alt="Imagen adjunta" class="w-full max-h-[500px] object-cover hover:opacity-95 transition-opacity cursor-pointer">
                            </div>
                        </div>

                        <!-- RENDERIZADO DE ENCUESTA (Rediseño Premium) -->
                        <div v-if="post.poll_options && post.poll_options.length > 0" class="mt-6 bg-white dark:bg-[#1C1C1E] rounded-2xl p-5 sm:p-6 border border-gray-200/60 dark:border-zinc-700 shadow-inner">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-chart-bar text-indigo-500 text-sm"></i>
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ post.is_multiple_choice ? 'Encuesta: Selección Múltiple' : 'Encuesta: Selecciona una opción' }}
                                    </span>
                                </div>
                                <span v-if="post.is_poll_closed" class="text-[10px] font-bold bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400 px-2 py-1 rounded-full uppercase tracking-wider">
                                    <i class="pi pi-lock text-[8px] mr-1"></i> Cerrada
                                </span>
                            </div>

                            <div class="space-y-3">
                                <button 
                                    v-for="option in post.poll_options" :key="option.id"
                                    @click="!post.is_poll_closed ? submitVote(option.id) : null"
                                    :disabled="post.is_poll_closed"
                                    class="relative w-full overflow-hidden rounded-xl border transition-all duration-300 text-left group"
                                    :class="[
                                        option.has_voted ? 'border-indigo-500 dark:border-indigo-500 shadow-md shadow-indigo-500/10' : 'border-gray-200 dark:border-zinc-700',
                                        !post.is_poll_closed && !option.has_voted ? 'hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-sm cursor-pointer' : '',
                                        post.is_poll_closed ? 'cursor-default opacity-90' : ''
                                    ]"
                                >
                                    <!-- Barra de Progreso (Fondo dinámico) -->
                                    <div 
                                        v-if="post.user_has_voted_any || post.is_poll_closed"
                                        class="absolute inset-y-0 left-0 transition-all duration-1000 ease-out" 
                                        :class="option.has_voted ? 'bg-indigo-50 dark:bg-indigo-500/20' : 'bg-gray-50 dark:bg-zinc-800/50'"
                                        :style="{ width: option.percentage + '%' }"
                                    ></div>
                                    
                                    <div class="relative z-10 flex justify-between items-center px-4 py-3.5">
                                        <div class="flex items-center gap-3.5 w-full">
                                            <!-- Círculo o Cuadrado interactivo -->
                                            <div class="w-5 h-5 flex-shrink-0 flex items-center justify-center transition-all duration-300 border-2"
                                                :class="[
                                                    post.is_multiple_choice ? 'rounded-md' : 'rounded-full',
                                                    option.has_voted ? 'border-indigo-500 bg-indigo-500 dark:border-indigo-500 dark:bg-indigo-500 scale-110' : 'border-gray-300 dark:border-zinc-500 group-hover:border-indigo-400 bg-white dark:bg-zinc-800'
                                                ]">
                                                <i v-if="option.has_voted" class="pi pi-check text-white text-[10px] font-extrabold"></i>
                                            </div>
                                            <!-- Texto de la opción -->
                                            <span class="text-[15px] font-medium transition-colors" :class="option.has_voted ? 'text-indigo-900 dark:text-white font-bold' : 'text-gray-700 dark:text-gray-300'">
                                                {{ option.text }}
                                            </span>
                                        </div>
                                        <!-- Porcentaje flotante -->
                                        <div v-if="post.user_has_voted_any || post.is_poll_closed" class="flex-shrink-0 pl-4">
                                            <span class="text-sm font-bold transition-colors" :class="option.has_voted ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400'">
                                                {{ option.percentage }}%
                                            </span>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            
                            <!-- Pie de encuesta (Votos totales) -->
                            <div class="mt-4 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                                <div class="flex -space-x-2">
                                    <!-- Simulamos avatares apilados para darle un toque social si hay votos -->
                                    <div v-if="post.poll_total_votes > 0" class="w-5 h-5 rounded-full bg-gray-200 dark:bg-zinc-700 border border-white dark:border-[#1C1C1E]"></div>
                                    <div v-if="post.poll_total_votes > 1" class="w-5 h-5 rounded-full bg-gray-300 dark:bg-zinc-600 border border-white dark:border-[#1C1C1E]"></div>
                                </div>
                                <span>{{ post.poll_total_votes }} {{ post.poll_total_votes === 1 ? 'persona votó' : 'personas votaron' }}</span>
                            </div>
                        </div>

                        <!-- LÍNEA DIVISORIA SUTIL -->
                        <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-200 dark:via-zinc-800 to-transparent my-5"></div>

                        <!-- ACCIONES (Likes y Comentarios) -->
                        <div class="flex items-center space-x-2 sm:space-x-6">
                            <!-- Botón Like -->
                            <button @click="toggleLike(post.id)" 
                                class="flex items-center gap-2.5 text-sm font-bold transition-all group px-3 py-1.5 rounded-full hover:bg-gray-50 dark:hover:bg-white/5"
                                :class="post.has_liked ? 'text-rose-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'">
                                <div class="relative w-8 h-8 flex items-center justify-center rounded-full transition-all duration-300"
                                    :class="post.has_liked ? 'bg-rose-100 dark:bg-rose-500/20 scale-110' : 'bg-gray-100 dark:bg-[#2C2C2E] group-hover:scale-110'">
                                    <i class="pi transition-all duration-300" :class="post.has_liked ? 'pi-heart-fill scale-110' : 'pi-heart'"></i>
                                </div>
                                <span class="tracking-wide">{{ post.likes_count > 0 ? post.likes_count : 'Me gusta' }}</span>
                            </button>
                            
                            <!-- Botón Comentar -->
                            <button @click="toggleCommentSection(post.id)" 
                                class="flex items-center gap-2.5 text-sm font-bold transition-all group px-3 py-1.5 rounded-full hover:bg-gray-50 dark:hover:bg-white/5"
                                :class="activeComments[post.id] ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'">
                                <div class="relative w-8 h-8 flex items-center justify-center rounded-full transition-all duration-300"
                                    :class="activeComments[post.id] ? 'bg-indigo-100 dark:bg-indigo-500/20 scale-110' : 'bg-gray-100 dark:bg-[#2C2C2E] group-hover:scale-110'">
                                    <i class="pi pi-comment"></i>
                                </div>
                                <span class="tracking-wide">{{ post.comments?.length || 0 }} {{ post.comments?.length === 1 ? 'Comentario' : 'Comentarios' }}</span>
                            </button>
                        </div>

                        <!-- SECCIÓN DE COMENTARIOS (Diseño Integrado) -->
                        <div v-show="activeComments[post.id]" class="mt-6 pt-1">
                            
                            <!-- Input para nuevo comentario -->
                            <div class="flex gap-3 items-center mb-6">
                                <div class="flex-shrink-0">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 flex items-center justify-center text-sm font-bold text-indigo-700 dark:text-indigo-300 shadow-sm">
                                        {{ getInitials($page.props.auth.user.name) }}
                                    </div>
                                </div>
                                <div class="flex-1 relative group">
                                    <input 
                                        v-model="newComments[post.id]" 
                                        @keyup.enter="submitComment(post.id)" 
                                        type="text" 
                                        placeholder="Escribe un comentario..." 
                                        class="w-full rounded-full border-0 bg-gray-100 dark:bg-black/30 text-[15px] py-3 pl-5 pr-14 focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white shadow-inner transition-all placeholder-gray-500 dark:placeholder-gray-500" 
                                        :disabled="processingComments[post.id]"
                                    >
                                    <button 
                                        @click="submitComment(post.id)" 
                                        :disabled="!newComments[post.id]?.trim() || processingComments[post.id]" 
                                        class="absolute right-1.5 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-full bg-indigo-600 text-white disabled:opacity-0 disabled:scale-75 disabled:invisible hover:bg-indigo-700 hover:scale-105 transition-all shadow-md">
                                        <i v-if="processingComments[post.id]" class="pi pi-spin pi-spinner text-sm"></i>
                                        <i v-else class="pi pi-send text-[13px] ml-0.5 mt-0.5"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Lista de Comentarios -->
                            <div class="space-y-4">
                                <div v-for="comment in post.comments" :key="comment.id" class="flex gap-3 group/comment">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-zinc-400">
                                            {{ getInitials(comment.user?.name) }}
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="inline-block bg-gray-100 dark:bg-zinc-800/60 rounded-2xl rounded-tl-sm px-4 py-2.5 max-w-full">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ comment.user?.name }}</p>
                                            <p class="text-[14px] text-gray-700 dark:text-gray-300 mt-0.5 leading-snug whitespace-pre-wrap">{{ comment.content }}</p>
                                        </div>
                                        <div class="mt-1 pl-2">
                                            <span class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">{{ comment.created_at_human || 'Recientemente' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Paginación con Estilo Apple -->
                <div v-if="posts.links && posts.links.length > 3" class="mt-10 flex justify-center pb-12">
                    <div class="flex items-center gap-1.5 bg-white/50 dark:bg-[#1C1C1E]/50 backdrop-blur-xl p-1.5 rounded-2xl shadow-sm border border-gray-200/50 dark:border-white/5">
                        <template v-for="(link, key) in posts.links" :key="key">
                            <div v-if="link.url === null" class="px-3.5 py-2 text-sm text-gray-400 dark:text-zinc-600 rounded-xl font-medium" v-html="link.label" />
                            <Link v-else :href="link.url" 
                                class="px-3.5 py-2 text-sm rounded-xl transition-all duration-200 font-semibold" 
                                :class="link.active ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 scale-105' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-800 hover:text-gray-900 dark:hover:text-white'" 
                                v-html="link.label" />
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
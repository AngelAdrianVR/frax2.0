<script setup>
import { ref, computed } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import CreatePostBox from '@/Components/CreatePostBox.vue'; // <-- Importamos nuestro nuevo componente ligero

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

        <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 text-gray-800 dark:text-zinc-100 p-4 sm:p-8 transition-colors duration-300 font-sans tracking-tight">
            
            <div class="max-w-3xl mx-auto space-y-6">
                
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Muro de Avisos</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Entérate de lo que sucede en tu comunidad y participa en encuestas.</p>
                </div>

                <!-- Usamos el componente externo para que este archivo no pese 1000 líneas -->
                <CreatePostBox />

                <!-- Feed de Publicaciones -->
                <div class="space-y-4">
                    <div v-if="posts.data.length === 0" class="bg-white dark:bg-[#1C1C1E] rounded-[24px] shadow-sm p-12 text-center border border-black/5 dark:border-white/5">
                        <div class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600 mb-4">
                            <i class="pi pi-comments" style="font-size: 3rem"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Aún no hay publicaciones</h3>
                        <p class="mt-1 text-sm text-gray-500">¡Sé el primero en compartir algo con tu comunidad!</p>
                    </div>

                    <div v-for="post in posts.data" :key="post.id" class="bg-white dark:bg-[#1C1C1E] rounded-[24px] p-5 md:p-6 shadow-[0_2px_10px_rgba(0,0,0,0.04)] dark:shadow-none border border-black/5 dark:border-white/5 transition-colors relative overflow-hidden">
                        
                        <!-- Etiqueta lateral -->
                        <div v-if="post.is_pinned" class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
                        <div v-else-if="post.type === 'alert'" class="absolute top-0 left-0 w-1.5 h-full bg-red-500"></div>

                        <!-- Badge -->
                        <div v-if="post.is_pinned || post.type !== 'general'" class="mb-4 flex items-center">
                            <span class="inline-flex items-center gap-x-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider"
                                :class="{
                                    'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400': post.is_pinned && post.type !== 'alert',
                                    'bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400': post.type === 'announcement' && !post.is_pinned,
                                    'bg-red-50 text-red-700 dark:bg-red-500/20 dark:text-red-400': post.type === 'alert'
                                }">
                                <i :class="post.type === 'alert' ? 'pi pi-exclamation-triangle' : 'pi pi-megaphone'" class="text-[10px]"></i>
                                {{ post.type === 'alert' ? 'Alerta' : (post.is_pinned ? 'Aviso Fijado' : 'Comunicado') }}
                            </span>
                        </div>

                        <!-- Cabecera -->
                        <div class="flex justify-between items-start">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center font-bold text-sm bg-gray-100 text-gray-600 dark:bg-zinc-800 dark:text-zinc-400">
                                        {{ getInitials(post.user.name) }}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ post.user.name }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">{{ post.created_at_human || 'Recientemente' }}</p>
                                </div>
                            </div>
                            
                            <button v-if="$page.props.auth.user.id === post.user_id || isAdmin" @click="confirmDelete(post.id)" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all">
                                <i class="pi pi-trash text-sm"></i>
                            </button>
                        </div>

                        <!-- Texto -->
                        <div class="mt-4 text-gray-800 dark:text-gray-200 text-[15px] leading-relaxed whitespace-pre-wrap">
                            {{ post.content }}
                        </div>

                        <!-- RENDERIZADO DE ENCUESTA (Estilo iOS / WhatsApp) -->
                        <div v-if="post.poll_options && post.poll_options.length > 0" class="mt-5 bg-gray-50 dark:bg-[#2C2C2E]/40 rounded-2xl p-4 border border-gray-100 dark:border-zinc-700/50">
                            <div class="space-y-2.5">
                                <button 
                                    v-for="option in post.poll_options" :key="option.id"
                                    @click="!post.user_has_voted_any ? submitVote(option.id) : null"
                                    :disabled="post.user_has_voted_any"
                                    class="relative w-full overflow-hidden rounded-xl border transition-all text-left group"
                                    :class="[
                                        option.has_voted ? 'border-indigo-500 dark:border-indigo-400' : 'border-gray-200 dark:border-zinc-600',
                                        !post.user_has_voted_any ? 'hover:border-indigo-400 hover:shadow-sm cursor-pointer' : 'cursor-default'
                                    ]"
                                >
                                    <!-- Barra de Progreso (solo visible si ya votaste) -->
                                    <div 
                                        v-if="post.user_has_voted_any"
                                        class="absolute inset-y-0 left-0 bg-indigo-100 dark:bg-indigo-500/20 transition-all duration-700 ease-out" 
                                        :style="{ width: option.percentage + '%' }"
                                    ></div>
                                    
                                    <div class="relative z-10 flex justify-between items-center px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <!-- Círculo de Check/Radio -->
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                                :class="option.has_voted ? 'border-indigo-500 bg-indigo-500 dark:border-indigo-400 dark:bg-indigo-400' : 'border-gray-300 dark:border-zinc-500 group-hover:border-indigo-400'">
                                                <i v-if="option.has_voted" class="pi pi-check text-white dark:text-zinc-900" style="font-size: 0.6rem; font-weight: bold;"></i>
                                            </div>
                                            <span class="text-sm font-medium" :class="option.has_voted ? 'text-indigo-900 dark:text-indigo-100 font-bold' : 'text-gray-700 dark:text-gray-200'">
                                                {{ option.text }}
                                            </span>
                                        </div>
                                        <span v-if="post.user_has_voted_any" class="text-sm font-bold" :class="option.has_voted ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500'">
                                            {{ option.percentage }}%
                                        </span>
                                    </div>
                                </button>
                            </div>
                            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400 font-medium">
                                {{ post.poll_total_votes }} votos en total
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="mt-5 pt-3 flex items-center space-x-6">
                            <button @click="toggleLike(post.id)" :class="post.has_liked ? 'text-rose-500' : 'text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="flex items-center space-x-2 text-sm font-bold transition-colors group">
                                <div :class="post.has_liked ? 'bg-rose-50 dark:bg-rose-500/10' : 'bg-gray-50 dark:bg-[#2C2C2E] group-hover:bg-gray-100 dark:group-hover:bg-zinc-700'" class="w-8 h-8 flex items-center justify-center rounded-full transition-colors">
                                    <i class="pi" :class="post.has_liked ? 'pi-heart-fill' : 'pi-heart'"></i>
                                </div>
                                <span>{{ post.likes_count > 0 ? post.likes_count : 'Me gusta' }}</span>
                            </button>
                            
                            <button @click="toggleCommentSection(post.id)" :class="activeComments[post.id] ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="flex items-center space-x-2 text-sm font-bold transition-colors group">
                                <div :class="activeComments[post.id] ? 'bg-indigo-50 dark:bg-indigo-500/10' : 'bg-gray-50 dark:bg-[#2C2C2E] group-hover:bg-gray-100 dark:group-hover:bg-zinc-700'" class="w-8 h-8 flex items-center justify-center rounded-full transition-colors">
                                    <i class="pi pi-comment"></i>
                                </div>
                                <span>{{ post.comments?.length || 0 }} Comentarios</span>
                            </button>
                        </div>

                        <!-- SECCIÓN DE COMENTARIOS (Oculto por defecto) -->
                        <div v-if="activeComments[post.id]" class="mt-4 pt-4 border-t border-gray-100 dark:border-zinc-800/50">
                            <!-- Input para nuevo comentario -->
                            <div class="flex gap-3 items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ getInitials($page.props.auth.user.name) }}
                                    </div>
                                </div>
                                <div class="flex-1 relative">
                                    <input v-model="newComments[post.id]" @keyup.enter="submitComment(post.id)" type="text" placeholder="Escribe un comentario..." class="w-full rounded-full border-gray-200 dark:border-zinc-700 bg-white dark:bg-[#1C1C1E] text-sm py-2.5 pl-4 pr-12 focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white shadow-sm transition-all" :disabled="processingComments[post.id]">
                                    <button @click="submitComment(post.id)" :disabled="!newComments[post.id]?.trim() || processingComments[post.id]" class="absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center rounded-full bg-indigo-600 text-white disabled:opacity-50 hover:bg-indigo-700 transition-all shadow-sm">
                                        <i v-if="processingComments[post.id]" class="pi pi-spin pi-spinner text-xs"></i>
                                        <i v-else class="pi pi-send text-xs ml-0.5 mt-0.5"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div v-for="comment in post.comments" :key="comment.id" class="flex gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-zinc-400">
                                            {{ getInitials(comment.user?.name) }}
                                        </div>
                                    </div>
                                    <div class="flex-1 bg-gray-50 dark:bg-[#2C2C2E] rounded-2xl px-4 py-3">
                                        <div class="flex justify-between items-baseline mb-1">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ comment.user?.name }}</span>
                                            <span class="text-[10px] text-gray-500 font-medium">{{ comment.created_at_human || 'Recientemente' }}</span>
                                        </div>
                                        <p class="text-[13px] text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ comment.content }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="posts.links && posts.links.length > 3" class="mt-8 flex justify-center pb-8">
                    <div class="flex flex-wrap gap-1 bg-white dark:bg-[#1C1C1E] p-1 rounded-2xl shadow-sm border border-black/5 dark:border-white/5">
                        <template v-for="(link, key) in posts.links" :key="key">
                            <div v-if="link.url === null" class="px-3 py-1.5 text-sm text-gray-300 dark:text-zinc-600 rounded-xl" v-html="link.label" />
                            <Link v-else :href="link.url" class="px-3 py-1.5 text-sm rounded-xl transition-all font-medium" :class="link.active ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#2C2C2E]'" v-html="link.label" />
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
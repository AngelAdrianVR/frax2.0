<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
// Asumiendo que tienes un Layout principal, impórtalo aquí si es necesario
// import AppLayout from '@/Layouts/AppLayout.vue'; 

const props = defineProps({
    posts: Object
});

const form = useForm({
    content: '',
    type: 'general',
    is_pinned: false,
});

const submitPost = () => {
    form.post(route('notice-board.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('content'),
    });
};

const toggleLike = (postId) => {
    router.post(route('notice-board.react', postId), {}, {
        preserveScroll: true,
    });
};

const deletePost = (postId) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta publicación?')) {
        router.delete(route('notice-board.destroy', postId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <!-- Contenedor principal con fondo gris claro típico de iOS -->
    <div class="min-h-screen bg-[#F2F2F7] py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto space-y-6">
            
            <!-- Título de la sección -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Muro de Avisos</h1>
                <p class="text-gray-500 mt-1">Entérate de lo que sucede en tu comunidad</p>
            </div>

            <!-- Tarjeta para crear publicación (Estilo iOS: bordes súper redondeados, sombra suave) -->
            <div class="bg-white rounded-3xl p-5 shadow-[0_2px_10px_rgba(0,0,0,0.04)]">
                <form @submit.prevent="submitPost">
                    <div class="flex items-start space-x-4">
                        <!-- Avatar del usuario actual (Placeholder si no tienes foto) -->
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                {{ $page.props.auth.user.name.charAt(0) }}
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <textarea 
                                v-model="form.content"
                                rows="3" 
                                class="block w-full rounded-2xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6 resize-none transition-all" 
                                placeholder="¿Qué quieres compartir con los vecinos?"></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between pl-14">
                        <div class="flex space-x-2">
                            <!-- Aquí podrías agregar botones para subir imágenes en el futuro -->
                        </div>
                        <button 
                            type="submit" 
                            :disabled="form.processing || !form.content.trim()"
                            class="inline-flex items-center rounded-full bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                            Publicar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Feed de Publicaciones -->
            <div class="space-y-5">
                <div v-for="post in posts.data" :key="post.id" class="bg-white rounded-3xl p-5 shadow-[0_2px_10px_rgba(0,0,0,0.04)] transition-all">
                    
                    <!-- Badge de Aviso Oficial (Fijado) -->
                    <div v-if="post.is_pinned || post.type === 'announcement'" class="mb-3 flex items-center">
                        <span class="inline-flex items-center gap-x-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                            <svg class="h-3 w-3 fill-current" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v7h1a1 1 0 010 2h-1v4.5a1.5 1.5 0 01-3 0V12H7a1 1 0 010-2h1V3a1 1 0 011-1z"/></svg>
                            Aviso de Administración
                        </span>
                    </div>

                    <!-- Cabecera del Post -->
                    <div class="flex justify-between items-start">
                        <div class="flex space-x-3">
                            <div class="flex-shrink-0">
                                <img v-if="post.user.profile_photo_url" :src="post.user.profile_photo_url" alt="" class="h-10 w-10 rounded-full bg-gray-100">
                                <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-medium">
                                    {{ post.user.name.charAt(0) }}
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ post.user.name }}</p>
                                <p class="text-xs text-gray-500">{{ post.created_at_human }}</p>
                            </div>
                        </div>
                        
                        <!-- Menú de Opciones (Eliminar) -->
                        <button v-if="$page.props.auth.user.id === post.user_id || $page.props.auth.user.roles?.includes('admin')" 
                                @click="deletePost(post.id)"
                                class="text-gray-400 hover:text-red-500 transition-colors rounded-full p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>

                    <!-- Contenido -->
                    <div class="mt-4 text-gray-800 text-[15px] leading-relaxed whitespace-pre-wrap">
                        {{ post.content }}
                    </div>

                    <!-- Botones de Acción (Like) -->
                    <div class="mt-5 border-t border-gray-100 pt-3 flex items-center space-x-6">
                        <button 
                            @click="toggleLike(post.id)"
                            :class="post.has_liked ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="flex items-center space-x-2 text-sm font-medium transition-colors group">
                            <div :class="post.has_liked ? 'bg-blue-50' : 'bg-gray-50 group-hover:bg-gray-100'" class="p-2 rounded-full transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" :fill="post.has_liked ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z" />
                                </svg>
                            </div>
                            <span>{{ post.likes_count > 0 ? post.likes_count : 'Me gusta' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Paginación Simple (Opcional visualmente) -->
            <div v-if="posts.links.length > 3" class="mt-6 flex justify-center">
                <!-- Aquí puedes integrar tu componente de paginación de Inertia -->
            </div>

        </div>
    </div>
</template>
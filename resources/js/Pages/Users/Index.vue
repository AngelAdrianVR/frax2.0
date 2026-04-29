<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

const props = defineProps({
    users: Object,
    filters: Object
});

const search = ref(props.filters.search || '');

// Buscador reactivo
watch(search, debounce((value) => {
    router.get(route('users.index'), { search: value }, { 
        preserveState: true, 
        replace: true,
        preserveScroll: true
    });
}, 300));
</script>

<template>
    <AppLayout title="Directorio de Residentes">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Encabezado y Buscador -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-8 px-4 sm:px-0 gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Directorio de la Comunidad</h1>
                        <p class="text-zinc-500 dark:text-zinc-400 mt-1">Conoce a tus vecinos y residentes del fraccionamiento.</p>
                    </div>
                    
                    <div class="w-full md:w-auto relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="pi pi-search text-zinc-400"></i>
                        </span>
                        <input 
                            v-model="search" 
                            type="text" 
                            placeholder="Buscar por nombre o correo..." 
                            class="pl-10 pr-4 py-2 w-full md:w-80 rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all"
                        >
                    </div>
                </div>

                <!-- Estado Vacío -->
                <div v-if="users.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <div class="mx-auto h-16 w-16 text-zinc-400 mb-4 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                        <i class="pi pi-users text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">No se encontraron residentes</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Prueba buscando con otro nombre o correo electrónico.</p>
                </div>

                <!-- Grid de Tarjetas de Residentes -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 px-4 sm:px-0">
                    <div v-for="user in users.data" :key="user.id" class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-zinc-100 dark:border-zinc-700 overflow-hidden group">
                        
                        <!-- Header / Banner de la tarjeta -->
                        <div class="h-24 bg-gradient-to-r from-indigo-500 to-blue-500 relative">
                            <!-- Badge del Rol -->
                            <span class="absolute top-3 right-3 px-2.5 py-1 text-[10px] font-bold uppercase rounded-full bg-white/20 text-white backdrop-blur-sm border border-white/30">
                                {{ user.role }}
                            </span>
                        </div>

                        <!-- Contenido -->
                        <div class="px-6 pb-6 relative">
                            <!-- Avatar Flotante -->
                            <div class="flex justify-center -mt-12 mb-3">
                                <img 
                                    :src="user.avatar" 
                                    :alt="user.name"
                                    class="h-24 w-24 rounded-full object-cover border-4 border-white dark:border-zinc-800 shadow-md bg-white"
                                >
                            </div>

                            <div class="text-center">
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white line-clamp-1" :title="user.name">
                                    {{ user.name }}
                                </h3>
                                
                                <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-700/50 text-sm font-medium text-zinc-600 dark:text-zinc-300 border border-zinc-100 dark:border-zinc-600">
                                    <i class="pi pi-home text-indigo-500"></i>
                                    {{ user.property }}
                                </div>
                            </div>

                            <!-- Botón de Contacto -->
                            <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-700 flex justify-center">
                                <a :href="`mailto:${user.email}`" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                    <i class="pi pi-envelope"></i>
                                    Enviar Correo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paginación Simple (Inertia Links) -->
                <div v-if="users.links && users.links.length > 3" class="flex justify-center mt-8 pb-8">
                    <div class="flex gap-1 flex-wrap justify-center">
                        <component
                            :is="link.url ? 'a' : 'span'"
                            v-for="(link, key) in users.links" :key="key"
                            :href="link.url"
                            v-html="link.label"
                            @click.prevent="link.url ? router.get(link.url, {}, { preserveScroll: true }) : null"
                            class="px-4 py-2 border rounded-lg text-sm transition-colors cursor-pointer"
                            :class="[
                                link.active 
                                    ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' 
                                    : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700',
                                !link.url && 'opacity-50 cursor-not-allowed'
                            ]"
                        />
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
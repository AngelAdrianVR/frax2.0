<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Sidebar from '@/Components/MyComponents/Sidebar.vue';

const props = defineProps(['title']);
const page = usePage();

// ── Multi-propiedad (Context Switcher) ──
const availableUnits = computed(() => page.props.auth.properties || []);
const currentUnit = computed(() => page.props.auth.current_property || null);
const currentRole = computed(() => page.props.auth.user.role || 'Usuario');
const showUnitDropdown = ref(false);

const switchUnit = (unit) => {
    showUnitDropdown.value = false;
    if (currentUnit.value && unit.property_id === currentUnit.value.property_id) return;
    router.post(route('context.switch'), { property_id: unit.property_id }, {
        preserveState: false,
        preserveScroll: true,
    });
};

// ── Sidebar ──
const isSidebarOpen = ref(true);
const isMobile = ref(false);

const checkScreenSize = () => {
    isMobile.value = window.innerWidth < 768;
    if (isMobile.value) {
        isSidebarOpen.value = false;
    } else {
        const savedState = localStorage.getItem('sidebar-state');
        isSidebarOpen.value = savedState === null ? true : savedState === 'true';
    }
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
    if (!isMobile.value) {
        localStorage.setItem('sidebar-state', isSidebarOpen.value);
    }
};

// ── Búsqueda global ──
const searchQuery = ref('');
const isSearchFocused = ref(false);

// ── Notificaciones ──
const showNotifications = ref(false);
const unreadCount = ref(3); // Mock — conectar a backend

// ── Dropdown de usuario ──
const showUserMenu = ref(false);

// ── Quick Actions ──
const showQuickActions = ref(false);

const closeAllDropdowns = (e) => {
    if (showUnitDropdown.value && !e.target.closest('#unit-selector')) showUnitDropdown.value = false;
    if (showUserMenu.value && !e.target.closest('#user-menu')) showUserMenu.value = false;
    if (showNotifications.value && !e.target.closest('#notifications-panel')) showNotifications.value = false;
    if (showQuickActions.value && !e.target.closest('#quick-actions')) showQuickActions.value = false;
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
    document.addEventListener('click', closeAllDropdowns);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize);
    document.removeEventListener('click', closeAllDropdowns);
});
</script>

<template>
    <div class="min-h-screen bg-[#f8f8f7] dark:bg-zinc-950 transition-colors duration-500 font-sans antialiased">
        <Head :title="title" />
        <Banner />

        <!-- Overlay móvil -->
        <div 
            v-if="isMobile && isSidebarOpen" 
            @click="toggleSidebar"
            class="fixed inset-0 z-40 bg-black/20 backdrop-blur-sm transition-opacity"
        ></div>

        <!-- Sidebar flotante -->
        <Sidebar :isOpen="isSidebarOpen" @toggleSidebar="toggleSidebar" />

        <!-- ── CONTENIDO PRINCIPAL ── -->
        <div 
            :class="[
                'min-h-screen flex flex-col transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]',
                !isMobile ? (isSidebarOpen ? 'ml-[280px]' : 'ml-[80px]') : 'ml-0'
            ]"
        >
            <!-- ═══════════════ HEADER GLASS ═══════════════ -->
            <header class="sticky top-0 z-30">
                <div class="mx-3 mt-3 px-4 h-14 flex items-center gap-3 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-2xl border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02),0_4px_16px_rgba(0,0,0,0.02)]">
                    
                    <!-- Botón hamburguesa (móvil) -->
                    <button 
                        v-if="isMobile" 
                        @click.stop="toggleSidebar" 
                        class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <!-- Breadcrumb / Título -->
                    <div class="hidden sm:block flex-shrink-0">
                        <h1 class="text-sm font-semibold text-zinc-800 dark:text-zinc-100 tracking-tight">{{ title }}</h1>
                    </div>

                    <!-- ═══ BARRA DE BÚSQUEDA ═══ -->
                    <div class="flex-1 max-w-md mx-auto relative">
                        <div :class="[
                            'flex items-center gap-2 px-3 h-9 rounded-xl transition-all duration-300',
                            isSearchFocused 
                                ? 'bg-white dark:bg-zinc-800 shadow-[0_0_0_2px_rgba(0,0,0,0.05)] dark:shadow-[0_0_0_2px_rgba(255,255,255,0.05)] ring-1 ring-zinc-200 dark:ring-zinc-700' 
                                : 'bg-zinc-100 dark:bg-zinc-800/50 ring-1 ring-transparent'
                        ]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0 text-zinc-400 dark:text-zinc-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            <input 
                                v-model="searchQuery"
                                @focus="isSearchFocused = true"
                                @blur="isSearchFocused = false"
                                type="text" 
                                placeholder="Buscar residentes, unidades, avisos..."
                                class="flex-1 bg-transparent border-none outline-none text-[13px] text-zinc-800 dark:text-zinc-200 placeholder:text-zinc-400 dark:placeholder:text-zinc-500"
                            />
                            <kbd class="hidden sm:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-medium text-zinc-400 dark:text-zinc-500 bg-zinc-200/50 dark:bg-zinc-700/50 rounded-md font-mono leading-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-2.5 h-2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                K
                            </kbd>
                        </div>
                    </div>

                    <!-- ═══ ACCIONES DERECHA ═══ -->
                    <div class="flex items-center gap-1 flex-shrink-0">

                        <!-- Quick Actions (+) -->
                        <div id="quick-actions" class="relative">
                            <button 
                                @click="showQuickActions = !showQuickActions"
                                class="w-8 h-8 flex items-center justify-center rounded-xl text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-700 dark:hover:text-zinc-300 transition-all"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            </button>
                            <transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 scale-95 -translate-y-1"
                                enter-to-class="opacity-100 scale-100 translate-y-0"
                                leave-active-class="transition ease-in duration-100"
                                leave-from-class="opacity-100 scale-100 translate-y-0"
                                leave-to-class="opacity-0 scale-95 -translate-y-1"
                            >
                                <div v-if="showQuickActions" class="absolute right-0 mt-2 w-56 bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.3)] ring-1 ring-black/[0.04] dark:ring-white/[0.04] py-2 z-50">
                                    <div class="px-4 py-1.5">
                                        <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em]">Acciones rápidas</p>
                                    </div>
                                    <Link :href="route('admin.private-units.create')" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" /></svg>
                                        Nueva Unidad
                                    </Link>
                                    <Link :href="route('users.create')" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>
                                        Alta Residente
                                    </Link>
                                    <Link :href="route('notice-board.index')" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84a11.66 11.66 0 01-.858-.595 8.09 8.09 0 01-.78-.61l-.002-.002a4.814 4.814 0 01-.686-.906 4.57 4.57 0 01-.378-.728M4.06 18.34a10.49 10.49 0 002.339 2.307A10.378 10.378 0 0112 21.75a10.404 10.404 0 005.602-1.683 10.49 10.49 0 002.338-2.307M4.06 5.66A10.49 10.49 0 016.4 3.353 10.38 10.38 0 0112 2.25a10.39 10.39 0 015.598 1.682c.861.6 1.633 1.35 2.301 2.209M4.06 5.66L12 12.75l7.94-7.09M4.06 5.66l-.163.163a1.97 1.97 0 00-.434.72 1.93 1.93 0 00-.213.832V18c0 .62.504 1.125 1.125 1.125H19.5c.621 0 1.125-.504 1.125-1.125V7.375c0-.284-.045-.562-.152-.826a1.968 1.968 0 00-.495-.726L19.94 5.66" /></svg>
                                        Publicar Aviso
                                    </Link>
                                </div>
                            </transition>
                        </div>

                        <!-- Notificaciones -->
                        <div id="notifications-panel" class="relative">
                            <button 
                                @click="showNotifications = !showNotifications"
                                class="relative w-8 h-8 flex items-center justify-center rounded-xl text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-700 dark:hover:text-zinc-300 transition-all"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                                <span v-if="unreadCount > 0" class="absolute top-1 right-1 w-[7px] h-[7px] bg-blue-500 rounded-full ring-2 ring-white dark:ring-zinc-900"></span>
                            </button>
                            <transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 scale-95 -translate-y-1"
                                enter-to-class="opacity-100 scale-100 translate-y-0"
                                leave-active-class="transition ease-in duration-100"
                                leave-from-class="opacity-100 scale-100 translate-y-0"
                                leave-to-class="opacity-0 scale-95 -translate-y-1"
                            >
                                <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.3)] ring-1 ring-black/[0.04] dark:ring-white/[0.04] py-2 z-50">
                                    <div class="flex items-center justify-between px-4 py-1.5">
                                        <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em]">Notificaciones</p>
                                        <span class="text-[11px] font-medium text-blue-500">{{ unreadCount }} sin leer</span>
                                    </div>
                                    <div class="px-4 py-6 text-center">
                                        <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                                        </div>
                                        <p class="text-[13px] text-zinc-500 dark:text-zinc-400">Todo al día por ahora</p>
                                    </div>
                                </div>
                            </transition>
                        </div>

                        <!-- ── SELECTOR DE PROPIEDAD ── -->
                        <div id="unit-selector" class="relative" v-if="currentUnit">
                            <button 
                                @click="showUnitDropdown = !showUnitDropdown"
                                class="flex items-center gap-2 h-8 px-2.5 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all text-zinc-600 dark:text-zinc-300"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-zinc-400 dark:text-zinc-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                                </svg>
                                <span class="hidden md:inline text-[12px] font-medium max-w-[120px] truncate">
                                    {{ currentUnit.address_label || currentUnit.unit_number }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :class="['w-3 h-3 transition-transform duration-300', showUnitDropdown ? 'rotate-180' : '']">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 scale-95 -translate-y-1"
                                enter-to-class="opacity-100 scale-100 translate-y-0"
                                leave-active-class="transition ease-in duration-100"
                                leave-from-class="opacity-100 scale-100 translate-y-0"
                                leave-to-class="opacity-0 scale-95 -translate-y-1"
                            >
                                <div v-if="showUnitDropdown" class="absolute right-0 mt-2 w-64 bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.3)] ring-1 ring-black/[0.04] dark:ring-white/[0.04] py-2 z-50">
                                    <div class="px-4 py-1.5">
                                        <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em]">Cambiar Propiedad</p>
                                    </div>
                                    <div class="max-h-56 overflow-y-auto custom-scrollbar">
                                        <button 
                                            v-for="unit in availableUnits" :key="unit.property_id"
                                            @click="switchUnit(unit)"
                                            class="w-full text-left px-4 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800 flex items-center gap-3 transition-colors"
                                        >
                                            <div :class="unit.property_id === currentUnit.property_id ? 'text-blue-500' : 'text-zinc-300 dark:text-zinc-600'">
                                                <svg v-if="unit.property_id === currentUnit.property_id" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" /></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[12px] font-medium text-zinc-800 dark:text-zinc-200 truncate">{{ unit.address_label || unit.unit_number }}</p>
                                                <p class="text-[10px] text-zinc-500 dark:text-zinc-400">{{ unit.subdivision_name }}</p>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </transition>
                        </div>

                        <!-- ── USER MENU ── -->
                        <div id="user-menu" class="relative">
                            <button 
                                @click="showUserMenu = !showUserMenu"
                                class="flex items-center gap-2 h-8 pl-1.5 pr-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all"
                            >
                                <img 
                                    class="h-6 w-6 rounded-lg object-cover ring-1 ring-black/[0.04] dark:ring-white/[0.06]" 
                                    :src="$page.props.auth?.user?.avatar || `https://ui-avatars.com/api/?name=${$page.props.auth?.user?.name}&color=7F9CF5&background=EBF4FF`" 
                                    alt=""
                                />
                                <span class="hidden lg:inline text-[12px] font-medium text-zinc-700 dark:text-zinc-300 max-w-[100px] truncate">
                                    {{ $page.props.auth?.user?.name }}
                                </span>
                            </button>
                            <transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 scale-95 -translate-y-1"
                                enter-to-class="opacity-100 scale-100 translate-y-0"
                                leave-active-class="transition ease-in duration-100"
                                leave-from-class="opacity-100 scale-100 translate-y-0"
                                leave-to-class="opacity-0 scale-95 -translate-y-1"
                            >
                                <div v-if="showUserMenu" class="absolute right-0 mt-2 w-56 bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.08)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.3)] ring-1 ring-black/[0.04] dark:ring-white/[0.04] py-2 z-50">
                                    <div class="px-4 py-2 border-b border-zinc-100 dark:border-zinc-800">
                                        <p class="text-[13px] font-semibold text-zinc-800 dark:text-zinc-200">{{ $page.props.auth?.user?.name }}</p>
                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">{{ $page.props.auth?.user?.email }}</p>
                                        <span class="inline-block mt-1 text-[10px] font-medium text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded-md">{{ currentRole }}</span>
                                    </div>
                                    <Link :href="route('profile.show')" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                        Perfil
                                    </Link>
                                    <Link :href="route('profile.show')" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /></svg>
                                        Configuración
                                    </Link>
                                    <div class="border-t border-zinc-100 dark:border-zinc-800 mt-1 pt-1">
                                        <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center gap-3 px-4 py-2.5 text-[13px] text-red-500 hover:bg-red-50 dark:hover:bg-red-950/50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                                            Cerrar Sesión
                                        </Link>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ── CONTENIDO DINÁMICO ── -->
            <main class="flex-1 p-3 md:p-5">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.25); border-radius: 20px; }
</style>
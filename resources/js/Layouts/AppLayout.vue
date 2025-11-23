<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Sidebar from '@/Components/MyComponents/Sidebar.vue';

defineProps({
    title: String,
});

// --- Control del Sidebar ---
const isSidebarOpen = ref(true);
const isMobile = ref(false);

const checkScreenSize = () => {
    isMobile.value = window.innerWidth < 1024;
    if (isMobile.value) {
        isSidebarOpen.value = false;
    } else {
        // Recuperar preferencia de tamaño en desktop
        const savedState = localStorage.getItem('sidebar-state');
        isSidebarOpen.value = savedState === 'true';
    }
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
    if (!isMobile.value) {
        localStorage.setItem('sidebar-state', isSidebarOpen.value);
    }
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
        <Head :title="title" />
        <Banner />

        <!-- Overlay para Móvil -->
        <div 
            v-if="isMobile && isSidebarOpen" 
            @click="isSidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        ></div>

        <!-- Componente Sidebar Extraído -->
        <Sidebar 
            :isOpen="isSidebarOpen" 
            @toggleSidebar="toggleSidebar"
        />

        <!-- CONTENIDO PRINCIPAL -->
        <div 
            :class="[
                'min-h-screen flex flex-col transition-all duration-300 ease-in-out', 
                // Ajuste de margen basado en el estado del sidebar
                isSidebarOpen && !isMobile ? 'ml-72' : (isMobile ? 'ml-0' : 'ml-20')
            ]"
        >
            <!-- Navbar Superior (Visible en Móvil o como barra auxiliar) -->
            <nav class="bg-white dark:bg-slate-900/80 sticky top-0 z-20 h-16 flex items-center px-4 justify-between lg:justify-end border-b border-gray-200 dark:border-slate-800 backdrop-blur-sm">
                
                <!-- Toggle Móvil -->
                <button v-if="isMobile" @click="toggleSidebar" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-slate-800">
                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Barra Superior Derecha -->
                <div class="flex items-center gap-4">
                     <!-- Aquí puedes poner notificaciones o el nombre del equipo actual -->
                     <div class="hidden md:flex flex-col items-end mr-2">
                        <span class="text-xs text-gray-400 dark:text-slate-500 uppercase font-bold">Equipo</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-slate-300">
                            {{ $page.props.auth.user.current_team?.name || 'General' }}
                        </span>
                     </div>
                </div>
            </nav>

            <!-- Contenido -->
            <main class="flex-1 p-6 bg-gray-50 dark:bg-slate-900">
                <slot />
            </main>
        </div>
    </div>
</template>
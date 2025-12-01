<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Sidebar from '@/Components/MyComponents/Sidebar.vue';

// CORRECCIÓN: Usamos sintaxis de array para evitar errores de compilación con objetos
const props = defineProps(['title']);

const page = usePage();

// --- 1. Lógica Multi-propiedad (Context Switcher) ---

// Mapeo a la estructura de HandleInertiaRequests.php
const availableUnits = computed(() => page.props.auth.properties || []); 
const currentUnit = computed(() => page.props.auth.current_property || null);

// Rol Actual: Viene calculado desde el Middleware (HandleInertiaRequests)
const currentRole = computed(() => page.props.auth.user.role || 'Usuario');

const showUnitDropdown = ref(false);

const switchUnit = (unit) => {
    showUnitDropdown.value = false;
    
    // Evitar recarga si es la misma unidad
    if (currentUnit.value && unit.property_id === currentUnit.value.property_id) return;

    router.post(route('context.switch'), { property_id: unit.property_id }, {
        preserveState: false, // Recargar para actualizar permisos (Spatie)
        preserveScroll: true,
    });
};

// --- 2. Control del Sidebar ---
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

const closeDropdowns = (e) => {
    if (showUnitDropdown.value && !e.target.closest('#unit-selector')) {
        showUnitDropdown.value = false;
    }
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
    document.addEventListener('click', closeDropdowns);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize);
    document.removeEventListener('click', closeDropdowns);
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-300 font-sans">
        <Head :title="title" />
        <Banner />

        <!-- Overlay Móvil -->
        <div 
            v-if="isMobile && isSidebarOpen" 
            @click="toggleSidebar"
            class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm transition-opacity"
        ></div>

        <!-- Sidebar -->
        <Sidebar 
            :isOpen="isSidebarOpen" 
            @toggleSidebar="toggleSidebar"
        />

        <!-- CONTENIDO PRINCIPAL -->
        <div 
            :class="[
                'min-h-screen flex flex-col transition-all duration-300 ease-in-out', 
                !isMobile ? (isSidebarOpen ? 'ml-72' : 'ml-20') : 'ml-0'
            ]"
        >
            <!-- Navbar Superior -->
            <nav class="bg-white dark:bg-slate-800/90 sticky top-0 z-20 h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-slate-700 backdrop-blur-sm shadow-sm">
                
                <!-- IZQUIERDA -->
                <div class="flex items-center gap-4">
                    <button 
                        v-if="isMobile" 
                        @click.stop="toggleSidebar" 
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-slate-700 transition-colors focus:outline-none"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    
                    <h1 class="hidden sm:block text-xl font-semibold text-gray-800 dark:text-white tracking-tight">
                        {{ title }}
                    </h1>
                </div>

                <!-- DERECHA: Selector de Propiedad -->
                <div class="flex items-center gap-3 md:gap-6">
                    
                    <!-- === SELECTOR DE PROPIEDAD === -->
                    <div class="relative" id="unit-selector" v-if="currentUnit">
                        <button 
                            @click="showUnitDropdown = !showUnitDropdown"
                            class="flex items-center gap-2 md:gap-3 pl-3 pr-2 py-1.5 rounded-full border border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-700/50 hover:bg-white dark:hover:bg-slate-700 transition-all shadow-sm group"
                        >
                            <!-- Icono Casa -->
                            <div class="p-1.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                 </svg>                                  
                            </div>

                            <!-- Texto Propiedad -->
                            <div class="text-left hidden md:block">
                                <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-slate-400 leading-none truncate max-w-[140px]">
                                    {{ currentUnit.subdivision_name || 'Fraccionamiento' }}
                                </p>
                                <!-- MODIFICADO: Usamos address_label (Calle + #) -->
                                <p class="text-sm font-bold text-gray-700 dark:text-slate-200 leading-tight truncate max-w-[140px]" :title="currentUnit.address_label || currentUnit.unit_number">
                                    {{ currentUnit.address_label || currentUnit.unit_number }}
                                </p>
                            </div>
                            
                            <!-- Flecha -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :class="['w-4 h-4 text-gray-400 transition-transform duration-200', showUnitDropdown ? 'rotate-180' : '']">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <transition
                            enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <div 
                                v-if="showUnitDropdown" 
                                class="absolute right-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 py-2 z-50 origin-top-right ring-1 ring-black ring-opacity-5"
                            >
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-slate-700">
                                    <p class="text-xs font-semibold text-gray-400 uppercase">Cambiar Propiedad</p>
                                </div>
                                
                                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                    <template v-if="availableUnits.length > 0">
                                        <button 
                                            v-for="unit in availableUnits" 
                                            :key="unit.property_id"
                                            @click="switchUnit(unit)"
                                            class="w-full text-left px-4 py-3 text-sm hover:bg-blue-50 dark:hover:bg-slate-700 flex items-center justify-between group transition-colors border-l-4"
                                            :class="unit.property_id === currentUnit.property_id ? 'bg-blue-50/50 dark:bg-slate-700/30 border-blue-500' : 'border-transparent'"
                                        >
                                            <div class="flex flex-col">
                                                <span class="text-xs text-gray-500 dark:text-slate-400 font-medium uppercase">
                                                    {{ unit.subdivision_name }}
                                                </span>
                                                <!-- MODIFICADO: Usamos address_label -->
                                                <span :class="unit.property_id === currentUnit.property_id ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-700 dark:text-slate-300 font-semibold'">
                                                    {{ unit.address_label || unit.unit_number }}
                                                </span>
                                                
                                                <!-- LÓGICA DE ROL -->
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span v-if="unit.community_role" class="text-[10px] text-white bg-blue-500 dark:bg-blue-600 px-1.5 py-0.5 rounded font-bold">
                                                        {{ unit.community_role }}
                                                    </span>
                                                    <span v-if="unit.role_in_unit && unit.role_in_unit !== 'Staff'" class="text-[10px] text-gray-400 bg-gray-100 dark:bg-slate-900 px-1.5 py-0.5 rounded">
                                                        {{ unit.role_in_unit }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Check activo -->
                                            <svg v-if="unit.property_id === currentUnit.property_id" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-blue-600 dark:text-blue-400">
                                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>
                                    <div v-else class="px-4 py-3 text-sm text-gray-500 italic text-center">
                                        No hay propiedades asignadas.
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-100 dark:border-slate-700 mt-1 pt-1">
                                    <Link :href="route('profile.show')" class="block px-4 py-2 text-xs text-blue-600 dark:text-blue-400 font-medium hover:underline text-center">
                                        Gestionar mis residencias
                                    </Link>
                                </div>
                            </div>
                        </transition>
                    </div>

                    <!-- Datos de Rol en la Unidad Actual -->
                    <div class="hidden lg:flex flex-col items-end border-l border-gray-200 dark:border-slate-700 pl-4 h-8 justify-center">
                        <span class="text-[10px] text-gray-400 dark:text-slate-500 uppercase font-bold tracking-wider">ROL ACTUAL</span>
                        <span class="text-xs font-bold text-gray-700 dark:text-slate-300 bg-gray-100 dark:bg-slate-700 px-2 py-0.5 rounded-md">
                            {{ currentRole }}
                        </span>
                     </div>

                </div>
            </nav>

            <!-- Contenido Dinámico -->
            <main class="flex-1 p-4 md:p-8 overflow-x-hidden">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.5); border-radius: 20px; }
</style>
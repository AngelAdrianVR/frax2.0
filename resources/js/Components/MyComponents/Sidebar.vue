<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';

// Definición explícita de props para evitar errores de compilación
const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true
    }
});

const emit = defineEmits(['toggleSidebar']);

// --- Referencias y Lógica de Click Outside ---
const sidebarRef = ref(null);

// Función para cerrar categorías si se hace click fuera
const handleClickOutside = (event) => {
    // Solo aplica si el sidebar está cerrado (modo iconos)
    if (!props.isOpen) {
        // Verificar si el click fue dentro de un dropdown flotante (que ahora está en el body)
        const dropdowns = document.querySelectorAll('.sidebar-floating-dropdown');
        let clickedInsideDropdown = false;
        
        dropdowns.forEach(el => {
            if (el.contains(event.target)) clickedInsideDropdown = true;
        });

        // Si el click no fue en el sidebar ni en un dropdown, cerramos todo
        if (!clickedInsideDropdown && sidebarRef.value && !sidebarRef.value.contains(event.target)) {
            closeAllCategories();
        }
    }
};

const closeAllCategories = () => {
    Object.keys(openCategories.value).forEach(key => {
        openCategories.value[key] = false;
    });
};

// --- Estado del Tema (Dark/Light) ---
const isDark = ref(false);
const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    // Recuperar tema
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
    
    // Listeners globales
    document.addEventListener('click', handleClickOutside);
    // Cerrar dropdowns al hacer scroll para evitar que se desfasen de su posición original
    window.addEventListener('scroll', closeAllCategories, true);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('scroll', closeAllCategories, true);
});

// --- Estado de Categorías y Posición del Dropdown ---
const openCategories = ref({
    inmobiliaria: false,
    finanzas: false,
    seguridad: false,
    operaciones: false,
});

// Almacena la posición dinámica (top/left) para el menú flotante
const dropdownPos = ref({ top: 0, left: 0 });

const toggleCategory = (categoryKey, event) => {
    // Lógica para cuando el sidebar está cerrado (Menú Flotante)
    if (!props.isOpen && event) {
        // Obtenemos la posición del botón que fue clickeado
        const buttonRect = event.currentTarget.getBoundingClientRect();
        
        // Posicionamos el menú alineado al top del botón y a la derecha del sidebar
        dropdownPos.value = {
            top: buttonRect.top,
            left: buttonRect.right
        };

        // Cerramos otras categorías para que actúe como un menú exclusivo
        Object.keys(openCategories.value).forEach(key => {
            if (key !== categoryKey) openCategories.value[key] = false;
        });
    }

    // Toggle normal
    openCategories.value[categoryKey] = !openCategories.value[categoryKey];
};

// --- Datos del Menú ---
const menuItems = [
    {
        category: 'Gestión Inmobiliaria',
        key: 'inmobiliaria',
        icon: 'M3 21h18M5 21V7l8-4 8 4v14M5 21h14', 
        items: [
            { name: 'Residentes', route: 'residents.index' },
            { name: 'Unidades Privadas', route: 'private_units.index' },
            { name: 'Vehículos y Mascotas', route: 'vehicles.index' },
        ]
    },
    {
        category: 'Finanzas y Cobranza',
        key: 'finanzas',
        icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        items: [
            { name: 'Cuotas Generadas', route: 'fees.index' },
            { name: 'Pagos Recibidos', route: 'payments.index' },
            { name: 'Conciliación Bancaria', route: 'bank.reconciliations' },
            { name: 'Morosos', route: 'slow.payers' },
        ]
    },
    {
        category: 'Seguridad y Acceso',
        key: 'seguridad',
        icon: 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
        items: [
            { name: 'Bitácora de Accesos', route: 'access_log.index' },
            { name: 'Visitas y QR', route: 'visits.index', badge: 5 }, 
            { name: 'Rondines', route: 'patrols.index' },
        ]
    },
    {
        category: 'Operaciones',
        key: 'operaciones',
        icon: 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z',
        items: [
            { name: 'Amenidades', route: 'amenities.index' },
            { name: 'Reservaciones', route: 'reservations.index' },
            { name: 'Proveedores', route: 'suppliers.index' },
        ]
    }
];

// Lógica de Estatus de Pago
const paymentStatus = ref('current');
const statusText = computed(() => {
    switch(paymentStatus.value) {
        case 'current': return 'Al corriente con los pagos';
        case 'late': return 'Pagos atrasados';
        case 'defaulter': return 'Deudas vencidas "Moroso"';
        default: return 'Estado desconocido';
    }
});
</script>

<template>
    <aside 
        ref="sidebarRef"
        :class="[
            'fixed top-0 left-0 z-50 h-screen transition-all duration-300 ease-in-out shadow-xl border-r flex flex-col',
            isOpen ? 'w-72' : 'w-20',
            // ESTILOS DE COLOR
            'bg-white border-gray-200',
            'dark:bg-slate-700/50 dark:backdrop-blur-md dark:border-slate-700/50'
        ]"
    >
        <!-- 1. Header (FIXED TOP) -->
        <div class="h-20 flex-none flex items-center justify-between px-6 z-10">
            <!-- Logo -->
            <div class="flex items-center gap-2 transition-opacity duration-300 overflow-hidden">
                <Link :href="route('dashboard')" class="flex-shrink-0">
                    <ApplicationMark class="block h-8 w-auto text-blue-600 dark:text-blue-400" />
                </Link>
                <span v-if="isOpen" class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white">
                        Frax<span class="text-blue-500">.</span>
                    </span>
            </div>
            
            <!-- Toggle Button (Visible if open) -->
            <button 
                v-if="isOpen"
                @click="$emit('toggleSidebar')" 
                class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 dark:text-slate-400 focus:outline-none ml-auto"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>
        </div>

        <!-- Toggle Button (Visible if closed - Centered) -->
        <div v-if="!isOpen" class="w-full flex-none flex justify-center mb-4">
             <button @click="$emit('toggleSidebar')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 dark:text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
             </button>
        </div>

        <!-- 2. Perfil y Semáforo -->
        <div class="px-4 mb-6 flex-none">
            <Link :href="route('profile.show')" :class="[
                'block relative rounded-2xl transition-all duration-300 border border-transparent group cursor-pointer',
                isOpen 
                    ? 'p-4 bg-slate-50 dark:bg-slate-800/50 dark:border-slate-700/50 hover:bg-blue-50 dark:hover:bg-slate-800' 
                    : 'p-0 bg-transparent flex justify-center'
            ]">
                <div class="flex items-center gap-4" :class="{'justify-center w-full': !isOpen}">
                    <!-- Avatar -->
                    <div class="relative flex-shrink-0">
                        <img class="h-10 w-10 md:h-12 md:w-12 rounded-full object-cover border-2 border-white dark:border-slate-600 shadow-sm group-hover:border-blue-200 transition-colors" 
                                :src="$page.props.auth.user.profile_photo_url" 
                                :alt="$page.props.auth.user.name">
                        
                        <!-- Semáforo con Tooltip -->
                        <div class="group/tooltip absolute -top-1 -right-1 cursor-help">
                            <div class="flex gap-0.5 bg-white dark:bg-slate-900 rounded-full px-1 py-0.5 shadow-sm border border-gray-100 dark:border-slate-700">
                                <div class="w-2 h-2 rounded-full transition-all" :class="paymentStatus === 'current' ? 'bg-green-500 shadow-[0_0_6px_rgba(34,197,94,0.8)] scale-110' : 'bg-gray-300 dark:bg-slate-700'"></div>
                                <div class="w-2 h-2 rounded-full transition-all" :class="paymentStatus === 'late' ? 'bg-amber-500 shadow-[0_0_6px_rgba(245,158,11,0.8)] scale-110' : 'bg-gray-300 dark:bg-slate-700'"></div>
                                <div class="w-2 h-2 rounded-full transition-all" :class="paymentStatus === 'defaulter' ? 'bg-red-500 shadow-[0_0_6px_rgba(239,68,68,0.8)] scale-110' : 'bg-gray-300 dark:bg-slate-700'"></div>
                            </div>
                            
                            <!-- Tooltip Body -->
                            <div class="absolute left-full top-0 ml-2 w-max max-w-[150px] invisible opacity-0 group-hover/tooltip:visible group-hover/tooltip:opacity-100 transition-all duration-200 z-50">
                                <div class="bg-gray-900 text-white text-xs rounded py-1 px-2 shadow-lg relative">
                                    {{ statusText }}
                                    <div class="absolute top-2 -left-1 w-2 h-2 bg-gray-900 transform rotate-45"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Texto Usuario -->
                    <div v-if="isOpen" class="overflow-hidden">
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate group-hover:text-blue-600 transition-colors">
                            {{ $page.props.auth.user.name }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            Ver Perfil
                        </p>
                    </div>
                </div>
            </Link>
        </div>

        <!-- 3. Navegación (SCROLLABLE) -->
        <nav class="flex-1 px-3 space-y-1 overflow-y-auto custom-scrollbar pb-4">
            <!-- Dashboard Link -->
            <Link 
                :href="route('dashboard')"
                :class="[
                    'flex items-center p-3 rounded-xl transition-all duration-200 mb-4',
                    route().current('dashboard')
                        ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30'
                        : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50'
                ]"
                :title="!isOpen ? 'Dashboard' : ''"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                <span v-if="isOpen" class="ml-3 font-medium">Dashboard</span>
            </Link>

            <div v-if="isOpen" class="px-2 mb-2 mt-4">
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Módulos</p>
            </div>

            <!-- Categorías -->
            <div v-for="(category, index) in menuItems" :key="index" class="relative group">
                <!-- Botón de Categoría: Pasamos el evento $event para calcular posición -->
                <button 
                    @click="(e) => toggleCategory(category.key, e)"
                    :class="[
                        'w-full flex items-center p-3 rounded-xl transition-all duration-200',
                        openCategories[category.key]
                            ? 'bg-slate-100 dark:bg-slate-800/60 text-slate-900 dark:text-white' 
                            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/30'
                    ]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="category.icon" />
                    </svg>

                    <div v-if="isOpen" class="flex-1 flex items-center justify-between ml-3 overflow-hidden">
                        <span class="text-sm font-medium whitespace-nowrap">{{ category.category }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :class="['w-3 h-3 transition-transform duration-200', openCategories[category.key] ? 'rotate-180' : '']">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <!-- DROPDOWN FLOTANTE (TELEPORT AL BODY) -->
                <!-- Esto saca el menú del sidebar para evitar que se corte o cause scroll -->
                <Teleport to="body">
                    <div v-if="!isOpen && openCategories[category.key]" 
                        class="fixed z-[9999] bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-700 py-2 w-56 animate-fade-in-up sidebar-floating-dropdown"
                        :style="{ top: `${dropdownPos.top}px`, left: `${dropdownPos.left + 12}px` }"
                    >
                         <div class="px-4 py-2 border-b border-gray-100 dark:border-slate-700 mb-1">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ category.category }}</span>
                         </div>
                         <template v-for="item in category.items" :key="item.name">
                            <Link 
                                v-if="route().has(item.route)" 
                                :href="route(item.route)" 
                                class="flex items-center justify-between px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors mx-2 rounded-lg"
                            >
                                <span>{{ item.name }}</span>
                                <span v-if="item.badge" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm shadow-red-500/30">
                                    {{ item.badge }}
                                </span>
                            </Link>
                         </template>
                    </div>
                </Teleport>

                <!-- ACORDEÓN NORMAL (DENTRO DEL SIDEBAR) -->
                <transition 
                    enter-active-class="transition-all duration-300 ease-in-out overflow-hidden" 
                    leave-active-class="transition-all duration-300 ease-in-out overflow-hidden" 
                    enter-from-class="max-h-0 opacity-0" 
                    enter-to-class="max-h-96 opacity-100" 
                    leave-from-class="max-h-96 opacity-100" 
                    leave-to-class="max-h-0 opacity-0"
                >
                    <div v-if="isOpen && openCategories[category.key]" class="pl-11 pr-2 space-y-1 pb-2 pt-1">
                        <template v-for="item in category.items" :key="item.name">
                            <Link 
                                v-if="route().has(item.route)" 
                                :href="route(item.route)" 
                                :class="[
                                    'flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors border-l-2',
                                    route().current(item.route) 
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10 font-medium' 
                                        : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/30'
                                ]"
                            >
                                <span>{{ item.name }}</span>
                                <span v-if="item.badge" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm shadow-red-500/30">
                                    {{ item.badge }}
                                </span>
                            </Link>
                            <span v-else class="block px-3 py-2 text-xs text-gray-400">{{ item.name }}</span>
                        </template>
                    </div>
                </transition>
            </div>
        </nav>

        <!-- 4. Footer (FIXED BOTTOM) -->
        <div class="flex-none p-4 border-t border-gray-200 dark:border-slate-700/50 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md z-10">
             
             <!-- Toggle Theme -->
             <button 
                @click="toggleTheme" 
                class="w-full flex items-center justify-center gap-2 p-2 mb-2 rounded-lg border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors"
                :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
            >
                <div v-if="isDark" class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-yellow-400"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                    <span v-if="isOpen" class="text-sm">Modo Claro</span>
                </div>
                <div v-else class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-indigo-500"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" /></svg>
                    <span v-if="isOpen" class="text-sm">Modo Oscuro</span>
                </div>
             </button>

             <!-- Logout -->
            <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 transition-colors justify-center md:justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                <span v-if="isOpen" class="text-sm font-medium">Cerrar Sesión</span>
            </Link>
        </div>
    </aside>
</template>

<style scoped>
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in-up { animation: fadeInUp 0.2s ease-out forwards; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(148, 163, 184, 0.3); border-radius: 20px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: rgba(148, 163, 184, 0.6); }
</style>
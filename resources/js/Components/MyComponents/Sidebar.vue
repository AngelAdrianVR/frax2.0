<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import ApplicationMark from '@/Components/ApplicationMark.vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true
    }
});

const emit = defineEmits(['toggleSidebar']);

const page = usePage();

// --- Lógica de Roles ---
const userRole = computed(() => page.props.auth.user.role || 'Residente'); 

// MODIFICACIÓN: Forzado a retornar siempre true para que te muestre todos los módulos sin importar el rol
const checkRole = (itemRoles) => {
    return true; 
};

// --- Referencias y Lógica de Click Outside ---
const sidebarRef = ref(null);

const handleClickOutside = (event) => {
    if (!props.isOpen) {
        const dropdowns = document.querySelectorAll('.sidebar-floating-dropdown');
        let clickedInsideDropdown = false;
        dropdowns.forEach(el => {
            if (el.contains(event.target)) clickedInsideDropdown = true;
        });

        if (!clickedInsideDropdown && sidebarRef.value && !sidebarRef.value.contains(event.target)) {
            closeAllCategories();
        }
    }
    
    if (window.innerWidth < 768 && props.isOpen && sidebarRef.value && !sidebarRef.value.contains(event.target)) {
        emit('toggleSidebar');
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

// --- Lógica de Estatus de Pago ---
const paymentStatus = ref('current');

const fetchPaymentStatus = async () => {
    if (userRole.value !== 'Residente') return;

    try {
        const response = await axios.get(route('payment.status'));
        paymentStatus.value = response.data.status;
    } catch (error) {
        console.error('Error obteniendo estatus de pagos:', error);
        paymentStatus.value = 'current';
    }
};

onMounted(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('scroll', closeAllCategories, true);

    fetchPaymentStatus();
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('scroll', closeAllCategories, true);
});

// --- Estado de Categorías y Posición del Dropdown ---
const openCategories = ref({});
const dropdownPos = ref({ top: 0, left: 0 });

const toggleCategory = (categoryKey, event) => {
    if (!props.isOpen && event) {
        const buttonRect = event.currentTarget.getBoundingClientRect();
        dropdownPos.value = { top: buttonRect.top, left: buttonRect.right };
        Object.keys(openCategories.value).forEach(key => {
            if (key !== categoryKey) openCategories.value[key] = false;
        });
    }
    if (openCategories.value[categoryKey] === undefined) {
        openCategories.value[categoryKey] = false;
    }
    openCategories.value[categoryKey] = !openCategories.value[categoryKey];
};

const isCategoryActive = (items) => {
    return items.some(item => route().has(item.route) && route().current(item.route));
};

// --- Lógica para Tooltips Flotantes ---
const hoveredTooltip = ref(null);
const tooltipPos = ref({ top: 0, left: 0 });

const showTooltip = (text, event) => {
    if (props.isOpen) return;
    const rect = event.currentTarget.getBoundingClientRect();
    hoveredTooltip.value = text;
    tooltipPos.value = {
        top: rect.top + (rect.height / 2),
        left: rect.right + 10
    };
};

const hideTooltip = () => {
    hoveredTooltip.value = null;
};

// --- Datos del Menú ---
const menuItems = computed(() => [
    {
        category: 'Mi Comunidad',
        key: 'comunidad',
        icon: 'pi pi-users', 
        items: [
            { name: 'Gestión de Propiedades', route: 'admin.private-units.index' },
            { name: 'Directorio / Residentes', route: 'users.index' },
            { name: 'Mascotas', route: ['Admin', 'Empleado'].includes(userRole.value) ? 'admin.pets.index' : 'pets.index', },
            { name: 'Vehículos', route: ['Admin', 'Empleado'].includes(userRole.value) ? 'admin.vehicles.index' : 'vehicles.index', },
        ]
    },
    {
        category: 'Finanzas',
        key: 'finanzas',
        icon: 'pi pi-wallet', 
        items: [
            { name: 'Mi Estado de Cuenta', route: 'fees.index'}, 
            { name: 'Ingresos y Pagos', route: 'payments.index'}, 
            { name: 'Morosos', route: 'slowPayers.index'},
            { name: 'Catálogo de Cobros', route: 'billing-concepts.index'}, 
            { name: 'Conciliación Bancaria', route: 'bank_reconciliations.index'},
        ]
    },
    {
        category: 'Amenidades',
        key: 'amenidades',
        icon: 'pi pi-calendar', 
        items: [
            { name: 'Reservaciones y Amenidades', route: 'amenities.index' },
        ]
    },
    {
        category: 'Caseta y Accesos',
        key: 'caseta',
        icon: 'pi pi-id-card', 
        items: [
            { name: 'Mis Invitaciones', route: 'register_invitations.index'},
            { name: 'Bitácora de Visitas', route: 'visits.index'},
            { name: 'Paquetería', route: 'parcel_services.index'},
        ]
    },
    {
        category: 'Seguridad',
        key: 'seguridad',
        icon: 'pi pi-shield', 
        items: [
            { name: 'Bitácora de Rondines', route: 'patrols.index' },
            { name: 'Puntos de Control', route: 'checkpoints.index'},
        ]
    },
    {
        category: 'Configuración',
        key: 'configuracion',
        icon: 'pi pi-cog', 
        items: [
            { name: 'Datos del Coto', route: 'subdivisions.index'},
            { name: 'Roles y Permisos', route: 'roles.index'},
        ]
    }
]);

const filteredMenuItems = computed(() => {
    return menuItems.value.map(category => {
        const filteredItems = category.items.filter(item => checkRole(item.allowedRoles));
        return {
            ...category,
            items: filteredItems
        };
    }).filter(category => category.items.length > 0);
});

const paymentStatusText = computed(() => {
    switch (paymentStatus.value) {
        case 'current': return 'Al corriente con los pagos';
        case 'late': return 'Pago(s) retrasado(s)';
        case 'defaulter': return 'Moroso';
        default: return '';
    }
});
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 bg-gray-900/50 z-40 md:hidden transition-opacity" @click="$emit('toggleSidebar')"></div>

    <aside 
        ref="sidebarRef"
        :class="[
            'fixed top-0 left-0 z-50 h-screen transition-transform duration-300 ease-in-out shadow-xl border-r flex flex-col',
            isOpen ? 'translate-x-0 w-72' : '-translate-x-full w-72 md:translate-x-0 md:w-20',
            'bg-white border-gray-200 dark:bg-zinc-800/30 dark:border-slate-700/50'
        ]"
    >
        <div class="h-20 flex-none flex items-center justify-between px-6 z-10">
            <div class="flex items-center gap-2 transition-opacity duration-300 overflow-hidden">
                <Link :href="route('dashboard') || '#'" class="flex-shrink-0">
                    <ApplicationMark class="block h-8 w-auto text-blue-600 dark:text-blue-400" />
                </Link>
                <span v-if="isOpen" class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white">
                    Frax<span class="text-blue-500">.</span>
                </span>
            </div>
            
            <button v-if="isOpen" @click="$emit('toggleSidebar')" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 dark:text-slate-400 focus:outline-none ml-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
            </button>
        </div>

        <div v-if="!isOpen" class="w-full flex-none hidden md:flex justify-center mb-4">
             <button @click="$emit('toggleSidebar')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 dark:text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
             </button>
        </div>

        <div class="px-4 mb-6 flex-none">
            <Link :href="route().has('profile.show') ? route('profile.show') : '#'" :class="[
                'block relative rounded-2xl transition-all duration-300 border border-transparent group cursor-pointer',
                isOpen ? 'p-4 bg-slate-50 dark:bg-slate-800/50 dark:border-slate-700/50 hover:bg-blue-50 dark:hover:bg-slate-800' : 'p-0 bg-transparent flex justify-center'
            ]">
                <div class="flex items-center gap-4" :class="{'justify-center w-full': !isOpen}">
                    <div class="relative flex-shrink-0">
                        <img class="h-10 w-10 md:h-14 md:w-14 rounded-full object-cover border-2 border-white dark:border-slate-600 shadow-sm group-hover:border-blue-200 transition-colors" 
                                :src="$page.props.auth?.user?.avatar || `https://ui-avatars.com/api/?name=${$page.props.auth?.user?.name}&color=7F9CF5&background=EBF4FF`" 
                                alt="Avatar">
                        
                        <div v-if="userRole === 'Residente'" class="group/tooltip absolute -top-4 -right-0 md:right-2 cursor-help" :title="paymentStatusText">
                            <div class="flex gap-0.5 bg-white dark:bg-slate-900 rounded-full px-1 py-0.5 shadow-sm border border-gray-100 dark:border-slate-700">
                                <div class="w-2 h-2 rounded-full transition-all" :class="paymentStatus === 'current' ? 'bg-green-500' : 'bg-gray-300 dark:bg-slate-700'"></div>
                                <div class="w-2 h-2 rounded-full transition-all" :class="paymentStatus === 'late' ? 'bg-amber-500' : 'bg-gray-300 dark:bg-slate-700'"></div>
                                <div class="w-2 h-2 rounded-full transition-all" :class="paymentStatus === 'defaulter' ? 'bg-red-500' : 'bg-gray-300 dark:bg-slate-700'"></div>
                            </div>
                        </div>
                    </div>

                    <div v-if="isOpen" class="overflow-hidden">
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate group-hover:text-blue-600 transition-colors">{{ $page.props.auth?.user?.name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ userRole }}</p>
                    </div>
                </div>
            </Link>
        </div>

        <nav class="flex-1 px-3 space-y-1 overflow-y-auto custom-scrollbar pb-4">
            <Link 
                :href="route().has('dashboard') ? route('dashboard') : '#'"
                @mouseenter="showTooltip('Dashboard', $event)" @mouseleave="hideTooltip"
                :class="[
                    'flex items-center p-3 rounded-xl transition-all duration-200 mb-4 group relative',
                    route().has('dashboard') && route().current('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50'
                ]"
            >
                <i class="pi pi-home text-xl flex-shrink-0 w-6 text-center"></i>
                <span v-if="isOpen" class="ml-3 font-medium">Dashboard</span>
            </Link>

            <div v-if="isOpen" class="px-2 mb-2 mt-4">
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Módulos</p>
            </div>

            <div v-for="(category, index) in filteredMenuItems" :key="index" class="relative group">
                <button 
                    @click="(e) => toggleCategory(category.key, e)"
                    @mouseenter="showTooltip(category.category, $event)" @mouseleave="hideTooltip"
                    :class="[
                        'w-full flex items-center p-3 rounded-xl transition-all duration-200',
                        openCategories[category.key] || isCategoryActive(category.items) ? 'bg-slate-100 dark:bg-slate-800/60 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/30'
                    ]"
                >
                    <i :class="[category.icon, 'text-xl flex-shrink-0 w-6 text-center']"></i>
                    <div v-if="isOpen" class="flex-1 flex items-center justify-between ml-3 overflow-hidden">
                        <span class="text-sm font-medium whitespace-nowrap">{{ category.category }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :class="['w-3 h-3 transition-transform duration-200', openCategories[category.key] ? 'rotate-180' : '']">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </button>

                <Teleport to="body">
                    <div v-if="!isOpen && openCategories[category.key]" class="fixed z-[9999] bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-700 py-2 w-56 animate-fade-in-up sidebar-floating-dropdown" :style="{ top: `${dropdownPos.top}px`, left: `${dropdownPos.left + 12}px` }">
                         <div class="px-4 py-2 border-b border-gray-100 dark:border-slate-700 mb-1">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ category.category }}</span>
                         </div>
                         <template v-for="item in category.items" :key="item.name">
                            <Link :href="route().has(item.route) ? route(item.route) : '#'" :class="[
                                    'flex items-center justify-between px-4 py-2 text-sm transition-colors mx-2 rounded-lg',
                                    route().has(item.route) && route().current(item.route) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700/50 hover:text-blue-600 dark:hover:text-blue-400'
                                ]">
                                <span>{{ item.name }}</span>
                                <span v-if="item.badge" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm shadow-red-500/30">{{ item.badge }}</span>
                            </Link>
                         </template>
                    </div>
                </Teleport>

                <transition enter-active-class="transition-all duration-300 ease-in-out overflow-hidden" leave-active-class="transition-all duration-300 ease-in-out overflow-hidden" enter-from-class="max-h-0 opacity-0" enter-to-class="max-h-96 opacity-100" leave-from-class="max-h-96 opacity-100" leave-to-class="max-h-0 opacity-0">
                    <div v-if="isOpen && openCategories[category.key]" class="pl-11 pr-2 space-y-1 pb-2 pt-1">
                        <template v-for="item in category.items" :key="item.name">
                            <Link :href="route().has(item.route) ? route(item.route) : '#'" :class="[
                                    'flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors border-l-2',
                                    route().has(item.route) && route().current(item.route) ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10 font-medium' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/30'
                                ]">
                                <span>{{ item.name }}</span>
                                <span v-if="item.badge" class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm shadow-red-500/30">{{ item.badge }}</span>
                            </Link>
                        </template>
                    </div>
                </transition>
            </div>
        </nav>

        <div class="flex-none p-4 border-t border-gray-200 dark:border-slate-700/50 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md z-10">
             <button @click="toggleTheme" class="w-full flex items-center justify-center gap-2 p-2 mb-2 rounded-lg border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors">
                <div v-if="isDark" class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-yellow-400"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                    <span v-if="isOpen" class="text-sm">Claro</span>
                </div>
                <div v-else class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-indigo-500"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" /></svg>
                    <span v-if="isOpen" class="text-sm">Oscuro</span>
                </div>
             </button>

            <Link :href="route().has('logout') ? route('logout') : '#'" method="post" as="button" class="w-full flex items-center gap-3 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 transition-colors justify-center md:justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                <span v-if="isOpen" class="text-sm font-medium">Salir</span>
            </Link>
        </div>
    </aside>

    <Teleport to="body">
        <transition name="fade">
            <div v-if="hoveredTooltip && !isOpen" class="fixed z-[10000] px-2 py-1 text-xs font-medium text-white bg-slate-900 rounded shadow-lg pointer-events-none transform -translate-y-1/2 whitespace-nowrap" :style="{ top: `${tooltipPos.top}px`, left: `${tooltipPos.left}px` }">{{ hoveredTooltip }}</div>
        </transition>
    </Teleport>
</template>

<style scoped>
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in-up { animation: fadeInUp 0.2s ease-out forwards; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(148, 163, 184, 0.3); border-radius: 20px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: rgba(148, 163, 184, 0.6); }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
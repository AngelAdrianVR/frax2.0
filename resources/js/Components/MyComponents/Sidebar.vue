<script setup>
import { ref, onMounted, computed, onUnmounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import ApplicationMark from '@/Components/ApplicationMark.vue';

const props = defineProps({
    isOpen: { type: Boolean, required: true }
});

const emit = defineEmits(['toggleSidebar']);

const page = usePage();

// ── Roles ──
const userRole = computed(() => page.props.auth.user.role || 'Residente');
const checkRole = () => true;

// ── Click Outside ──
const sidebarRef = ref(null);

const handleClickOutside = (event) => {
    if (!props.isOpen) {
        const dropdowns = document.querySelectorAll('.sidebar-floating-dropdown');
        let clickedInsideDropdown = false;
        dropdowns.forEach(el => { if (el.contains(event.target)) clickedInsideDropdown = true; });
        if (!clickedInsideDropdown && sidebarRef.value && !sidebarRef.value.contains(event.target)) {
            closeAllCategories();
        }
    }
    if (window.innerWidth < 768 && props.isOpen && sidebarRef.value && !sidebarRef.value.contains(event.target)) {
        emit('toggleSidebar');
    }
};

const closeAllCategories = () => {
    Object.keys(openCategories.value).forEach(key => { openCategories.value[key] = false; });
};

// ── Tema ──
const isDark = ref(false);
const toggleTheme = () => {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

// ── Estatus de Pago ──
const paymentStatus = ref('current');
const fetchPaymentStatus = async () => {
    if (userRole.value !== 'Residente') return;
    try {
        const response = await axios.get(route('payment.status'));
        paymentStatus.value = response.data.status;
    } catch (error) {
        paymentStatus.value = 'current';
    }
};

onMounted(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true; document.documentElement.classList.add('dark');
    } else {
        isDark.value = false; document.documentElement.classList.remove('dark');
    }
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('scroll', closeAllCategories, true);
    fetchPaymentStatus();
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('scroll', closeAllCategories, true);
});

// ── Categorías y Dropdown flotante ──
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
    if (openCategories.value[categoryKey] === undefined) openCategories.value[categoryKey] = false;
    openCategories.value[categoryKey] = !openCategories.value[categoryKey];
};

const isCategoryActive = (items) => items.some(item => route().has(item.route) && route().current(item.route));

// ── Tooltips flotantes (collapsed) ──
const hoveredTooltip = ref(null);
const tooltipPos = ref({ top: 0, left: 0 });
const showTooltip = (text, event) => {
    if (props.isOpen) return;
    const rect = event.currentTarget.getBoundingClientRect();
    hoveredTooltip.value = text;
    tooltipPos.value = { top: rect.top + rect.height / 2, left: rect.right + 10 };
};
const hideTooltip = () => { hoveredTooltip.value = null; };

// ── Menú ──
const menuItems = computed(() => [
    {
        category: 'Mi Comunidad',
        key: 'comunidad',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>`,
        items: [
            { name: 'Muro de avisos', route: 'notice-board.index' },
            { name: 'Gestión de Propiedades', route: 'admin.private-units.index' },
            { name: 'Directorio / Residentes', route: 'users.index' },
            { name: 'Mascotas', route: ['Admin','Empleado'].includes(userRole.value) ? 'admin.pets.index' : 'pets.index' },
            { name: 'Vehículos', route: ['Admin','Empleado'].includes(userRole.value) ? 'admin.vehicles.index' : 'vehicles.index' },
        ]
    },
    {
        category: 'Finanzas',
        key: 'finanzas',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>`,
        items: [
            { name: 'Mi Estado de Cuenta', route: 'fees.index' },
            { name: 'Ingresos y Pagos', route: 'payments.index' },
            { name: 'Morosos', route: 'slowPayers.index' },
            { name: 'Catálogo de Cobros', route: 'billing-concepts.index' },
            { name: 'Conciliación Bancaria', route: 'bank_reconciliations.index' },
        ]
    },
    {
        category: 'Amenidades',
        key: 'amenidades',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>`,
        items: [
            { name: 'Catálogo', route: 'amenities.index' },
            { name: 'Reservaciones', route: 'reservations.index' },
        ]
    },
    {
        category: 'Accesos y Seguridad',
        key: 'accesos',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>`,
        items: [
            { name: 'Dashboard de Guardia', route: 'access-control.dashboard' },
            { name: 'Bitácora de Visitas', route: 'visits.index' },
            { name: 'Visitantes Frecuentes', route: 'frequent-visitors.index' },
            { name: 'Paquetería', route: 'parcel-services.index' },
            { name: 'Invitaciones', route: 'register-invitations.index' },
            { name: 'Rondines', route: 'patrols.index' },
            { name: 'Puntos de Control', route: 'checkpoints.index' },
            { name: 'Incidencias', route: 'incidents.index' },
            { name: 'Bitácora de Accesos', route: 'access-logs.index' },
        ]
    },
    {
        category: 'Configuración',
        key: 'configuracion',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>`,
        items: [
            { name: 'Datos del Coto', route: 'subdivisions.index' },
            { name: 'Roles y Permisos', route: 'roles.index' },
        ]
    }
]);

const filteredMenuItems = computed(() => {
    return menuItems.value.map(cat => ({
        ...cat,
        items: cat.items.filter(() => checkRole())
    })).filter(cat => cat.items.length > 0);
});

const paymentStatusText = computed(() => {
    switch (paymentStatus.value) {
        case 'current': return 'Al corriente';
        case 'late': return 'Pago retrasado';
        case 'defaulter': return 'Moroso';
        default: return '';
    }
});
</script>

<template>
    <!-- Overlay móvil -->
    <div v-if="isOpen" class="fixed inset-0 bg-black/20 z-40 md:hidden backdrop-blur-sm transition-opacity" @click="$emit('toggleSidebar')" />

    <!-- ── SIDEBAR FLOTANTE ── -->
    <aside 
        ref="sidebarRef"
        :class="[
            'fixed z-50 flex flex-col transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]',
            // Margen flotante respecto a los bordes
            'top-3 bottom-3',
            // Posición horizontal y ancho
            isOpen ? 'left-3 w-64' : '-left-full w-64 md:left-3 md:w-[56px]',
            // Estilo premium: fondo, bordes redondeados, sombra suave
            'bg-white/90 dark:bg-zinc-900/90 backdrop-blur-xl',
            'rounded-[20px]',
            'shadow-[0_0_0_0.5px_rgba(0,0,0,0.06),0_4px_24px_rgba(0,0,0,0.04),0_8px_48px_rgba(0,0,0,0.02)]',
            'dark:shadow-[0_0_0_0.5px_rgba(255,255,255,0.06),0_4px_24px_rgba(0,0,0,0.2)]',
            'ring-1 ring-black/[0.02] dark:ring-white/[0.03]'
        ]"
    >
        <!-- Cabecera: Logo + Toggle -->
        <div class="flex-none flex items-center h-14 px-3">
            <div class="flex items-center gap-2.5 overflow-hidden flex-1 min-w-0">
                <Link :href="route('dashboard') || '#'" class="flex-shrink-0">
                    <ApplicationMark class="block h-7 w-auto text-zinc-800 dark:text-zinc-200" />
                </Link>
                <transition name="fade-slide">
                    <span v-if="isOpen" class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white whitespace-nowrap">
                        Frax<span class="text-zinc-400 dark:text-zinc-500">.</span>
                    </span>
                </transition>
            </div>
            <button 
                @click="$emit('toggleSidebar')" 
                class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-400 dark:text-zinc-500 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="isOpen ? 'M15.75 19.5L8.25 12l7.5-7.5' : 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5'" />
                </svg>
            </button>
        </div>

        <!-- Perfil de usuario compacto -->
        <div class="flex-none px-3 pb-3">
            <Link :href="route().has('profile.show') ? route('profile.show') : '#'" class="block group">
                <div :class="[
                    'flex items-center rounded-2xl transition-all duration-200',
                    isOpen ? 'gap-3 p-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' : 'justify-center'
                ]">
                    <div class="relative flex-shrink-0">
                        <img 
                            class="h-8 w-8 rounded-xl object-cover ring-1 ring-black/[0.04] dark:ring-white/[0.06] shadow-sm group-hover:ring-zinc-300 dark:group-hover:ring-zinc-600 transition-all" 
                            :src="$page.props.auth?.user?.avatar || `https://ui-avatars.com/api/?name=${$page.props.auth?.user?.name}&color=7F9CF5&background=EBF4FF`" 
                            alt="Avatar"
                        />
                        <!-- Indicador de estado de pago -->
                        <div v-if="userRole === 'Residente'" class="absolute -bottom-0.5 -right-0.5">
                            <div class="w-2.5 h-2.5 rounded-full ring-2 ring-white dark:ring-zinc-900 transition-colors"
                                :class="{
                                    'bg-emerald-500': paymentStatus === 'current',
                                    'bg-amber-500': paymentStatus === 'late',
                                    'bg-red-500': paymentStatus === 'defaulter'
                                }"
                            ></div>
                        </div>
                    </div>
                    <transition name="fade-slide">
                        <div v-if="isOpen" class="overflow-hidden flex-1 min-w-0">
                            <p class="text-[13px] font-semibold text-zinc-800 dark:text-zinc-100 truncate leading-tight">{{ $page.props.auth?.user?.name }}</p>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate leading-tight">{{ paymentStatus === 'current' ? userRole : paymentStatusText }}</p>
                        </div>
                    </transition>
                </div>
            </Link>
        </div>

        <!-- Separador sutil -->
        <div class="flex-none px-4">
            <div class="h-px bg-gradient-to-r from-transparent via-zinc-200 dark:via-zinc-800 to-transparent"></div>
        </div>

        <!-- Navegación -->
        <nav class="flex-1 overflow-y-auto custom-scrollbar px-2 py-3 space-y-1">
            <!-- Dashboard link -->
            <Link 
                :href="route().has('dashboard') ? route('dashboard') : '#'"
                @mouseenter="showTooltip('Dashboard', $event)" @mouseleave="hideTooltip"
                :class="[
                    'flex items-center rounded-xl transition-all duration-200 group',
                    isOpen ? 'px-3 py-2 gap-3' : 'justify-center py-2.5',
                    route().has('dashboard') && route().current('dashboard') 
                        ? 'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 shadow-sm' 
                        : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60 hover:text-zinc-800 dark:hover:text-zinc-200'
                ]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <transition name="fade-slide">
                    <span v-if="isOpen" class="text-[13px] font-medium leading-none">Dashboard</span>
                </transition>
            </Link>

            <!-- Separador de sección -->
            <div v-if="isOpen" class="pt-3 pb-1 px-3">
                <p class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em]">Módulos</p>
            </div>
            <div v-else class="py-1">
                <div class="mx-3 h-px bg-zinc-100 dark:bg-zinc-800"></div>
            </div>

            <!-- Categorías del menú -->
            <div v-for="(category, idx) in filteredMenuItems" :key="idx" class="relative">
                <button 
                    @click="(e) => toggleCategory(category.key, e)"
                    @mouseenter="showTooltip(category.category, $event)" @mouseleave="hideTooltip"
                    :class="[
                        'w-full flex items-center rounded-xl transition-all duration-200 group',
                        isOpen ? 'px-3 py-2 gap-3' : 'justify-center py-2.5',
                        openCategories[category.key] || isCategoryActive(category.items)
                            ? 'bg-zinc-100 dark:bg-zinc-800/70 text-zinc-800 dark:text-zinc-200' 
                            : 'text-zinc-500 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 hover:text-zinc-700 dark:hover:text-zinc-300'
                    ]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] flex-shrink-0" v-html="category.icon"></svg>
                    <transition name="fade-slide">
                        <div v-if="isOpen" class="flex-1 flex items-center justify-between min-w-0">
                            <span class="text-[13px] font-medium leading-none whitespace-nowrap">{{ category.category }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" :class="['w-3 h-3 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]', openCategories[category.key] ? 'rotate-180' : '']">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </transition>
                </button>

                <!-- Dropdown flotante (modo colapsado) -->
                <Teleport to="body">
                    <div 
                        v-if="!isOpen && openCategories[category.key]" 
                        class="fixed z-[9999] bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl rounded-2xl shadow-[0_0_0_0.5px_rgba(0,0,0,0.06),0_20px_60px_rgba(0,0,0,0.08)] dark:shadow-[0_0_0_0.5px_rgba(255,255,255,0.06),0_20px_60px_rgba(0,0,0,0.3)] ring-1 ring-black/[0.03] dark:ring-white/[0.04] py-2 w-56 animate-fade-in-up sidebar-floating-dropdown" 
                        :style="{ top: `${dropdownPos.top}px`, left: `${dropdownPos.left + 12}px` }"
                    >
                        <div class="px-4 py-1.5 mb-1">
                            <span class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.08em]">{{ category.category }}</span>
                        </div>
                        <Link 
                            v-for="item in category.items" :key="item.name"
                            :href="route().has(item.route) ? route(item.route) : '#'" 
                            :class="[
                                'flex items-center justify-between px-4 py-2 text-[13px] transition-colors mx-2 rounded-lg',
                                route().has(item.route) && route().current(item.route) 
                                    ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-medium' 
                                    : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-800 dark:hover:text-zinc-200'
                            ]"
                        >
                            <span>{{ item.name }}</span>
                            <span v-if="item.badge" class="bg-red-500 text-white text-[10px] font-semibold px-1.5 py-0.5 rounded-full">{{ item.badge }}</span>
                        </Link>
                    </div>
                </Teleport>

                <!-- Submenú expandido (modo abierto) -->
                <transition 
                    enter-active-class="overflow-hidden transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
                    leave-active-class="overflow-hidden transition-all duration-200 ease-[cubic-bezier(0.16,1,0.3,1)]"
                    enter-from-class="max-h-0 opacity-0"
                    enter-to-class="max-h-[500px] opacity-100"
                    leave-from-class="max-h-[500px] opacity-100"
                    leave-to-class="max-h-0 opacity-0"
                >
                    <div v-if="isOpen && openCategories[category.key]" class="space-y-0.5 pb-1.5 pt-0.5 pl-10 pr-2">
                        <Link 
                            v-for="item in category.items" :key="item.name"
                            :href="route().has(item.route) ? route(item.route) : '#'" 
                            :class="[
                                'flex items-center justify-between px-3 py-1.5 rounded-lg text-[13px] transition-all duration-200',
                                route().has(item.route) && route().current(item.route) 
                                    ? 'text-zinc-900 dark:text-zinc-100 font-medium bg-zinc-100 dark:bg-zinc-800/50' 
                                    : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/30'
                            ]"
                        >
                            <span>{{ item.name }}</span>
                            <span v-if="item.badge" class="bg-red-500 text-white text-[10px] font-semibold px-1.5 py-0.5 rounded-full">{{ item.badge }}</span>
                        </Link>
                    </div>
                </transition>
            </div>
        </nav>

        <!-- Footer: Tema + Logout -->
        <div class="flex-none px-3 pb-3 pt-1">
            <div class="h-px bg-gradient-to-r from-transparent via-zinc-200 dark:via-zinc-800 to-transparent mb-3"></div>
            <div :class="['flex items-center', isOpen ? 'gap-1' : 'flex-col gap-2']">
                <!-- Toggle tema -->
                <button 
                    @click="toggleTheme" 
                    :class="[
                        'flex items-center rounded-xl transition-all duration-200 text-zinc-400 dark:text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-600 dark:hover:text-zinc-300',
                        isOpen ? 'gap-2.5 px-3 py-2 flex-1' : 'p-2 justify-center'
                    ]"
                >
                    <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] flex-shrink-0 text-amber-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] flex-shrink-0 text-zinc-400"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" /></svg>
                    <transition name="fade-slide">
                        <span v-if="isOpen" class="text-[13px] font-medium">{{ isDark ? 'Tema Claro' : 'Tema Oscuro' }}</span>
                    </transition>
                </button>

                <!-- Logout -->
                <Link 
                    :href="route().has('logout') ? route('logout') : '#'" method="post" as="button" 
                    :class="[
                        'flex items-center rounded-xl transition-all duration-200 text-zinc-400 dark:text-zinc-500 hover:bg-red-50 dark:hover:bg-red-950/50 hover:text-red-500 dark:hover:text-red-400',
                        isOpen ? 'gap-2.5 px-3 py-2' : 'p-2 justify-center'
                    ]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px] flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                    <transition name="fade-slide">
                        <span v-if="isOpen" class="text-[13px] font-medium">Cerrar Sesión</span>
                    </transition>
                </Link>
            </div>
        </div>
    </aside>

    <!-- Tooltip flotante (modo colapsado) -->
    <Teleport to="body">
        <transition name="tooltip-fade">
            <div 
                v-if="hoveredTooltip && !isOpen" 
                class="fixed z-[10000] px-2.5 py-1.5 text-[11px] font-medium text-white bg-zinc-900 dark:bg-zinc-100 dark:text-zinc-900 rounded-lg shadow-lg pointer-events-none whitespace-nowrap"
                :style="{ top: `${tooltipPos.top}px`, left: `${tooltipPos.left}px` }"
            >{{ hoveredTooltip }}</div>
        </transition>
    </Teleport>
</template>

<style scoped>
@keyframes fadeInUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in-up { animation: fadeInUp 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

.fade-slide-enter-active { transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
.fade-slide-leave-active { transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1); }
.fade-slide-enter-from { opacity: 0; transform: translateX(-8px); }
.fade-slide-leave-to { opacity: 0; transform: translateX(-8px); }

.tooltip-fade-enter-active { transition: opacity 0.15s ease, transform 0.15s cubic-bezier(0.16, 1, 0.3, 1); }
.tooltip-fade-leave-active { transition: opacity 0.1s ease; }
.tooltip-fade-enter-from { opacity: 0; transform: translateX(-4px); }
.tooltip-fade-leave-to { opacity: 0; }

.custom-scrollbar::-webkit-scrollbar { width: 3px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(148, 163, 184, 0.2); border-radius: 20px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: rgba(148, 163, 184, 0.35); }
</style>
<script>
import { Head } from '@inertiajs/vue3';

export default {
    name: 'LandingLayout',
    props: {
        title: String,
    },
    data() {
        return {
            isMobileMenuOpen: false,
            isDesktopDrawerOpen: false, // Estado para el drawer lateral de escritorio
            showNavbar: true,
            lastScrollPosition: 0,
            isAtTop: true,
            scrollPercentage: 0,
        };
    },
    components:{
        Head
    },
    methods: {
        toggleMobileMenu() {
            this.isMobileMenuOpen = !this.isMobileMenuOpen;
            // Bloquear scroll cuando el menú está abierto
            document.body.style.overflow = this.isMobileMenuOpen ? 'hidden' : '';
        },
        toggleDesktopDrawer() {
            this.isDesktopDrawerOpen = !this.isDesktopDrawerOpen;
            document.body.style.overflow = this.isDesktopDrawerOpen ? 'hidden' : '';
        },
        navigateTo(route) {
            console.log(`Navegando a: ${route}`);
            // Aquí iría tu router.visit o similar
        },
        scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                // Offset para compensar el navbar fijo
                const headerOffset = 80; 
                const elementPosition = element.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
            
            // Cerrar menús si están abiertos
            this.isMobileMenuOpen = false;
            document.body.style.overflow = '';
        },
        handleScroll() {
            const currentScrollPosition = window.scrollY || document.documentElement.scrollTop;
            
            if (currentScrollPosition < 0) return;
            
            // Ocultar/Mostrar Navbar
            if (Math.abs(currentScrollPosition - this.lastScrollPosition) < 60) return;
            this.showNavbar = currentScrollPosition < this.lastScrollPosition;
            this.lastScrollPosition = currentScrollPosition;
            
            // Top absoluto
            this.isAtTop = currentScrollPosition < 50;

            // Scroll Percentage
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (currentScrollPosition / height) * 100;
            this.scrollPercentage = Math.min(scrolled, 100);
        },
        scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
        document.title = this.title ? `${this.title} - Frax` : 'Frax';
    },
    beforeUnmount() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    computed: {
        strokeDashoffset() {
            const circumference = 30 * 2 * Math.PI; 
            return circumference - (this.scrollPercentage / 100) * circumference;
        }
    }
}
</script>

<template>
    <Head :title="title" />
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900 selection:bg-blue-500 selection:text-white">

        <!-- NAVBAR INTELIGENTE -->
        <header 
            class="fixed w-full z-50 transition-all duration-500 ease-in-out transform"
            :class="[
                showNavbar ? 'translate-y-0' : '-translate-y-full',
                isAtTop ? 'bg-transparent py-6' : 'bg-white/90 backdrop-blur-md shadow-lg py-4'
            ]"
        >
            <nav class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-1 cursor-pointer z-50" @click="scrollToTop">
                    <img class="h-10" src="@/../../public/images/isologo-grande.png" alt="Frax Logo" onerror="this.style.display='none'"> 
                    <span v-if="true" class="text-2xl font-bold tracking-tighter" :class="(isAtTop && !isMobileMenuOpen) ? 'text-white' : 'text-gray-900'">
                        Frax<span class="text-blue-500">.</span>
                    </span>
                </div>

                <!-- Menu Desktop Central -->
                <div class="hidden md:flex items-center space-x-8">
                    <button @click="scrollToSection('servicios')" 
                       class="text-sm font-medium transition-colors hover:text-blue-500"
                       :class="isAtTop ? 'text-gray-200' : 'text-gray-600'">
                        Servicios
                    </button>
                    <button @click="scrollToSection('infraestructura')" 
                       class="text-sm font-medium transition-colors hover:text-blue-500"
                       :class="isAtTop ? 'text-gray-200' : 'text-gray-600'">
                        Infraestructura
                    </button>
                    <button @click="scrollToSection('planes')" 
                       class="text-sm font-medium transition-colors hover:text-blue-500"
                       :class="isAtTop ? 'text-gray-200' : 'text-gray-600'">
                        Planes
                    </button>
                    <button @click="scrollToSection('nosotros')" 
                       class="text-sm font-medium transition-colors hover:text-blue-500"
                       :class="isAtTop ? 'text-gray-200' : 'text-gray-600'">
                        Nosotros
                    </button>
                </div>

                <!-- Botones Acción Desktop -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Hamburger para Drawer Lateral (Desktop) -->
                    <button @click="toggleDesktopDrawer" class="mr-2 focus:outline-none group p-2 rounded-full hover:bg-white/20 transition-colors">
                        <div class="space-y-1.5">
                            <span class="block w-6 h-0.5 bg-current transition-transform group-hover:w-4 ml-auto" :class="isAtTop ? '!bg-white' : 'bg-gray-900'"></span>
                            <span class="block w-8 h-0.5 bg-current transition-transform" :class="isAtTop ? '!bg-white' : 'bg-gray-900'"></span>
                            <span class="block w-5 h-0.5 bg-current transition-transform group-hover:w-8 ml-auto" :class="isAtTop ? '!bg-white' : 'bg-gray-900'"></span>
                        </div>
                    </button>

                    <button class="text-sm font-medium transition-colors hover:text-blue-500"
                        :class="isAtTop ? 'text-white' : 'text-gray-900'"
                        @click="$inertia.visit(route('login'))">
                        Ingresar
                    </button>
                    <button class="px-5 py-2.5 rounded-full text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/30 hover:scale-105"
                         @click="navigateTo('registro')">
                        Empezar Gratis
                    </button>
                </div>

                <!-- Mobile Menu Toggle Button -->
                <button @click="toggleMobileMenu" class="md:hidden text-2xl z-50 relative" :class="(isAtTop && !isMobileMenuOpen) ? 'text-white' : 'text-gray-900'">
                    <i :class="isMobileMenuOpen ? 'pi pi-times' : 'pi pi-bars'"></i>
                </button>
            </nav>
        </header>

        <!-- ==========================================
             MENÚ MÓVIL (Overlay)
             ========================================== -->
        <div 
            class="fixed inset-0 z-40 bg-white flex flex-col pt-24 px-6 md:hidden transition-transform duration-300 ease-in-out"
            :class="isMobileMenuOpen ? 'translate-x-0' : 'translate-x-full'"
        >
            <div class="flex flex-col space-y-6 text-xl font-medium text-gray-900">
                <button @click="scrollToSection('servicios')" class="text-left border-b border-gray-100 pb-4 hover:text-blue-600">Servicios</button>
                <button @click="scrollToSection('infraestructura')" class="text-left border-b border-gray-100 pb-4 hover:text-blue-600">Infraestructura</button>
                <button @click="scrollToSection('planes')" class="text-left border-b border-gray-100 pb-4 hover:text-blue-600">Planes y Precios</button>
                <button @click="scrollToSection('nosotros')" class="text-left border-b border-gray-100 pb-4 hover:text-blue-600">Nosotros</button>
                <button @click="navigateTo('login')" class="text-left pt-4 text-blue-600">Iniciar Sesión</button>
                <button @click="navigateTo('registro')" class="bg-blue-600 text-white py-3 rounded-xl text-center shadow-lg shadow-blue-600/20">
                    Empezar Gratis
                </button>
            </div>
        </div>

        <!-- ==========================================
             DRAWER LATERAL (Desktop) - OPTIMIZADO
             ========================================== -->
        <!-- Overlay Fondo Oscuro: Quitamos backdrop-blur-sm para rendimiento -->
        <div 
            v-if="isDesktopDrawerOpen" 
            @click="toggleDesktopDrawer"
            class="fixed inset-0 z-[60] bg-black/60 transition-opacity duration-300"
        ></div>

        <!-- Panel Lateral: Añadimos will-change-transform para GPU acceleration -->
        <div 
            class="fixed top-0 right-0 h-full w-[400px] bg-[#0B0F19] z-[70] shadow-2xl transform transition-transform duration-500 ease-out text-white p-10 flex flex-col border-l border-gray-800 will-change-transform"
            :class="isDesktopDrawerOpen ? 'translate-x-0' : 'translate-x-full'"
        >
            <!-- Cabecera Drawer -->
            <div class="flex justify-between items-center mb-12">
                <div class="flex items-center gap-2">
                    <img class="h-8" src="@/../../public/images/isologo-grande.png" alt="" onerror="this.style.display='none'">
                    <span class="text-2xl font-bold tracking-tighter text-white">
                        Frax<span class="text-blue-500">.</span>
                    </span>
                </div>
                <button @click="toggleDesktopDrawer" class="text-gray-400 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-full">
                    <i class="pi pi-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-12 overflow-y-auto custom-scrollbar flex-1">
                <!-- Texto Intro -->
                <p class="text-gray-400 leading-relaxed">
                    Desarrollando experiencias personalizadas para incrementar la plusvalía y seguridad de tu comunidad. Reconocidos por líderes de la industria.
                </p>

                <!-- Search -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-4">Buscar en Frax</h4>
                    <div class="relative">
                        <input type="text" placeholder="¿Qué estás buscando?" 
                            class="w-full bg-gray-900 border border-gray-700 text-white px-4 py-3 pr-12 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-400 p-2">
                            <i class="pi pi-search text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-6">Contacto</h4>
                    <ul class="space-y-6">
                        <li>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Teléfono / WhatsApp</p>
                            <p class="text-lg font-medium hover:text-blue-400 cursor-pointer transition-colors">+52 55 1234 5678</p>
                        </li>
                        <li>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Correo Corporativo</p>
                            <p class="text-lg font-medium hover:text-blue-400 cursor-pointer transition-colors">contacto@frax.com</p>
                        </li>
                        <li>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Oficinas Centrales</p>
                            <p class="text-lg font-medium text-gray-300">Av. Reforma 222, CDMX, México.</p>
                        </li>
                    </ul>
                </div>

                <!-- Socials -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-4">Síguenos</h4>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-blue-600 hover:text-white transition-all">
                            <i class="pi pi-facebook"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-blue-600 hover:text-white transition-all">
                            <i class="pi pi-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-blue-600 hover:text-white transition-all">
                            <i class="pi pi-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-blue-600 hover:text-white transition-all">
                            <i class="pi pi-linkedin"></i>
                        </a>
                    </div>
                </div>

                <a href="https://dtw.com.mx/" target="_blank" class="flex items-center space-x-2">
                    <span class="font-bold text-gray-100 text-xl">BY</span> <img class="w-24" src="@/../../public/images/DTW_logo_blanco.png" alt="Logo DTW">
                </a>
            </div>
        </div>


        <!-- SLOT PRINCIPAL -->
        <main class="relative">
            <slot />
        </main>

        <!-- FOOTER (Sección Nosotros/Contacto) -->
        <footer id="nosotros" class="bg-[#0B0F19] text-white pt-24 pb-12 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none opacity-20">
                 <div class="absolute -right-20 top-20 w-96 h-96 bg-blue-600 rounded-full blur-[120px]"></div>
                 <div class="absolute -left-20 bottom-20 w-72 h-72 bg-gray-600 rounded-full blur-[100px]"></div>
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                
                <!-- Newsletter -->
                <div class="bg-gray-900/50 border border-gray-800 rounded-3xl p-8 md:p-12 mb-20 flex flex-col md:flex-row items-center justify-between gap-8 relative backdrop-blur-sm">
                    <div class="max-w-xl">
                        <h3 class="text-3xl md:text-4xl font-bold mb-4">Suscríbete a nuestro boletín.</h3>
                        <p class="text-gray-400">Recibe las últimas actualizaciones sobre gestión condominal.</p>
                    </div>
                    <div class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
                        <input type="email" placeholder="Tu correo electrónico" class="bg-gray-800 border border-gray-700 text-white px-6 py-3 rounded-full focus:outline-none focus:border-blue-500 w-full sm:w-80 transition-colors">
                        <button class="bg-blue-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-blue-500 transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20">
                            Suscribirse <i class="pi pi-arrow-up-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Links Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                    <!-- Brand -->
                    <div>
                        <div class="flex items-center gap-2 mb-6">
                            <img class="h-14" src="@/../../public/images/isologo-grande.png" alt="" onerror="this.style.display='none'">
                            <span class="text-2xl font-bold tracking-tighter">Frax<span class="text-blue-500 text-2xl">.</span></span>
                        </div>
                        <p class="text-gray-400 leading-relaxed mb-6">
                            Seguridad y gestión en un solo lugar.
                        </p>
                        <div class="flex gap-4">
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition-colors"><i class="pi pi-facebook"></i></a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition-colors"><i class="pi pi-instagram"></i></a>
                            <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition-colors"><i class="pi pi-twitter"></i></a>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-6">Servicios</h4>
                        <ul class="space-y-4 text-gray-400">
                            <li><a href="#" class="hover:text-blue-400 transition-colors">Gestión Financiera</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition-colors">Accesos QR</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition-colors">Votaciones</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition-colors">Domótica</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-6">Empresa</h4>
                        <ul class="space-y-4 text-gray-400">
                            <li><a href="#" class="hover:text-blue-400 transition-colors">Acerca de</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition-colors">Blog</a></li>
                            <li><a href="#" class="hover:text-blue-400 transition-colors">Contacto</a></li>
                        </ul>
                    </div>

                     <div>
                        <h4 class="text-lg font-bold mb-6">Contacto</h4>
                        <ul class="space-y-4 text-gray-400">
                            <li class="flex items-start gap-3">
                                <i class="pi pi-map-marker mt-1 text-blue-500"></i>
                                <span>CDMX, México.</span>
                            </li>
                             <li class="flex items-center gap-3">
                                <i class="pi pi-phone text-blue-500"></i>
                                <span>+52 55 1234 5678</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-gray-500 text-sm">
                    <p>&copy; 2025 <strong class="text-gray-200">Frax</strong><span class="text-blue-500 text-2xl">.</span> Todos los derechos reservados.</p>

                    <a href="https://dtw.com.mx/" target="_blank" class="flex items-center space-x-2">
                        <span class="font-bold text-gray-100 text-xl">BY</span> <img class="w-24" src="@/../../public/images/DTW_logo_blanco.png" alt="Logo DTW">
                    </a>
                </div>
            </div>
        </footer>

        <!-- SCROLL TO TOP -->
        <button 
            @click="scrollToTop"
            class="fixed bottom-8 right-8 z-40 transform transition-all duration-500 group"
            :class="scrollPercentage > 5 ? 'translate-y-0 opacity-100' : 'translate-y-20 opacity-0'"
        >
            <div class="relative w-14 h-14 flex items-center justify-center bg-white rounded-full shadow-xl cursor-pointer group-hover:-translate-y-1 transition-transform">
                <svg class="absolute top-0 left-0 w-full h-full -rotate-90" viewBox="0 0 64 64">
                    <circle cx="32" cy="32" r="30" stroke="currentColor" stroke-width="3" fill="none" class="text-gray-200" />
                    <circle 
                        cx="32" cy="32" r="30" 
                        stroke="currentColor" stroke-width="3" fill="none" 
                        class="text-blue-600 transition-all duration-100 ease-out"
                        :style="{ strokeDasharray: 30 * 2 * Math.PI, strokeDashoffset: strokeDashoffset }"
                    />
                </svg>
                <i class="pi pi-arrow-up text-gray-900 text-lg font-bold group-hover:text-blue-600 transition-colors"></i>
            </div>
        </button>

    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #1f2937;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 4px;
}
.will-change-transform {
    will-change: transform;
}
</style>
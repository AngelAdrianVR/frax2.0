<script>
import LandingLayout from '@/Layouts/LandingLayout.vue';
import { Head } from '@inertiajs/vue3';

export default {
    name: 'Welcome',
    components: {
        LandingLayout,
        Head
    },
    data() {
        return {
            // --- Control del Video Modal ---
            showVideoModal: false,

            // --- Control del ciclo de facturación (Pricing) ---
            billingCycle: 'monthly', // 'monthly' | 'yearly'

            // --- Control de la Galería de Infraestructura ---
            isGalleryOpen: false,
            activeGalleryIndex: 0,
            currentGalleryImages: [],
            galleryTitle: '',
            
            // Datos de las galerías por tarjeta
            infrastructureGalleries: {
                access: {
                    title: 'Accesos Inteligentes',
                    images: [
                        'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?q=80&w=1000&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1626125345510-4603468eedfb?q=80&w=1000&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1563013544-824ae1b704d3?q=80&w=1000&auto=format&fit=crop'
                    ]
                },
                security: {
                    title: 'Video Vigilancia 360°',
                    images: [
                        'https://images.unsplash.com/photo-1557597774-9d273605dfa9?q=80&w=1000&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1572509018344-f3dd8d3e745f?q=80&w=1000&auto=format&fit=crop',
                    ]
                },
                iot: {
                    title: 'Domótica & Espacios',
                    images: [
                        'https://images.unsplash.com/photo-1558002038-1091a575039f?q=80&w=1000&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1585566601963-3198ae5136e5?q=80&w=1000&auto=format&fit=crop',
                    ]
                }
            },

            // --- Control de Testimonios (Feedback) ---
            activeTestimonial: 1, 
            testimonials: [
                {
                    id: 1,
                    name: 'Carlos Ruiz',
                    role: 'Administrador General',
                    image: 'https://randomuser.me/api/portraits/men/32.jpg',
                    text: "Desde que implementamos Frax, las quejas de los residentes bajaron un 90%. La transparencia en las finanzas generó una confianza que nunca habíamos tenido en la comunidad."
                },
                {
                    id: 2,
                    name: 'Mariana Costa',
                    role: 'Presidenta del Comité',
                    image: 'https://randomuser.me/api/portraits/women/44.jpg',
                    text: "La integración con las cámaras y el acceso vehicular es simplemente increíble. Me siento mucho más segura sabiendo que tenemos control total desde la app en tiempo real."
                },
                {
                    id: 3,
                    name: 'Roberto Gómez',
                    role: 'Residente',
                    image: 'https://randomuser.me/api/portraits/men/86.jpg',
                    text: "Pagar mi mantenimiento solía ser un dolor de cabeza. Ahora lo hago en 2 clics y recibo mi factura al instante. La interfaz es muy moderna y fácil de usar."
                }
            ]
        }
    },
    computed: {
        isMonthly() {
            return this.billingCycle === 'monthly';
        },
        prices() {
            return {
                basic: this.isMonthly ? '99' : '990',
                standard: this.isMonthly ? '249' : '2,490',
                premium: this.isMonthly ? '499' : '4,990',
                period: this.isMonthly ? '/por mes' : '/por año',
                savings: !this.isMonthly
            }
        }
    },
    methods: {
        openGallery(type) {
            const data = this.infrastructureGalleries[type];
            if (data) {
                this.galleryTitle = data.title;
                this.currentGalleryImages = data.images;
                this.activeGalleryIndex = 0;
                this.isGalleryOpen = true;
                document.body.style.overflow = 'hidden';
            }
        },
        closeGallery() {
            this.isGalleryOpen = false;
            document.body.style.overflow = '';
        },
        nextImage() {
            this.activeGalleryIndex = (this.activeGalleryIndex + 1) % this.currentGalleryImages.length;
        },
        prevImage() {
            this.activeGalleryIndex = (this.activeGalleryIndex - 1 + this.currentGalleryImages.length) % this.currentGalleryImages.length;
        },
        setActiveTestimonial(index) {
            this.activeTestimonial = index;
        }
    }
}
</script>

<template>
    <LandingLayout title="El Futuro de tu Fraccionamiento">
        
        <!-- ==========================================
             HERO SECTION REDISEÑADO
             ========================================== -->
        <div class="relative bg-[#0B0F19] overflow-hidden min-h-screen flex items-center">
            
            <!-- Sidebar "Scroll Down" Indicator (Estilo Referencia) -->
            <div class="absolute left-0 top-0 bottom-0 w-16 md:w-24 hidden md:flex flex-col items-center justify-end pb-12 z-20 border-r border-gray-800/30">
                <div class="writing-mode-vertical transform rotate-180 flex items-center gap-4 text-gray-400 text-sm font-medium tracking-widest uppercase">
                    <span class="animate-bounce text-blue-500"><i class="pi pi-arrow-down"></i></span>
                    Scroll Down
                </div>
            </div>

            <!-- Background Gradients -->
            <div class="absolute inset-0 w-full h-full pointer-events-none">
                <div class="absolute -top-[20%] -left-[10%] w-[70%] h-[70%] bg-blue-900/10 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-0 right-0 w-[60%] h-[60%] bg-teal-900/10 rounded-full blur-[100px]"></div>
                <!-- Pattern Overlay sutil -->
                <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 32px 32px;"></div>
            </div>

            <div class="container max-w-7xl mx-auto px-6 lg:px-8 relative z-10 ml-auto md:pl-32"> <!-- Added padding-left for sidebar -->
                <div class="grid lg:grid-cols-12 gap-12 items-center">
                    
                    <!-- Left Content (Typography & Video Inline) -->
                    <div class="lg:col-span-7 space-y-10">
                        <!-- Tag superior -->
                        <div class="inline-flex items-center gap-2 animate-fade-in-up" style="animation-delay: 0.1s;">
                            <i class="pi pi-bolt text-teal-400"></i>
                            <span class="text-teal-500 font-bold tracking-widest text-sm uppercase">Tecnología Residencial</span>
                        </div>

                        <!-- Main Headline with Inline Video -->
                        <h1 class="text-5xl sm:text-6xl lg:text-7xl xl:text-[5.5rem] font-bold text-white tracking-tight leading-[1.1] animate-fade-in-up" style="animation-delay: 0.2s;">
                            Gestión
                            <br>
                            <!-- Video Pill Container -->
                            <span class="relative inline-flex items-center align-middle mx-2 group cursor-pointer h-[60px] sm:h-[80px] w-[140px] sm:w-[180px] rounded-full overflow-hidden border-2 border-gray-700 hover:border-teal-400 transition-all duration-300" @click="showVideoModal = true">
                                <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=400&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-80 group-hover:scale-110 transition-all duration-500" alt="Video thumbnail">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center group-hover:bg-teal-500 transition-colors">
                                        <i class="pi pi-play-circle text-white text-2xl"></i>
                                    </div>
                                </div>
                            </span>
                            Integral
                            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-blue-500">
                                & Confiable.
                            </span>
                        </h1>

                        <p class="text-lg text-gray-400 max-w-lg leading-relaxed animate-fade-in-up" style="animation-delay: 0.3s;">
                            Evolucionamos la forma de vivir en comunidad. Seguridad automatizada y finanzas transparentes en una sola plataforma.
                        </p>

                        <div class="flex flex-wrap items-center gap-6 animate-fade-in-up" style="animation-delay: 0.4s;">
                            <a href="#" class="px-8 py-4 rounded-full bg-teal-500 text-[#0B0F19] font-bold hover:bg-white hover:shadow-[0_0_20px_rgba(20,184,166,0.5)] transition-all duration-300 flex items-center gap-2">
                                Iniciar Prueba Gratis
                            </a>
                            <a href="#" class="flex items-center gap-3 text-gray-300 hover:text-white transition-colors group">
                                <div class="w-10 h-10 rounded-full border border-gray-700 flex items-center justify-center group-hover:border-teal-500 transition-colors">
                                    <i class="pi pi-phone text-teal-400"></i>
                                </div>
                                <span class="font-medium">800-FRAX-247</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right Content (Image & Glass Card) -->
                    <div class="lg:col-span-5 relative lg:h-[700px] flex items-center animate-fade-in-up" style="animation-delay: 0.5s;">
                        
                        <!-- Main Image with Unique Shape/Mask -->
                        <div class="relative z-10 w-full aspect-[4/5] rounded-[2rem] overflow-hidden border border-gray-800 shadow-2xl shadow-black/50 group">
                            <img 
                                src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=1000&auto=format&fit=crop" 
                                alt="Dashboard Frax" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                            >
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0B0F19] via-transparent to-transparent opacity-80"></div>
                        </div>

                        <!-- Glassmorphism Card Floating (Like 'Business Growth') -->
                        <div class="absolute -bottom-6 -left-12 z-20 bg-gray-900/60 backdrop-blur-xl border border-gray-700/50 p-6 rounded-2xl shadow-xl animate-float w-64">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Eficiencia</p>
                                    <h4 class="text-white text-2xl font-bold mt-1">+120%</h4>
                                </div>
                                <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                                    <i class="pi pi-chart-line text-green-400 text-xl"></i>
                                </div>
                            </div>
                            <!-- Fake Bar Chart -->
                            <div class="flex items-end gap-2 h-16">
                                <div class="w-1/4 bg-gray-700 rounded-t-sm h-[40%] animate-pulse-slow"></div>
                                <div class="w-1/4 bg-gray-600 rounded-t-sm h-[60%] animate-pulse-slow" style="animation-delay: 0.2s"></div>
                                <div class="w-1/4 bg-gray-500 rounded-t-sm h-[30%] animate-pulse-slow" style="animation-delay: 0.4s"></div>
                                <div class="w-1/4 bg-teal-500 rounded-t-sm h-[85%] shadow-[0_0_10px_rgba(20,184,166,0.5)]"></div>
                            </div>
                        </div>

                        <!-- Name Tag Floating (Like 'Eade Marren') -->
                        <div class="absolute top-20 -right-8 z-20 hidden lg:flex items-center gap-3 bg-[#0B0F19] py-3 px-5 rounded-l-full border border-gray-800 shadow-lg">
                            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                            <div class="text-right">
                                <p class="text-white font-bold text-sm">Sistema Activo</p>
                                <p class="text-xs text-gray-500">v2.4.0 Stable</p>
                            </div>
                        </div>

                        <!-- Decorative Circle behind -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[80%] border border-gray-800/30 rounded-full rotate-45 -z-10"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             SERVICIOS
             ========================================== -->
        <div id="servicios" class="bg-white py-24 sm:py-32">
             <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center mb-16">
                    <h2 class="text-base font-semibold leading-7 text-blue-600">Software Integral</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Gestión administrativa simplificada
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group p-8 rounded-3xl bg-gray-50 hover:bg-blue-600 transition-colors duration-300 cursor-pointer">
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 group-hover:bg-white/20 flex items-center justify-center mb-6 transition-colors">
                            <i class="pi pi-wallet text-2xl text-blue-600 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-white mb-3">Finanzas Claras</h3>
                        <p class="text-gray-500 group-hover:text-blue-100 leading-relaxed">
                            Automatiza cobros y genera reportes transparentes para todos los residentes.
                        </p>
                    </div>

                    <div class="group p-8 rounded-3xl bg-gray-50 hover:bg-blue-600 transition-colors duration-300 cursor-pointer">
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 group-hover:bg-white/20 flex items-center justify-center mb-6 transition-colors">
                            <i class="pi pi-shield text-2xl text-blue-600 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-white mb-3">Seguridad Total</h3>
                        <p class="text-gray-500 group-hover:text-blue-100 leading-relaxed">
                            Control de accesos QR y registro de visitas en tiempo real desde la app.
                        </p>
                    </div>

                    <div class="group p-8 rounded-3xl bg-gray-50 hover:bg-blue-600 transition-colors duration-300 cursor-pointer">
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 group-hover:bg-white/20 flex items-center justify-center mb-6 transition-colors">
                            <i class="pi pi-mobile text-2xl text-blue-600 group-hover:text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-white mb-3">App Móvil</h3>
                        <p class="text-gray-500 group-hover:text-blue-100 leading-relaxed">
                            Reservas, comunicados y votaciones al alcance de la mano de cada vecino.
                        </p>
                    </div>
                </div>
             </div>
        </div>

        <!-- ==========================================
             INFRAESTRUCTURA & DOMÓTICA (Interactivo)
             ========================================== -->
        <div id="infraestructura" class="bg-[#0B0F19] py-24 relative overflow-hidden">
            <!-- Gradient de fondo sutil -->
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-[#0B0F19] to-[#111827] pointer-events-none"></div>
            
            <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
                <div class="max-w-3xl mb-16">
                    <h2 class="text-base font-semibold leading-7 text-blue-500 uppercase tracking-widest">Hardware & Automatización</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Infraestructura inteligente para un entorno seguro.
                    </p>
                    <p class="mt-4 text-gray-400 text-lg">
                        Haz clic en las tarjetas para explorar nuestra galería de instalaciones y tecnología física.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Card 1: Accesos Automatizados (Clickable) -->
                    <div @click="openGallery('access')" class="relative group rounded-2xl bg-gray-800/40 border border-gray-700 hover:border-blue-500/50 overflow-hidden transition-all duration-300 cursor-pointer hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-900/20">
                        <div class="absolute top-0 left-0 w-full h-1 bg-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                        <!-- Overlay de "Ver Galería" -->
                        <div class="absolute inset-0 bg-blue-900/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 backdrop-blur-sm">
                            <span class="text-white font-bold text-lg flex items-center gap-2"><i class="pi pi-images"></i> Ver Galería</span>
                        </div>

                        <div class="p-8 relative z-0">
                            <div class="w-12 h-12 rounded-lg bg-blue-900/30 flex items-center justify-center text-blue-400 mb-6 group-hover:scale-110 transition-transform">
                                <i class="pi pi-car text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Accesos Inteligentes</h3>
                            <p class="text-gray-400 text-sm leading-relaxed mb-4">
                                Olvídate de los controles remotos antiguos. Implementamos lectura de placas y más.
                            </p>
                            <ul class="space-y-2 text-sm text-gray-300">
                                <li class="flex items-center gap-2"><i class="pi pi-check text-green-400 text-xs"></i> Lectura de Placas (LPR)</li>
                                <li class="flex items-center gap-2"><i class="pi pi-check text-green-400 text-xs"></i> Tags RFID</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 2: Seguridad & CCTV (Clickable) -->
                    <div @click="openGallery('security')" class="relative group rounded-2xl bg-gray-800/40 border border-gray-700 hover:border-blue-500/50 overflow-hidden transition-all duration-300 cursor-pointer hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-900/20">
                        <div class="absolute top-0 left-0 w-full h-1 bg-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                         <!-- Overlay de "Ver Galería" -->
                         <div class="absolute inset-0 bg-blue-900/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 backdrop-blur-sm">
                            <span class="text-white font-bold text-lg flex items-center gap-2"><i class="pi pi-images"></i> Ver Galería</span>
                        </div>

                        <div class="p-8 relative z-0">
                            <div class="w-12 h-12 rounded-lg bg-blue-900/30 flex items-center justify-center text-blue-400 mb-6 group-hover:scale-110 transition-transform">
                                <i class="pi pi-camera text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Video Vigilancia 360°</h3>
                            <p class="text-gray-400 text-sm leading-relaxed mb-4">
                                Seguridad perimetral y monitoreo activo para la tranquilidad de todos.
                            </p>
                            <ul class="space-y-2 text-sm text-gray-300">
                                <li class="flex items-center gap-2"><i class="pi pi-check text-green-400 text-xs"></i> Cámaras IP</li>
                                <li class="flex items-center gap-2"><i class="pi pi-check text-green-400 text-xs"></i> Detección IA</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 3: Domótica IoT (Clickable) -->
                    <div @click="openGallery('iot')" class="relative group rounded-2xl bg-gray-800/40 border border-gray-700 hover:border-blue-500/50 overflow-hidden transition-all duration-300 cursor-pointer hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-900/20">
                        <div class="absolute top-0 left-0 w-full h-1 bg-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                         <!-- Overlay de "Ver Galería" -->
                         <div class="absolute inset-0 bg-blue-900/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 backdrop-blur-sm">
                            <span class="text-white font-bold text-lg flex items-center gap-2"><i class="pi pi-images"></i> Ver Galería</span>
                        </div>

                        <div class="p-8 relative z-0">
                            <div class="w-12 h-12 rounded-lg bg-blue-900/30 flex items-center justify-center text-blue-400 mb-6 group-hover:scale-110 transition-transform">
                                <i class="pi pi-home text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3">Domótica & Espacios</h3>
                            <p class="text-gray-400 text-sm leading-relaxed mb-4">
                                Transformamos áreas comunes y hogares en espacios eficientes.
                            </p>
                            <ul class="space-y-2 text-sm text-gray-300">
                                <li class="flex items-center gap-2"><i class="pi pi-check text-green-400 text-xs"></i> Iluminación Smart</li>
                                <li class="flex items-center gap-2"><i class="pi pi-check text-green-400 text-xs"></i> Riego WiFi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             NUEVA SECCIÓN: TESTIMONIOS (Feedback)
             ========================================== -->
        <div class="bg-gradient-to-b from-slate-50 to-slate-100 py-24 relative overflow-hidden">
            <!-- Elementos decorativos de fondo -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none opacity-30">
                <div class="absolute right-0 top-20 text-[300px] leading-none text-gray-200 font-serif italic select-none font-black">”</div>
            </div>

            <div class="max-w-5xl mx-auto px-6 text-center relative z-10">
                <!-- Pill Header -->
                <div class="inline-block mb-12">
                    <span class="bg-white border border-blue-100 shadow-sm rounded-full px-6 py-2 text-sm font-semibold text-gray-600">
                        Nuestros clientes aman <span class="text-blue-600">Frax Feedback</span>
                    </span>
                </div>

                <!-- Carousel Container -->
                <div class="flex flex-col items-center">
                    
                    <!-- 1. Avatares (Navegación) -->
                    <div class="flex items-center justify-center gap-6 mb-6">
                        <button 
                            v-for="(item, index) in testimonials" 
                            :key="'avatar-' + item.id"
                            @click="setActiveTestimonial(index)"
                            class="relative transition-all duration-300 focus:outline-none"
                            :class="activeTestimonial === index ? 'scale-125 z-10' : 'scale-90 opacity-50 grayscale hover:grayscale-0'"
                        >
                            <img 
                                :src="item.image" 
                                :alt="item.name" 
                                class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-md"
                                :class="activeTestimonial === index ? 'ring-2 ring-offset-2 ring-blue-500' : ''"
                            >
                        </button>
                    </div>

                    <!-- 2. Nombre y Cargo Activo -->
                    <div class="mb-8 animate-fade-in-up" :key="'info-' + activeTestimonial">
                        <h3 class="text-xl font-bold text-gray-900">{{ testimonials[activeTestimonial].name }}</h3>
                        <p class="text-gray-500 text-sm">{{ testimonials[activeTestimonial].role }}</p>
                    </div>

                    <!-- 3. Quote Box (La burbuja grande) -->
                    <div class="relative max-w-3xl w-full">
                        <!-- Triángulo de la burbuja apuntando arriba -->
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-6 h-6 bg-white rotate-45 shadow-[-2px_-2px_5px_rgba(0,0,0,0.03)] z-20"></div>

                        <transition name="fade" mode="out-in">
                            <div :key="activeTestimonial" class="bg-white rounded-3xl p-10 md:p-14 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.08)] relative z-10 min-h-[200px] flex items-center justify-center">
                                <p class="text-gray-600 text-lg md:text-xl leading-relaxed font-medium italic">
                                    "{{ testimonials[activeTestimonial].text }}"
                                </p>
                            </div>
                        </transition>
                    </div>

                    <!-- 4. Puntos de Paginación -->
                    <div class="flex items-center gap-2 mt-8">
                        <button 
                            v-for="(item, index) in testimonials" 
                            :key="'dot-' + item.id"
                            @click="setActiveTestimonial(index)"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="activeTestimonial === index ? 'w-8 bg-blue-600' : 'w-2 bg-gray-300 hover:bg-blue-300'"
                        ></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             PRICING SECTION
             ========================================== -->
        <div id="planes" class="bg-gray-50 py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                
                <!-- Header Pricing -->
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                    <div>
                        <h2 class="text-sm font-bold tracking-widest text-blue-600 uppercase mb-2">Planes Flexibles</h2>
                        <p class="text-4xl md:text-5xl font-bold text-gray-900">Nuestros Planes de Precios.</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <p class="text-gray-500 max-w-xs text-sm md:text-right">
                            Selecciona el plan que mejor se adapte al tamaño de tu comunidad.
                        </p>
                        <!-- Toggle Mensual/Anual Funcional -->
                        <div class="bg-[#0B0F19] p-1 rounded-full flex items-center relative">
                            <button 
                                @click="billingCycle = 'monthly'"
                                class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 relative z-10"
                                :class="isMonthly ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                            >
                                Mensual
                            </button>
                            <button 
                                @click="billingCycle = 'yearly'"
                                class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 relative z-10"
                                :class="!isMonthly ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-400 hover:text-white'"
                            >
                                Anual <span v-if="!isMonthly" class="text-xs ml-1 bg-green-500 text-white px-1 rounded">-20%</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cards Container -->
                <div class="bg-[#0B0F19] rounded-[2.5rem] p-8 md:p-12 transition-all duration-500">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Card 1: Basic Plan -->
                        <div class="bg-transparent border border-gray-800 rounded-3xl p-8 flex flex-col transition-transform hover:-translate-y-1 duration-300">
                            <h3 class="text-2xl font-bold text-white mb-4">Plan Básico</h3>
                            <div class="flex items-baseline mb-4">
                                <span class="text-5xl font-bold text-white">${{ prices.basic }}</span>
                                <span class="text-gray-400 ml-2">{{ prices.period }}</span>
                            </div>
                            <p class="text-gray-400 text-sm leading-relaxed mb-8">
                                Ideal para pequeños condominios que inician su transformación digital.
                            </p>
                            <div class="mt-auto">
                                <ul class="space-y-4 mb-8">
                                    <li class="flex items-center gap-3 text-gray-300 text-sm"><i class="pi pi-angle-double-right text-blue-500"></i> Gestión de Residentes</li>
                                    <li class="flex items-center gap-3 text-gray-300 text-sm"><i class="pi pi-angle-double-right text-blue-500"></i> Control de Ingresos/Egresos</li>
                                </ul>
                                <button class="w-full py-4 rounded-full bg-blue-600/20 text-white font-bold border border-blue-600/30 hover:bg-blue-600 transition-colors flex items-center justify-center gap-2 group">
                                    Elegir Plan <i class="pi pi-arrow-up-right text-sm group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Card 2: Standard Plan -->
                        <div class="bg-transparent border border-gray-800 rounded-3xl p-8 flex flex-col transition-transform hover:-translate-y-1 duration-300">
                            <h3 class="text-2xl font-bold text-white mb-4">Plan Estándar</h3>
                            <div class="flex items-baseline mb-4">
                                <span class="text-5xl font-bold text-white">${{ prices.standard }}</span>
                                <span class="text-gray-400 ml-2">{{ prices.period }}</span>
                            </div>
                            <p class="text-gray-400 text-sm leading-relaxed mb-8">
                                Para comunidades en crecimiento que requieren control de accesos.
                            </p>
                            <div class="mt-auto">
                                <ul class="space-y-4 mb-8">
                                    <li class="flex items-center gap-3 text-gray-300 text-sm"><i class="pi pi-angle-double-right text-blue-500"></i> App Móvil para Residentes</li>
                                    <li class="flex items-center gap-3 text-gray-300 text-sm"><i class="pi pi-angle-double-right text-blue-500"></i> Generación de códigos QR</li>
                                </ul>
                                <button class="w-full py-4 rounded-full bg-blue-600/20 text-white font-bold border border-blue-600/30 hover:bg-blue-600 transition-colors flex items-center justify-center gap-2 group">
                                    Elegir Plan <i class="pi pi-arrow-up-right text-sm group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Card 3: Premium Plan -->
                        <div class="bg-white rounded-3xl p-8 flex flex-col transform lg:-translate-y-4 shadow-2xl shadow-blue-900/20 transition-transform hover:scale-[1.02] duration-300">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Plan Premium</h3>
                            <div class="flex items-baseline mb-4">
                                <span class="text-5xl font-bold text-gray-900">${{ prices.premium }}</span>
                                <span class="text-gray-500 ml-2">{{ prices.period }}</span>
                            </div>
                            <p class="text-gray-500 text-sm leading-relaxed mb-8">
                                La solución definitiva con integraciones de hardware y soporte VIP.
                            </p>
                            <div class="mt-auto">
                                <ul class="space-y-4 mb-8">
                                    <li class="flex items-center gap-3 text-gray-600 text-sm"><i class="pi pi-angle-double-right text-blue-600"></i> Integración LPR (Placas)</li>
                                    <li class="flex items-center gap-3 text-gray-600 text-sm"><i class="pi pi-angle-double-right text-blue-600"></i> Pagos en línea integrados</li>
                                    <li class="flex items-center gap-3 text-gray-600 text-sm"><i class="pi pi-angle-double-right text-blue-600"></i> Usuarios Ilimitados</li>
                                </ul>
                                <button class="w-full py-4 rounded-full bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 shadow-lg shadow-blue-600/30 group">
                                    Elegir Plan <i class="pi pi-arrow-up-right text-sm group-hover:translate-x-1 transition-transform"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             MODAL DE GALERÍA (Lightbox)
             ========================================== -->
        <Transition name="fade">
            <div v-if="isGalleryOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-sm" @click.self="closeGallery">
                <!-- Close Button -->
                <button @click="closeGallery" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors z-50">
                    <i class="pi pi-times text-4xl"></i>
                </button>

                <div class="relative w-full max-w-6xl px-4 flex flex-col items-center">
                    <h3 class="text-white text-xl font-light mb-4 tracking-wider">{{ galleryTitle }}</h3>
                    
                    <div class="relative w-full h-[70vh] flex items-center justify-center">
                         <!-- Nav Prev -->
                        <button @click="prevImage" class="absolute left-0 p-4 text-white hover:text-blue-400 transition-colors z-50 bg-black/20 hover:bg-black/50 rounded-full">
                            <i class="pi pi-chevron-left text-3xl"></i>
                        </button>

                        <!-- Image -->
                        <img 
                            :src="currentGalleryImages[activeGalleryIndex]" 
                            class="max-h-full max-w-full object-contain rounded-lg shadow-2xl animate-fade-in-up"
                            :key="activeGalleryIndex"
                        >

                        <!-- Nav Next -->
                        <button @click="nextImage" class="absolute right-0 p-4 text-white hover:text-blue-400 transition-colors z-50 bg-black/20 hover:bg-black/50 rounded-full">
                            <i class="pi pi-chevron-right text-3xl"></i>
                        </button>
                    </div>
                    
                    <!-- Dots indicator -->
                    <div class="flex gap-2 mt-6">
                         <button 
                            v-for="(img, idx) in currentGalleryImages" 
                            :key="idx"
                            @click="activeGalleryIndex = idx"
                            class="h-1.5 rounded-full transition-all"
                            :class="activeGalleryIndex === idx ? 'w-8 bg-blue-500' : 'w-2 bg-gray-600'"
                        ></button>
                    </div>
                </div>
            </div>
        </Transition>

         <!-- ==========================================
             MODAL DE VIDEO (NUEVO)
             ========================================== -->
        <Transition name="fade">
            <div v-if="showVideoModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 backdrop-blur-md p-4" @click.self="showVideoModal = false">
                 <div class="relative w-full max-w-4xl aspect-video bg-black rounded-2xl overflow-hidden shadow-2xl border border-gray-800">
                     <button @click="showVideoModal = false" class="absolute top-4 right-4 z-10 bg-black/50 hover:bg-black text-white p-2 rounded-full transition-all">
                         <i class="pi pi-times text-xl"></i>
                     </button>
                     <!-- Placeholder de Video (Iframe o Video Tag) -->
                     <iframe class="w-full h-full" src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1" title="Video Demo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                 </div>
            </div>
        </Transition>

    </LandingLayout>
</template>

<style scoped>
/* ANIMACIONES ANTERIORES */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    opacity: 0;
    animation: fadeInUp 0.8s ease-out forwards;
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}
.animate-float {
    animation: float 6s ease-in-out infinite;
}
@keyframes pulseSlow {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.1); }
}
.animate-pulse-slow {
    animation: pulseSlow 8s infinite;
}
@keyframes spinSlow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-slow {
    animation: spinSlow 20s linear infinite;
}

/* NUEVAS TRANSICIONES */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* UTILIDADES */
.writing-mode-vertical {
    writing-mode: vertical-rl;
}
</style>
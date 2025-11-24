<script>
export default {
    name: 'InfrastructureSection',
    emits: ['open-lightbox'], // Emitimos un evento genérico para abrir el lightbox global
    data() {
        return {
            selectedFeature: null, // Controla qué servicio se está viendo en detalle
            features: [
                {
                    id: 'access',
                    title: 'Accesos Inteligentes',
                    icon: 'pi-car',
                    shortDesc: 'Olvídate de los controles remotos antiguos. Implementamos lectura de placas y más.',
                    fullDesc: 'Nuestro sistema de control de accesos revoluciona la entrada y salida vehicular. Utilizando cámaras de alta velocidad con reconocimiento óptico de caracteres (LPR), el sistema identifica a los residentes automáticamente, abriendo las barreras en menos de 2 segundos sin necesidad de tags o tarjetas físicas.',
                    specs: [
                        'Lectura de Placas (LPR) con 99% de precisión',
                        'Tags RFID encriptados de largo alcance',
                        'Códigos QR temporales para visitas',
                        'Bitácora digital de ingresos en la nube'
                    ],
                    images: [
                        'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?q=80&w=1000&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1626125345510-4603468eedfb?q=80&w=1000&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1563013544-824ae1b704d3?q=80&w=1000&auto=format&fit=crop'
                    ]
                },
                {
                    id: 'security',
                    title: 'Video Vigilancia 360°',
                    icon: 'pi-camera',
                    shortDesc: 'Seguridad perimetral y monitoreo activo para la tranquilidad de todos.',
                    fullDesc: 'Más que simples cámaras, ofrecemos un ecosistema de vigilancia proactiva. Nuestros equipos cuentan con visión nocturna a color y análisis de video basado en IA para detectar comportamientos inusuales, merodeo o cruces de perímetro no autorizados, alertando a la caseta en tiempo real.',
                    specs: [
                        'Cámaras IP 4K con visión nocturna',
                        'Detección de movimiento por IA',
                        'Grabación en la nube y local (Híbrido)',
                        'Integración directa con App Móvil'
                    ],
                    images: [
                        'https://images.unsplash.com/photo-1557597774-9d273605dfa9?q=80&w=1000&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1572509018344-f3dd8d3e745f?q=80&w=1000&auto=format&fit=crop',
                    ]
                },
                {
                    id: 'iot',
                    title: 'Domótica & Espacios',
                    icon: 'pi-home',
                    shortDesc: 'Transformamos áreas comunes y hogares en espacios eficientes.',
                    fullDesc: 'La gestión de amenidades nunca fue tan sencilla. Controlamos la iluminación de parques, el encendido de bombas de agua y el acceso a casas club mediante horarios automatizados o sensores de presencia, reduciendo el consumo energético de la comunidad hasta en un 40%.',
                    specs: [
                        'Iluminación Smart Dimerizable',
                        'Riego WiFi predictivo (según clima)',
                        'Sensores de calidad de aire',
                        'Gestión energética de áreas comunes'
                    ],
                    images: [
                        'https://images.unsplash.com/photo-1558002038-1091a575039f?q=80&w=1000&auto=format&fit=crop',
                        'https://images.unsplash.com/photo-1585566601963-3198ae5136e5?q=80&w=1000&auto=format&fit=crop',
                    ]
                }
            ]
        }
    },
    methods: {
        openDetail(feature) {
            this.selectedFeature = feature;
            document.body.style.overflow = 'hidden';
        },
        closeDetail() {
            this.selectedFeature = null;
            document.body.style.overflow = '';
        },
        openFullGallery(index) {
            // Emitimos al padre (Welcome.vue) los datos necesarios para el Lightbox
            this.$emit('open-lightbox', {
                title: this.selectedFeature.title,
                images: this.selectedFeature.images,
                startIndex: index
            });
        }
    }
}
</script>

<template>
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
                    Haz clic en las tarjetas para conocer los detalles técnicos y galerías.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Cards Loop -->
                <div 
                    v-for="feature in features" 
                    :key="feature.id"
                    @click="openDetail(feature)" 
                    class="relative group rounded-2xl bg-gray-800/40 border border-gray-700 hover:border-blue-500/50 overflow-hidden transition-all duration-300 cursor-pointer hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-900/20 flex flex-col"
                >
                    <div class="absolute top-0 left-0 w-full h-1 bg-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                    
                    <!-- Overlay de "Ver Detalles" -->
                    <div class="absolute inset-0 bg-blue-900/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 backdrop-blur-sm">
                        <span class="text-white font-bold text-lg flex items-center gap-2">
                            <i class="pi pi-info-circle"></i> Ver Detalles
                        </span>
                    </div>

                    <div class="p-8 relative z-0 flex-1 flex flex-col">
                        <div class="w-12 h-12 rounded-lg bg-blue-900/30 flex items-center justify-center text-blue-400 mb-6 group-hover:scale-110 transition-transform">
                            <i :class="`pi ${feature.icon} text-2xl`"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ feature.title }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-4 flex-1">
                            {{ feature.shortDesc }}
                        </p>
                        <!-- Mini preview de specs -->
                        <ul class="space-y-2 text-sm text-gray-300 mt-4">
                            <li v-for="(spec, idx) in feature.specs.slice(0, 2)" :key="idx" class="flex items-center gap-2">
                                <i class="pi pi-check text-green-400 text-xs"></i> {{ spec }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             MODAL DE DETALLE (Service Detail)
             ========================================== -->
        <Transition name="modal-fade">
            <div v-if="selectedFeature" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" role="dialog">
                
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity" @click="closeDetail"></div>

                <!-- Panel -->
                <div class="relative bg-[#111827] border border-gray-700 rounded-3xl w-full max-w-5xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col md:flex-row overflow-hidden animate-scale-up">
                    
                    <!-- Close Button Mobile -->
                    <button @click="closeDetail" class="absolute top-4 right-4 md:hidden z-20 bg-black/50 text-white p-2 rounded-full">
                        <i class="pi pi-times"></i>
                    </button>

                    <!-- Left Column: Content -->
                    <div class="p-8 md:p-12 md:w-1/2 flex flex-col relative">
                        <!-- Decorative line -->
                        <div class="absolute top-0 left-0 w-full h-1 md:w-1 md:h-full bg-gradient-to-r md:bg-gradient-to-b from-blue-500 to-teal-400"></div>
                        
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400">
                                <i :class="`pi ${selectedFeature.icon} text-xl`"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white">{{ selectedFeature.title }}</h3>
                        </div>

                        <div class="prose prose-invert mb-8">
                            <p class="text-gray-300 leading-relaxed text-lg">
                                {{ selectedFeature.fullDesc }}
                            </p>
                        </div>

                        <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700/50 mt-auto">
                            <h4 class="text-sm font-bold text-blue-400 uppercase tracking-wider mb-4">Especificaciones Técnicas</h4>
                            <ul class="grid grid-cols-1 gap-3">
                                <li v-for="(spec, idx) in selectedFeature.specs" :key="idx" class="flex items-start gap-3 text-gray-300 text-sm">
                                    <i class="pi pi-check-circle text-teal-500 mt-0.5"></i>
                                    <span>{{ spec }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Right Column: Gallery Grid -->
                    <div class="bg-gray-900 md:w-1/2 p-4 md:p-8 flex flex-col">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-white font-medium flex items-center gap-2">
                                <i class="pi pi-images text-gray-500"></i> Galería
                            </h4>
                            <button @click="closeDetail" class="hidden md:flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-sm font-medium">
                                Cerrar <i class="pi pi-times"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-4 auto-rows-[150px]">
                            <div 
                                v-for="(img, idx) in selectedFeature.images" 
                                :key="idx"
                                class="relative group rounded-xl overflow-hidden cursor-zoom-in border border-gray-800"
                                :class="{ 'col-span-2 row-span-2': idx === 0 }"
                                @click="openFullGallery(idx)"
                            >
                                <img :src="img" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Gallery">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors duration-300 flex items-center justify-center">
                                    <i class="pi pi-search-plus text-white opacity-0 group-hover:opacity-100 transform scale-50 group-hover:scale-100 transition-all duration-300 text-3xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-center text-gray-500 text-xs mt-6">
                            Haz clic en una imagen para ampliar
                        </p>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

@keyframes scaleUp {
    from { opacity: 0; transform: scale(0.95) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-scale-up {
    animation: scaleUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
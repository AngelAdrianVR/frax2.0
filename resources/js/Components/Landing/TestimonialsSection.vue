<script>
export default {
    name: 'TestimonialsSection',
    data() {
        return {
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
    methods: {
        setActiveTestimonial(index) {
            this.activeTestimonial = index;
        }
    }
}
</script>

<template>
    <div class="bg-gradient-to-b from-slate-50 to-slate-100 py-24 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none opacity-30">
            <div class="absolute right-0 top-20 text-[300px] leading-none text-gray-200 font-serif italic select-none font-black">”</div>
        </div>

        <div class="max-w-5xl mx-auto px-6 text-center relative z-10">
            <div class="inline-block mb-12">
                <span class="bg-white border border-blue-100 shadow-sm rounded-full px-6 py-2 text-sm font-semibold text-gray-600">
                    Nuestros clientes aman <span class="text-blue-600">Frax Feedback</span>
                </span>
            </div>

            <div class="flex flex-col items-center">
                
                <!-- 1. Avatares -->
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

                <!-- 2. Nombre -->
                <div class="mb-8 animate-fade-in-up" :key="'info-' + activeTestimonial">
                    <h3 class="text-xl font-bold text-gray-900">{{ testimonials[activeTestimonial].name }}</h3>
                    <p class="text-gray-500 text-sm">{{ testimonials[activeTestimonial].role }}</p>
                </div>

                <!-- 3. Quote Box -->
                <div class="relative max-w-3xl w-full">
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-6 h-6 bg-white rotate-45 shadow-[-2px_-2px_5px_rgba(0,0,0,0.03)] z-20"></div>

                    <transition name="fade" mode="out-in">
                        <div :key="activeTestimonial" class="bg-white rounded-3xl p-10 md:p-14 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.08)] relative z-10 min-h-[200px] flex items-center justify-center">
                            <p class="text-gray-600 text-lg md:text-xl leading-relaxed font-medium italic">
                                "{{ testimonials[activeTestimonial].text }}"
                            </p>
                        </div>
                    </transition>
                </div>

                <!-- 4. Dots -->
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
</template>

<style scoped>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    opacity: 0;
    animation: fadeInUp 0.8s ease-out forwards;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
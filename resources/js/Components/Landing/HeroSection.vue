<script>
export default {
    name: 'HeroSection',
    emits: ['open-video']
}
</script>

<template>
    <div class="relative bg-[#0B0F19] overflow-hidden min-h-screen flex items-center">
        
        <!-- Sidebar "Scroll Down" Indicator -->
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
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 32px 32px;"></div>
        </div>

        <div class="container max-w-7xl mx-auto px-6 lg:px-8 relative z-10 ml-auto md:pl-32"> 
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-10">
                    <div class="inline-flex items-center gap-2 animate-fade-in-up" style="animation-delay: 0.1s;">
                        <i class="pi pi-bolt text-teal-400"></i>
                        <span class="text-teal-500 font-bold tracking-widest text-sm uppercase">Tecnología Residencial</span>
                    </div>

                    <h1 class="text-5xl sm:text-6xl lg:text-7xl xl:text-[5.5rem] font-bold text-white tracking-tight leading-[1.1] animate-fade-in-up" style="animation-delay: 0.2s;">
                        Gestión
                        <br>
                        <span class="relative inline-flex items-center align-middle mx-2 group cursor-pointer h-[60px] sm:h-[80px] w-[140px] sm:w-[180px] rounded-full overflow-hidden border-2 border-gray-700 hover:border-teal-400 transition-all duration-300" @click="$emit('open-video')">
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

                <!-- Right Content -->
                <div class="lg:col-span-5 relative lg:h-[700px] flex items-center animate-fade-in-up" style="animation-delay: 0.5s;">
                    <div class="relative z-10 w-full aspect-[4/5] rounded-[2rem] overflow-hidden border border-gray-800 shadow-2xl shadow-black/50 group">
                        <img 
                            src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=1000&auto=format&fit=crop" 
                            alt="Dashboard Frax" 
                            class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0B0F19] via-transparent to-transparent opacity-80"></div>
                    </div>

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
                        <div class="flex items-end gap-2 h-16">
                            <div class="w-1/4 bg-gray-700 rounded-t-sm h-[40%] animate-pulse-slow"></div>
                            <div class="w-1/4 bg-gray-600 rounded-t-sm h-[60%] animate-pulse-slow" style="animation-delay: 0.2s"></div>
                            <div class="w-1/4 bg-gray-500 rounded-t-sm h-[30%] animate-pulse-slow" style="animation-delay: 0.4s"></div>
                            <div class="w-1/4 bg-teal-500 rounded-t-sm h-[85%] shadow-[0_0_10px_rgba(20,184,166,0.5)]"></div>
                        </div>
                    </div>

                    <div class="absolute top-20 -right-8 z-20 hidden lg:flex items-center gap-3 bg-[#0B0F19] py-3 px-5 rounded-l-full border border-gray-800 shadow-lg">
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <div class="text-right">
                            <p class="text-white font-bold text-sm">Sistema Activo</p>
                            <p class="text-xs text-gray-500">v2.4.0 Stable</p>
                        </div>
                    </div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[80%] border border-gray-800/30 rounded-full rotate-45 -z-10"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.writing-mode-vertical {
    writing-mode: vertical-rl;
}
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
</style>
<script>
export default {
    name: 'StatsSection',
    data() {
        return {
            hasAnimatedStats: false,
            statsNumbers: {
                satisfaction: 0,
                users: 0,
                growth: 0,
                security: 0
            },
            statsTargets: {
                satisfaction: 98,
                users: 1500,
                growth: 85,
                security: 100
            }
        }
    },
    mounted() {
        this.setupIntersectionObserver();
    },
    methods: {
        setupIntersectionObserver() {
            const options = {
                root: null,
                rootMargin: '0px',
                threshold: 0.2 
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.hasAnimatedStats) {
                        this.animateStats();
                        this.hasAnimatedStats = true;
                    }
                });
            }, options);

            const target = this.$refs.statsSection;
            if (target) observer.observe(target);
        },
        animateStats() {
            const duration = 2000; 
            const interval = 20;
            const steps = duration / interval;

            Object.keys(this.statsTargets).forEach(key => {
                const target = this.statsTargets[key];
                const increment = target / steps;
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        this.statsNumbers[key] = target;
                        clearInterval(timer);
                    } else {
                        this.statsNumbers[key] = Math.floor(current);
                    }
                }, interval);
            });
        }
    }
}
</script>

<template>
    <div ref="statsSection" class="bg-[#0B0F19] py-24 relative border-t border-gray-800/50">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6">

                <!-- 1. Card Grande Texto -->
                <div class="lg:col-span-7 rounded-[2rem] bg-gradient-to-br from-gray-800/30 to-gray-900/30 border border-gray-700/50 p-8 md:p-12 flex flex-col justify-center relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 bg-teal-500/10 border border-teal-500/20 rounded-full px-4 py-1.5 mb-6">
                            <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                            <span class="text-teal-400 text-xs font-bold tracking-wider uppercase">Resultados Comprobados</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6 leading-tight">
                            Impulsando la <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-blue-500">excelencia</span> y la innovación en tu comunidad.
                        </h2>
                        <a href="#planes" class="inline-flex items-center gap-2 text-gray-300 hover:text-white transition-colors group/link font-medium">
                            Ver planes disponibles 
                            <span class="bg-gray-700 rounded-full p-1 group-hover/link:bg-teal-500 group-hover/link:rotate-45 transition-all duration-300">
                                <i class="pi pi-arrow-up-right text-xs"></i>
                            </span>
                        </a>
                    </div>
                    <div class="absolute right-0 top-0 w-64 h-64 bg-teal-500/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:bg-teal-500/10 transition-colors duration-700"></div>
                </div>

                <!-- 2. Card Estadística 1 (Satisfacción) -->
                <div class="lg:col-span-5 rounded-[2rem] bg-gray-100 p-8 flex flex-col justify-between relative overflow-hidden group hover:shadow-[0_0_30px_rgba(255,255,255,0.1)] transition-shadow duration-300">
                    <div class="flex justify-between items-start">
                        <div class="w-12 h-12 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-900 text-xl shadow-sm">
                            <i class="pi pi-check-circle"></i>
                        </div>
                        <span class="text-gray-400 font-mono font-bold">01.</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-2">Satisfacción Garantizada</p>
                        <div class="text-6xl md:text-7xl font-bold text-gray-900 tracking-tight">
                            {{ statsNumbers.satisfaction }}<span class="text-teal-500">%</span>
                        </div>
                    </div>
                        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-teal-100 rounded-full blur-2xl opacity-50 pointer-events-none"></div>
                </div>

                <!-- 3. Card "Happy Customers" -->
                <div class="lg:col-span-4 rounded-[2rem] bg-[#0f3c3a] border border-teal-900/50 p-8 flex flex-col justify-end relative overflow-hidden min-h-[260px] group">
                    <div class="absolute inset-0 opacity-20 mix-blend-overlay">
                        <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=1000&auto=format&fit=crop" class="w-full h-full object-cover grayscale" alt="Pattern">
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center mb-6">
                            <div class="flex -space-x-4">
                                <img class="w-12 h-12 rounded-full border-2 border-[#0f3c3a]" src="https://randomuser.me/api/portraits/women/44.jpg" alt="User">
                                <img class="w-12 h-12 rounded-full border-2 border-[#0f3c3a]" src="https://randomuser.me/api/portraits/men/32.jpg" alt="User">
                                <img class="w-12 h-12 rounded-full border-2 border-[#0f3c3a]" src="https://randomuser.me/api/portraits/women/68.jpg" alt="User">
                            </div>
                            <button class="w-12 h-12 rounded-full bg-white text-[#0f3c3a] flex items-center justify-center -ml-4 border-2 border-[#0f3c3a] hover:scale-110 transition-transform z-10 shadow-lg">
                                <i class="pi pi-plus"></i>
                            </button>
                        </div>
                        
                        <h3 class="text-white text-2xl font-bold leading-tight">
                            +{{ statsNumbers.users }} <br>
                            <span class="text-teal-200 font-normal text-lg">Residentes Felices</span>
                        </h3>
                    </div>
                    <div class="absolute top-8 right-8 text-teal-400 text-4xl opacity-50 animate-pulse-slow">✦</div>
                </div>

                <!-- 4. Card Estadística 2 -->
                <div class="lg:col-span-4 rounded-[2rem] bg-gray-800/40 border border-gray-700/50 p-8 flex flex-col justify-between hover:bg-gray-800/60 transition-colors duration-300">
                        <div class="flex justify-between items-start">
                        <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400 text-xl">
                            <i class="pi pi-globe"></i>
                        </div>
                        <span class="text-gray-600 font-mono font-bold">02.</span>
                    </div>
                    <div class="mt-8">
                        <p class="text-gray-400 text-sm mb-1">Reducción de Morosidad</p>
                        <div class="text-5xl font-bold text-white">
                            {{ statsNumbers.growth }}<span class="text-blue-500">%</span>
                        </div>
                    </div>
                </div>

                <!-- 5. Card Estadística 3 -->
                <div class="lg:col-span-4 rounded-[2rem] bg-white p-8 flex flex-col justify-between relative overflow-hidden hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex justify-between items-start">
                        <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl">
                            <i class="pi pi-chart-bar"></i>
                        </div>
                        <span class="text-gray-400 font-mono font-bold">03.</span>
                    </div>
                    <div class="mt-8">
                        <p class="text-gray-500 text-sm mb-1">Seguridad Percibida</p>
                        <div class="text-5xl font-bold text-gray-900">
                            {{ statsNumbers.security }}<span class="text-indigo-600 text-3xl align-top ml-1">/100</span>
                        </div>
                    </div>
                </div>

            </div>
            </div>
    </div>
</template>

<style scoped>
@keyframes pulseSlow {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.1); }
}
.animate-pulse-slow {
    animation: pulseSlow 8s infinite;
}
</style>
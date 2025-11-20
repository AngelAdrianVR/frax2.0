<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50 relative overflow-hidden selection:bg-teal-500 selection:text-white">
        
        <!-- Background Gradients (Ajustados para Modo Claro) -->
        <div class="absolute inset-0 w-full h-full pointer-events-none">
            <!-- Burbuja azul suave superior izquierda -->
            <div class="absolute -top-[20%] -left-[10%] w-[70%] h-[70%] bg-blue-200/40 rounded-full blur-[100px] mix-blend-multiply"></div>
            <!-- Burbuja teal suave inferior derecha -->
            <div class="absolute bottom-0 right-0 w-[60%] h-[60%] bg-teal-200/40 rounded-full blur-[100px] mix-blend-multiply"></div>
            <!-- Patrón de puntos sutil -->
            <div class="absolute inset-0 opacity-[0.4]" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 24px 24px;"></div>
        </div>

        <!-- Logo Container -->
        <div class="relative z-10 mb-6 animate-fade-in-down">
            <!-- Brillo detrás del logo -->
            <div class="absolute inset-0 bg-white/50 blur-xl rounded-full"></div>
            <div class="relative">
                <slot name="logo" />
            </div>
        </div>

        <!-- Card Glassmorphism (Versión Blanca) -->
        <div class="relative z-10 w-full sm:max-w-md px-8 py-10 bg-white/70 backdrop-blur-xl border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden sm:rounded-2xl animate-fade-in-up">
            <!-- Línea decorativa superior -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-teal-400 to-transparent opacity-80"></div>
            
            <slot />
        </div>

        <!-- Footer Text -->
        <div class="relative z-10 mt-8 text-center">
            <p class="text-gray-400 text-sm font-medium">
                &copy; {{ new Date().getFullYear() }} Frax. Todos los derechos reservados.
            </p>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-down {
    animation: fadeInDown 0.6s ease-out forwards;
}
</style>
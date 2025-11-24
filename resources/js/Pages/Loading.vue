<script setup>
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

// Configuración de tiempos
const TOTAL_DURATION = 2800; 

onMounted(() => {
    setTimeout(() => {
        router.visit('/inicio');
    }, TOTAL_DURATION + 200);
});
</script>

<template>
    <!-- Fondo actualizado: Degradado oscuro profesional (Slate/Gray/Black) -->
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-[#0B1120] to-black overflow-hidden text-white">
        
        <!-- Efecto de brillo de fondo sutil para profundidad -->
        <div class="absolute w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-3xl opacity-30 pointer-events-none"></div>

        <div class="relative flex items-center justify-center p-10 z-10">
            
            <!-- Contenedor del Isologotipo -->
            <div class="logo-wrapper overflow-hidden">
                <img 
                    src="@/../../public/images/isologo-grande.png" 
                    alt="Frax Logo" 
                    class="h-16 w-auto object-contain logo-reveal opacity-0"
                />
            </div>

            <!-- Contenedor del Texto "Frax" -->
            <div class="relative flex items-baseline">
                <!-- Texto "Frax" ahora en blanco puro para contraste -->
                <span class="text-6xl font-bold text-white tracking-tight text-reveal opacity-0 drop-shadow-lg">
                    Frax
                </span>

                <!-- El Punto Azul (Con un leve resplandor/glow para que brille en la oscuridad) -->
                <div class="blue-dot bg-blue-500 rounded-full absolute shadow-[0_0_15px_rgba(59,130,246,0.6)]"></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Dimensiones del punto */
.blue-dot {
    width: 12px;
    height: 12px;
    bottom: 12px; /* Alineado con la base del texto */
    right: -20px; /* Posición final a la derecha de la x */
    opacity: 0;   
    animation: dotSequence 2.5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
}

/* Animación del Texto Frax */
.text-reveal {
    animation: fadeInText 0.8s ease-out forwards;
    animation-delay: 1.4s; 
}

/* Animación del Logo (Icono) */
.logo-reveal {
    animation: slideInLogo 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    animation-delay: 1.6s; 
}

/* SECUENCIA DEL PUNTO */
@keyframes dotSequence {
    0% {
        opacity: 1;
        transform: translate(-100px, -50px); 
    }
    15% {
        transform: translate(-100px, 0); /* Cae */
    }
    25% {
        transform: translate(-100px, -35px); /* Rebote */
    }
    35% {
        transform: translate(-100px, 0); /* Cae */
    }
    45% {
        transform: translate(-100px, 0) scale(1.2, 0.8); /* Impulso */
    }
    60% {
        transform: translate(-50px, -40px) scale(1, 1); /* Salto */
    }
    80% {
        transform: translate(0, 0); /* Aterrizaje */
    }
    85% {
        transform: translate(0, -5px); /* Rebote final */
    }
    90% {
        transform: translate(0, 0);
    }
    100% {
        opacity: 1;
        transform: translate(0, 0);
    }
}

@keyframes fadeInText {
    from {
        opacity: 0;
        transform: translateX(10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInLogo {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
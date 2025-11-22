<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { defineProps } from 'vue';

defineProps({
    status: {
        type: Number,
        default: 404
    },
    message: {
        type: String,
        default: 'Página no encontrada'
    }
});

// Obtenemos el objeto page para acceder a la URL actual
const page = usePage();

const goBack = () => {
    window.history.back();
};

const thematicQuote = "Parece que esta dirección no aparece en nuestros planos.";
</script>

<template>
    <Head title="404 - Ruta no encontrada" />

    <!-- Fondo con gradiente suave y moderno -->
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-indigo-900 transition-colors duration-500 font-sans selection:bg-indigo-100 selection:text-indigo-600">
        
        <!-- Contenedor Principal con efecto "Glass" y Sombra Suave -->
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 bg-white/80 dark:bg-slate-800/90 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_50px_rgba(8,_112,_184,_0.1)] dark:shadow-none overflow-hidden border border-white/20 ring-1 ring-black/5 transition-all duration-500">

            <!-- Columna Izquierda: Contenido y Texto -->
            <div class="p-10 sm:p-12 flex flex-col justify-center order-2 lg:order-1">
                <div class="space-y-6">
                    <!-- Etiqueta de Error pequeña -->
                    <span class="inline-block px-4 py-2 text-sm font-bold tracking-wider text-indigo-500 uppercase bg-indigo-50 dark:bg-indigo-900/30 rounded-full">
                        Error 404
                    </span>

                    <!-- Título Grande -->
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-800 dark:text-white tracking-tight leading-tight">
                        Ruta no <br/> <span class="text-indigo-600 dark:text-indigo-400">encontrada</span>
                    </h1>

                    <!-- Frase Temática (Elegante, sin fondo negro) -->
                    <p class="text-lg text-slate-600 dark:text-slate-300 font-medium border-l-4 border-indigo-200 dark:border-indigo-700 pl-4 italic">
                        "{{ thematicQuote }}"
                    </p>

                    <!-- Descripción Técnica -->
                    <p class="text-slate-500 dark:text-slate-400 text-base leading-relaxed">
                        Lo sentimos, no pudimos localizar la página <code class="bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded text-indigo-600 dark:text-indigo-300 font-mono text-sm border border-slate-200 dark:border-slate-600 break-all">{{ page.url }}</code>. Puede que haya sido movida o eliminada.
                    </p>

                    <!-- Botón de Acción -->
                    <div class="pt-4">
                        <button
                            @click="goBack"
                            class="group relative inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold text-white transition-all duration-200 bg-indigo-600 rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-lg shadow-indigo-200 dark:shadow-none overflow-hidden"
                        >
                            <span class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-gradient-to-b from-transparent via-transparent to-black"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                            </svg>
                            Regresar al inicio
                        </button>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Imagen (Limpia, sin overlays oscuros) -->
            <div class="relative order-1 lg:order-2 bg-indigo-50 dark:bg-slate-700/50 flex items-center justify-center p-8 lg:p-0 overflow-hidden">
                <!-- Elementos decorativos de fondo (burbujas abstractas) -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-indigo-200/30 dark:bg-indigo-500/10 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 rounded-full bg-blue-200/30 dark:bg-blue-500/10 blur-3xl"></div>

                <div class="relative z-10 transform transition-transform duration-700 hover:scale-105 hover:rotate-1">
                    <!-- Contenedor de imagen con sombra flotante -->
                    <div class="rounded-2xl overflow-hidden shadow-2xl shadow-indigo-200/50 dark:shadow-black/40 border-4 border-white dark:border-slate-600">
                        <img
                            src="@/../../public/images/404.png"
                            alt="Ilustración perdida"
                            class="w-full h-auto max-w-sm object-cover"
                        />
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Opcional: Si deseas animaciones suaves al cargar */
div {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
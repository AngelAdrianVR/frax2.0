<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button 
        :type="type" 
        :disabled="loading"
        class="relative inline-flex items-center justify-center px-6 py-2.5 
               bg-blue-900 dark:bg-blue-300 border border-transparent rounded-lg 
               font-semibold text-xs text-blue-300 dark:text-gray-900 uppercase tracking-widest 
               hover:bg-gray-700 dark:hover:bg-gray-200 
               active:bg-gray-900 dark:active:bg-gray-300 
               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
               disabled:opacity-70 disabled:cursor-not-allowed
               transition-all duration-300 ease-in-out shadow-md hover:shadow-lg"
    >
        <!-- Contenido del botón (Texto) con transición de desplazamiento -->
        <span 
            class="transform transition-all duration-300 flex items-center"
            :class="loading ? 'translate-y-3 opacity-0' : 'translate-y-0 opacity-100'"
        >
            <slot />
        </span>

        <!-- Spinner de carga con transición de entrada -->
        <div 
            v-if="loading" 
            class="absolute inset-0 flex items-center justify-center transform transition-all duration-300"
            :class="loading ? 'translate-y-0 opacity-100' : '-translate-y-3 opacity-0'"
        >
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </button>
</template>
<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button 
        :type="type" 
        class="inline-flex items-center justify-center px-6 py-3 bg-blue-400 border border-transparent rounded-lg font-bold text-xs text-[#0B0F19] uppercase tracking-widest hover:bg-blue-200 hover:shadow-[0_0_15px_rgba(20,184,166,0.6)] active:bg-blue-600 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition-all duration-300 transform active:scale-95"
    >
        <slot />
    </button>
</template>
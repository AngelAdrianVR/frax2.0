<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="bg-white border border-gray-200 text-gray-900 rounded-lg shadow-sm 
               focus:border-teal-500 focus:ring focus:ring-teal-500/20 focus:ring-opacity-50 
               placeholder-gray-400 transition-all duration-300"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
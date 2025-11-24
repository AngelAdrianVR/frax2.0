<script setup>
import { computed, useSlots } from 'vue';
import SectionTitle from './SectionTitle.vue';

defineEmits(['submitted']);

const hasActions = computed(() => !! useSlots().actions);
</script>

<template>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <SectionTitle>
            <template #title>
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </SectionTitle>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form @submit.prevent="$emit('submitted')">
                <div
                    class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10"
                    :class="hasActions ? 'sm:rounded-tl-xl sm:rounded-tr-xl' : 'sm:rounded-xl'"
                >
                    <div class="grid grid-cols-6 gap-6">
                        <slot name="form" />
                    </div>
                </div>

                <div 
                    v-if="hasActions" 
                    class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-800/50 text-end sm:px-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 border-t border-gray-100 dark:border-gray-700 sm:rounded-bl-xl sm:rounded-br-xl"
                >
                    <slot name="actions" />
                </div>
            </form>
        </div>
    </div>
</template>
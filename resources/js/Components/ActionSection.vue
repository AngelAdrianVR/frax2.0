<script setup>
import SectionTitle from './SectionTitle.vue';
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
            <!-- Se agregó dark:bg-gray-800, bordes sutiles y rounded-xl para consistencia -->
            <div class="px-4 py-5 sm:p-6 bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 sm:rounded-xl">
                <slot name="content" />
            </div>
        </div>
    </div>
</template>
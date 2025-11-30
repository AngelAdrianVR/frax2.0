<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        {{ $page.props.auth }}
    </AppLayout>
</template>

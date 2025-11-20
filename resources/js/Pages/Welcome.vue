<script>
import LandingLayout from '@/Layouts/LandingLayout.vue';
import { Head } from '@inertiajs/vue3';

// Componentes Modulares
import HeroSection from '@/Components/Landing/HeroSection.vue';
import StatsSection from '@/Components/Landing/StatsSection.vue';
import ServicesSection from '@/Components/Landing/ServicesSection.vue';
import InfrastructureSection from '@/Components/Landing/InfrastructureSection.vue';
import TestimonialsSection from '@/Components/Landing/TestimonialsSection.vue';
import PricingSection from '@/Components/Landing/PricingSection.vue';
import VideoModal from '@/Components/Landing/VideoModal.vue';
import GalleryModal from '@/Components/Landing/GalleryModal.vue';

export default {
    name: 'Welcome',
    components: {
        Head,
        VideoModal,
        HeroSection,
        GalleryModal,
        StatsSection,
        LandingLayout,
        PricingSection,
        ServicesSection,
        TestimonialsSection,
        InfrastructureSection,
    },
    data() {
        return {
            // --- Estado Global de Modales ---
            showVideoModal: false,
            isGalleryOpen: false,
            
            // --- Estado del Modal de Galería (Lightbox) ---
            galleryTitle: '',
            currentGalleryImages: [],
            // El índice inicial para el lightbox se podría pasar como prop si el modal lo soporta,
            // pero por simplicidad asumiremos que siempre se reinicia o el modal lo maneja.
        }
    },
    methods: {
        /**
         * Maneja la apertura del lightbox global desde cualquier componente hijo.
         * Espera un objeto: { title, images, startIndex (opcional) }
         */
        handleOpenLightbox(payload) {
            this.galleryTitle = payload.title;
            this.currentGalleryImages = payload.images;
            this.isGalleryOpen = true;
            
            // Si tu GalleryModal soporta abrirse en un indice especifico,
            // podrías usar payload.startIndex aquí, por ejemplo, accediendo via ref
            // o pasando una prop extra al modal.
            // Por defecto GalleryModal empieza en 0.
        }
    }
}
</script>

<template>
    <LandingLayout title="El Futuro de tu Fraccionamiento">
        
        <HeroSection 
            @open-video="showVideoModal = true" 
        />

        <StatsSection />

        <ServicesSection />

        <!-- Escuchamos el evento open-lightbox que emite InfrastructureSection -->
        <InfrastructureSection 
            @open-lightbox="handleOpenLightbox" 
        />

        <TestimonialsSection />

        <PricingSection />

        <!-- Modals Globales -->
        <VideoModal 
            :isOpen="showVideoModal" 
            @close="showVideoModal = false" 
        />

        <GalleryModal 
            :isOpen="isGalleryOpen" 
            :title="galleryTitle"
            :images="currentGalleryImages"
            @close="isGalleryOpen = false" 
        />

    </LandingLayout>
</template>
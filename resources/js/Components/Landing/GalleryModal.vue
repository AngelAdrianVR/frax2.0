<script>
export default {
    name: 'GalleryModal',
    props: {
        isOpen: {
            type: Boolean,
            required: true
        },
        title: {
            type: String,
            default: ''
        },
        images: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            activeGalleryIndex: 0
        }
    },
    watch: {
        isOpen(val) {
            if (val) {
                this.activeGalleryIndex = 0;
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    },
    emits: ['close'],
    methods: {
        nextImage() {
            this.activeGalleryIndex = (this.activeGalleryIndex + 1) % this.images.length;
        },
        prevImage() {
            this.activeGalleryIndex = (this.activeGalleryIndex - 1 + this.images.length) % this.images.length;
        }
    }
}
</script>

<template>
    <Transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-sm" @click.self="$emit('close')">
            <!-- Close Button -->
            <button @click="$emit('close')" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors z-50">
                <i class="pi pi-times text-4xl"></i>
            </button>

            <div class="relative w-full max-w-6xl px-4 flex flex-col items-center">
                <h3 class="text-white text-xl font-light mb-4 tracking-wider">{{ title }}</h3>
                
                <div class="relative w-full h-[70vh] flex items-center justify-center">
                        <!-- Nav Prev -->
                    <button @click="prevImage" class="absolute left-0 p-4 text-white hover:text-blue-400 transition-colors z-50 bg-black/20 hover:bg-black/50 rounded-full">
                        <i class="pi pi-chevron-left text-3xl"></i>
                    </button>

                    <!-- Image -->
                    <img 
                        v-if="images.length > 0"
                        :src="images[activeGalleryIndex]" 
                        class="max-h-full max-w-full object-contain rounded-lg shadow-2xl animate-fade-in-up"
                        :key="activeGalleryIndex"
                    >

                    <!-- Nav Next -->
                    <button @click="nextImage" class="absolute right-0 p-4 text-white hover:text-blue-400 transition-colors z-50 bg-black/20 hover:bg-black/50 rounded-full">
                        <i class="pi pi-chevron-right text-3xl"></i>
                    </button>
                </div>
                
                <!-- Dots indicator -->
                <div class="flex gap-2 mt-6">
                        <button 
                        v-for="(img, idx) in images" 
                        :key="idx"
                        @click="activeGalleryIndex = idx"
                        class="h-1.5 rounded-full transition-all"
                        :class="activeGalleryIndex === idx ? 'w-8 bg-blue-500' : 'w-2 bg-gray-600'"
                    ></button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}
</style>
<script>
export default {
    name: 'VideoModal',
    props: {
        isOpen: {
            type: Boolean,
            required: true
        },
        videoUrl: {
            type: String,
            default: 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1'
        }
    },
    emits: ['close']
}
</script>

<template>
    <Transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 backdrop-blur-md p-4" @click.self="$emit('close')">
            <div class="relative w-full max-w-4xl aspect-video bg-black rounded-2xl overflow-hidden shadow-2xl border border-gray-800">
                <button @click="$emit('close')" class="absolute top-4 right-4 z-10 bg-black/50 hover:bg-black text-white p-2 rounded-full transition-all">
                    <i class="pi pi-times text-xl"></i>
                </button>
                <!-- Iframe -->
                <iframe class="w-full h-full" :src="videoUrl" title="Video Demo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
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
</style>
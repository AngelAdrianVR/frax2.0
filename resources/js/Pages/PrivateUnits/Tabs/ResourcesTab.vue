<template>
    <div class="space-y-8">
        
        <!-- Sección Vehículos -->
        <div>
            <h3 class="flex items-center gap-2 text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">
                <i class="pi pi-car"></i> Vehículos Registrados ({{ unit.vehicles.length }})
            </h3>
            
            <div v-if="unit.vehicles.length === 0" class="text-sm text-gray-400 italic bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl">
                Sin vehículos.
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div v-for="car in unit.vehicles" :key="car.id" 
                    class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded">
                                {{ car.plate }}
                            </span>
                            <i class="pi pi-car text-gray-300"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ car.brand }} {{ car.model }}</p>
                        <p class="text-xs text-gray-500">{{ car.color }}</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-gray-50 dark:border-gray-700">
                        <span class="text-[10px] text-gray-400">TAG: {{ car.tag_access || 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección Mascotas -->
        <div>
            <h3 class="flex items-center gap-2 text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">
                <i class="pi pi-heart"></i> Mascotas ({{ unit.pets.length }})
            </h3>

            <div v-if="unit.pets.length === 0" class="text-sm text-gray-400 italic bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl">
                Sin mascotas.
            </div>

            <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div v-for="pet in unit.pets" :key="pet.id" 
                    class="bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700 text-center">
                    <div class="w-10 h-10 mx-auto bg-pink-50 dark:bg-pink-900/20 rounded-full flex items-center justify-center text-pink-500 mb-2">
                        <i class="fa-solid fa-paw"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ pet.name }}</p>
                    <p class="text-xs text-gray-500">{{ pet.species }} - {{ pet.race }}</p>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    props: ['unit']
}
</script>
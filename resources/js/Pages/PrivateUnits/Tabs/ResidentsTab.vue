<template>
    <div class="space-y-4">
        <div v-if="unit.users.length === 0" class="text-center py-10 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300">
            <i class="pi pi-users text-4xl text-gray-300 mb-2"></i>
            <p class="text-gray-500">No hay usuarios registrados.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div 
                v-for="user in unit.users" 
                :key="user.id"
                class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4 transition hover:shadow-md"
            >
                <!-- Avatar / Iniciales -->
                <div class="h-12 w-12 rounded-full flex items-center justify-center text-lg font-bold"
                    :class="user.pivot.role_in_unit === 'Dueño' 
                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' 
                        : 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300'">
                    {{ getInitials(user.name) }}
                </div>

                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">
                        {{ user.name }}
                    </h4>
                    <p class="text-xs text-gray-500 truncate">
                        {{ user.email || 'Sin correo' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ user.phone || 'Sin teléfono' }}
                    </p>
                </div>

                <!-- Badge Rol -->
                <span class="px-2 py-1 text-[10px] uppercase font-bold rounded-lg"
                    :class="user.pivot.role_in_unit === 'Dueño' 
                        ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20' 
                        : 'bg-orange-50 text-orange-600 dark:bg-orange-900/20'">
                    {{ user.pivot.role_in_unit }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['unit'],
    methods: {
        getInitials(name) {
            if(!name) return '';
            return name
                .split(' ')
                .map(n => n[0])
                .join('')
                .substring(0, 2)
                .toUpperCase();
        }
    }
}
</script>
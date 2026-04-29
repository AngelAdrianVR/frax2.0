<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    invitations: Object
});

const getStatusColor = (status) => {
    switch(status) {
        case 'Aceptado': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'Pendiente': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'Expirado': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default: return 'bg-zinc-100 text-zinc-800';
    }
};
</script>

<template>
    <AppLayout title="Mis Invitaciones">
        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-6 px-4 sm:px-0">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Invitaciones de Registro</h1>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Invita a familiares o inquilinos a unirse a la app.</p>
                    </div>
                    <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md text-sm font-medium transition-colors">
                        Enviar Invitación
                    </button>
                </div>

                <div v-if="invitations.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                    <i class="pi pi-envelope text-4xl text-zinc-400 mb-3"></i>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Sin invitaciones</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">No has enviado invitaciones para tu domicilio.</p>
                </div>

                <div v-else class="bg-white dark:bg-zinc-800 shadow-xl sm:rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Correo Electrónico</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Rol Asignado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Vencimiento</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">Estatus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            <tr v-for="inv in invitations.data" :key="inv.id" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">{{ inv.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ inv.role_type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ inv.expires_at }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold" :class="getStatusColor(inv.status)">
                                        {{ inv.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
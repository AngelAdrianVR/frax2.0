<script setup>
import { ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    unit: Object
});

const confirm = useConfirm();
const toast = useToast();
const showLinkUserModal = ref(false);

const linkUserForm = useForm({
    email: '',
    role_in_unit: 'Inquilino'
});

const submitLinkUser = () => {
    linkUserForm.post(route('admin.private-units.link-user', props.unit.id), { 
        preserveScroll: true,
        onSuccess: () => {
            showLinkUserModal.value = false;
            linkUserForm.reset();
            toast.add({ severity: 'success', summary: 'Usuario Vinculado', detail: 'La invitación/vinculación fue procesada correctamente.', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo vincular el usuario, verifica el correo.', life: 3000 });
        }
    });
};

const unlinkUser = (userId) => {
    confirm.require({
        message: '¿Estás seguro de que deseas desvincular a este usuario de la propiedad?',
        header: 'Desvincular Usuario',
        icon: 'pi pi-unlink',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('admin.private-units.unlink-user', [props.unit.id, userId]), { 
                preserveScroll: true,
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Desvinculado', detail: 'El usuario ya no pertenece a la propiedad.', life: 3000 });
                }
            });
        }
    });
};
</script>

<template>
    <div class="space-y-6 animate-fade-in">
        <div class="flex justify-between items-end ml-4 mb-2 pr-4">
            <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider">Usuarios Vinculados</h2>
            <button @click="showLinkUserModal = true" class="text-[13px] font-semibold text-indigo-600 hover:text-indigo-800">+ Vincular Usuario</button>
        </div>
        
        <div v-if="!unit.users || unit.users.length === 0" class="bg-white dark:bg-[#1C1C1E] rounded-[20px] p-8 text-center border border-black/5 dark:border-white/5">
            <i class="pi pi-users text-4xl text-gray-300 mb-2 block"></i>
            <p class="text-gray-500 text-sm">No hay residentes registrados en esta propiedad.</p>
        </div>

        <div v-else class="bg-white dark:bg-[#1C1C1E] rounded-[20px] border border-black/5 dark:border-white/5 shadow-sm divide-y divide-gray-100 dark:divide-zinc-800">
            <div v-for="user in unit.users" :key="user.id" class="p-4 flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-[#2C2C2E] flex justify-center items-center overflow-hidden">
                        <i class="pi pi-user text-gray-400"></i>
                    </div>
                    <div>
                        <p class="text-[15px] font-semibold text-gray-900 dark:text-white">{{ user.name }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                {{ user.pivot.role_in_unit }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Acciones Rápidas -->
                <div class="flex gap-2">
                    <a v-if="user.phone" :href="`https://wa.me/52${user.phone.replace(/\\D/g,'')}`" target="_blank" class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 hover:bg-green-100 flex items-center justify-center transition tooltip" title="WhatsApp">
                        <i class="pi pi-whatsapp"></i>
                    </a>
                    <!-- Redirige al directorio filtrando por su nombre -->
                    <Link :href="route('directory.index', { search: user.name })" class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition tooltip" title="Ver en Directorio">
                        <i class="pi pi-id-card text-sm"></i>
                    </Link>
                    <button @click="unlinkUser(user.id)" class="w-8 h-8 rounded-full bg-gray-50 dark:bg-[#2C2C2E] text-gray-400 hover:text-red-500 flex items-center justify-center transition tooltip" title="Desvincular">
                        <i class="pi pi-unlink text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Vincular Usuario -->
        <div v-if="showLinkUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#1C1C1E] rounded-3xl p-6 w-full max-w-sm shadow-2xl">
                <h3 class="text-lg font-bold mb-4 dark:text-white">Vincular Usuario</h3>
                <p class="text-xs text-gray-500 mb-4">Ingresa el correo del residente. Si no tiene cuenta, se le enviará una invitación por correo.</p>
                <form @submit.prevent="submitLinkUser" class="space-y-4">
                    <input v-model="linkUserForm.email" type="email" placeholder="Correo electrónico" class="w-full rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white" required>
                    <select v-model="linkUserForm.role_in_unit" class="w-full rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white">
                        <option value="Inquilino">Inquilino</option>
                        <option value="Dueño">Dueño</option>
                    </select>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="showLinkUserModal = false" class="px-4 py-2 text-gray-500 text-sm font-bold">Cancelar</button>
                        <button type="submit" :disabled="linkUserForm.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-bold disabled:opacity-50">Vincular</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
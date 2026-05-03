<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router, useForm, Link } from '@inertiajs/vue3';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';

const props = defineProps({
    emergencies: Object, 
    isAdmin: Boolean
});

const confirm = useConfirm();

const showModal = ref(false);
const isEditing = ref(false);

const form = useForm({
    id: null,
    type: 'internal',
    name: '',
    phone: '',
    description: '',
    address: '',
    notes: '',
    icon: 'pi-phone',
    is_active: true
});

const openModal = (emergency = null) => {
    if (emergency) {
        isEditing.value = true;
        form.id = emergency.id;
        form.type = emergency.type;
        form.name = emergency.name;
        form.phone = emergency.phone;
        form.description = emergency.description || '';
        form.address = emergency.address || '';
        form.notes = emergency.notes || '';
        form.icon = emergency.icon || 'pi-phone';
        form.is_active = emergency.is_active !== 0; 
    } else {
        isEditing.value = false;
        form.reset();
    }
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        // Actualizada para users
        form.put(route('users.emergencies.update', form.id), {
            onSuccess: () => { showModal.value = false; form.reset(); }
        });
    } else {
        form.post(route('users.emergencies.store'), {
            onSuccess: () => { showModal.value = false; form.reset(); }
        });
    }
};

const deleteContact = (id) => {
    confirm.require({
        message: '¿Estás seguro de que deseas eliminar este contacto de emergencia? Esta acción no se puede deshacer.',
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Sí, eliminar',
        rejectLabel: 'Cancelar',
        accept: () => {
            router.delete(route('users.emergencies.destroy', id), { preserveScroll: true });
        }
    });
};

const toggleStatus = (emergency) => {
    const action = emergency.is_active ? 'inactivar' : 'activar';
    confirm.require({
        message: `¿Estás seguro de que deseas ${action} este contacto?`,
        header: 'Cambiar Estado',
        icon: 'pi pi-info-circle',
        acceptClass: emergency.is_active ? 'p-button-warning' : 'p-button-success',
        acceptLabel: `Sí, ${action}`,
        rejectLabel: 'Cancelar',
        accept: () => {
            router.patch(route('users.emergencies.toggle-status', emergency.id), {}, { preserveScroll: true });
        }
    });
};

const reportOutdated = (id) => {
    confirm.require({
        message: '¿El número es incorrecto o ya no funciona? Se notificará a la administración para que lo actualice.',
        header: 'Reportar número desactualizado',
        icon: 'pi pi-flag',
        acceptClass: 'p-button-warning',
        acceptLabel: 'Sí, reportar',
        rejectLabel: 'Cancelar',
        accept: () => {
            router.post(route('users.emergencies.report', id), {}, { preserveScroll: true });
        }
    });
};
</script>

<template>
    <AppLayout title="Contactos de Emergencia">
        <ConfirmDialog></ConfirmDialog>

        <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-8 px-4 sm:px-0">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Directorio Comunitario</h1>
                            <p class="text-zinc-500 dark:text-zinc-400 mt-1">Encuentra a tus vecinos, contactos de emergencia y servicios recomendados.</p>
                        </div>
                    </div>
                    
                    <div class="border-b border-zinc-200 dark:border-zinc-700 flex gap-6 overflow-x-auto">
                        <Link :href="route('users.index')" class="pb-3 font-medium text-sm border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 whitespace-nowrap transition-colors">
                            <i class="pi pi-users mr-2"></i>Residentes
                        </Link>
                        <div class="pb-3 font-medium text-sm border-b-2 border-red-500 text-red-600 dark:text-red-400 whitespace-nowrap">
                            <i class="pi pi-shield mr-2"></i>Emergencias
                        </div>
                        <Link :href="route('users.services')" class="pb-3 font-medium text-sm border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 whitespace-nowrap transition-colors">
                            <i class="pi pi-briefcase mr-2"></i>Habilidades y Servicios
                        </Link>
                    </div>
                </div>

                <!-- CONTENIDO EMERGENCIAS -->
                <div class="flex justify-between items-center mb-6 px-4 sm:px-0">
                    <h2 class="text-xl font-bold text-zinc-800 dark:text-white">Contactos de Emergencia</h2>
                    <button @click="openModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors shadow-sm">
                        <i class="pi pi-plus mr-1"></i> Agregar Contacto
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-4 sm:px-0">
                    <!-- Internos -->
                    <div>
                        <h3 class="font-semibold text-zinc-500 uppercase tracking-wider text-xs mb-4 flex items-center gap-2">
                            <i class="pi pi-building text-red-500"></i> Servicios Internos (Coto)
                        </h3>
                        <div class="space-y-4">
                            <div v-if="!emergencies?.internal || emergencies.internal.length === 0" class="text-sm text-zinc-400 italic bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 text-center">No hay contactos registrados.</div>
                            
                            <div v-for="contact in emergencies?.internal" :key="contact.id" 
                                :class="['p-5 rounded-xl shadow-sm border transition-all relative group', 
                                         contact.is_active ? 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700' : 'bg-zinc-100 dark:bg-zinc-800/50 border-zinc-200 dark:border-zinc-700 opacity-75']">
                                
                                <span v-if="!contact.is_active" class="absolute top-3 right-3 text-[10px] font-bold bg-zinc-200 text-zinc-600 px-2 py-1 rounded">INACTIVO</span>

                                <div class="flex items-start gap-4">
                                    <div :class="['w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0', contact.is_active ? 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400' : 'bg-zinc-200 text-zinc-500']">
                                        <i :class="['pi text-xl', contact.icon || 'pi-phone']"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-lg text-zinc-900 dark:text-white leading-tight">{{ contact.name }}</h4>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ contact.description }}</p>
                                        <p v-if="contact.address" class="text-xs text-zinc-500 mt-2 flex items-start gap-1"><i class="pi pi-map-marker mt-0.5"></i> {{ contact.address }}</p>
                                        <div v-if="contact.notes" class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 text-xs rounded-lg border border-yellow-100 dark:border-yellow-800/50">
                                            <strong>Nota:</strong> {{ contact.notes }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-700 flex justify-between items-center">
                                    <button @click="reportOutdated(contact.id)" class="text-xs text-zinc-400 hover:text-orange-500 transition-colors flex items-center gap-1" title="Reportar si no contestan o cambió el número">
                                        <i class="pi pi-flag"></i> Reportar
                                    </button>
                                    
                                    <div class="flex items-center gap-3">
                                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="toggleStatus(contact)" :class="contact.is_active ? 'text-orange-400 hover:text-orange-600' : 'text-green-500 hover:text-green-600'" class="p-2 tooltip" :title="contact.is_active ? 'Inactivar' : 'Activar'"><i :class="contact.is_active ? 'pi pi-power-off' : 'pi pi-check-circle'"></i></button>
                                            <button @click="openModal(contact)" class="text-indigo-400 hover:text-indigo-600 p-2"><i class="pi pi-pencil"></i></button>
                                            <button @click="deleteContact(contact.id)" class="text-red-400 hover:text-red-600 p-2"><i class="pi pi-trash"></i></button>
                                        </div>
                                        <a :href="`tel:${contact.phone}`" :class="['px-4 py-1.5 rounded-lg font-bold transition-colors', contact.is_active ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/40 dark:text-red-300' : 'bg-zinc-200 text-zinc-500 cursor-not-allowed pointer-events-none']">
                                            <i class="pi pi-phone mr-1 text-sm"></i> {{ contact.phone }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Externos -->
                    <div>
                        <h3 class="font-semibold text-zinc-500 uppercase tracking-wider text-xs mb-4 flex items-center gap-2">
                            <i class="pi pi-map-marker text-red-500"></i> Autoridades Externas
                        </h3>
                        <div class="space-y-4">
                            <div v-if="!emergencies?.external || emergencies.external.length === 0" class="text-sm text-zinc-400 italic bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 text-center">No hay contactos registrados.</div>
                            
                            <div v-for="contact in emergencies?.external" :key="contact.id" 
                                :class="['p-5 rounded-xl shadow-sm border border-l-4 transition-all relative group', 
                                         contact.is_active ? 'bg-white dark:bg-zinc-800 border-zinc-200 border-l-red-500 dark:border-zinc-700 dark:border-l-red-500' : 'bg-zinc-100 dark:bg-zinc-800/50 border-zinc-200 border-l-zinc-400 opacity-75']">
                                
                                <span v-if="!contact.is_active" class="absolute top-3 right-3 text-[10px] font-bold bg-zinc-200 text-zinc-600 px-2 py-1 rounded">INACTIVO</span>

                                <div class="flex items-start gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-lg text-zinc-900 dark:text-white leading-tight">{{ contact.name }}</h4>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ contact.description }}</p>
                                        <p v-if="contact.address" class="text-xs text-zinc-500 mt-2 flex items-start gap-1"><i class="pi pi-map-marker mt-0.5"></i> {{ contact.address }}</p>
                                        <div v-if="contact.notes" class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 text-xs rounded-lg border border-yellow-100">
                                            <strong>Nota:</strong> {{ contact.notes }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-700 flex justify-between items-center">
                                    <button @click="reportOutdated(contact.id)" class="text-xs text-zinc-400 hover:text-orange-500 transition-colors flex items-center gap-1">
                                        <i class="pi pi-flag"></i> Reportar
                                    </button>
                                    
                                    <div class="flex items-center gap-3">
                                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="toggleStatus(contact)" :class="contact.is_active ? 'text-orange-400 hover:text-orange-600' : 'text-green-500 hover:text-green-600'" class="p-2"><i :class="contact.is_active ? 'pi pi-power-off' : 'pi pi-check-circle'"></i></button>
                                            <button @click="openModal(contact)" class="text-indigo-400 hover:text-indigo-600 p-2"><i class="pi pi-pencil"></i></button>
                                            <button @click="deleteContact(contact.id)" class="text-red-400 hover:text-red-600 p-2"><i class="pi pi-trash"></i></button>
                                        </div>
                                        <a :href="`tel:${contact.phone}`" :class="['px-4 py-1.5 rounded-lg font-bold transition-colors', contact.is_active ? 'text-white hover:bg-zinc-900 shadow-sm bg-zinc-800' : 'bg-zinc-200 text-zinc-500 cursor-not-allowed pointer-events-none']">
                                            <i class="pi pi-phone mr-1 text-sm"></i> {{ contact.phone }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL AGREGAR / EDITAR -->
        <div v-if="showModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div @click="showModal = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div class="bg-white dark:bg-zinc-800 rounded-2xl w-full max-w-lg p-6 relative z-10 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-bold dark:text-white">{{ isEditing ? 'Editar Contacto' : 'Nuevo Contacto de Emergencia' }}</h3>
                    <button @click="showModal = false" class="text-zinc-400 hover:text-zinc-600"><i class="pi pi-times"></i></button>
                </div>
                
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Tipo</label>
                            <select v-model="form.type" class="w-full rounded-lg border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                <option value="internal">Interno (Caseta, Admon)</option>
                                <option value="external">Externo (Bomberos, Policía, CFE)</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Dependencia / Nombre</label>
                            <input v-model="form.name" type="text" class="w-full rounded-lg border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white" required>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Teléfono</label>
                            <input v-model="form.phone" type="text" class="w-full rounded-lg border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Descripción (Opcional)</label>
                            <input v-model="form.description" type="text" placeholder="Ej. Patrulla de cuadrante, Caseta Norte..." class="w-full rounded-lg border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Dirección (Opcional)</label>
                            <input v-model="form.address" type="text" placeholder="Ubicación física si aplica" class="w-full rounded-lg border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1 dark:text-zinc-300">Notas / Avisos (Opcional)</label>
                            <textarea v-model="form.notes" rows="2" placeholder="Ej. Solo llamar en caso de robos, no atienden reportes de ruido..." class="w-full rounded-lg border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-700">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-zinc-700 bg-zinc-100 rounded-lg">Cancelar</button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">
                            {{ isEditing ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
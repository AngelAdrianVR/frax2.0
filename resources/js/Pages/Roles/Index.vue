<template>
    <AppLayout title="Roles y Permisos">
        <template v-slot:header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gestión de Roles y Permisos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Pestañas de Navegación -->
                <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button 
                            @click="activeTab = 'roles'"
                            :class="[
                                activeTab === 'roles'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors'
                            ]"
                        >
                            Roles
                        </button>
                        <button 
                            @click="activeTab = 'permissions'"
                            :class="[
                                activeTab === 'permissions'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors'
                            ]"
                        >
                            Permisos del Sistema
                        </button>
                    </nav>
                </div>

                <!-- CONTENIDO PESTAÑA ROLES -->
                <div v-if="activeTab === 'roles'">
                    <!-- Botón Crear Nuevo Rol -->
                    <div class="flex justify-end mb-4">
                        <button 
                            @click="openModal(null)"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition-colors duration-200 flex items-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Nuevo Rol
                        </button>
                    </div>

                    <!-- Lista de Roles -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg transition-colors duration-200">
                        <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rol</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Permisos Asignados</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ role.name }}</div>
                                                    <div v-if="role.name === 'Admin'" class="text-xs text-yellow-600 dark:text-yellow-400 font-semibold flex items-center mt-1">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                                        Sistema
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                <template v-if="role.name === 'Admin'">
                                                     <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200 border border-green-200 dark:border-green-800">
                                                        Acceso Total
                                                    </span>
                                                </template>
                                                <template v-else>
                                                    <span 
                                                        v-for="perm in role.permissions.slice(0, 5)" 
                                                        :key="perm.id"
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200 border border-blue-200 dark:border-blue-800"
                                                    >
                                                        {{ perm.name }}
                                                    </span>
                                                    <span v-if="role.permissions.length > 5" class="text-xs text-gray-500 dark:text-gray-400 font-medium px-1">
                                                        +{{ role.permissions.length - 5 }} más...
                                                    </span>
                                                    <span v-if="role.permissions.length === 0" class="text-xs text-gray-400 dark:text-gray-500 italic">Sin permisos</span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button 
                                                @click="openModal(role)" 
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3 transition-colors"
                                            >
                                                {{ role.name === 'Admin' ? 'Ver Detalles' : 'Editar' }}
                                            </button>
                                            
                                            <button 
                                                v-if="role.name !== 'Admin'"
                                                @click="deleteRole(role)" 
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                            >
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- CONTENIDO PESTAÑA PERMISOS -->
                <div v-if="activeTab === 'permissions'">
                     <!-- Botón Crear Nuevo Permiso -->
                    <div class="flex justify-end mb-4">
                        <button 
                            @click="openPermissionModal(null)"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow transition-colors duration-200 flex items-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Nuevo Permiso
                        </button>
                    </div>

                    <!-- Lista de Permisos Agrupada -->
                    <div class="space-y-6">
                         <!-- Iteramos sobre los GRUPOS (reutilizando la lógica computada) -->
                        <div v-for="(groupPerms, groupName) in groupedPermissions" :key="groupName" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ groupName }}</h3>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="permission in groupPerms" :key="permission.id" class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ permission.name }}</span>
                                    <div class="flex space-x-2">
                                        <button @click="openPermissionModal(permission)" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 p-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button @click="deletePermission(permission)" class="text-red-600 hover:text-red-800 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 p-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <!-- Permisos Sin Categoría -->
                         <div v-if="miscPermissions.length > 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Otros Permisos</h3>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="permission in miscPermissions" :key="permission.id" class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/20">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ permission.name }}</span>
                                    <div class="flex space-x-2">
                                        <button @click="openPermissionModal(permission)" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 p-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button @click="deletePermission(permission)" class="text-red-600 hover:text-red-800 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 p-1 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Crear/Editar ROL -->
        <DialogModal :show="showModal" @close="closeModal" maxWidth="4xl">
            <template v-slot:title>
                <div class="flex items-center dark:text-white">
                    <span v-if="isReadOnly" class="flex items-center text-yellow-500 mr-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                    {{ modalTitle }}
                </div>
            </template>

            <template v-slot:content>
                <div class="mt-4">
                    <!-- Banner de Admin -->
                    <div v-if="isReadOnly" class="mb-6 bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                    El rol de <strong>Administrador</strong> tiene acceso total al sistema por defecto. Sus permisos no pueden ser modificados manualmente para evitar bloqueos del sistema.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Nombre del Rol -->
                    <div class="mb-6">
                        <InputLabel for="name" value="Nombre del Rol" class="dark:text-gray-300" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full dark:bg-gray-900 dark:text-white dark:border-gray-700 disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-gray-800"
                            placeholder="Ej. Supervisor de Alberca"
                            ref="nameInput"
                            :disabled="isReadOnly"
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <!-- Lista de Permisos Agrupada (Checkbox) -->
                    <div class="mt-6">
                        <InputLabel value="Permisos del Rol" class="mb-3 dark:text-gray-300 text-lg font-semibold" />
                        
                        <div class="space-y-6 max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
                            <!-- Grupos -->
                            <div v-for="(groupPerms, groupName) in groupedPermissions" :key="groupName" class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-600 pb-2 uppercase tracking-wide">
                                    {{ groupName }}
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    <div v-for="permission in groupPerms" :key="permission.id" class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input 
                                                type="checkbox" 
                                                :value="permission.name" 
                                                v-model="form.permissions"
                                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-800 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                                :id="'perm_' + permission.id"
                                                :disabled="isReadOnly"
                                            >
                                        </div>
                                        <div class="ml-2 text-sm">
                                            <label :for="'perm_' + permission.id" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none" :class="{'cursor-not-allowed': isReadOnly}">
                                                {{ permission.name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Otros -->
                            <div v-if="miscPermissions.length > 0" class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-600 pb-2 uppercase tracking-wide">
                                    Otros Permisos
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    <div v-for="permission in miscPermissions" :key="permission.id" class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            :value="permission.name" 
                                            v-model="form.permissions"
                                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-800 dark:focus:ring-offset-gray-800 disabled:opacity-50"
                                            :id="'perm_' + permission.id"
                                            :disabled="isReadOnly"
                                        >
                                        <label :for="'perm_' + permission.id" class="ml-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                                            {{ permission.name }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template v-slot:footer>
                <SecondaryButton @click="closeModal" class="dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    {{ isReadOnly ? 'Cerrar' : 'Cancelar' }}
                </SecondaryButton>

                <PrimaryButton 
                    v-if="!isReadOnly"
                    class="ml-3" 
                    :class="{ 'opacity-25': form.processing }" 
                    :disabled="form.processing"
                    @click="saveRole"
                >
                    {{ isEditing ? 'Guardar Cambios' : 'Crear Rol' }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Modal Crear/Editar PERMISO -->
        <DialogModal :show="showPermissionModal" @close="closePermissionModal">
            <template v-slot:title>
                <span class="dark:text-white">{{ isEditingPermission ? 'Editar Permiso' : 'Crear Nuevo Permiso' }}</span>
            </template>

            <template v-slot:content>
                <div class="mt-4">
                    <div class="mb-4">
                        <InputLabel for="perm_name" value="Nombre del Permiso" class="dark:text-gray-300" />
                        <TextInput
                            id="perm_name"
                            v-model="permForm.name"
                            type="text"
                            class="mt-1 block w-full dark:bg-gray-900 dark:text-white dark:border-gray-700"
                            placeholder="Ej. Crear Amenidades"
                            ref="permNameInput"
                        />
                        <InputError :message="permForm.errors.name" class="mt-2" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            Sugerencia: Usa nombres claros como "Acción + Objeto" (Ej. <i>Editar Usuarios</i>) para que se agrupen automáticamente.
                        </p>
                    </div>
                </div>
            </template>

            <template v-slot:footer>
                <SecondaryButton @click="closePermissionModal" class="dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton 
                    class="ml-3" 
                    :class="{ 'opacity-25': permForm.processing }" 
                    :disabled="permForm.processing"
                    @click="savePermission"
                >
                    {{ isEditingPermission ? 'Guardar Cambios' : 'Crear Permiso' }}
                </PrimaryButton>
            </template>
        </DialogModal>

    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';

export default {
    components: {
        AppLayout,
        DialogModal,
        PrimaryButton,
        SecondaryButton,
        TextInput,
        InputLabel,
        InputError,
    },
    props: {
        roles: Array,
        permissions: Array, // Array plano desde el backend
    },
    data() {
        return {
            activeTab: 'roles', // 'roles' | 'permissions'

            // --- ESTADO ROLES ---
            showModal: false,
            isEditing: false,
            isReadOnly: false, 
            editingRoleId: null,
            form: useForm({
                name: '',
                permissions: [] 
            }),

            // --- ESTADO PERMISOS ---
            showPermissionModal: false,
            isEditingPermission: false,
            editingPermissionId: null,
            permForm: useForm({
                name: ''
            }),

            // Configuración de Grupos (Compartido)
            permissionCategories: {
                'Seguridad y Accesos': ['QR', 'Rondin', 'Bitácora', 'Puntos de Control', 'Historial de Patrullaje', 'Vigilantes'],
                'Residentes y Visitas': ['Residentes', 'Invitaciones', 'Visitas', 'Mascotas', 'Vehículos'],
                'Amenidades y Reservas': ['Amenidades', 'Reservas', 'Reglas'],
                'Finanzas y Pagos': ['Finanzas', 'Pagos', 'Cobros', 'Cuotas', 'Conciliar', 'Proveedores'],
                'Comunicación': ['Avisos', 'Eventos', 'Incidentes', 'Comentarios'],
                'Administración': ['Usuarios', 'Fraccionamiento', 'Paquetería']
            }
        };
    },
    computed: {
        modalTitle() {
            if (this.isReadOnly) return 'Detalles del Rol (Solo Lectura)';
            return this.isEditing ? 'Editar Rol' : 'Crear Nuevo Rol';
        },
        // Lógica para agrupar permisos (usada en ambas pestañas)
        groupedPermissions() {
            const groups = {};
            Object.keys(this.permissionCategories).forEach(key => {
                groups[key] = [];
            });

            this.permissions.forEach(perm => {
                let assigned = false;
                for (const [category, keywords] of Object.entries(this.permissionCategories)) {
                    if (keywords.some(keyword => perm.name.includes(keyword))) {
                        groups[category].push(perm);
                        assigned = true;
                        break;
                    }
                }
            });

            Object.keys(groups).forEach(key => {
                if (groups[key].length === 0) delete groups[key];
            });

            return groups;
        },
        miscPermissions() {
            return this.permissions.filter(perm => {
                let assigned = false;
                for (const keywords of Object.values(this.permissionCategories)) {
                    if (keywords.some(keyword => perm.name.includes(keyword))) {
                        assigned = true;
                        break;
                    }
                }
                return !assigned;
            });
        }
    },
    methods: {
        // --- MÉTODOS ROLES ---
        openModal(role = null) {
            this.form.clearErrors();
            this.form.reset();
            if (role) {
                this.isEditing = true;
                this.editingRoleId = role.id;
                this.isReadOnly = (role.name === 'Admin');
                this.form.name = role.name;
                this.form.permissions = role.permissions.map(p => p.name);
            } else {
                this.isEditing = false;
                this.isReadOnly = false;
                this.editingRoleId = null;
                this.form.name = '';
                this.form.permissions = [];
            }
            this.showModal = true;
            this.$nextTick(() => {
                if(!this.isReadOnly && this.$refs.nameInput) this.$refs.nameInput.focus();
            });
        },
        closeModal() {
            this.showModal = false;
            this.form.reset();
            this.isReadOnly = false;
        },
        saveRole() {
            if (this.isReadOnly) return;
            if (this.isEditing) {
                this.form.put(route('roles.update', this.editingRoleId), {
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                });
            } else {
                this.form.post(route('roles.store'), {
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                });
            }
        },
        deleteRole(role) {
            if (role.name === 'Admin') return;
            if (confirm(`¿Estás seguro de eliminar el rol "${role.name}"?`)) {
                this.$inertia.delete(route('roles.destroy', role.id));
            }
        },

        // --- MÉTODOS PERMISOS ---
        openPermissionModal(permission = null) {
            this.permForm.clearErrors();
            this.permForm.reset();
            if (permission) {
                this.isEditingPermission = true;
                this.editingPermissionId = permission.id;
                this.permForm.name = permission.name;
            } else {
                this.isEditingPermission = false;
                this.editingPermissionId = null;
                this.permForm.name = '';
            }
            this.showPermissionModal = true;
            this.$nextTick(() => {
                if(this.$refs.permNameInput) this.$refs.permNameInput.focus();
            });
        },
        closePermissionModal() {
            this.showPermissionModal = false;
            this.permForm.reset();
        },
        savePermission() {
            if (this.isEditingPermission) {
                this.permForm.put(route('permissions.update', this.editingPermissionId), { // Asegúrate de tener esta ruta definida
                    preserveScroll: true,
                    onSuccess: () => this.closePermissionModal(),
                });
            } else {
                this.permForm.post(route('permissions.store'), { // Asegúrate de tener esta ruta definida
                    preserveScroll: true,
                    onSuccess: () => this.closePermissionModal(),
                });
            }
        },
        deletePermission(permission) {
            if (confirm(`¿Estás seguro de eliminar el permiso "${permission.name}"? Esto afectará a todos los roles que lo tengan asignado.`)) {
                this.$inertia.delete(route('permissions.destroy', permission.id));
            }
        }
    }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.5); border-radius: 20px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.8); }
</style>
<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { ref, watch } from 'vue';
    import { router, Link } from '@inertiajs/vue3';
    import debounce from 'lodash/debounce';

    // Importaciones de PrimeVue para la confirmación y la alerta (Toast)
    import { useConfirm } from "primevue/useconfirm";
    import { useToast } from "primevue/usetoast";
    import ConfirmDialog from 'primevue/confirmdialog';
    import Toast from 'primevue/toast';

    const props = defineProps({
        users: Object,
        filters: Object
    });

    const search = ref(props.filters.search || '');
    const confirm = useConfirm();
    const toast = useToast();

    // Buscador reactivo
    watch(search, debounce((value) => {
        router.get(route('users.index'), { search: value }, { 
            preserveState: true, 
            replace: true,
            preserveScroll: true
        });
    }, 300));

    // Eliminar usuario con Confirmación de PrimeVue
    const deleteUser = (id) => {
        confirm.require({
            message: '¿Estás seguro de que deseas eliminar este residente? Esta acción no se puede deshacer.',
            header: 'Confirmar Eliminación',
            icon: 'pi pi-exclamation-triangle',
            acceptLabel: 'Sí, eliminar',
            rejectLabel: 'Cancelar',
            acceptClass: 'p-button-danger', // Estilo rojo para el botón de confirmación
            accept: () => {
                router.delete(route('users.destroy', id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        // Muestra la etiqueta/toast al terminar exitosamente
                        toast.add({ 
                            severity: 'success', 
                            summary: '¡Eliminado!', 
                            detail: 'El residente ha sido eliminado correctamente.', 
                            life: 3000 
                        });
                    }
                });
            }
        });
    };
    </script>

    <template>
        <AppLayout title="Directorio de Residentes">
            <!-- Componentes Globales de PrimeVue para esta vista -->
            <ConfirmDialog></ConfirmDialog>
            <Toast />

            <div class="py-12 bg-zinc-50 dark:bg-zinc-900 min-h-screen transition-colors duration-300">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    
                    <!-- Encabezado y Navegación de Pestañas -->
                    <div class="mb-8 px-4 sm:px-0">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                            <div>
                                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">Directorio Comunitario</h1>
                                <p class="text-zinc-500 dark:text-zinc-400 mt-1">Encuentra a tus vecinos, contactos de emergencia y servicios.</p>
                            </div>
                            <div class="flex gap-3">
                                <Link :href="route('users.settings')" class="bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 px-4 py-2 rounded-xl text-sm font-medium border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 shadow-sm transition-colors flex items-center gap-2">
                                    <i class="pi pi-cog"></i> Mis Ajustes
                                </Link>
                                <Link :href="route('users.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-700 shadow-sm transition-colors flex items-center gap-2">
                                    <i class="pi pi-plus"></i> Nuevo Residente
                                </Link>
                            </div>
                        </div>
                        
                        <!-- Pestañas Reales (Navegación) -->
                        <div class="border-b border-zinc-200 dark:border-zinc-700 flex gap-6 overflow-x-auto">
                            <div class="pb-3 font-medium text-sm border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                <i class="pi pi-users mr-2"></i>Residentes
                            </div>
                            <Link :href="route('users.emergencies')" class="pb-3 font-medium text-sm border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 whitespace-nowrap transition-colors">
                                <i class="pi pi-shield mr-2"></i>Emergencias
                            </Link>
                            <Link :href="route('users.services')" class="pb-3 font-medium text-sm border-b-2 border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 whitespace-nowrap transition-colors">
                                <i class="pi pi-briefcase mr-2"></i>Habilidades y Servicios
                            </Link>
                        </div>
                    </div>

                    <!-- Buscador -->
                    <div class="flex justify-end mb-6 px-4 sm:px-0">
                        <div class="w-full md:w-80 relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="pi pi-search text-zinc-400"></i>
                            </span>
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Buscar residente..." 
                                class="pl-10 pr-4 py-2 w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all"
                            >
                        </div>
                    </div>

                    <!-- Estado Vacío -->
                    <div v-if="users.data.length === 0" class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm p-12 text-center border border-dashed border-zinc-300 dark:border-zinc-700">
                        <div class="mx-auto h-16 w-16 text-zinc-400 mb-4 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                            <i class="pi pi-users text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">No se encontraron residentes</h3>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Prueba buscando con otro nombre o correo electrónico.</p>
                    </div>

                    <!-- Grid de Tarjetas de Residentes -->
                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 px-4 sm:px-0">
                    <div v-for="user in users.data" :key="user.id" class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-zinc-100 dark:border-zinc-700 overflow-hidden group">
                        
                        <!-- Header / Banner de la tarjeta con color dinámico -->
                        <div :class="[
                            'h-24 relative', 
                            user.system_role === 'Administrador' || user.system_role === 'Admin' 
                                ? 'bg-gradient-to-r from-purple-600 to-indigo-600' 
                                : 'bg-gradient-to-r from-blue-500 to-cyan-500'
                        ]">
                            <!-- Etiqueta del Puesto/Rol -->
                            <span class="absolute top-3 right-3 px-2.5 py-1 text-[10px] font-bold uppercase rounded-full bg-white/20 text-white backdrop-blur-sm border border-white/30">
                                {{ user.display_role }}
                            </span>
                        </div>

                        <!-- Contenido -->
                        <div class="px-6 pb-6 relative">
                                <!-- Avatar Flotante -->
                                <div class="flex justify-center -mt-12 mb-3">
                                    <img 
                                        :src="user.avatar" 
                                        :alt="user.name"
                                        class="h-24 w-24 rounded-full object-cover border-4 border-white dark:border-zinc-800 shadow-md bg-white"
                                    >
                                </div>

                                <div class="text-center">
                                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white line-clamp-1" :title="user.name">
                                        {{ user.name }}
                                    </h3>
                                    
                                    <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-700/50 text-sm font-medium text-zinc-600 dark:text-zinc-300 border border-zinc-100 dark:border-zinc-600">
                                        <i class="pi pi-home text-indigo-500"></i>
                                        {{ user.property }}
                                    </div>
                                </div>

                                <!-- NUEVO: Mostrar Correo y Teléfono -->
                                <div class="mt-4 space-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    <div v-if="user.email" class="flex items-center justify-center gap-2">
                                        <i class="pi pi-envelope text-xs"></i> {{ user.email }}
                                    </div>
                                    <div v-if="user.phone" class="flex items-center justify-center gap-2">
                                        <i class="pi pi-phone text-xs"></i> {{ user.phone }}
                                    </div>
                                </div>

                                <!-- Botones de Acción y Contacto -->
                                <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-700 flex justify-center gap-4">
                                    <a v-if="user.email" :href="`mailto:${user.email}`" class="text-zinc-400 hover:text-indigo-500 transition-colors tooltip" title="Enviar Correo">
                                        <i class="pi pi-envelope text-xl"></i>
                                    </a>
                                    <a v-if="user.phone" :href="`https://wa.me/${user.phone.replace(/[^0-9]/g, '')}`" target="_blank" rel="noopener noreferrer" class="text-zinc-400 hover:text-green-500 transition-colors tooltip" title="Enviar WhatsApp">
                                        <i class="pi pi-whatsapp text-xl"></i>
                                    </a>
                                    
                                    <div class="w-px h-6 bg-zinc-200 dark:bg-zinc-700 mx-1"></div>

                                    <!-- Acciones CRUD -->
                                    <Link :href="route('users.edit', user.id)" class="text-zinc-400 hover:text-amber-500 transition-colors tooltip" title="Editar Usuario">
                                        <i class="pi pi-pencil text-xl"></i>
                                    </Link>
                                    <button @click="deleteUser(user.id)" class="text-zinc-400 hover:text-red-500 transition-colors tooltip" title="Eliminar Usuario">
                                        <i class="pi pi-trash text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paginación Simple -->
                    <div v-if="users.links && users.links.length > 3" class="flex justify-center mt-8 pb-8">
                        <div class="flex gap-1 flex-wrap justify-center">
                            <component
                                :is="link.url ? 'a' : 'span'"
                                v-for="(link, key) in users.links" :key="key"
                                :href="link.url"
                                v-html="link.label"
                                @click.prevent="link.url ? router.get(link.url, {}, { preserveScroll: true }) : null"
                                class="px-4 py-2 border rounded-lg text-sm transition-colors cursor-pointer"
                                :class="[
                                    link.active 
                                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' 
                                        : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </AppLayout>
    </template>
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    unit: Object
});

// Extraer el usuario principal si existe
const primaryUser = props.unit.users && props.unit.users.length > 0 ? props.unit.users[0] : null;

// Inicializamos el formulario con los datos existentes
const form = useForm({
    _method: 'PUT', // Truco de Laravel/Inertia para enviar archivos en edición
    unit_street: props.unit.unit_street || '',
    exterior_number: props.unit.exterior_number || '',
    int_number: props.unit.int_number || '',
    lot_number: props.unit.lot_number || '',
    square_meters: props.unit.square_meters || '',
    status: props.unit.status || 'Activo',
    access_block: props.unit.access_block ? true : false,
    
    // Propietario Inicial (Si hay datos los carga)
    owner_name: primaryUser ? primaryUser.name : '',
    owner_email: primaryUser ? primaryUser.email : '',
    owner_phone: primaryUser ? primaryUser.phone : '',
    owner_role: primaryUser && primaryUser.pivot ? primaryUser.pivot.role_in_unit : 'Dueño',

    // Documentación
    deed_file: null, 
    lease_file: null, 
    id_file: null, 

    // Arreglos dinámicos
    access_tags: [''],
    emergency_contacts: [{ name: '', phone: '', relation: '' }]
});

const addTag = () => form.access_tags.push('');
const removeTag = (index) => form.access_tags.splice(index, 1);

const addContact = () => form.emergency_contacts.push({ name: '', phone: '', relation: '' });
const removeContact = (index) => form.emergency_contacts.splice(index, 1);

const handleFileUpload = (e, field) => {
    form[field] = e.target.files[0];
};

const submit = () => {
    // Usamos POST porque Inertia no puede enviar archivos por PUT nativo, pero Laravel lo lee como PUT gracias a _method: 'PUT'
    form.post(route('admin.private-units.update', props.unit.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            // El controlador redirige al index
        }
    });
};
</script>

<template>
    <AppLayout title="Editar Propiedad">
        <div class="py-8 px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-3xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <Link :href="route('admin.private-units.index')" class="w-10 h-10 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-400 hover:text-zinc-100 transition border border-zinc-700/50">
                            <i class="pi pi-angle-left text-xl"></i>
                        </Link>
                        <div>
                            <h1 class="text-lg font-medium text-zinc-100 tracking-tight">Editar Propiedad</h1>
                        </div>
                    </div>
                    <button @click="submit" :disabled="form.processing" class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50 flex items-center gap-2">
                        <i v-if="form.processing" class="pi pi-spin pi-spinner"></i>
                        Actualizar
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    
                    <section>
                        <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider ml-1 mb-3">Detalles de la Propiedad</h2>
                        <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                            
                            <div class="flex items-center p-4 border-b border-zinc-800/60">
                                <label class="w-1/3 text-sm font-medium text-zinc-200">Calle / Avenida</label>
                                <input v-model="form.unit_street" type="text" placeholder="Ej. Paseo de los Cedros" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-sm text-zinc-300 placeholder-zinc-500">
                            </div>

                            <div class="flex items-center p-4 border-b border-zinc-800/60">
                                <label class="w-1/3 text-sm font-medium text-zinc-200">Núm. Exterior</label>
                                <input v-model="form.exterior_number" type="text" placeholder="Ej. 124" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-sm text-zinc-300 placeholder-zinc-500">
                            </div>

                            <div class="flex items-center p-4 border-b border-zinc-800/60">
                                <label class="w-1/3 text-sm font-medium text-zinc-200">Núm. Interior</label>
                                <input v-model="form.int_number" type="text" placeholder="Opcional" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-sm text-zinc-300 placeholder-zinc-500">
                            </div>

                            <div class="flex items-center p-4 border-b border-zinc-800/60">
                                <label class="w-1/3 text-sm font-medium text-zinc-200">Lote Catastral</label>
                                <input v-model="form.lot_number" type="text" placeholder="Ej. MZA-14-LT-2" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-sm text-zinc-300 placeholder-zinc-500" required>
                            </div>

                            <div class="flex items-center p-4">
                                <label class="w-1/3 text-sm font-medium text-zinc-200">Dimensiones (m²)</label>
                                <input v-model="form.square_meters" type="number" step="0.01" placeholder="Ej. 120.50" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-sm text-zinc-300 placeholder-zinc-500">
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider ml-1 mb-3">Control de Acceso al Fraccionamiento</h2>
                        <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                            
                            <div class="flex items-center justify-between p-4 border-b border-zinc-800/60">
                                <div>
                                    <span class="text-sm font-medium text-zinc-200 block">Estado del Sistema</span>
                                    <span class="text-xs text-zinc-500">Activar o desactivar cuenta en la app</span>
                                </div>
                                <select v-model="form.status" class="bg-transparent border-none focus:ring-0 text-right text-sm font-medium text-[#0E63B1] p-0 cursor-pointer">
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>

                            <div class="flex items-center justify-between p-4">
                                <div>
                                    <span class="text-sm font-medium text-red-400 block">Bloquear Acceso en Caseta</span>
                                    <span class="text-xs text-zinc-500">Denegar entrada automática a vehículos/visitas</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="form.access_block" class="sr-only peer">
                                    <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500 border border-zinc-600"></div>
                                </label>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider ml-1 mb-3">Residente Principal / Titular</h2>
                        <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                            
                            <div class="flex items-center p-4 border-b border-zinc-800/60">
                                <label class="w-1/3 text-sm font-medium text-zinc-200">Nombre Completo</label>
                                <input v-model="form.owner_name" type="text" placeholder="Nombre del titular" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-sm text-zinc-300 placeholder-zinc-500">
                            </div>

                            <div class="flex items-center p-4 border-b border-zinc-800/60">
                                <label class="w-1/3 text-sm font-medium text-zinc-200">Correo / App ID</label>
                                <input v-model="form.owner_email" type="email" placeholder="correo@ejemplo.com" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-sm text-zinc-300 placeholder-zinc-500">
                            </div>

                            <div class="flex items-center p-4 border-b border-zinc-800/60">
                                <label class="w-1/3 text-sm font-medium text-zinc-200">Teléfono Móvil</label>
                                <input v-model="form.owner_phone" type="tel" placeholder="(000) 000-0000" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-sm text-zinc-300 placeholder-zinc-500">
                            </div>

                            <div class="flex items-center justify-between p-4">
                                <label class="text-sm font-medium text-zinc-200">Etiqueta / Rol</label>
                                <select v-model="form.owner_role" class="bg-transparent border-none focus:ring-0 text-right text-sm font-medium text-[#0E63B1] p-0 cursor-pointer">
                                    <option value="Dueño">Dueño / Propietario</option>
                                    <option value="Inquilino">Inquilino / Arrendatario</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider ml-1 mb-3">Expediente y Documentación</h2>
                        <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden divide-y divide-zinc-800/60">
                            
                            <div class="p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 flex items-center justify-center"><i class="pi pi-file-pdf"></i></div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-200">Escrituras / Predial</p>
                                        <p class="text-xs text-zinc-500">{{ form.deed_file ? form.deed_file.name : 'Actualizar archivo' }}</p>
                                    </div>
                                </div>
                                <label class="text-sm font-medium text-[#0E63B1] cursor-pointer hover:underline">
                                    <input type="file" class="hidden" accept=".pdf" @change="e => handleFileUpload(e, 'deed_file')">
                                    Subir
                                </label>
                            </div>

                            <div class="p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center"><i class="pi pi-file"></i></div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-200">Contrato de Arrendamiento</p>
                                        <p class="text-xs text-zinc-500">{{ form.lease_file ? form.lease_file.name : 'Actualizar archivo' }}</p>
                                    </div>
                                </div>
                                <label class="text-sm font-medium text-[#0E63B1] cursor-pointer hover:underline">
                                    <input type="file" class="hidden" accept=".pdf" @change="e => handleFileUpload(e, 'lease_file')">
                                    Subir
                                </label>
                            </div>

                            <div class="p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-500/10 text-green-400 flex items-center justify-center"><i class="pi pi-id-card"></i></div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-200">Identificación Oficial (INE)</p>
                                        <p class="text-xs text-zinc-500">{{ form.id_file ? form.id_file.name : 'Actualizar archivo' }}</p>
                                    </div>
                                </div>
                                <label class="text-sm font-medium text-[#0E63B1] cursor-pointer hover:underline">
                                    <input type="file" class="hidden" accept=".pdf,image/*" @change="e => handleFileUpload(e, 'id_file')">
                                    Subir
                                </label>
                            </div>

                        </div>
                    </section>

                    <div class="h-10"></div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
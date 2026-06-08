<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    // Detalles principales
    unit_street: '',
    exterior_number: '',
    int_number: '',
    lot_number: '',
    square_meters: '',
    status: 'Activo',
    access_block: false,
    
    // Propietario Inicial
    owner_name: '',
    owner_email: '',
    owner_phone: '',
    owner_role: 'Dueño', // Dueño o Residente

    // Documentación
    deed_file: null, // Escrituras
    lease_file: null, // Contrato Arrendamiento
    id_file: null, // Identificación

    // Arreglos dinámicos
    access_tags: [''],
    emergency_contacts: [{ name: '', phone: '', relation: '' }]
});

// Funciones para Tags dinámicos
const addTag = () => form.access_tags.push('');
const removeTag = (index) => form.access_tags.splice(index, 1);

// Funciones para Contactos dinámicos
const addContact = () => form.emergency_contacts.push({ name: '', phone: '', relation: '' });
const removeContact = (index) => form.emergency_contacts.splice(index, 1);

// Manejo de Archivos
const handleFileUpload = (e, field) => {
    form[field] = e.target.files[0];
};

const submit = () => {
    // Al incluir archivos, Inertia automáticamente usa multipart/form-data
    form.post(route('admin.private-units.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // El redireccionamiento lo maneja el controlador
        }
    });
};
</script>

<template>
    <AppLayout title="Registrar Propiedad">
        <div class="py-8 px-4 sm:px-6 lg:px-8">
            
            <div class="max-w-3xl mx-auto">
                <!-- Header y Botón Regresar -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <Link :href="route('admin.private-units.index')" class="w-10 h-10 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-400 hover:text-zinc-100 transition border border-zinc-700/50">
                            <i class="pi pi-angle-left text-xl"></i>
                        </Link>
                        <div>
                            <h1 class="text-lg font-medium text-zinc-100 tracking-tight">Nueva Propiedad</h1>
                        </div>
                    </div>
                    <button @click="submit" :disabled="form.processing" class="px-5 py-2.5 bg-[#0E63B1] hover:bg-[#0c5599] text-white font-medium rounded-xl shadow-lg shadow-blue-900/20 transition-all duration-200 text-sm disabled:opacity-50 flex items-center gap-2">
                        <i v-if="form.processing" class="pi pi-spin pi-spinner"></i>
                        Guardar
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- SECCIÓN: DETALLES DE LA PROPIEDAD -->
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

                    <!-- SECCIÓN: CONTROL DE ACCESO -->
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

                    <!-- SECCIÓN: PROPIETARIO / RESIDENTE -->
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
                        <p class="text-xs text-zinc-500 mt-2 ml-1">Se creará un usuario automáticamente y se le enviará invitación si colocas su correo.</p>
                    </section>

                    <!-- SECCIÓN: DOCUMENTACIÓN (PDFs) -->
                    <section>
                        <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider ml-1 mb-3">Expediente y Documentación</h2>
                        <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden divide-y divide-zinc-800/60">
                            
                            <div class="p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 flex items-center justify-center"><i class="pi pi-file-pdf"></i></div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-200">Escrituras / Predial</p>
                                        <p class="text-xs text-zinc-500">{{ form.deed_file ? form.deed_file.name : 'Ningún archivo (PDF)' }}</p>
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
                                        <p class="text-xs text-zinc-500">{{ form.lease_file ? form.lease_file.name : 'Ningún archivo (PDF)' }}</p>
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
                                        <p class="text-xs text-zinc-500">{{ form.id_file ? form.id_file.name : 'Ningún archivo (PDF/IMG)' }}</p>
                                    </div>
                                </div>
                                <label class="text-sm font-medium text-[#0E63B1] cursor-pointer hover:underline">
                                    <input type="file" class="hidden" accept=".pdf,image/*" @change="e => handleFileUpload(e, 'id_file')">
                                    Subir
                                </label>
                            </div>

                        </div>
                    </section>

                    <!-- SECCIÓN: TAGS DE ACCESO -->
                    <section>
                        <div class="flex justify-between items-end ml-1 mb-3">
                            <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Tags / Tarjetas Físicas</h2>
                            <button type="button" @click="addTag" class="text-xs font-medium text-[#0E63B1] hover:text-[#0c5599] transition-colors">Añadir Tag</button>
                        </div>
                        <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                            
                            <div v-if="form.access_tags.length === 0" class="p-6 text-center text-zinc-500 text-sm">
                                No hay tarjetas registradas.
                            </div>

                            <div v-for="(tag, index) in form.access_tags" :key="index" class="flex items-center p-4 border-b border-zinc-800/60 last:border-0">
                                <div class="w-8 flex-shrink-0 text-zinc-500"><i class="pi pi-credit-card"></i></div>
                                <div class="flex-1">
                                    <input v-model="form.access_tags[index]" type="text" placeholder="ID del Dispositivo (Ej. TAG-98210)" class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm text-zinc-200 placeholder-zinc-500">
                                </div>
                                <button type="button" @click="removeTag(index)" class="w-8 h-8 rounded-full bg-red-500/10 text-red-400 flex items-center justify-center hover:bg-red-500/20 transition-colors ml-2">
                                    <i class="pi pi-minus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN: CONTACTOS DE EMERGENCIA -->
                    <section>
                        <div class="flex justify-between items-end ml-1 mb-3">
                            <h2 class="text-xs font-medium text-zinc-400 uppercase tracking-wider">Contactos de Emergencia</h2>
                            <button type="button" @click="addContact" class="text-xs font-medium text-[#0E63B1] hover:text-[#0c5599] transition-colors">Añadir Contacto</button>
                        </div>
                        <div class="bg-zinc-900 border border-zinc-800/60 rounded-xl overflow-hidden">
                            
                            <div v-if="form.emergency_contacts.length === 0" class="p-6 text-center text-zinc-500 text-sm">
                                No hay contactos adicionales.
                            </div>

                            <div v-for="(contact, index) in form.emergency_contacts" :key="'contact-'+index" class="p-4 border-b border-zinc-800/60 last:border-0 flex flex-col md:flex-row md:items-center gap-3">
                                
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <input v-model="contact.name" type="text" placeholder="Nombre completo" class="bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-3 py-2 text-sm transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 w-full">
                                    
                                    <input v-model="contact.relation" type="text" placeholder="Parentesco (Ej. Hijo, Familiar)" class="bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-3 py-2 text-sm transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 w-full">
                                    
                                    <input v-model="contact.phone" type="tel" placeholder="Teléfono" class="bg-zinc-800 border border-zinc-700/40 text-zinc-100 placeholder-zinc-500 rounded-xl px-3 py-2 text-sm transition-all duration-200 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-600 w-full">
                                </div>

                                <div class="flex justify-end mt-2 md:mt-0">
                                    <button type="button" @click="removeContact(index)" class="w-8 h-8 rounded-full bg-red-500/10 text-red-400 flex items-center justify-center hover:bg-red-500/20 transition-colors">
                                        <i class="pi pi-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="h-10"></div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
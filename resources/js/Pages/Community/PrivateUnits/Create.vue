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
        <!-- Fondo iOS #F2F2F7 -->
        <div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-8 px-4 sm:px-6 lg:px-8 font-sans tracking-tight transition-colors duration-300">
            
            <div class="max-w-3xl mx-auto">
                <!-- Header y Botón Regresar -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <Link :href="route('admin.private-units.index')" class="w-10 h-10 rounded-full bg-white dark:bg-[#1C1C1E] flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-sm hover:bg-gray-50 transition border border-black/5 dark:border-white/5">
                            <i class="pi pi-angle-left text-xl"></i>
                        </Link>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Nueva Propiedad</h1>
                        </div>
                    </div>
                    <button @click="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-full font-semibold shadow-md transition disabled:opacity-50 flex items-center gap-2">
                        <i v-if="form.processing" class="pi pi-spin pi-spinner"></i>
                        Guardar
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- SECCIÓN: DETALLES DE LA PROPIEDAD -->
                    <section>
                        <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider ml-4 mb-2">Detalles de la Propiedad</h2>
                        <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] overflow-hidden border border-black/5 dark:border-white/5 shadow-sm">
                            
                            <div class="flex items-center p-4 border-b border-gray-100 dark:border-zinc-800">
                                <label class="w-1/3 text-[15px] font-medium text-gray-900 dark:text-white">Calle / Avenida</label>
                                <input v-model="form.unit_street" type="text" placeholder="Ej. Paseo de los Cedros" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-[15px] text-gray-600 dark:text-gray-300 placeholder-gray-400">
                            </div>

                            <div class="flex items-center p-4 border-b border-gray-100 dark:border-zinc-800">
                                <label class="w-1/3 text-[15px] font-medium text-gray-900 dark:text-white">Núm. Exterior</label>
                                <input v-model="form.exterior_number" type="text" placeholder="Ej. 124" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-[15px] text-gray-600 dark:text-gray-300 placeholder-gray-400">
                            </div>

                            <div class="flex items-center p-4 border-b border-gray-100 dark:border-zinc-800">
                                <label class="w-1/3 text-[15px] font-medium text-gray-900 dark:text-white">Núm. Interior</label>
                                <input v-model="form.int_number" type="text" placeholder="Opcional" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-[15px] text-gray-600 dark:text-gray-300 placeholder-gray-400">
                            </div>

                            <div class="flex items-center p-4 border-b border-gray-100 dark:border-zinc-800">
                                <label class="w-1/3 text-[15px] font-medium text-gray-900 dark:text-white">Lote Catastral</label>
                                <input v-model="form.lot_number" type="text" placeholder="Ej. MZA-14-LT-2" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-[15px] text-gray-600 dark:text-gray-300 placeholder-gray-400" required>
                            </div>

                            <div class="flex items-center p-4">
                                <label class="w-1/3 text-[15px] font-medium text-gray-900 dark:text-white">Dimensiones (m²)</label>
                                <input v-model="form.square_meters" type="number" step="0.01" placeholder="Ej. 120.50" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-[15px] text-gray-600 dark:text-gray-300 placeholder-gray-400">
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN: CONTROL DE ACCESO -->
                    <section>
                        <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider ml-4 mb-2">Control de Acceso al Fraccionamiento</h2>
                        <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] overflow-hidden border border-black/5 dark:border-white/5 shadow-sm">
                            
                            <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-zinc-800">
                                <div>
                                    <span class="text-[15px] font-medium text-gray-900 dark:text-white block">Estado del Sistema</span>
                                    <span class="text-[12px] text-gray-500">Activar o desactivar cuenta en la app</span>
                                </div>
                                <select v-model="form.status" class="bg-transparent border-none focus:ring-0 text-right text-[15px] font-medium text-indigo-600 dark:text-indigo-400 p-0 cursor-pointer">
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>

                            <div class="flex items-center justify-between p-4">
                                <div>
                                    <span class="text-[15px] font-medium text-red-600 dark:text-red-400 block">Bloquear Acceso en Caseta</span>
                                    <span class="text-[12px] text-gray-500">Denegar entrada automática a vehículos/visitas</span>
                                </div>
                                <!-- Toggle switch iOS style -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="form.access_block" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-[#2C2C2E] peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-[#FF453A] border border-gray-300 dark:border-zinc-700"></div>
                                </label>
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN: PROPIETARIO / RESIDENTE -->
                    <section>
                        <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider ml-4 mb-2">Residente Principal / Titular</h2>
                        <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] overflow-hidden border border-black/5 dark:border-white/5 shadow-sm">
                            
                            <div class="flex items-center p-4 border-b border-gray-100 dark:border-zinc-800">
                                <label class="w-1/3 text-[15px] font-medium text-gray-900 dark:text-white">Nombre Completo</label>
                                <input v-model="form.owner_name" type="text" placeholder="Nombre del titular" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-[15px] text-gray-600 dark:text-gray-300 placeholder-gray-400">
                            </div>

                            <div class="flex items-center p-4 border-b border-gray-100 dark:border-zinc-800">
                                <label class="w-1/3 text-[15px] font-medium text-gray-900 dark:text-white">Correo / App ID</label>
                                <input v-model="form.owner_email" type="email" placeholder="correo@ejemplo.com" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-[15px] text-gray-600 dark:text-gray-300 placeholder-gray-400">
                            </div>

                            <div class="flex items-center p-4 border-b border-gray-100 dark:border-zinc-800">
                                <label class="w-1/3 text-[15px] font-medium text-gray-900 dark:text-white">Teléfono Móvil</label>
                                <input v-model="form.owner_phone" type="tel" placeholder="(000) 000-0000" class="w-2/3 text-right bg-transparent border-none focus:ring-0 p-0 text-[15px] text-gray-600 dark:text-gray-300 placeholder-gray-400">
                            </div>

                            <div class="flex items-center justify-between p-4">
                                <label class="text-[15px] font-medium text-gray-900 dark:text-white">Etiqueta / Rol</label>
                                <select v-model="form.owner_role" class="bg-transparent border-none focus:ring-0 text-right text-[15px] font-medium text-indigo-600 dark:text-indigo-400 p-0 cursor-pointer">
                                    <option value="Dueño">Dueño / Propietario</option>
                                    <option value="Inquilino">Inquilino / Arrendatario</option>
                                </select>
                            </div>
                        </div>
                        <p class="text-[12px] text-gray-500 mt-2 ml-4">Se creará un usuario automáticamente y se le enviará invitación si colocas su correo.</p>
                    </section>

                    <!-- SECCIÓN: DOCUMENTACIÓN (PDFs) -->
                    <section>
                        <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider ml-4 mb-2">Expediente y Documentación</h2>
                        <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] overflow-hidden border border-black/5 dark:border-white/5 shadow-sm divide-y divide-gray-100 dark:divide-zinc-800">
                            
                            <!-- Escrituras -->
                            <div class="p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center"><i class="pi pi-file-pdf"></i></div>
                                    <div>
                                        <p class="text-[15px] font-medium text-gray-900 dark:text-white">Escrituras / Predial</p>
                                        <p class="text-[12px] text-gray-500">{{ form.deed_file ? form.deed_file.name : 'Ningún archivo (PDF)' }}</p>
                                    </div>
                                </div>
                                <label class="text-[14px] font-medium text-indigo-600 cursor-pointer hover:underline">
                                    <input type="file" class="hidden" accept=".pdf" @change="e => handleFileUpload(e, 'deed_file')">
                                    Subir
                                </label>
                            </div>

                            <!-- Contrato -->
                            <div class="p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center"><i class="pi pi-file"></i></div>
                                    <div>
                                        <p class="text-[15px] font-medium text-gray-900 dark:text-white">Contrato de Arrendamiento</p>
                                        <p class="text-[12px] text-gray-500">{{ form.lease_file ? form.lease_file.name : 'Ningún archivo (PDF)' }}</p>
                                    </div>
                                </div>
                                <label class="text-[14px] font-medium text-indigo-600 cursor-pointer hover:underline">
                                    <input type="file" class="hidden" accept=".pdf" @change="e => handleFileUpload(e, 'lease_file')">
                                    Subir
                                </label>
                            </div>

                            <!-- INE -->
                            <div class="p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-500 flex items-center justify-center"><i class="pi pi-id-card"></i></div>
                                    <div>
                                        <p class="text-[15px] font-medium text-gray-900 dark:text-white">Identificación Oficial (INE)</p>
                                        <p class="text-[12px] text-gray-500">{{ form.id_file ? form.id_file.name : 'Ningún archivo (PDF/IMG)' }}</p>
                                    </div>
                                </div>
                                <label class="text-[14px] font-medium text-indigo-600 cursor-pointer hover:underline">
                                    <input type="file" class="hidden" accept=".pdf,image/*" @change="e => handleFileUpload(e, 'id_file')">
                                    Subir
                                </label>
                            </div>

                        </div>
                    </section>

                    <!-- SECCIÓN: TAGS DE ACCESO -->
                    <section>
                        <div class="flex justify-between items-end ml-4 mb-2 pr-4">
                            <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider">Tags / Tarjetas Físicas</h2>
                            <button type="button" @click="addTag" class="text-[13px] font-semibold text-indigo-600 hover:text-indigo-800">Añadir Tag</button>
                        </div>
                        <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] overflow-hidden border border-black/5 dark:border-white/5 shadow-sm">
                            
                            <div v-if="form.access_tags.length === 0" class="p-6 text-center text-gray-400 text-sm">
                                No hay tarjetas registradas.
                            </div>

                            <div v-for="(tag, index) in form.access_tags" :key="index" class="flex items-center p-4 border-b border-gray-100 dark:border-zinc-800 last:border-0">
                                <div class="w-8 flex-shrink-0 text-gray-400"><i class="pi pi-credit-card"></i></div>
                                <div class="flex-1">
                                    <input v-model="form.access_tags[index]" type="text" placeholder="ID del Dispositivo (Ej. TAG-98210)" class="w-full bg-transparent border-none focus:ring-0 p-0 text-[15px] text-gray-900 dark:text-white placeholder-gray-400">
                                </div>
                                <button type="button" @click="removeTag(index)" class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors ml-2">
                                    <i class="pi pi-minus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- SECCIÓN: CONTACTOS DE EMERGENCIA -->
                    <section>
                        <div class="flex justify-between items-end ml-4 mb-2 pr-4">
                            <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider">Contactos de Emergencia</h2>
                            <button type="button" @click="addContact" class="text-[13px] font-semibold text-indigo-600 hover:text-indigo-800">Añadir Contacto</button>
                        </div>
                        <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] overflow-hidden border border-black/5 dark:border-white/5 shadow-sm">
                            
                            <div v-if="form.emergency_contacts.length === 0" class="p-6 text-center text-gray-400 text-sm">
                                No hay contactos adicionales.
                            </div>

                            <div v-for="(contact, index) in form.emergency_contacts" :key="'contact-'+index" class="p-4 border-b border-gray-100 dark:border-zinc-800 last:border-0 flex flex-col md:flex-row md:items-center gap-3">
                                
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <input v-model="contact.name" type="text" placeholder="Nombre completo" class="bg-gray-50 dark:bg-[#2C2C2E] border border-gray-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-[14px] text-gray-900 dark:text-white placeholder-gray-400 px-3 py-2 w-full">
                                    
                                    <input v-model="contact.relation" type="text" placeholder="Parentesco (Ej. Hijo, Familiar)" class="bg-gray-50 dark:bg-[#2C2C2E] border border-gray-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-[14px] text-gray-900 dark:text-white placeholder-gray-400 px-3 py-2 w-full">
                                    
                                    <input v-model="contact.phone" type="tel" placeholder="Teléfono" class="bg-gray-50 dark:bg-[#2C2C2E] border border-gray-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-[14px] text-gray-900 dark:text-white placeholder-gray-400 px-3 py-2 w-full">
                                </div>

                                <div class="flex justify-end mt-2 md:mt-0">
                                    <button type="button" @click="removeContact(index)" class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition-colors">
                                        <i class="pi pi-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Espacio final para asegurar scroll -->
                    <div class="h-10"></div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
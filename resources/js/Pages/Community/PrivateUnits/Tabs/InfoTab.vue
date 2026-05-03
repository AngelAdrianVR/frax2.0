<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    unit: Object
});

const toast = useToast();
const showContactModal = ref(false);

const contactForm = useForm({
    name: '',
    relation: '',
    phone: '',
    status: 'Activo' // <- ¡Agrega esta línea!
});

const submitContact = () => {
    // Petición real al backend
    contactForm.post(route('admin.private-units.contacts.store', props.unit.id), { 
        preserveScroll: true,
        onSuccess: () => {
            showContactModal.value = false;
            contactForm.reset();
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Contacto de emergencia añadido correctamente.', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Verifica los campos del formulario.', life: 3000 });
        }
    });
};
</script>

<template>
    <div class="space-y-6 animate-fade-in">
        <!-- Documentación -->
        <section>
            <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider ml-4 mb-2">Documentación del Expediente</h2>
            <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] border border-black/5 dark:border-white/5 shadow-sm divide-y divide-gray-100 dark:divide-zinc-800">
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center"><i class="pi pi-file-pdf text-xl"></i></div>
                        <div><p class="text-[15px] font-semibold text-gray-900 dark:text-white">Escrituras / Predial</p><p class="text-[12px] text-gray-500">Documento oficial</p></div>
                    </div>
                    <button class="text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 px-4 py-1.5 rounded-full text-xs font-bold hover:bg-indigo-100 transition">Ver PDF</button>
                </div>
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center"><i class="pi pi-file text-xl"></i></div>
                        <div><p class="text-[15px] font-semibold text-gray-900 dark:text-white">Contrato de Arrendamiento</p><p class="text-[12px] text-gray-500">No hay documento</p></div>
                    </div>
                    <label class="text-indigo-600 font-bold text-xs cursor-pointer hover:underline">Subir</label>
                </div>
            </div>
        </section>

        <!-- Contactos de Emergencia -->
        <section>
            <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider ml-4 mb-2">Contactos de Emergencia Secundarios</h2>
            <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] border border-black/5 dark:border-white/5 shadow-sm">
                
                <!-- Iteramos sobre los contactos reales que vengan del backend (Asegúrate de que unit.emergency_contacts exista en tu controlador) -->
                <div v-for="contact in unit.emergency_contacts" :key="contact.id" class="p-4 border-b border-gray-100 dark:border-zinc-800 flex justify-between items-center last:border-0">
                    <div>
                        <p class="text-[15px] font-semibold text-gray-900 dark:text-white">{{ contact.name }}</p>
                        <p class="text-[12px] text-gray-500">{{ contact.relation }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a :href="`https://wa.me/52${contact.phone.replace(/\\D/g,'')}`" target="_blank" class="text-green-600 bg-green-50 hover:bg-green-100 dark:bg-[#2C2C2E] w-8 h-8 rounded-full flex items-center justify-center transition" title="Enviar WhatsApp"><i class="pi pi-whatsapp"></i></a>
                        <a :href="`tel:${contact.phone}`" class="text-indigo-600 bg-gray-50 hover:bg-gray-100 dark:bg-[#2C2C2E] w-8 h-8 rounded-full flex items-center justify-center transition" title="Llamar"><i class="pi pi-phone"></i></a>
                    </div>
                </div>

                <div v-if="!unit.emergency_contacts || unit.emergency_contacts.length === 0" class="p-6 text-center">
                    <p class="text-sm text-gray-400 mb-2">No hay contactos secundarios registrados.</p>
                </div>
                
                <div class="p-4 text-center border-t border-gray-100 dark:border-zinc-800">
                    <button @click="showContactModal = true" class="text-indigo-600 text-sm font-semibold hover:underline">+ Añadir contacto secundario</button>
                </div>
            </div>
        </section>

        <!-- Modal Contacto -->
        <div v-if="showContactModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#1C1C1E] rounded-3xl p-6 w-full max-w-sm shadow-2xl">
                <h3 class="text-lg font-bold mb-4 dark:text-white">Nuevo Contacto</h3>
                <form @submit.prevent="submitContact" class="space-y-4">
                    <input v-model="contactForm.name" type="text" placeholder="Nombre completo" class="w-full rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white" required>
                    <input v-model="contactForm.relation" type="text" placeholder="Parentesco (Ej. Hijo, Esposa)" class="w-full rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white" required>
                    <input v-model="contactForm.phone" type="tel" placeholder="Teléfono" class="w-full rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white" required>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="showContactModal = false" class="px-4 py-2 text-gray-500 text-sm font-bold">Cancelar</button>
                        <button type="submit" :disabled="contactForm.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-bold disabled:opacity-50">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
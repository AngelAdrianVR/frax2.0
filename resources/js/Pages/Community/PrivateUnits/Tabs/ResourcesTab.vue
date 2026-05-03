<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";

const props = defineProps({
    unit: Object
});

const toast = useToast();
const confirm = useConfirm();

const showVehicleModal = ref(false);
const showTagModal = ref(false);
const showPetModal = ref(false);

const vehicleForm = useForm({ plate: '', brand: '', model: '', color: '', tag_access: '' });
const tagForm = useForm({ tag_code: '' });
const petForm = useForm({ name: '', species: 'Perro', race: '' });

const submitVehicle = () => {
    vehicleForm.post(route('admin.private-units.vehicles.store', props.unit.id), { 
        preserveScroll: true,
        onSuccess: () => {
            showVehicleModal.value = false;
            vehicleForm.reset();
            toast.add({ severity: 'success', summary: 'Vehículo guardado', detail: 'El auto se registró correctamente.', life: 3000 });
        }
    });
};

const submitTag = () => {
    tagForm.post(route('admin.private-units.tags.store', props.unit.id), { 
        preserveScroll: true,
        onSuccess: () => {
            showTagModal.value = false;
            tagForm.reset();
            toast.add({ severity: 'success', summary: 'Tag asignado', detail: 'Dispositivo vinculado a la propiedad.', life: 3000 });
        }
    });
};

const submitPet = () => {
    petForm.post(route('admin.private-units.pets.store', props.unit.id), { 
        preserveScroll: true,
        onSuccess: () => {
            showPetModal.value = false;
            petForm.reset();
            toast.add({ severity: 'success', summary: 'Mascota registrada', detail: 'La mascota se guardó con éxito.', life: 3000 });
        }
    });
};

// Funciones de Eliminación
const deleteResource = (routeUrl, type) => {
    confirm.require({
        message: `¿Estás seguro de que deseas eliminar este ${type}?`,
        header: `Eliminar ${type}`,
        icon: 'pi pi-trash',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(routeUrl, {
                preserveScroll: true,
                onSuccess: () => toast.add({ severity: 'success', summary: 'Eliminado', detail: `El ${type} fue eliminado.`, life: 3000 })
            });
        }
    });
};
</script>

<template>
    <div class="space-y-8 animate-fade-in">
        
        <!-- Vehículos -->
        <section>
            <div class="flex justify-between items-end ml-4 mb-2 pr-4">
                <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider">Vehículos Registrados</h2>
                <button @click="showVehicleModal = true" class="text-[13px] font-semibold text-indigo-600">+ Añadir Auto</button>
            </div>
            <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] border border-black/5 dark:border-white/5 shadow-sm divide-y divide-gray-100 dark:divide-zinc-800">
                <div v-if="!unit.vehicles || unit.vehicles.length === 0" class="p-6 text-center text-sm text-gray-400">Sin vehículos registrados.</div>
                
                <div v-for="car in unit.vehicles" :key="car.id" class="p-4 flex items-center justify-between group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-[#2C2C2E] flex items-center justify-center text-gray-500"><i class="pi pi-car"></i></div>
                        <div>
                            <p class="text-[15px] font-semibold text-gray-900 dark:text-white">{{ car.brand }} {{ car.model }} <span class="text-gray-400 font-normal">| {{ car.color }}</span></p>
                            <div class="flex gap-2 mt-1">
                                <span class="text-[11px] font-mono font-bold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-[#2C2C2E] px-2 py-0.5 rounded">{{ car.plate }}</span>
                                <span v-if="car.tag_access" class="text-[11px] text-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 px-2 py-0.5 rounded">TAG: {{ car.tag_access }}</span>
                            </div>
                        </div>
                    </div>
                    <button @click="deleteResource(route('admin.private-units.vehicles.destroy', car.id), 'vehículo')" class="text-red-400 hover:text-red-600 p-2 opacity-0 group-hover:opacity-100 transition"><i class="pi pi-trash"></i></button>
                </div>
            </div>
        </section>

        <!-- Tags -->
        <section>
            <div class="flex justify-between items-end ml-4 mb-2 pr-4">
                <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider">Dispositivos / Tags de Acceso</h2>
                <button @click="showTagModal = true" class="text-[13px] font-semibold text-indigo-600">+ Añadir Tag</button>
            </div>
            <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] border border-black/5 dark:border-white/5 shadow-sm divide-y divide-gray-100 dark:divide-zinc-800">
                <div v-if="!unit.tags || unit.tags.length === 0" class="p-6 text-center text-sm text-gray-400">No hay tags activos.</div>
                
                <div v-for="tag in unit.tags" :key="tag.id" class="p-4 flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <i class="pi pi-credit-card text-indigo-500"></i>
                        <p class="text-[14px] font-mono text-gray-900 dark:text-white">{{ tag.tag_code }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded">Activo</span>
                        <button @click="deleteResource(route('admin.private-units.tags.destroy', tag.id), 'tag')" class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition"><i class="pi pi-trash"></i></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mascotas -->
        <section>
            <div class="flex justify-between items-end ml-4 mb-2 pr-4">
                <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider">Mascotas</h2>
                <button @click="showPetModal = true" class="text-[13px] font-semibold text-indigo-600">+ Registrar Mascota</button>
            </div>
            <div class="bg-white dark:bg-[#1C1C1E] rounded-[20px] border border-black/5 dark:border-white/5 shadow-sm divide-y divide-gray-100 dark:divide-zinc-800">
                <div v-if="!unit.pets || unit.pets.length === 0" class="p-6 text-center text-sm text-gray-400">Sin mascotas registradas.</div>
                
                <div v-for="pet in unit.pets" :key="pet.id" class="p-4 flex items-center justify-between group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-pink-50 dark:bg-pink-900/20 text-pink-500 flex items-center justify-center"><i class="fa-solid fa-paw"></i></div>
                        <div>
                            <p class="text-[15px] font-semibold text-gray-900 dark:text-white">{{ pet.name }}</p>
                            <p class="text-[12px] text-gray-500">{{ pet.species }} - {{ pet.race }}</p>
                        </div>
                    </div>
                    <button @click="deleteResource(route('admin.private-units.pets.destroy', pet.id), 'mascota')" class="text-red-400 hover:text-red-600 p-2 opacity-0 group-hover:opacity-100 transition"><i class="pi pi-trash"></i></button>
                </div>
            </div>
        </section>

        <!-- MODALES -->
        <!-- Vehículo -->
        <div v-if="showVehicleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#1C1C1E] rounded-3xl p-6 w-full max-w-md shadow-2xl">
                <h3 class="text-lg font-bold mb-4 dark:text-white">Registrar Vehículo</h3>
                <form @submit.prevent="submitVehicle" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <input v-model="vehicleForm.plate" type="text" placeholder="Placas" class="col-span-2 rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white" required>
                        <input v-model="vehicleForm.brand" type="text" placeholder="Marca (Ej. Toyota)" class="rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white" required>
                        <input v-model="vehicleForm.model" type="text" placeholder="Modelo (Ej. Corolla)" class="rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white" required>
                        <input v-model="vehicleForm.color" type="text" placeholder="Color" class="rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white" required>
                        <input v-model="vehicleForm.tag_access" type="text" placeholder="Tag (Opcional)" class="rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white">
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="showVehicleModal = false" class="px-4 py-2 text-gray-500 text-sm font-bold">Cancelar</button>
                        <button type="submit" :disabled="vehicleForm.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-bold disabled:opacity-50">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tag -->
        <div v-if="showTagModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#1C1C1E] rounded-3xl p-6 w-full max-w-sm shadow-2xl">
                <h3 class="text-lg font-bold mb-4 dark:text-white">Añadir Tag de Acceso</h3>
                <form @submit.prevent="submitTag" class="space-y-4">
                    <input v-model="tagForm.tag_code" type="text" placeholder="Código del Tag (Ej. TAG-123)" class="w-full rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white" required>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="showTagModal = false" class="px-4 py-2 text-gray-500 text-sm font-bold">Cancelar</button>
                        <button type="submit" :disabled="tagForm.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-bold disabled:opacity-50">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mascota -->
        <div v-if="showPetModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#1C1C1E] rounded-3xl p-6 w-full max-w-sm shadow-2xl">
                <h3 class="text-lg font-bold mb-4 dark:text-white">Registrar Mascota</h3>
                <form @submit.prevent="submitPet" class="space-y-4">
                    <input v-model="petForm.name" type="text" placeholder="Nombre de la mascota" class="w-full rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white" required>
                    <select v-model="petForm.species" class="w-full rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white">
                        <option value="Perro">Perro</option>
                        <option value="Gato">Gato</option>
                        <option value="Otro">Otro</option>
                    </select>
                    <input v-model="petForm.race" type="text" placeholder="Raza" class="w-full rounded-xl bg-gray-50 dark:bg-[#2C2C2E] border-none focus:ring-indigo-500 text-sm dark:text-white">
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="showPetModal = false" class="px-4 py-2 text-gray-500 text-sm font-bold">Cancelar</button>
                        <button type="submit" :disabled="petForm.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-bold disabled:opacity-50">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
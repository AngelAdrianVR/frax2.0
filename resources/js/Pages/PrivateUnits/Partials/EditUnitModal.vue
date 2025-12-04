<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div @click="$emit('close')" class="absolute inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm"></div>
        
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
            
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Editar Unidad</h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
                    <i class="pi pi-times text-lg"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Calle / Avenida</label>
                        <input v-model="form.unit_street" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Núm. Exterior</label>
                        <input v-model="form.exterior_number" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Núm. Interior</label>
                        <input v-model="form.int_number" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lote</label>
                        <input v-model="form.lot_number" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">M2</label>
                        <input v-model="form.square_meters" type="number" step="0.01" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:ring-indigo-500">
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-2">
                    <div class="flex items-center justify-between bg-red-50 dark:bg-red-900/10 p-3 rounded-lg border border-red-200 dark:border-red-800 mt-3">
                        <span class="text-sm font-medium text-red-800 dark:text-red-300">Bloquear Acceso</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="form.access_block" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                <button @click="$emit('close')" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 text-sm">Cancelar</button>
                <button @click="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md text-sm font-medium transition-colors flex items-center gap-2" :disabled="processing">
                    <i v-if="processing" class="pi pi-spin pi-spinner"></i>
                    {{ processing ? 'Guardando...' : 'Guardar Cambios' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';

export default {
    props: ['unit'],
    data() {
        return {
            processing: false,
            form: { ...this.unit }
        }
    },
    methods: {
        submit() {
            this.processing = true;
            router.put(route('admin.private-units.update', this.form.id), this.form, {
                onSuccess: () => {
                    this.processing = false;
                    this.$emit('close');
                },
                onError: () => this.processing = false
            });
        }
    }
}
</script>
<script setup>
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    unit: Object
});

const toast = useToast();

const chargeForm = useForm({ concept: '', amount: '', notes: '' });
const balanceForm = useForm({ amount: '', reference: '' });

const submitManualCharge = () => {
    chargeForm.post(route('admin.fees.storeManual', props.unit.id), { 
        preserveScroll: true,
        onSuccess: () => {
            chargeForm.reset();
            toast.add({ severity: 'error', summary: 'Cargo Aplicado', detail: 'El cargo o multa se asignó correctamente al estado de cuenta.', life: 4000 });
        },
        onError: () => {
            toast.add({ severity: 'warn', summary: 'Error', detail: 'Verifica los datos del cargo.', life: 3000 });
        }
    });
};

const submitBalance = () => {
    balanceForm.post(route('admin.fees.addBalance', props.unit.id), { 
        preserveScroll: true,
        onSuccess: () => {
            balanceForm.reset();
            toast.add({ severity: 'success', summary: 'Abono Exitoso', detail: 'El saldo a favor fue registrado en la cuenta de la propiedad.', life: 4000 });
        }
    });
};
</script>

<template>
    <div class="space-y-6 animate-fade-in">
        
        <!-- Saldo a Favor Widget -->
        <section>
            <div class="bg-gradient-to-br from-green-500 to-emerald-700 rounded-[24px] p-6 text-white shadow-md relative overflow-hidden">
                <i class="pi pi-wallet absolute -right-4 -bottom-4 text-[100px] text-white/10"></i>
                <p class="text-sm font-medium text-white/80 uppercase tracking-wider mb-1">Saldo a Favor</p>
                <h3 class="text-4xl font-extrabold tracking-tight mb-4">${{ unit.credit_balance || '0.00' }}</h3>
                
                <form @submit.prevent="submitBalance" class="bg-white/10 p-4 rounded-2xl backdrop-blur-sm border border-white/20 mt-4 space-y-3 relative z-10">
                    <p class="text-xs font-semibold mb-2">Registrar Pago Adelantado / Saldo</p>
                    <div class="flex gap-2">
                        <input v-model="balanceForm.amount" type="number" step="0.01" placeholder="Monto ($)" class="w-1/3 bg-white/20 border-none text-white placeholder-white/50 rounded-xl text-sm focus:ring-white" required>
                        <input v-model="balanceForm.reference" type="text" placeholder="Ref/Notas" class="flex-1 bg-white/20 border-none text-white placeholder-white/50 rounded-xl text-sm focus:ring-white" required>
                        <button type="submit" :disabled="balanceForm.processing" class="bg-white text-green-700 px-4 font-bold rounded-xl text-sm hover:bg-gray-100 transition disabled:opacity-50">Abonar</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Generador de Cargos Manuales -->
        <section>
            <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider ml-4 mb-2">Generador de Cargos y Multas</h2>
            <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] p-6 border border-black/5 dark:border-white/5 shadow-sm">
                <form @submit.prevent="submitManualCharge" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Concepto del Cargo</label>
                            <input v-model="chargeForm.concept" type="text" placeholder="Ej. Multa por ruido" class="w-full bg-gray-50 dark:bg-[#2C2C2E] border-none rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Monto ($)</label>
                            <input v-model="chargeForm.amount" type="number" step="0.01" placeholder="Ej. 500.00" class="w-full bg-gray-50 dark:bg-[#2C2C2E] border-none rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Notas Internas (Auditoría)</label>
                        <input v-model="chargeForm.notes" type="text" placeholder="Razón o folio de evidencia" class="w-full bg-gray-50 dark:bg-[#2C2C2E] border-none rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500">
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit" :disabled="chargeForm.processing" class="bg-red-500 hover:bg-red-600 text-white font-bold px-6 py-2 rounded-full text-sm shadow-md transition-all flex items-center gap-2 disabled:opacity-50">
                            <i class="pi pi-bolt"></i> Cargar a la Cuenta
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Historial de Movimientos -->
        <section>
            <div class="flex justify-between items-end ml-4 mb-2 pr-4">
                <h2 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider">Historial de Movimientos</h2>
                <span class="text-[11px] text-gray-400">Últimos 10 registros</span>
            </div>
            <div class="bg-white dark:bg-[#1C1C1E] rounded-[24px] border border-black/5 dark:border-white/5 shadow-sm overflow-hidden">
                <div v-if="!unit.generated_fees || unit.generated_fees.length === 0" class="p-6 text-center text-sm text-gray-400">
                    No hay registros financieros recientes.
                </div>
                
                <div v-for="fee in unit.generated_fees" :key="fee.id" class="p-4 border-b border-gray-100 dark:border-zinc-800 last:border-0 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-[#2C2C2E]/50 transition-colors">
                    <div>
                        <p class="text-[14px] font-bold text-gray-900 dark:text-white">{{ fee.payment_reference || fee.concept || 'Cargo Registrado' }}</p>
                        <p class="text-[12px] text-gray-500">Vence: {{ fee.expiration_date }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[15px] font-bold text-gray-900 dark:text-white">${{ fee.total_amount }}</p>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider mt-1 inline-block"
                            :class="{
                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': fee.status === 'Pagado',
                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': fee.status === 'Atrasada',
                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': fee.status === 'Pendiente' || fee.status === 'Parcial'
                            }">
                            {{ fee.status }}
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
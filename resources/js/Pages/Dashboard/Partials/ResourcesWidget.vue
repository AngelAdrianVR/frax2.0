<script setup>
const props = defineProps({ data: Object });

const resources = [
    { label: 'Vehículos', value: props.data.vehicles, color: 'bg-blue-500', bg: 'bg-blue-50 dark:bg-blue-500/10', text: 'text-blue-600 dark:text-blue-400', icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />` },
    { label: 'Mascotas', value: props.data.pets, color: 'bg-orange-500', bg: 'bg-orange-50 dark:bg-orange-500/10', text: 'text-orange-600 dark:text-orange-400', icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />` },
    { label: 'Tags de Acceso', value: props.data.tags, color: 'bg-emerald-500', bg: 'bg-emerald-50 dark:bg-emerald-500/10', text: 'text-emerald-600 dark:text-emerald-400', icon: `<path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />` },
];
</script>

<template>
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-5 border border-black/[0.03] dark:border-white/[0.04] shadow-[0_0_0_0.5px_rgba(0,0,0,0.04),0_1px_4px_rgba(0,0,0,0.02)] h-full">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-[13px] font-semibold text-zinc-800 dark:text-zinc-100">Recursos Registrados</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Inventario de la comunidad</p>
            </div>
        </div>

        <div class="space-y-2">
            <div v-for="(item, idx) in resources" :key="idx" class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors group">
                <div class="flex items-center gap-3">
                    <div :class="['w-8 h-8 rounded-[10px] flex items-center justify-center transition-transform group-hover:scale-105', item.bg]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" :class="['w-4 h-4', item.text]" v-html="item.icon"></svg>
                    </div>
                    <span class="text-[13px] font-medium text-zinc-700 dark:text-zinc-300">{{ item.label }}</span>
                </div>
                <span class="text-[15px] font-bold text-zinc-900 dark:text-white tabular-nums">{{ item.value }}</span>
            </div>
        </div>
    </div>
</template>
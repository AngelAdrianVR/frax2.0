<script>
export default {
    name: 'ServicesSection'
}
</script>

<template>
    <div id="servicios" class="bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:text-center mb-16">
                <h2 class="text-base font-semibold leading-7 text-blue-600">Software Integral</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                    Gestión administrativa simplificada
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="group p-8 rounded-3xl bg-gray-50 hover:bg-blue-600 transition-colors duration-300 cursor-pointer">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 group-hover:bg-white/20 flex items-center justify-center mb-6 transition-colors">
                        <i class="pi pi-wallet text-2xl text-blue-600 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-white mb-3">Finanzas Claras</h3>
                    <p class="text-gray-500 group-hover:text-blue-100 leading-relaxed">
                        Automatiza cobros y genera reportes transparentes para todos los residentes.
                    </p>
                </div>

                <div class="group p-8 rounded-3xl bg-gray-50 hover:bg-blue-600 transition-colors duration-300 cursor-pointer">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 group-hover:bg-white/20 flex items-center justify-center mb-6 transition-colors">
                        <i class="pi pi-shield text-2xl text-blue-600 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-white mb-3">Seguridad Total</h3>
                    <p class="text-gray-500 group-hover:text-blue-100 leading-relaxed">
                        Control de accesos QR y registro de visitas en tiempo real desde la app.
                    </p>
                </div>

                <div class="group p-8 rounded-3xl bg-gray-50 hover:bg-blue-600 transition-colors duration-300 cursor-pointer">
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 group-hover:bg-white/20 flex items-center justify-center mb-6 transition-colors">
                        <i class="pi pi-mobile text-2xl text-blue-600 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-white mb-3">App Móvil</h3>
                    <p class="text-gray-500 group-hover:text-blue-100 leading-relaxed">
                        Reservas, comunicados y votaciones al alcance de la mano de cada vecino.
                    </p>
                </div>
            </div>
            </div>
    </div>
</template>
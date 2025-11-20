import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Importa PrimeVue y su configuración
import PrimeVue from 'primevue/config';
import Accordion from 'primevue/accordion';
// import AccordionTab from 'primevue/accordiontab';
import ToastService from 'primevue/toastservice';
// import Chart from 'primevue/chart';
import 'primeicons/primeicons.css';
import Aura from '@primeuix/themes/aura'; // Se importa el preset del tema Aura

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            // Configuración de PrimeVue con el tema Aura y modo oscuro
            .use(PrimeVue, {
                ripple: true,
                theme: {
                    preset: Aura,
                    options: {
                        // Conecta el modo oscuro de PrimeVue con el de Tailwind CSS
                        darkModeSelector: '.dark',
                    }
                }
            })
            .component('Accordion', Accordion)
            // .component('AccordionTab', AccordionTab)
            // .component('Chart', Chart)
            .use(ToastService)
            .mount(el);
    },
    progress: {
        color: '#0f7bc1',
    },
});

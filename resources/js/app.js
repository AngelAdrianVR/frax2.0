import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Importaciones de PrimeVue
import PrimeVue from 'primevue/config';
import Accordion from 'primevue/accordion';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import 'primeicons/primeicons.css';
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';

// Configuración de Tema (Aura + Zinc)
import Aura from '@primeuix/themes/aura'; 
import { definePreset } from '@primeuix/themes';

const MyPreset = definePreset(Aura, {
    semantic: {
        // Mapeo de la paleta primaria global a Zinc
        primary: {
            50: '{zinc.50}',
            100: '{zinc.100}',
            200: '{zinc.200}',
            300: '{zinc.300}',
            400: '{zinc.400}',
            500: '{zinc.500}',
            600: '{zinc.600}',
            700: '{zinc.700}',
            800: '{zinc.800}',
            900: '{zinc.900}',
            950: '{zinc.950}'
        },
        // Separación explícita de esquemas de color para Inputs (InputText, Select, DatePicker, etc.)
        colorScheme: {
            light: {
                formField: {
                    background: '{zinc.50}',         // Fondo claro en Light Mode
                    borderColor: '{zinc.300}',        // Borde gris suave
                    hoverBorderColor: '{zinc.400}',   // Borde al pasar el mouse
                    focusBorderColor: '{zinc.500}',   // Borde al hacer foco
                    color: '{zinc.900}'               // Color del texto escrito
                }
            },
            dark: {
                formField: {
                    background: '{zinc.800}',         // Fondo Zinc 800 solicitado para modo oscuro
                    borderColor: '{zinc.700}',        // Borde sutil oscuro que combina con border-zinc-800/60
                    hoverBorderColor: '{zinc.500}',   // Borde intermedio al pasar el mouse
                    focusBorderColor: '{zinc.400}',   // Borde claro al enfocar (Estilo minimalista Tesla)
                    color: '{zinc.100}'               // Color del texto escrito en modo oscuro
                }
            }
        }
    }
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            // Se registran los servicios globales de PrimeVue
            .use(ToastService)
            .use(ConfirmationService)
            // Se configura PrimeVue con el tema personalizado
            .use(PrimeVue, {
                ripple: true,
                theme: {
                    preset: MyPreset,
                    options: {
                        darkModeSelector: '.dark',
                    }
                }
            })
            // Registro de componentes globales
            .component('Accordion', Accordion)
            .component('DatePicker', DatePicker)
            .component('Button', Button)
            .component('InputText', InputText)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
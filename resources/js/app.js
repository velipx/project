import '../css/app.css';
import './bootstrap';

import { createInertiaApp, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, onMounted } from 'vue';
import { renderApp } from '@inertiaui/modal-vue';
import { Modal, ModalLink } from '@inertiaui/modal-vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia'
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia()

// Dark mode
import { useDarkModeStore } from './darkMode'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: renderApp(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component("Modal", Modal)
            .component("ModalLink", ModalLink)
            .component("Link", Link)
            .use(pinia)
            .use(Toast, {
                position: POSITION.BOTTOM_RIGHT,
                hideProgressBar: true,
                timeout: 5000,
            })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
        showSpinner: true,
    },
});

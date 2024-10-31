import '../css/app.css';
import './bootstrap';

import { createInertiaApp, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia'
import { modal } from '../../vendor/emargareten/inertia-modal'
import moment from 'moment'; // Uvezi moment za formatiranje datuma
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia()
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';

// Dark mode
// Uncomment, if you'd like to restore persisted darkMode setting, or use `prefers-color-scheme: dark`. Make sure to uncomment localStorage block in src/stores/darkMode.js
import { useDarkModeStore } from './darkMode'

const darkModeStore = useDarkModeStore(pinia)

darkModeStore.set(true)


createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        return app
            .use(modal, {
                resolve: (name) =>
                    resolvePageComponent(
                        `./Pages/${name}.vue`,
                        import.meta.glob('./Pages/**/*.vue'),
                    ),
            })
            .use(plugin)
            .component("Link", Link)
            .use(ZiggyVue)
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

import './bootstrap';
import '../css/app.css';
import '../css/bm.css';
import '../js/bm.js';
import "vue-toastification/dist/index.css";

import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast, { POSITION } from "vue-toastification";

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

const appName = import.meta.env.VITE_APP_NAME || 'BaroqueMusic.ru';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(Toast, { position: POSITION.BOTTOM_RIGHT })
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

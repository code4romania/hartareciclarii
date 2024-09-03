import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '@/Helpers/useRoute';
import { i18nVue } from 'laravel-vue-i18n';
import VueClickAway from 'vue3-click-away';
import PrimeVue from 'primevue/config';
import pt from './primevue.js';

import 'virtual:svg-icons-register';

import.meta.glob(['../images/**']);

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(PrimeVue, {
                unstyled: true,
                pt,
            })
            .use(ZiggyVue)
            .use(VueClickAway)
            .use(plugin);

        return app.use(i18nVue, {
            resolve: async (lang) => {
                const langs = import.meta.glob('../../lang/*.json');
                return await langs[`../../lang/${lang}.json`]();
            },
            onLoad: () => {
                app.mount(el);
            },
        });
    },
});

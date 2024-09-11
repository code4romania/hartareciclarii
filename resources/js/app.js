import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '@/Helpers/useRoute';
import { i18nVue } from 'laravel-vue-i18n';
import VueClickAway from 'vue3-click-away';
import PrimeVue from 'primevue/config';
import pt from './primevue.js';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';

import 'virtual:svg-icons-register';

import.meta.glob(['../images/**']);

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        let page = pages[`./Pages/${name}.vue`];

        page.default.layout = name.startsWith('Auth/') ? undefined : page.default.layout || DefaultLayout;

        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .provide('recaptcha_site_key', props.initialPage.props.recaptcha_site_key)
            .provide('max_map_bounds', props.initialPage.props.max_map_bounds)
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

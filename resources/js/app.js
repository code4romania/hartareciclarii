import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '@/Helpers/useRoute';
import { i18nVue } from 'laravel-vue-i18n';
import VueClickAway from 'vue3-click-away';

import 'virtual:svg-icons-register';

import.meta.glob(['../images/**']);

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(ZiggyVue)
            .use(i18nVue, {
                resolve: async (lang) => {
                    const langs = import.meta.glob('../../lang/*.json');
                    return await langs[`../../lang/${lang}.json`]();
                },
            })
            .use(VueClickAway)
            .use(plugin)
            .mount(el);
    },
});

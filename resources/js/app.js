import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { i18nVue } from 'laravel-vue-i18n';
import DefaultLayout from "@/Layouts/DefaultLayout.vue";
import 'virtual:svg-icons-register';

import.meta.glob(['../images/**']);

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))
    // .then(page => {
    //     page.default.layout = DefaultLayout;

    //     return page;
    // })

    ,
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(i18nVue, {
                resolve: async lang => {
                    const langs = import.meta.glob('../../lang/*.json');
                    return await langs[`../../lang/${lang}.json`]();
                }
            })
            .use(plugin)
            .mount(el)
    },
})

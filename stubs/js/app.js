require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import _ from 'lodash';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const isPackage = name.includes('::');

        if(! isPackage) {
            return import(`./Pages/${name}.vue`).then(module => module.default)
        }

        const [packageName, packagePage] = name.split('::');
        const packageFolder = _.find(window.applicationModules, { name: packageName });

        if(! packageFolder) {
            throw new Error(`Module ${packageName} not found.`)
        }

        return import(`/../vendor/${packageFolder.vendor}/src/resources/js/Pages/${packagePage}.vue`)
            .then(module => module.default)
    },
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .mixin({ methods: { route } })
            .mount(el);
    },
});


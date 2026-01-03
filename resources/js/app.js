import { createInertiaApp } from '@inertiajs/svelte'
import { mount } from 'svelte'
import '../css/app.css';
import { registerSW } from 'virtual:pwa-register';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.svelte', { eager: true })
        return pages[`./Pages/${name}.svelte`]
    },
    setup({ el, App, props }) {
        mount(App, { target: el, props })
    },
})

// PWA (dev-friendly) SW registration
registerSW({ immediate: true });

import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Cargamos Alpine para las partes de Blade que lo necesiten
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Configuramos Inertia solo si encontramos el elemento raíz en el DOM
// Esto evita errores cuando cargamos páginas que solo usan Blade (como el Dashboard)
const el = document.getElementById('app');
if (el) {
    createInertiaApp({
        title: (title) => `${title} - Nosecaen`,
        resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
        setup({ el, App, props, plugin }) {
            return createApp({ render: () => h(App, props) })
                .use(plugin)
                .mount(el);
        },
        progress: {
            color: '#4B5563',
        },
    });
}

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

if (typeof window !== 'undefined') {
    createInertiaApp({
        resolve: (name) => {
            const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue', { eager: true });
            return pages[`./Pages/${name}.vue`];
        },
        setup({ el, App, props, plugin }) {
            createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(Toast, {
                    position: 'top-right',
                    timeout: 3000,
                    closeOnClick: true,
                    pauseOnFocusLoss: true,
                    pauseOnHover: true,
                    draggable: true,
                    showCloseButtonOnHover: false,
                    hideProgressBar: false,
                    closeButton: 'button',
                    icon: true,
                    rtl: false,
                })
                .mount(el);
        },
        progress: {
            color: '#4B5563',
        },
    });
}

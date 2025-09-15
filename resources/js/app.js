import '../css/app.css'
import './bootstrap'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

// ✅ PrimeVue & Theme
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/lara'
import '@primeuix/themes/lara'
import 'primeicons/primeicons.css'
import ToastService from 'primevue/toastservice'
import Tooltip from 'primevue/tooltip'

// ✅ PrimeVue Components (global register)
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

    // ✅ PrimeVue setup
    app.directive('tooltip', Tooltip)
    app.use(ToastService)
    app.use(PrimeVue, {
      theme: {
        preset: Aura,
        options: {
          darkModeSelector: '.dark',
        },
      },
    })

    // ✅ Global components
    app.component('DataTable', DataTable)
    app.component('Column', Column)
    app.component('InputText', InputText)
    app.component('Button', Button)

    app.use(plugin).use(ZiggyVue).mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})

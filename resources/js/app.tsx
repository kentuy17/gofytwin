import type { route as routeFn } from 'ziggy-js'

import { createInertiaApp } from '@inertiajs/react'
import { withProfiler } from '@sentry/react'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createRoot } from 'react-dom/client'
import OperatorProvider from './context/operator-context'
import { initializeTheme } from './hooks/use-appearance'
import { initializeSentry } from './sentry'
import '../css/app.css'

declare global {
  const route: typeof routeFn
}

const appName = import.meta.env.APP_NAME ?? 'Laravel'

void createInertiaApp({
  title: title => `${title} - ${appName}`,
  resolve: async name => resolvePageComponent(`./pages/${name}.jsx`, import.meta.glob('./pages/**/*.jsx')),
  setup({ el, App, props }) {
    const root = createRoot(el)

    withProfiler(App)

    root.render(<OperatorProvider><App {...props} /></OperatorProvider>)
  },
  progress: {
    color: '#4B5563',
  },
})

initializeSentry()

// This will set light / dark mode on load...
initializeTheme()

// import AppLogoIcon from '@/components/app-logo-icon'
import { ThemeProvider } from '@/components/theme-provider'
// import { Link } from '@inertiajs/react'

export default function AuthBlock({ children }) {
  return (
    <ThemeProvider defaultTheme="dark" storageKey="vite-ui-theme">

      <div className="bg-background flex min-h-svh w-full items-center justify-center p-6 md:p-10">
        <div className="w-full max-w-sm">
          {children}
        </div>
      </div>

    </ThemeProvider>

  )
}

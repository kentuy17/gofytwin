import type { PropsWithChildren } from 'react'
import { ManokLogo } from '@/components/app-header'
// import AppLogoIcon from '@/components/app-logo-icon'
import { ThemeProvider } from '@/components/theme-provider'
import { Link } from '@inertiajs/react'

interface AuthLayoutProps {
  name?: string
  title?: string
  description?: string
}

export default function AuthSimpleLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
  return (
    <ThemeProvider defaultTheme="dark" storageKey="vite-ui-theme">
      <div className="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10 bg-image-login">
        <div className="w-full max-w-sm" style={{ marginBottom: '300px' }}>
          <div className="flex flex-col gap-8">
            <div className="flex flex-col items-center gap-4">
              <Link href={route('home')} className="flex flex-col items-center gap-2 font-medium">
                <div className="mb-12 flex h-9 w-9 items-center justify-center rounded-md">
                  {/* <AppLogoIcon className="size-9 fill-current text-[var(--foreground)] dark:text-white" /> */}
                  <ManokLogo />
                </div>
                <span className="sr-only">{title}</span>
              </Link>

              <div className="space-y-2 text-center">
                <h1 className="text-xl font-medium">{title}</h1>
                <p className="text-muted-foreground text-center text-sm">{description}</p>
              </div>
            </div>
            {children}
          </div>
        </div>
      </div>
    </ThemeProvider>

  )
}

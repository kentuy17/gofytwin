import type { PropsWithChildren } from 'react'
// import { ManokLogo } from '@/components/app-header'
// import AppLogoIcon from '@/components/app-logo-icon'
import { ThemeProvider } from '@/components/theme-provider'
import { Link } from '@inertiajs/react'

interface AuthLayoutProps {
  name?: string
  title?: string
  description?: string
}

function MyManok() {
  return <img src="/images/gamefowl-master-logo-official.png" className="w-[100px] my-3" />
}

export default function AuthSimpleLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
  return (
    <ThemeProvider defaultTheme="dark" storageKey="vite-ui-theme">
      <div className="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10 bg-image-login">
        {/* <div className="w-full max-w-sm" style={{ marginBottom: '400px' }}> */}
        <div className="w-full max-w-sm top-[23%] fixed">
          <div className="flex flex-col gap-8">
            <div className="flex flex-col items-center gap-4">
              <Link href={route('home')} className="flex flex-col items-center gap-2 font-medium">
                <div className=" flex flex-col h-12 w-12 items-center justify-center rounded-md">
                  <MyManok />
                  <img className="block max-w-[235px] mb-36" src="/images/LEADERBOARD-BANNER-FRONT-PAGE-3.gif" alt="LEADERBOARD-BANNER-FRONT-PAGE" />
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

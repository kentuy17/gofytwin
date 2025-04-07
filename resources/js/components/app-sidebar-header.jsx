// import type { BreadcrumbItem as BreadcrumbItemType } from '@/types'
// import type { PageProps } from '@inertiajs/core'
import { Breadcrumbs } from '@/components/breadcrumbs'
import { SidebarTrigger } from '@/components/ui/sidebar'
import { formatMoney } from '@/lib/utils'
import { Link, usePage } from '@inertiajs/react'
import { PlusIcon } from 'lucide-react'
import { Button } from './ui/button'

export function AppSidebarHeader({ breadcrumbs }) {
  const { auth } = usePage().props
  const credits = formatMoney(auth?.user?.credits)

  return (
    <header className="justify-between items-center border-sidebar-border/50 flex h-16 shrink-0 gap-2 border-b px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
      <div className="flex items-center gap-2">
        <SidebarTrigger className="-ml-1" />
        <Breadcrumbs breadcrumbs={breadcrumbs} />
      </div>
      <div className="flex space-between">
        <span className="pr-1 text-yellow-300 font-bold">Credits: </span>
        <span>
          {credits}
        </span>
      </div>
      <div className="">
        <Button size="sm" className="bg-blue-500 text-white font-bold" asChild>
          <Link href="/player/deposit">
            <PlusIcon />
            <span>Cashin</span>
          </Link>
        </Button>
      </div>
    </header>
  )
}

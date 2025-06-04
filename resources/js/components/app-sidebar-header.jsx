import { Breadcrumbs } from '@/components/breadcrumbs'
import { SidebarTrigger } from '@/components/ui/sidebar'
import { OperatorContext } from '@/context/operator-context'
import { formatMoney } from '@/lib/utils'
import { Link, router, usePage } from '@inertiajs/react'
import { PlusIcon, VideoIcon, VideoOffIcon } from 'lucide-react'
import { useContext, useEffect, useState } from 'react'
import { Button } from './ui/button'

export function AppSidebarHeader({ breadcrumbs }) {
  const { setCredits, credits } = useContext(OperatorContext)
  const { auth, globals } = usePage().props
  const [showVideo, setShowVideo] = useState(globals.config.show_video === '1')
  const pts = auth?.user?.credits

  const toggleVideoBtn = (e) => {
    e.preventDefault()
    setShowVideo(!showVideo)
    router.post('/admin/toggle-video', {
      show_video: !showVideo,
    })
  }

  useEffect(() => {
    setCredits(pts)
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  return (
    <header className="justify-between items-center border-sidebar-border/50 flex h-16 shrink-0 gap-2 border-b px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
      <div className="flex items-center gap-2">
        <SidebarTrigger className="-ml-1" />
        <Breadcrumbs breadcrumbs={breadcrumbs} />
      </div>
      <div className="flex space-between">
        <span className="pr-1 text-yellow-300 font-bold">Credits: </span>
        <span>
          {formatMoney(credits)}
        </span>
      </div>
      <div className="">
        {Number.parseInt(auth.user.user_level) > 0
          ? (
              <Button onClick={toggleVideoBtn} variant="default" size="icon">
                {showVideo ? <VideoIcon /> : <VideoOffIcon />}
              </Button>
            )
          : (
              <Button size="sm" className="bg-blue-500 text-white font-bold" asChild>
                <Link href="/player/deposit">
                  <PlusIcon />
                  <span>Cashin</span>
                </Link>
              </Button>
            )}
      </div>
    </header>
  )
}

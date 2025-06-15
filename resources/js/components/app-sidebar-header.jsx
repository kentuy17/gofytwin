import { Breadcrumbs } from '@/components/breadcrumbs'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  // AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import { SidebarTrigger } from '@/components/ui/sidebar'
import { OperatorContext } from '@/context/operator-context'
import { formatMoney } from '@/lib/utils'
import { Link, router, usePage, usePoll } from '@inertiajs/react'
import { PlusIcon, VideoIcon, VideoOffIcon } from 'lucide-react'
import { useContext, useEffect, useState } from 'react'

import { Button } from './ui/button'

function VideoToggle({ children, showVideo, setShowVideo }) {
  const [open, setOpen] = useState(false)
  const action = showVideo ? 'OFF' : 'ON'

  const toggleVideoBtn = (e) => {
    e.preventDefault()
    setOpen(false)
    setShowVideo(!showVideo)
    router.post('/admin/toggle-video', {
      show_video: !showVideo,
    })
  }

  return (
    <AlertDialog open={open} onOpenChange={setOpen}>
      <AlertDialogTrigger asChild>
        {children}
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>{`TURN ${action} VIDEO?`}</AlertDialogTitle>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Cancel</AlertDialogCancel>
          <AlertDialogAction onClick={toggleVideoBtn}>Yes</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  )
}

export function AppSidebarHeader({ breadcrumbs }) {
  const { setCredits, credits } = useContext(OperatorContext)
  const { auth, globals } = usePage().props
  const [showVideo, setShowVideo] = useState(globals.config.show_video === '1')
  const pts = auth?.user?.credits
  // const true_credits = 0

  usePoll(5000)

  useEffect(() => {
    setCredits(pts)
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])
  // console.log(usePage().props.csrf_token)
  // fetch('/get-credits', {
  //   method: 'POST',
  //   headers: {
  //     'Accept': 'application/json',
  //     'Content-Type': 'application/json',
  //     'X-CSRF-TOKEN': usePage().props.csrf_token,
  //   },
  //   body: JSON.stringify({
  //     user_id: auth.user.id,
  //   }),
  // }).then((response) => {
  //   return Promise.all([response.json(), response])
  // }).then(([responseData]) => {
  //   if (responseData.success === true) {
  //     setCredits(responseData.data)
  //   }
  // })

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
              <VideoToggle showVideo={showVideo} setShowVideo={setShowVideo} asChild>
                <Button variant="default" size="icon">
                  {showVideo ? <VideoIcon /> : <VideoOffIcon />}
                </Button>
              </VideoToggle>

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

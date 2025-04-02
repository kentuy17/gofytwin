/* eslint-disable no-console */
import BetTrends from '@/components/bet-trends'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import VideoJS from '@/components/videojs'
// import { PlaceholderPattern } from '@/components/ui/placeholder-pattern'
import AppLayout from '@/layouts/app-layout'
import { Head, usePage } from '@inertiajs/react'
import React from 'react'
// import ReactPlayer from 'react-player'

const breadcrumbs = [
  {
    title: 'Home',
    href: '/home',
  },
]

const VIDEO_URL = 'https://nondrm.mystreamingserver.live/livecf/swarena2/playlist.m3u8'

export default function Dashboard({ commonData }) {
  commonData && console.log(commonData)

  // console.log(status)
  const { flash } = usePage().props
  console.log(flash)

  const playerRef = React.useRef(null)

  const videoJsOptions = {
    autoplay: true,
    controls: true,
    responsive: true,
    muted: false,
    fluid: true,
    sources: [{
      src: VIDEO_URL,
      type: 'application/x-mpegURL',
    }],
  }

  const handlePlayerReady = (player) => {
    playerRef.current = player

    // You can handle player events here, for example:
    player.on('waiting', () => {
      // videojs.log('player is waiting')
    })

    player.on('dispose', () => {
      // videojs.log('player will dispose')
    })
  }

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl">
        <div className="grid auto-rows-min gap-4 md:mx-6">
          <div className="border-sidebar-border/70 dark:border-sidebar-border max-w-3xl relative min-h-[100vh] flex-1 rounded-xl border md:min-h-min">
            {/* <ReactPlayer
              url={VIDEO_URL}
              controls={false}
              playIcon
              playing
              width="auto"
              height="auto"
            /> */}
            <VideoJS options={videoJsOptions} onReady={handlePlayerReady} />
            <div className=" flex flex-col md:max-w-2xl w-100">
              <Card>
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">
                    {/* Betting Console */}
                  </CardTitle>
                </CardHeader>
                <CardContent className="">
                  {/* <div className="text-red-400 text-2xl font-bold">₱45,455.66</div> */}
                  {/* <div className="text-green-400 text-2xl font-bold">₱40,231.89</div> */}
                  <div className="flex justify-around">
                    <div className="text-red-400 text-2xl font-black">MERON</div>
                    <div className="text-blue-400 text-2xl font-black">WALA</div>
                  </div>
                  <div className="h-[200px]">

                  </div>
                  <div className="">
                    <BetTrends />
                  </div>
                </CardContent>
              </Card>

            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  )
}

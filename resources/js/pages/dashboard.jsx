/* eslint-disable no-console */
import VideoJS from '@/components/videojs'
// import { PlaceholderPattern } from '@/components/ui/placeholder-pattern'
import AppLayout from '@/layouts/app-layout'
import { Head } from '@inertiajs/react'
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
  console.log(commonData)

  const playerRef = React.useRef(null)

  const videoJsOptions = {
    autoplay: true,
    controls: true,
    responsive: true,
    fluid: true,
    sources: [{
      src: VIDEO_URL,
      // type: 'video/mp4',
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
      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div className="grid auto-rows-min gap-4 md:grid-cols-3">
          <div className="border-sidebar-border/70 dark:border-sidebar-border relative min-h-[100vh] flex-1 rounded-xl border md:min-h-min">
            {/* <ReactPlayer
              url={VIDEO_URL}
              controls={false}
              playIcon
              playing
              width="auto"
              height="auto"
            /> */}
            <VideoJS options={videoJsOptions} onReady={handlePlayerReady} />
          </div>
        </div>
      </div>
    </AppLayout>
  )
}

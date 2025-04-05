/* eslint-disable no-alert */
/* eslint-disable no-console */
import BetTrends from '@/components/bet-trends'
import { Button } from '@/components/ui/button'
// import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import VideoJS from '@/components/videojs'
// import { PlaceholderPattern } from '@/components/ui/placeholder-pattern'
import AppLayout from '@/layouts/app-layout'
import { Head, usePage } from '@inertiajs/react'
import React, { useEffect, useState } from 'react'
// import ReactPlayer from 'react-player'

const breadcrumbs = [
  {
    title: 'Home',
    href: '/home',
  },
]

const VIDEO_URL = 'https://nondrm.mystreamingserver.live/livecf/swarena2/playlist.m3u8'

function formatMoney(value) {
  return Number.isNaN(value)
    ? '0.00'
    : new Intl.NumberFormat('en-US').format(value.toFixed(2))
}

function BetStats({ fight, meron, wala }) {
  const [cMeron, setCmeron] = useState(meron)
  const [cWala, setCwala] = useState(wala)

  const player = {
    bets: {
      meron: 500,
      wala: 0,
    },
  }

  const totalSum = cMeron + cWala

  const percentage = (side) => {
    const comm = side => (side * 15) / 100
    const win = totalSum - comm(side)
    return (win / side) * 100
  }

  const meronWinAmount = () => {
    return (player.bets.meron * percentage(cMeron)) / 100
  }

  const walaWinAmount = () => {
    return (player.bets.wala * percentage(cWala)) / 100
  }

  useEffect(() => {
    setCmeron(meron + player.bets.meron)
    setCwala(wala + player.bets.wala)
  }, [player.bets, meron, wala])

  return (
    <div className="leading-none flex justify-around w-full">
      <div className="bet-red flex flex-col w-2/5 justify-center items-center p-2">
        <div style={{ lineHeight: 1 }} className="text-white-400 text-2xl font-black">MERON</div>
        <div style={{ lineHeight: 1 }} className="text-yellow-300 text-xl">{formatMoney(cMeron)}</div>
        <div>{`(${formatMoney(percentage(cMeron))}%)`}</div>
        <div>{`${formatMoney(player.bets.meron)} = ${formatMoney(meronWinAmount())}`}</div>
      </div>
      <div className="px-4 bet-yellow flex flex-col justify-center items-center w-1/5">
        <span className="text-black">GAME</span>
        <span className="text-black">
          FIGHT
        </span>
        <div className="font-black text-3xl">{fight}</div>
      </div>
      <div className="bet-blue flex flex-col w-2/5 justify-center items-center">
        <div style={{ lineHeight: 1 }} className="text-white-400 text-2xl font-black">WALA</div>
        <div style={{ lineHeight: 1 }} className="text-yellow-300 text-xl">{formatMoney(cWala)}</div>
        <div>{`(${formatMoney(percentage(cWala))}%)`}</div>
        <div>{`${formatMoney(player.bets.wala)} = ${formatMoney(walaWinAmount())}`}</div>
      </div>
    </div>
  )
}

function PlaceBets() {
  const handleOnlickMeron = () => {
    const betAmount = prompt('Enter Amount:', 0)
    console.log(betAmount)
  }

  const handleOnlickWala = () => {
    const betAmount = prompt('Enter Amount:', 0)
    console.log(betAmount)
  }

  return (
    <div className="bg-black justify-around flex flex-row w-full p-2">
      <Button onClick={handleOnlickMeron} className="rounded-lg p-2">Place Bet</Button>
      <Button onClick={handleOnlickWala} className="rounded-lg p-2">Place Bet</Button>
    </div>
  )
}

export default function Dashboard({ commonData }) {
  commonData && console.log(commonData)

  // console.log(status)
  const { flash } = usePage().props
  console.log(flash)

  const playerRef = React.useRef(null)
  const autoplay = !import.meta.env.VITE_AUTO_PLAY

  const videoJsOptions = {
    autoplay,
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
      console.log('player is wating')
    })

    player.on('dispose', () => {
      console.log('player will dispose')
    })
  }

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <div className="h-full flex-1 flex-col gap-4 rounded-xl grid md:grid-cols-2 grid-cols-1">
        {/* <div className="grid auto-rows-min gap-4 md:mx-6"> */}
        <div className="border-sidebar-border/70 dark:border-sidebar-border md:max-w-2xl max-w-100 relative  flex-1 rounded-xl border">
          <VideoJS options={videoJsOptions} onReady={handlePlayerReady} />
          <div className="font-xs text-[9px] text-center italic py-2 text-red-500">
            Note Gamefowl Master available from 8AM-3AM daily with 250 Game Fights. Play Today!
          </div>
          <BetStats fight={250} meron={88888} wala={88888} />
          <PlaceBets />
        </div>
        <BetTrends />
        {/* </div> */}
      </div>
    </AppLayout>
  )
}

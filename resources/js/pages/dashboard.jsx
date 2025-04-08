/* eslint-disable no-alert */
/* eslint-disable no-console */
import BetTrends from '@/components/bet-trends'
import { Button } from '@/components/ui/button'
// import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import VideoJS from '@/components/videojs'
import { echoInstance } from '@/echo'
// import { PlaceholderPattern } from '@/components/ui/placeholder-pattern'
import AppLayout from '@/layouts/app-layout'
import { Banner } from '@/layouts/app/app-sidebar-layout'
import { formatMoney } from '@/lib/utils'
import { Head, usePage, usePoll } from '@inertiajs/react'
import axios from 'axios'
// import Echo from 'laravel-echo'
import React, { useEffect, useState } from 'react'

const breadcrumbs = [
  {
    title: 'Home',
    href: '/home',
  },
]

const VIDEO_URL = 'https://nondrm.mystreamingserver.live/livecf/swarena2/playlist.m3u8'

async function addBet(params) {
  try {
    const { message, amount, side, credits } = params
    if (message !== 'OPEN') {
      alert('Cannot Bet')
      return
    }

    if (amount < 10) {
      alert('Minimum bet is 10.00')
      return
    }

    if (amount > credits) {
      alert('Insuficient Points')
      return
    }

    if (amount > 1500) {
      alert('Maximum bet is 1,500.00')
      return
    }

    if (!confirm(`Bet ${formatMoney(amount)}?`)) {
      return
    }

    const { data } = await axios.post('/bet/add', {
      amount,
      side,
    })

    // if (data.status === 'OK') {
    //   this.player.points -= this.betAmount
    //   side === 'M'
    //     ? (this.player.bets.meron += this.betAmount)
    //     : (this.player.bets.wala += this.betAmount)
    //   this.clear()
    // }
    // console.log(data)
    return data.status
  }
  catch (err) {
    // alert(err.response.data.error)
    console.error(err)
  }
}

function PlaceBets({ setPlayerBet, playerBet }) {
  const { auth } = usePage().props
  const betMeron = Number.parseFloat(playerBet.meron)
  const betWala = Number.parseFloat(playerBet.wala)

  const handleOnlickMeron = async () => {
    const betAmount = prompt('Enter Amount:', 0)
    if (!betAmount)
      return

    const resp = await addBet({
      message: 'OPEN',
      amount: betAmount,
      side: 'M',
      credits: auth.user.credits,
    })

    if (resp === 'OK') {
      setPlayerBet({ ...playerBet, meron: betMeron + Number.parseFloat(betAmount) })
      console.log(betAmount)
    }
  }

  const handleOnlickWala = async () => {
    const betAmount = prompt('Enter Amount:', 0)
    if (!betAmount)
      return

    const resp = await addBet({
      message: 'OPEN',
      amount: Number.parseFloat(betAmount),
      side: 'W',
      credits: auth.user.credits,
    })

    if (resp === 'OK') {
      setPlayerBet({ ...playerBet, wala: betWala + Number.parseFloat(betAmount) })
    }
  }

  return (
    <div className="bg-black justify-around flex flex-row w-full p-2">
      <div className="flex w-2/5 justify-center items-center">
        <Button onClick={handleOnlickMeron} size="sm" className="text-red-500 bg-yellow-200 font-bold rounded-xl">Place Bet</Button>
      </div>

      <div className="w-1/5"></div>
      <div className="flex w-2/5 justify-center items-center">
        <Button onClick={handleOnlickWala} size="sm" className="text-blue-500 bg-yellow-200 font-bold rounded-xl">Place Bet</Button>
      </div>

    </div>
  )
}

function BetStats({ fight, meron, wala, playerBet }) {
  const [cMeron, setCmeron] = useState(meron)
  const [cWala, setCwala] = useState(wala)

  console.log(playerBet)

  const player = {
    bets: {
      meron: playerBet.meron,
      wala: playerBet.wala,
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
        <div style={{ lineHeight: 1 }} className="text-white-400 text-2xl font-black pb-2">MERON</div>
        <div style={{ lineHeight: 1 }} className="text-yellow-200 text-xl font-black">{formatMoney(cMeron)}</div>
        <div className="pb-2">{`(${formatMoney(percentage(cMeron), false)}%)`}</div>
        <div>{`${formatMoney(player.bets.meron, false)} = ${formatMoney(meronWinAmount(), false)}`}</div>
      </div>
      <div className="px-4 bet-yellow flex flex-col justify-center items-center w-1/5">
        <span className="text-black">GAME</span>
        <span className="text-black">
          FIGHT
        </span>
        <div className="font-black text-3xl">{fight}</div>
      </div>
      <div className="bet-blue flex flex-col w-2/5 justify-center items-center p-2">
        <div style={{ lineHeight: 1 }} className="text-white-400 text-2xl font-black pb-2">WALA</div>
        <div style={{ lineHeight: 1 }} className="text-yellow-200 text-xl font-black">{formatMoney(cWala)}</div>
        <div className="pb-2">{`(${formatMoney(percentage(cWala), false)}%)`}</div>
        <div>{`${formatMoney(player.bets.wala, false)} = ${formatMoney(walaWinAmount(), false)}`}</div>
      </div>
    </div>
  )
}

export default function Dashboard({ commonData, user }) {
  const { meron, wala, fight_no } = commonData
  const [totalMeron, setTotalMeron] = useState(meron)
  const [totalWala, setTotalWala] = useState(wala)
  const [playerBet, setPlayerBet] = useState({ meron: 0, wala: 0 })

  commonData && console.log(commonData)
  console.log(user)

  usePoll(2000, {
    onPrefetched() {
      console.log('prefetch')
    },
    onFinish() {
      console.log('finish')
    },
  }, { keepAlive: true })

  // console.log(status)
  const { flash } = usePage().props
  flash.message && console.log(flash)

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

  useEffect(() => {
    echoInstance.private('bet-channel')
      .listen('BetEvent', async (e) => {
        const { amount, side, user_id } = e.securedBet
        if (user.id === user_id)
          return

        console.log(e, 'reverb')
        if (side === 'M') {
          setTotalMeron(prev => prev + amount)
        }

        if (side === 'W') {
          setTotalWala(prev => prev + amount)
        }
      })
  }, [user.id])

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <Banner />
      <div className="h-full flex-1 flex-col gap-4 rounded-xl grid md:grid-cols-2 grid-cols-1">
        {/* <div className="grid auto-rows-min gap-4 md:mx-6"> */}
        <div className="border-sidebar-border/70">
          <VideoJS options={videoJsOptions} onReady={handlePlayerReady} />
          <div className="font-xs text-[9px] text-center italic py-2 text-red-500">
            Note Gamefowl Master available from 8AM-3AM daily with 250 Game Fights. Play Today!
          </div>
          <BetStats fight={fight_no} meron={totalMeron} wala={totalWala} playerBet={playerBet} />
          <PlaceBets setPlayerBet={setPlayerBet} playerBet={playerBet} />
        </div>
        <BetTrends />
        {/* </div> */}
      </div>
    </AppLayout>
  )
}

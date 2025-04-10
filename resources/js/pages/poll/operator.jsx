/* eslint-disable no-alert */
/* eslint-disable no-console */
import BetTrends from '@/components/bet-trends'
import { Button } from '@/components/ui/button'
// import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import VideoJS from '@/components/videojs'
// import { echoInstance } from '@/echo'
// import { PlaceholderPattern } from '@/components/ui/placeholder-pattern'
import AppLayout from '@/layouts/app-layout'
import { Banner } from '@/layouts/app/app-sidebar-layout'
import { formatMoney } from '@/lib/utils'
import { Head, usePage } from '@inertiajs/react'
import axios from 'axios'
import Pusher from 'pusher-js'
import React, { useEffect, useState } from 'react'
import { OperatorProvider, useOperatorContext } from '../../context/operator-context'

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

function BetStats() {
  // const [cMeron, setCmeron] = useState(meron)
  // const [cWala, setCwala] = useState(wala)

  const { fight, meron, wala, playerBet } = useOperatorContext()
  console.log([fight, meron, wala, playerBet])

  // const player = {
  //   bets: {
  //     meron: playerBet.meron,
  //     wala: playerBet.wala,
  //   },
  // }

  const totalSum = meron + wala

  const percentage = (side) => {
    const comm = side => (side * 15) / 100
    const win = totalSum - comm(side)
    return (win / side) * 100
  }

  const meronWinAmount = () => {
    return (playerBet.meron * percentage(meron)) / 100
  }

  const walaWinAmount = () => {
    return (playerBet.wala * percentage(wala)) / 100
  }

  // useEffect(() => {
  //   setCmeron(meron)
  //   setCwala(wala)
  // }, [meron, wala])

  return (
    <div className="leading-none flex justify-around w-full">
      <div className="bet-red flex flex-col w-2/5 justify-center items-center p-2">
        <div style={{ lineHeight: 1 }} className="text-white-400 text-2xl font-black pb-2">MERON</div>
        <div style={{ lineHeight: 1 }} className="text-yellow-200 text-xl font-black">{formatMoney(meron)}</div>
        <div className="pb-2">{`(${formatMoney(percentage(meron), false)}%)`}</div>
        <div>{`${formatMoney(playerBet.meron, false)} = ${formatMoney(meronWinAmount(), false)}`}</div>
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
        <div style={{ lineHeight: 1 }} className="text-yellow-200 text-xl font-black">{formatMoney(wala)}</div>
        <div className="pb-2">{`(${formatMoney(percentage(wala), false)}%)`}</div>
        <div>{`${formatMoney(playerBet.wala, false)} = ${formatMoney(walaWinAmount(), false)}`}</div>
      </div>
    </div>
  )
}

export default function Operator({ player, commonData }) {
  const {
    meron,
    wala,
    fight,
    playerBet,
    setPlayerBet,
    setMeron,
    setWala,
    setFight,
  } = useOperatorContext()

  setPlayerBet(player.bets)
  setMeron(commonData.meron)
  setWala(commonData.wala)
  setFight(commonData.fight_no)

  // console.log([meron, wala, fight, playerBet])

  const { flash, user } = usePage().props
  flash.message && console.log(flash)
  const [status, setStatus] = useState('WAITING')

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
      console.log('player is wating')
    })

    player.on('dispose', () => {
      console.log('player will dispose')
    })
  }

  const pusher = new Pusher('1ac728eb26e770e7b87d', {
    cluster: 'ap1',
  })

  // const fightPush = new Pusher('1ac728eb26e770e7b87d', {
  //   cluster: 'ap1',
  // })

  Pusher.logToConsole = true

  // const betChannel = pusher.subscribe('bet-channel')
  const fightChannel = pusher.subscribe('fight-channel')

  // useEffect(() => {
  //   betChannel.bind('bet-event', (e) => {
  //     const { amount, side, user_id } = e.securedBet
  //     // console.log(e)

  //     if (user_id === user.id) {
  //       if (side === 'M') {
  //         setPlayerBet({ ...playerBet.wala, meron: prev => prev.meron + amount })
  //       }
  //       else {
  //         setPlayerBet({ ...playerBet.meron, wala: prev => prev.wala + amount })
  //       }
  //     }

  //     if (side === 'M') {
  //       setMeron(prev => prev + amount)
  //     }

  //     if (side === 'W') {
  //       // setWala(prev => prev + amount)
  //       setWala(amount)
  //     }

  //     console.log(e, 'pusher')
  //   })

  //   // eslint-disable-next-line react-hooks/exhaustive-deps
  // }, [])

  useEffect(() => {
    fightChannel.bind('fight-event', (e) => {
      // console.log(e, 'fight')
      setFight(e.currentFight.fight_no)
      setStatus(e.currentFight.status)
    })
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  return (
    <OperatorProvider>
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
            <BetStats fight={fight} meron={meron} wala={wala} playerBet={playerBet} />
            <PlaceBets playerBet={playerBet} />
          </div>
          <BetTrends status={status} />
          {/* </div> */}
        </div>
      </AppLayout>
    </OperatorProvider>
  )
}

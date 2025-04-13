/* eslint-disable no-alert */
import { BetDialog } from '@/components/bet-dialog'
import BetTrends, { trendClasses } from '@/components/bet-trends'
import { Button } from '@/components/ui/button'
import VideoJS from '@/components/videojs'
import { OperatorContext } from '@/context/operator-context'
import AppLayout from '@/layouts/app-layout'
import { Banner } from '@/layouts/app/app-sidebar-layout'
import { formatMoney } from '@/lib/utils'
import { Head, usePage } from '@inertiajs/react'
import axios from 'axios'
import React, { useContext, useEffect } from 'react'
import { pusher } from '../../echo'
import { PlusCircleIcon } from 'lucide-react'

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

    // if (!confirm(`Bet ${formatMoney(amount)}?`)) {
    //   return
    // }

    const { data } = await axios.post('/bet/add', {
      amount,
      side,
    })

    return data.status
  }
  catch (err) {
    console.error(err)
  }
}

function PlaceBets() {
  const { auth } = usePage().props
  const { playerBet, setPlayerBet,  status } = useContext(OperatorContext)
  const betWala = Number.parseFloat(playerBet.wala)

  const handleOnlickWala = async () => {
    if (status !== 'OPEN') {
      alert(`Cannot bet.`)
      return
    }

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
        <BetDialog side='M'>
          <Button size="sm" disabled={status !== 'OPEN'} className="text-white bg-red-500 font-bold">
            <PlusCircleIcon />BET MERON
          </Button>
        </BetDialog>
      </div>

      <div className="w-1/5"></div>
      <div className="flex w-2/5 justify-center items-center">
        <BetDialog side='W'>
          <Button size="sm" disabled={status !== 'OPEN'} className="text-white bg-blue-500 font-bold">
            <PlusCircleIcon />BET WALA
          </Button>
        </BetDialog>
      </div>

    </div>
  )
}

function BetStats() {
  const { meron, wala, fight, playerBet } = useContext(OperatorContext)

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

  return (
    <div className="leading-none flex justify-around w-full">
      <div className="bet-red flex flex-col w-2/5 justify-center items-center p-2">
        <div style={{ lineHeight: 1 }} className="text-white-400 text-2xl font-black pb-2">MERON</div>
        <div style={{ lineHeight: 1 }} className="text-yellow-200 text-xl font-black">{formatMoney(meron)}</div>
        <div className="pb-2">{`(${formatMoney(percentage(meron), false)}%)`}</div>
        <div>{`${formatMoney(playerBet.meron, false)} = ${formatMoney(meronWinAmount(), false)}`}</div>
      </div>
      <div className="px-4 bet-yellow flex flex-col justify-center items-center w-1/5">
        <span className="text-black">FIGHT</span>
        <span className="text-black text-sm">
          NUMBER
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

export default function Dashboard({ commonData, player }) {
  const {
    setPlayerBet,
    setMeron,
    setWala,
    setFight,
    setStatus,
    setCredits,
    setLastResult,
  } = useContext(OperatorContext)

  const { flash } = usePage().props
  // eslint-disable-next-line no-console
  flash.message && console.log(flash)

  const playerRef = React.useRef(null)

  const videoJsOptions = {
    autoplay: true,
    controls: true,
    responsive: true,
    muted: true,
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
      // console.log('player will dispose')
    })
  }

  useEffect(() => {
    pusher.unbind_all()
    pusher.bind('bet-event', (e) => {
      const { amount, side } = e.securedBet

      if (side === 'M') {
        setMeron(prev => prev + amount)
      }

      if (side === 'W') {
        setWala(prev => prev + amount)
      }
    })

    pusher.bind('fight-event', (e) => {
      const { fight_no, status } = e.currentFight
      setFight(fight_no)
      setStatus(status)

      if (status === 'DONE') {
        // Clean-up here
        setFight(fight_no + 1)
        setMeron(0)
        setWala(0)
        setPlayerBet({
          meron: 0,
          wala: 0,
        })
      }
    })

    pusher.bind('result-event', (e) => {
      const { fight_no, game_winner } = e.fightResult
      const res = { fight_no, result: game_winner, itemClass: trendClasses[game_winner] }
      setLastResult(old => [...old, res])
    })
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  useEffect(() => {
    setMeron(commonData.meron)
    setWala(commonData.wala)
    setFight(commonData.fight_no)
    setStatus(commonData.status)
    setCredits(player.credits)
    setPlayerBet({
      meron: player.bets.meron,
      wala: player.bets.wala,
    })
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [commonData, player])

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
          <BetStats />
          <PlaceBets />
        </div>
        <BetTrends />
        {/* </div> */}
      </div>
    </AppLayout>
  )
}

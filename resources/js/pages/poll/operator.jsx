/* eslint-disable no-alert */
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

const breadcrumbs = [
  {
    title: 'Home',
    href: '/home',
  },
]

const COMMISSION = 10

// const VIDEO_URL = 'https://nondrm.mystreamingserver.live/livecf/swarena2/playlist.m3u8'
const VIDEO_URL = 'https://nondrm.mystreamingserver.live/livecf/sgmc1/playlist.m3u8'

async function addBet(params) {
  try {
    const { message, amount, side, credits } = params
    if (message !== 'OPEN') {
      alert('Cannot Bet')
      return
    }

    // if (amount < 10) {
    //   alert('Minimum bet is 10.00')
    //   return
    // }

    if (amount > credits) {
      alert('Insuficient Points')
      return
    }

    // if (amount > 1500) {
    //   alert('Maximum bet is 1,500.00')
    //   return
    // }

    if (!confirm(`Bet ${formatMoney(amount)}?`)) {
      return
    }

    const { data } = await axios.post('/bet/add', {
      amount,
      side,
    })
    return data.status
  }
  catch (err) {
    // alert(err.response.data.error)
    console.error(err)
  }
}

function PlaceBets() {
  const { auth } = usePage().props
  const { setPlayerBet, playerBet } = useContext(OperatorContext)
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
  const { fight, meron, wala, playerBet } = useContext(OperatorContext)

  const totalSum = meron + wala

  // const percentage = (side) => {
  //   const comm = side => (side * COMMISSION) / 100
  //   const win = totalSum - comm(side)
  //   return (win / side) * 100
  // }

  const percentage = (side) => {
    const comm = totalSum => (totalSum * Number.parseFloat(COMMISSION)) / 100
    const win = totalSum - comm(totalSum)
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

export default function Operator({ player, commonData, settings }) {
  const {
    setPlayerBet,
    setMeron,
    setWala,
    setFight,
    setStatus,
    setLastResult,
    setLastCall,
  } = useContext(OperatorContext)

  const { flash } = usePage().props

  flash.error && alert(flash.error)

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
    playsinline: true,
  }

  const handlePlayerReady = (player) => {
    playerRef.current = player

    // You can handle player events here, for example:
    player.on('waiting', () => {
      // console.log('player is wating')
    })

    player.on('dispose', () => {
      // console.log('player will dispose')
    })
  }

  useEffect(() => {
    pusher.unbind_all()
    pusher.bind('bet-event', (e) => {
      const { amount, side } = e.securedBet
      side === 'M'
        ? setMeron(prev => prev + amount)
        : setWala(prev => prev + amount)
    })

    pusher.bind('fight-event', (e) => {
      const { fight_no, game_winner, status } = e.currentFight
      setFight(fight_no)
      setStatus(status)

      if (status === 'DONE') {
        const res = { fight_no, result: game_winner, itemClass: trendClasses[game_winner] }
        setLastResult(old => [...old, res])

        setFight(fight_no + 1)
        setMeron(0)
        setWala(0)
        setPlayerBet({
          meron: 0,
          wala: 0,
        })
      }
    })

    pusher.bind('lastcall-event', (e) => {
      setLastCall(e.lastCall)
    })
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  useEffect(() => {
    setMeron(commonData.meron)
    setWala(commonData.wala)
    setFight(commonData.fight_no)
    setStatus(commonData.status)
    setLastCall(commonData.lastCall)
    setPlayerBet({
      meron: player.bets.meron,
      wala: player.bets.wala,
    })
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [commonData, player])

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <Banner image={settings.header_banner} />
      <div className="h-full flex-1 flex-col gap-4 rounded-xl grid md:grid-cols-2 grid-cols-1">
        {/* <div className="grid auto-rows-min gap-4 md:mx-6"> */}
        <div className="border-sidebar-border/70">
          {/* <VideoJS options={videoJsOptions} onReady={handlePlayerReady} /> */}
          {settings.show_video === '1'
            ? (
                <VideoJS options={videoJsOptions} playsInline onReady={handlePlayerReady} />
              )
            : (
                <img src={settings.video_banner} className="max-w-full" />
              )}
          <div className="font-xs text-[9px] text-center italic py-2 text-red-500">
            Note: Payout is processed hourly from 11:00 AM to 11:00 PM Daily. Play Now!
          </div>
          <BetStats />
          <PlaceBets />
        </div>
        <BetTrends image={settings.ci_promo_banner} />
        {/* </div> */}
      </div>
    </AppLayout>
  )
}

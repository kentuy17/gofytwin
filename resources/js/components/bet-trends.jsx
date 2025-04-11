/* eslint-disable no-console */
/* eslint-disable no-alert */
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import { OperatorContext } from '@/context/operator-context'
import { usePage } from '@inertiajs/react'
import axios from 'axios'
import { useContext, useEffect, useMemo, useState } from 'react'
import { pusher } from '../echo'
import { Badge } from './ui/badge'
import { Button } from './ui/button'
import { Label } from './ui/label'
import { Switch } from './ui/switch'
import '../../css/bet-arnie.css'

const statuses = {
  OPEN: {
    backgroundColor: 'bg-green-500',
    textColor: 'text-white',
    isBlink: false,
    text: 'OPEN',
  },
  CLOSED: {
    backgroundColor: 'bg-red-500',
    textColor: 'text-white',
    isBlink: false,
    text: 'CLOSED',
  },
  WAITING: {
    backgroundColor: 'bg-white',
    textColor: 'text-black',
    isBlink: false,
    text: 'WAITING',
  },
  DONE: {
    backgroundColor: 'bg-white',
    textColor: 'text-black',
    isBlink: false,
    text: 'WAITING',
  },
}

export const trendClasses = {
  M: 'trend-meron',
  W: 'trend-wala',
  X: 'trend-draw',
  C: 'bg-secondary',
}

function StatusBar({ last }) {
  const [isLastCall, setIsLastCall] = useState(last)

  const { status } = useContext(OperatorContext)

  useEffect(() => {
    setIsLastCall(last)
  }, [last])

  return (

    <div className="bet-yellow h6 m-0 rounded-top flex flex-row justify-around items-center py-3">
      <span className="text-black text-xl">Game Fight Results</span>
      {isLastCall && <div className="uppercase bg-black py-2 px-4 text-red-500 animate-blink">LAST CALL</div>}
      <div className={`${blink} uppercase ${statuses[status].backgroundColor} py-2 px-4 ${statuses[status].textColor}`}>{statuses[status].text}</div>

    </div>
  )
}

function PopUpSelectResult() {
  const [isDialogOpen, setIsDialogOpen] = useState(false)

  const walaWins = () => {
    alert('wala wens')
  }

  const meronWins = () => {
    alert('wens wens')
  }

  const handleDraw = () => {
    console.log('draw')
    setIsDialogOpen(false)
  }

  const handleCanceled = () => {
    console.log('cancelled')
    setIsDialogOpen(false)
  }

  return (
    <AlertDialog open={isDialogOpen} onOpenChange={setIsDialogOpen} allowOutside>
      <AlertDialogTrigger asChild>
        <Button variant="outline" className="bg-blue-400">DONE</Button>
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Sino nanalo?</AlertDialogTitle>
          <AlertDialogDescription>
            {/* This action cannot be undone. This will permanently delete your account
            and remove your data from our servers. */}
          </AlertDialogDescription>

        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Back</AlertDialogCancel>
          <Button onClick={() => handleCanceled()} variant="secondary">CANCELLED</Button>
          <Button onClick={() => handleDraw()} variant="default" className="bg-yellow-500">DRAW</Button>
          <AlertDialogCancel onClick={walaWins} className="bg-blue-500 text-white font-bold">WALA</AlertDialogCancel>
          <AlertDialogAction onClick={meronWins} className="bg-red-500 text-white font-bold">MERON</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  )
}

function OperatorControls() {
  const [toggled, setToggled] = useState(false)
  return (
    <div className="grid gap-4 md:grid-cols-4 grid-cols-2 p-6">
      <Button variant="secondary" className="bg-green-400">OPEN</Button>
      <Button variant="destructive">CLOSED</Button>
      {/* <Toggle className="w-full" defaultPressed={false}><Button variant="secondary">LAST CALL</Button></Toggle> */}
      <div className="flex items-center space-x-2">
        <Switch id="airplane-mode" defaultChecked={toggled} onCheckedChange={() => setToggled(prev => !prev)} />
        {
          toggled
            ? (
                <Label className="animate-blink text-red-500" htmlFor="airplane-mode">LAST CALL</Label>
              )
            : (
                <Label htmlFor="airplane-mode">LAST CALL</Label>
              )
        }
      </div>
      <PopUpSelectResult />
    </div>
  )
}

function TrendRowItem({ bg, fightNo }) {
  return <td><div className={`trend-item ${bg}`}>{fightNo}</div></td>
}

function TrendRows({ data, limit }) {
  const rows = []
  for (let index = 0; index < limit; index++) {
    const item = data[index] ?? { fight_no: null, itemClass: 'trend-empty' }
    rows.push(<TrendRowItem key={index} bg={item.itemClass} fightNo={item.fight_no} />)
  }

  return <tr>{rows}</tr>
}

function reconstructTrend(talpak, lastResult) {
  const fights = [...talpak, ...lastResult]
  const formatted = {
    row1: [],
    row2: [],
    row3: [],
    row4: [],
    row5: [],
  }
  const objLen = Object.keys(formatted).length
  let prevResult = null
  let prevPosition = { x: 0, y: 1 }
  let c = 1 // consecutive


  for (let index = 0; index < fights.length; index++) {
    const element = fights[index]
    const isConsecutive = element.result === prevResult
    const { x, y } = prevPosition

    let a = 1 // horizontal
    let b = y // vertial
    let isSame = false

    if ((isConsecutive && prevResult) && (c <= objLen && isConsecutive)) {
      a = c === objLen ? 1 : x + 1
      isSame = true
      formatted[`row${a}`].push(element)
    }

    if (!isConsecutive && prevResult) {
      b = y + 1
      for (let i = x + 1; i <= 5; i++) {
        const row = `row${i}`
        formatted[row].push(null)
      }

      formatted.row1.push(element)
    }

    if (!prevResult) {
      formatted.row1.push(element)
      a = b = 1
    }

    c = isSame ? c + 1 : 1
    prevResult = element.result
    prevPosition = { x: a, y: b }
  }

  return formatted
}

function createFightArray(results) {
  const fights = results.map((fyt) => {
    return {
      ...fyt,
      itemClass: trendClasses[fyt.result],
    }
  })
  return fights
}

function BetTrends({ last }) {
  const { status, lastResult } = useContext(OperatorContext)
  const { globals } = usePage().props
  const fightArray = createFightArray(globals.trend.results)
  const formattedFights = useMemo(
    () => reconstructTrend(fightArray, lastResult),
    [fightArray, lastResult],
  )

  const limit = formattedFights.row1.length > 50
    ? formattedFights.row1.length + 10
    : 50

  return (
    <div>
      <div>
        {window.location.pathname === '/operator'
          ? (
              <OperatorControls />
            )
          : (
              <StatusBar status={status} />
            )}
        {' '}
        <div className="trend-legend gp-panel-bg d-flex justify-content-between align-items-center py-3 px-2">
          {globals.trend.summary.map((item, key) => {
            return (
              <div key={key} className="bad-cont d-inline-flex align-items-center mr-1">
                <span className={`count rounded-circle ${item.class}`}>{item.count}</span>
                <p className="mb-0 count-label">{item.label}</p>
              </div>
            )
          })}
        </div>
        {' '}
        <div className="px-3 pb-3 gp-panel-bg rounded-bottom landscape-trend" style={{ position: 'relative' }}>
          <div className="table-responsive pb-3 gp-scroll">
            <table className="trend-table">
              <tbody>
                <TrendRows data={formattedFights.row1} limit={limit} />
                <TrendRows data={formattedFights.row2} limit={limit} />
                <TrendRows data={formattedFights.row3} limit={limit} />
                <TrendRows data={formattedFights.row4} limit={limit} />
                <TrendRows data={formattedFights.row5} limit={limit} />
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  )
}

export default BetTrends

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
import { useContext, useMemo, useState } from 'react'
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

function StatusBar() {
  const { status, lastCall } = useContext(OperatorContext)

  return (
    <div className="bet-yellow h6 m-0 rounded-top flex flex-row justify-around items-center py-3">
      <span className="text-black text-xl">Game Fight Results</span>
      {lastCall && <div className="uppercase bg-black py-2 px-4 text-red-500 animate-blink">LAST CALL</div>}
      <div className={`uppercase ${statuses[status] && statuses[status].backgroundColor} py-2 px-4 ${status && statuses[status].textColor}`}>
        {status && statuses[status].text}
      </div>

    </div>
  )
}

async function updateResult(result) {
  const { data } = await axios.post('/fight/update', {
    status: 'DONE',
    result,
  })
  return data
}

async function updateStatus(status) {
  const { data } = await axios.post('/fight/update', {
    status,
    result: null,
  })
  return data
}

async function updateLastCall(params) {
  const { data } = await axios.post('/fight/lastcall', params)
  return data
}

function PopUpSelectResult() {
  const [isDialogOpen, setIsDialogOpen] = useState(false)

  const walaWins = () => {
    alert('wala wens')
    updateResult('W')
  }

  const meronWins = () => {
    alert('meron wins')
    updateResult('M')
  }

  const handleDraw = () => {
    updateResult('X')
    setIsDialogOpen(false)
  }

  const handleCanceled = () => {
    updateResult('C')
    setIsDialogOpen(false)
  }

  return (
    <AlertDialog open={isDialogOpen} onOpenChange={setIsDialogOpen} allowOutside>
      <AlertDialogTrigger asChild>
        <Button variant="outline" className="bg-blue-400 cursor-pointer">DONE</Button>
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Sino nanalo?</AlertDialogTitle>
          <AlertDialogDescription>
            {/* This action cannot be undone. This will permanently delete your account
            and remove your data from our servers. */}
          </AlertDialogDescription>

        </AlertDialogHeader>
        <AlertDialogFooter className="grid md:grid-cols-2 grid-cols-1 gap-2">
          <AlertDialogAction onClick={meronWins} className="bg-red-500 text-white font-bold">MERON</AlertDialogAction>
          <AlertDialogCancel onClick={walaWins} className="dark:bg-blue-500 text-white font-bold">WALA</AlertDialogCancel>
          <Button onClick={() => handleDraw()} variant="default" className="bg-yellow-500">DRAW</Button>
          <Button onClick={() => handleCanceled()} variant="secondary">CANCELLED</Button>
          {/* <Button disabled variant="default">REVERT</Button> */}
          <AlertDialogCancel>Back</AlertDialogCancel>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  )
}

function OperatorControls() {
  const { status, setLastCall, lastCall } = useContext(OperatorContext)
  const { _token } = usePage().props

  const handleOpen = () => {
    return () => updateStatus('OPEN')
  }

  const handleClosed = () => {
    return () => updateStatus('CLOSED')
  }

  const handleLastCallChange = () => {
    setLastCall(prev => !prev)
    updateLastCall({ _token, lastCall: !lastCall })
  }

  return (
    <div className="grid">
      <div className="flex w-full items-center justify-around">
        <Badge className={`uppercase ${status && statuses[status].backgroundColor} ${status && statuses[status].textColor}`}>
          {status && statuses[status].text}
        </Badge>
      </div>
      <div className="grid gap-4 md:grid-cols-4 grid-cols-2 p-6">
        <Button
          onClick={handleOpen()}
          disabled={!['WAITING', 'CLOSED', 'DONE'].includes(status)}
          variant="secondary"
          className="bg-green-400 cursor-pointer"
        >
          OPEN
        </Button>
        <Button
          onClick={handleClosed()}
          disabled={status === 'CLOSED'}
          variant="destructive"
          className="cursor-pointer"
        >
          CLOSED
        </Button>
        {/* <Toggle className="w-full" defaultPressed={false}><Button variant="secondary">LAST CALL</Button></Toggle> */}
        <div className="flex items-center space-x-2">
          <Switch id="airplane-mode" defaultChecked={lastCall} disabled={status === 'CLOSED'} onCheckedChange={handleLastCallChange} />
          {
            lastCall
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

function BetTrends() {
  const { lastResult } = useContext(OperatorContext)
  const { globals } = usePage().props
  const fightArray = createFightArray(globals.trend.results)
  const formattedFights = useMemo(
    () => reconstructTrend(fightArray, lastResult),
    [fightArray, lastResult],
  )

  const limit = formattedFights.row1.length > 50
    ? formattedFights.row1.length + 10
    : 50

  lastResult.forEach((last) => {
    const index = globals.trend.summary.findIndex(item => item.class === last.itemClass)
    globals.trend.summary[index].count += 1
  })

  return (
    <div>
      <div>
        {window.location.pathname === '/operator'
          ? (
              <OperatorControls />
            )
          : (
              <StatusBar />
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

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
import axios from 'axios'
import { useContext, useEffect, useState } from 'react'
import { Badge } from './ui/badge'
import { Button } from './ui/button'
import { Label } from './ui/label'
import { Switch } from './ui/switch'
import '../../css/bet-arnie.css'
import { usePage } from '@inertiajs/react'

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
  const { status } = useContext(OperatorContext)

  const handleOpen = () => {
    return () => updateStatus('OPEN')
  }

  const handleClosed = () => {
    return () => updateStatus('CLOSED')
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
    </div>
  )
}

function TrendRowItem({bg,fightNo}) {
  return <td><div className={`trend-item ${bg}`}>{fightNo}</div></td>
}

function TrendRows() {
  const rows = []
  for (let index = 1; index < 45; index++) {
    rows.push(<TrendRowItem bg={'trend-meron'} fightNo={index} />)
  }

  return <tr>{rows}</tr>
}

function reconstructTrend(fights) {
  let prevResult = 'M'
  let prevPosition = {x:1,y:1}
  let formatted = {
    row1: [],
    row2: [],
    row3: [],
    row4: [],
    row5: []
  }

  // formatted.row1.push(fights[0])

  console.log(fights);

  for (let index = 0; index < fights.length; index++) {
    const element = fights[index];
    let {x, y} = prevPosition
    let a = 1
    let b = y
    if(element.result === prevResult) {
      a = x + 1
      for (let i = x+1; i <= 5; i++) {
        formatted['row'+i].push('skip-'+element.fight_no)
      }
      formatted['row'+a].push(element)
    } else {
      b = y + 1
      formatted['row1'].push(element)
    }

    prevResult = element.result
    prevPosition = {x:a, y:b}
    // console.log(prevPosition, prevResult + '----fyt#' + element.fight_no);
  }

  return formatted
}

function BetTrends({ last }) {
  const { status } = useContext(OperatorContext)
  const { globals } = usePage().props

  console.log(reconstructTrend(globals.trend.results.slice(0,10)));

  return (
    <div>
      <div>
        {window.location.pathname === '/operator'
          ? (
              <OperatorControls />
            )
          : (
              <StatusBar status={status} last={last} />
            )}
        <div className="trend-legend gp-panel-bg d-flex justify-content-between align-items-center py-3 px-2">
          {globals.trend.summary.map((item) => {
            return (
              <div className="bad-cont d-inline-flex align-items-center mr-1">
                <span className={`count rounded-circle ${item.class}`}>{item.count}</span>
                <p className="mb-0 count-label">{item.label}</p>
              </div>
            )
          })}
        </div>
        <div className="px-3 pb-3 gp-panel-bg rounded-bottom landscape-trend" style={{ position: 'relative' }}>
          <div className="table-responsive pb-3 gp-scroll">
            <table className="trend-table">
              <tbody>
                <TrendRows />
                <tr>
                  <td><div className="trend-item trend-meron">46</div></td>
                  <td><div className="trend-item trend-meron">50</div></td>
                  <td><div className="trend-item trend-wala">53</div></td>
                  <td><div className="trend-item trend-wala">55</div></td>
                  <td><div className="trend-item trend-meron">56</div></td>
                  <td><div className="trend-item trend-wala">59</div></td>
                  <td><div className="trend-item trend-meron">60</div></td>
                  <td><div className="trend-item trend-wala">61</div></td>
                  <td><div className="trend-item trend-meron">62</div></td>
                  <td><div className="trend-item trend-wala">65</div></td>
                  <td><div className="trend-item trend-meron">66</div></td>
                  <td><div className="trend-item trend-wala">67</div></td>
                  <td><div className="trend-item trend-meron">68</div></td>
                  <td><div className="trend-item trend-wala">69</div></td>
                  <td><div className="trend-item trend-meron">71</div></td>
                  <td><div className="trend-item trend-meron">75</div></td>
                  <td><div className="trend-item trend-wala">78</div></td>
                  <td><div className="trend-item trend-meron">80</div></td>
                  <td><div className="trend-item trend-meron">84</div></td>
                  <td><div className="trend-item trend-meron">88</div></td>
                  <td><div className="trend-item trend-wala">89</div></td>
                  <td><div className="trend-item trend-meron">90</div></td>
                  <td><div className="trend-item trend-wala">91</div></td>
                  <td><div className="trend-item trend-meron">93</div></td>
                  <td><div className="trend-item trend-cancel">×</div></td>
                  <td><div className="trend-item trend-wala">96</div></td>
                  <td><div className="trend-item trend-meron">97</div></td>
                  <td><div className="trend-item trend-wala">98</div></td>
                  <td><div className="trend-item trend-cancel">×</div></td>
                  <td><div className="trend-item trend-wala">100</div></td>
                  <td><div className="trend-item trend-cancel">×</div></td>
                  <td><div className="trend-item trend-wala">104</div></td>
                  <td><div className="trend-item trend-meron">106</div></td>
                  <td><div className="trend-item trend-wala">109</div></td>
                  <td><div className="trend-item trend-meron">110</div></td>
                  <td><div className="trend-item trend-wala">114</div></td>
                  <td><div className="trend-item trend-meron">115</div></td>
                  <td><div className="trend-item trend-wala">116</div></td>
                  <td><div className="trend-item trend-meron">120</div></td>
                  <td><div className="trend-item trend-wala">121</div></td>
                  <td><div className="trend-item trend-meron">122</div></td>
                  <td><div className="trend-item trend-wala">124</div></td>
                  <td><div className="trend-item trend-meron">125</div></td>
                </tr>
                <tr>
                  <td><div className="trend-item trend-meron">47</div></td>
                  <td><div className="trend-item trend-meron">51</div></td>
                  <td><div className="trend-item trend-draw">54</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">57</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">63</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">70</div></td>
                  <td><div className="trend-item trend-meron">72</div></td>
                  <td><div className="trend-item trend-meron">76</div></td>
                  <td><div className="trend-item trend-wala">79</div></td>
                  <td><div className="trend-item trend-meron">81</div></td>
                  <td><div className="trend-item trend-meron">85</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">92</div></td>
                  <td><div className="trend-item trend-meron">94</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">101</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">105</div></td>
                  <td><div className="trend-item trend-meron">107</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">111</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">117</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">123</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                </tr>
                <tr>
                  <td><div className="trend-item trend-meron">48</div></td>
                  <td><div className="trend-item trend-meron">52</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">58</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">64</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">73</div></td>
                  <td><div className="trend-item trend-meron">77</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">82</div></td>
                  <td><div className="trend-item trend-meron">86</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">102</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">108</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">112</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">118</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                </tr>
                <tr>
                  <td><div className="trend-item trend-meron">49</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">74</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">83</div></td>
                  <td><div className="trend-item trend-meron">87</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-meron">113</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-wala">119</div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                  <td><div className="trend-item trend-empty"></div></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  )
}

export default BetTrends

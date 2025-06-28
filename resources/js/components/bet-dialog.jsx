/* eslint-disable no-alert */
import {
  AlertDialog,
  AlertDialogAction,
  // AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'

import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'

import { OperatorContext } from '@/context/operator-context'
import { cn, formatMoney, numberWithCommas } from '@/lib/utils'
import { usePage } from '@inertiajs/react'
import axios from 'axios'
import { useContext, useEffect, useRef, useState } from 'react'
import CurrencyInput from 'react-currency-input-field'

const min = 20
const max = 20000

function InputBet({ className, bet, setBet, handleKeyDown }) {
  const [betVal, setBetVal] = useState(bet)

  const handleValueChange = (value) => {
    setBetVal(value)
    setBet(value)
  }

  useEffect(() => {
    if (bet > max || betVal > max) {
      setBetVal(max)
      setBet(max)
    }
    else {
      setBetVal(bet)
      setBet(bet)
    }
  }, [bet, betVal, setBet])

  return (
    <CurrencyInput
      onKeyDown={handleKeyDown}
      id="input-example"
      name="input-name"
      placeholder="Please enter a number"
      defaultValue={100}
      value={betVal}
      decimalsLimit={2}
      onValueChange={handleValueChange}
      className={cn(
        'border-input file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
        'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
        className,
      )}
    />
  )
}

async function addBet(params) {
  try {
    const { message, amount, side, credits, _token } = params
    if (message !== 'OPEN') {
      alert('Cannot Bet')
      return
    }

    if (amount < min) {
      alert(`Minimum bet is ${formatMoney(min)}`)
      return
    }

    if (amount > credits) {
      alert('Insuficient Points')
      return
    }

    if (amount > max) {
      alert(`Maximum bet is ${formatMoney(max)}`)
      return
    }

    const { data } = await axios.post('/bet/add', {
      amount,
      side,
      _token,
    })

    return data.status
  }
  catch (err) {
    console.error(err)
  }
}

function AmountBtn({ amount, setBet, className }) {
  return (
    <Button
      onClick={() => setBet(amount)}
      size="sm"
      className={cn(
        'h-5 px-2',
        className,
      )}
    >
      {numberWithCommas(amount)}
    </Button>
  )
}

export function BetDialog({ children, side, reset }) {
  const { csrf_token } = usePage().props
  const submitRef = useRef(null)
  const { credits, playerBet, setPlayerBet, setCredits } = useContext(OperatorContext)
  const [open, setOpen] = useState(false)
  const [disabled, setDisabled] = useState(false)
  const [bet, setBet] = useState(100)
  const betMeron = Number.parseFloat(playerBet.meron)
  const betWala = Number.parseFloat(playerBet.wala)

  const amounts = [20, 50, 100, 500, 1000, 1500, 'ALL-IN']

  const handleSumbit = async (e) => {
    e.preventDefault()

    if (bet < min) {
      alert(`Minimum bet is ${formatMoney(min)}`)
      return
    }

    if (bet > credits) {
      alert('Insuficient points')
      return
    }

    setOpen(false)
    setDisabled(true)
    const resp = await addBet({
      message: 'OPEN',
      amount: bet,
      side,
      credits,
      _token: csrf_token,
    })

    if (resp === 'OK') {
      side === 'M'
        ? setPlayerBet({ ...playerBet, meron: betMeron + Number.parseFloat(bet) })
        : setPlayerBet({ ...playerBet, wala: betWala + Number.parseFloat(bet) })

      setCredits(prev => prev - bet)
      reset(-1)
      localStorage.setItem('ctr', 0)
      setDisabled(false)
    }
    else {
      setDisabled(false)
      // console.log(resp)
    }
  }

  const handleOnEnter = (e) => {
    if (e.key === 'Enter') {
      submitRef.current.click()
    }
  }

  useEffect(() => {
    if (bet === 'ALL-IN') {
      setBet(credits)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [bet])

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        {children}
      </DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Input Amount</DialogTitle>
        </DialogHeader>
        <div className="grid gap-4 py-4">
          <div className="grid grid-cols-2 items-center gap-4">
            <InputBet setBet={setBet} bet={bet} handleKeyDown={handleOnEnter} className="col-span-3" />
          </div>
          <div className="flex gap-2 flex-row items-center justify-center flex-wrap">
            {amounts.map((value) => {
              return <AmountBtn className="bg-green-500 text-white" key={value} amount={value} setBet={setBet} />
            })}
          </div>
        </div>
        <DialogFooter>
          <Button type="submit" disabled={disabled} ref={submitRef} onClick={handleSumbit}>
            {`BET ${side === 'M' ? 'MERON' : 'WALA'}`}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}

export function CongratsDialog({ children, win_amount, status }) {
  const [open, setOpen] = useState(false)
  return (
    <AlertDialog open={open} onOpenChange={setOpen}>
      <AlertDialogTrigger asChild>
        {children}
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Congratulations!</AlertDialogTitle>
          <AlertDialogDescription>
            {(status === 'W')
              ? (`You win ${formatMoney(win_amount)}`)
              : (`Returned ${formatMoney(win_amount)}`)}
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          {/* <AlertDialogCancel>Cancel</AlertDialogCancel> */}
          <AlertDialogAction>OK</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  )
}

export function PromoBanner({ image }) {
  return (
    <div className="flex flex-col">
      <img src={image} style={{ height: '55px' }} className="max-w-full" />
    </div>
  )
}

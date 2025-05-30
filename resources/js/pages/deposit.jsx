/* eslint-disable no-console */
import InputError from '@/components/input-error'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
// import React from 'react'
import AppLayout from '@/layouts/app-layout'
import { cn, formatMoney } from '@/lib/utils'
import { Head, useForm } from '@inertiajs/react'
import formatDistanceToNow from 'date-fns/formatDistanceToNow'
import { useEffect, useRef, useState } from 'react'
import '../../css/bet-arnie.css'

function DepForms(xrftoken) {
  const submitRef = useRef(null)
  const [submitDisabled, setSubmitDisabled] = useState(true)
  const { data, setData, post, processing, reset } = useForm({
    amount: '',
    remember: true,
  })
  const submit = (e) => {
    e.preventDefault()

    // post('/otp/generate', {
    //   onFinish: () => reset('password'),
    // })
    if (data.amount == '') {
      alert('Plase input amount')

      return
    }

    fetch('/cash-in', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': xrftoken.xrftoken,
      },
      body: JSON.stringify({
        pg: 'gcash_redirect',
        amount: data.amount,
      }),
    }).then((response) => {
      return Promise.all([response.json(), response])
    }).then(([responseData, response]) => {
      // handle data here
      console.log(responseData, response)
      if (responseData.status === true) {
        window.location.href = responseData.redirect_url
      }
      else {
        console.log('wqewqe')
      }
      // console.log(response)
    })
  }
  const inputGradient = {
    background: 'rgb(1, 1, 2)',
    // eslint-disable-next-line no-dupe-keys
    background: 'linear-gradient(90deg, rgba(1,1,2,1) 0%, rgba(34,34,49,1) 48%, rgba(84,84,84,1) 100%)',
    borderColor: 'rgba(84,84,84,1)',
    marginLeft: '16px',
    width: 'calc(100% - 32px)',
  }

  return (
    <div className="space-y-6">
      <form onSubmit={submit}>
        <div className="grid gap-2">
          <Label htmlFor="amount">Enter Amount</Label>
          <Input
            onChange={handleAmountInput}
            value={data.amount}
            // onChange={e => setData('amount', e.target.value)}
            className="bg-gradient-to-l from-white to-zinc-900 to-75%"
            style={inputGradient}
            placeholder="Enter Amount"
          />

          {/* <InputError message={errors.email} /> */}
        </div>

        <div className="my-6 flex items-center justify-start">
          <Button type="submit" ref={submitRef} tabIndex={4} disabled={processing}>
            Submit
          </Button>
        </div>
      </form>

    </div>
  )
}

function PaymentProvider({ item }) {
  const { name, icon, min, max } = item

  return (
    <div className="dep-tile-2 rounded clearfix">
      <div className="btn-gold rounded gp-shadow-sm d-flex p-2 clickable">
        <div className="icn">
          <img src={icon} />
        </div>
        <div className="info">
          <div className="name" style={{ textTransform: 'uppercase' }}>
            {name}
            {' '}
            (Instant)
          </div>
          <div className="desc">
            <div>
              {`Minimum: ${formatMoney(min)}`}
            </div>
            <div>
              {`Maximum: ${formatMoney(max)}`}
            </div>
            <div>Processing: 2 - 5 mins</div>
          </div>
        </div>
      </div>
    </div>
  )
}

export function PaymentProviderTmp({ item }) {
  const [pg, setPg] = useState({ selected: 0 })
  return (
    <button
      type="button"
      key={item.name}
      className={cn(
        'flex flex-col items-start gap-2 rounded-lg border p-3 text-left text-sm transition-all hover:bg-accent',
        pg.selected === item.id && 'bg-muted',
      )}
      onClick={() => setPg({ selected: item.name })}
    >
      <div className="flex w-full flex-col gap-1">
        <div className="flex items-center">
          <div className="flex items-center gap-2">
            <div className="font-semibold">{item.name}</div>
            {!item.read && (
              <span className="flex h-2 w-2 rounded-full bg-blue-600" />
            )}
          </div>
          <div
            className={cn(
              'ml-auto text-xs',
              pg.selected === item.name
                ? 'text-foreground'
                : 'text-muted-foreground',
            )}
          >
            {formatDistanceToNow(new Date(item.date), {
              addSuffix: true,
            })}
          </div>
        </div>
        <div className="text-xs font-medium">{item.subject}</div>
      </div>
      <div className="line-clamp-2 text-xs text-muted-foreground">
        {item.text.substring(0, 300)}
      </div>
      {item.labels.length
        ? (
            <div className="flex items-center gap-2">
              {item.labels.map(label => (
                <Badge key={label} variant="default">
                  {label}
                </Badge>
              ))}
            </div>
          )
        : null}
    </button>
  )
}

function Deposit({ providers, xrftoken }) {
  console.log(providers)
  // console.log(xrftoken)
  // setData('xrf_token', xrftoken)
  const breadcrumbs = [
    {
      title: 'Cash-in',
      href: '/player/deposit',
    },
  ]
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <div className="flex flex-col m-6">
        <div className="text-center mb-3">
          Click to select outlet:
        </div>

        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {providers.map((item, key) => {
            return (
              <Card key={key} aria-selected className="hover:bg-accent">
                <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                  <CardTitle className="text-sm font-medium">
                    {item.name}
                  </CardTitle>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    className="h-4 w-4 text-muted-foreground"
                  >
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                  </svg>
                </CardHeader>
                <CardContent>
                  <PaymentProvider item={item} />
                </CardContent>
              </Card>
            )
          })}
          {/* <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">
                Subscriptions
              </CardTitle>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                className="h-4 w-4 text-muted-foreground"
              >
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
              </svg>
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">+2350</div>
              <p className="text-xs text-muted-foreground">
                +180.1% from last month
              </p>
            </CardContent>
          </Card>
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Sales</CardTitle>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                className="h-4 w-4 text-muted-foreground"
              >
                <rect width="20" height="14" x="2" y="5" rx="2" />
                <path d="M2 10h20" />
              </svg>
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">+12,234</div>
              <p className="text-xs text-muted-foreground">
                +19% from last month
              </p>
            </CardContent>
          </Card>
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">
                Active Now
              </CardTitle>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                className="h-4 w-4 text-muted-foreground"
              >
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
              </svg>
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">+573</div>
              <p className="text-xs text-muted-foreground">
                +201 since last hour
              </p>
            </CardContent>
          </Card> */}
        </div>
      </div>

      <DepForms xrftoken={xrftoken} />

    </AppLayout>
  )
}

export default Deposit

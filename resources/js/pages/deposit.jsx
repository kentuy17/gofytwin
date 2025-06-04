/* eslint-disable no-console */
/* eslint-disable no-alert */
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AppLayout from '@/layouts/app-layout'
import { formatMoney } from '@/lib/utils'
import { Head, useForm } from '@inertiajs/react'
import { useRef, useState } from 'react'
import '../../css/bet-arnie.css'

function DepForms({ xrftoken, inputValue }) {
  const submitRef = useRef(null)
  const [submitDisabled, setSubmitDisabled] = useState(true)
  const { data, setData, processing } = useForm({
    amount: '',
    remember: true,
  })
  const handleAmountInput = (e) => {
    let val = e.target.value
    val = val.replace(/\D/g, '')
    if (val.length > 6)
      return
    setSubmitDisabled(val.length < 2)
    setData('amount', val)
  }

  const submit = (e) => {
    e.preventDefault()

    if (data.amount === '') {
      alert('Plase input amount')

      return
    }
    if (inputValue == null) {
      alert('Plase select gateway')

      return
    }

    fetch('/cash-in', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': xrftoken,
      },
      body: JSON.stringify({
        pg: inputValue,
        amount: data.amount,
      }),
    }).then((response) => {
      return Promise.all([response.json(), response])
    }).then(([responseData]) => {
      // handle data here

      if (responseData.status === true) {
        window.location.href = responseData.redirect_url
      }
      else {
        console.log('wqewqe')
      }
      // console.log(response)
    })
  }

  return (
    <div className="space-y-6 p-6">
      <form onSubmit={submit}>
        <div className="grid my-6">
          Gateway:  &nbsp;
          {' '}
          {inputValue}
        </div>
        <div className="grid gap-2">
          <Label htmlFor="amount">Enter Amount</Label>
          <Input
            onChange={handleAmountInput}
            value={data.amount}
            // onChange={e => setData('amount', e.target.value)}
            placeholder="Enter Amount"
          />
          {/* <InputError message={errors.email} /> */}
        </div>
        {/* <Label htmlFor="amount">Chosen Gateway: </Label> */}

        <div className="my-6 flex items-center justify-start">
          <Button type="submit" ref={submitRef} tabIndex={4} disabled={processing || submitDisabled}>
            Submit
          </Button>
        </div>
      </form>

    </div>
  )
}

function PaymentProvider({ onInputChange, item }) {
  const { name, icon, min, max } = item
  const handleChange = () => {
    onInputChange(name)
    // let val = e.target.value
    // val = val.replace(/\D/g, '')
    // if (val.length > 6)
    //   return
    // setSubmitDisabled(val.length !== 6)
    // setData('amount', val)
  }

  return (
    <div className="dep-tile-2 rounded clearfix">
      <div className="btn-gold rounded gp-shadow-sm d-flex p-2 clickable" onClick={handleChange}>
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

function Deposit({ providers, xrftoken }) {
  const [inputValue, setInputValue] = useState(null)
  const handleInputChange = (newValue) => {
    setInputValue(newValue)
  }

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
              <Card key={key} className={inputValue === item.name ? 'border-4 border-yellow-400' : ''}>
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
                  <PaymentProvider inputValue={inputValue} onInputChange={handleInputChange} item={item} />
                </CardContent>
              </Card>
            )
          })}
        </div>
      </div>

      <DepForms xrftoken={xrftoken} inputValue={inputValue} />

    </AppLayout>
  )
}

export default Deposit

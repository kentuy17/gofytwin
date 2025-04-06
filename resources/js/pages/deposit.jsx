import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
// import React from 'react'
import AppLayout from '@/layouts/app-layout'
import { formatMoney } from '@/lib/utils'
import { Head } from '@inertiajs/react'
import '../../css/bet-arnie.css'

const breadcrumbs = [
  {
    title: 'Cash-in',
    href: '/player/deposit',
  },
]

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

function Deposit({ providers }) {
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
              <Card key={key}>
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
    </AppLayout>
  )
}

export default Deposit

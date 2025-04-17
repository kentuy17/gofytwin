/* eslint-disable no-console */
import { columns } from '@/components/events/columns'
import { DataTable } from '@/components/events/data-table'
import AppLayout from '@/layouts/app-layout'
import { Head } from '@inertiajs/react'

const breadcrumbs = [
  {
    title: 'Events',
    href: '/events',
  },
]

export function getData() {
  // Fetch data from your API here.
  return [
    {
      id: '728ed52f',
      amount: 100,
      status: 'pending',
      email: 'm@example.com',
    },
    {
      id: '489e1d42',
      amount: 125,
      status: 'processing',
      email: 'example@gmail.com',
    },
  ]
}

export default function Events({ events }) {
  const data = events
  console.log(events)

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <div className="flex flex-col m-6">
        {/* <div className="text-center mb-3">
          {JSON.stringify(events, true)}
        </div> */}

        <div className="container mx-auto py-10">
          <DataTable columns={columns} data={data} />
        </div>

      </div>
    </AppLayout>
  )
}

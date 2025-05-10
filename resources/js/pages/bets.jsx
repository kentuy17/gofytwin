import { columns } from '@/components/bets/columns'
import { DataTable } from '@/components/bets/data-table'
import AppLayout from '@/layouts/app-layout'
import { Head } from '@inertiajs/react'

const breadcrumbs = [
  {
    title: 'Bets',
    href: '/bets',
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

export default function Bets({ bets }) {
  const data = bets
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <div className="flex flex-col md:m-6 m-2">
        <div className="container mx-auto py-10">
          <DataTable className="grid grid-cols-1" columns={columns} data={data} />
        </div>
      </div>
    </AppLayout>
  )
}

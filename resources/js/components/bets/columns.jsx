// import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
// import {
//   DropdownMenu,
//   DropdownMenuContent,
//   DropdownMenuItem,
//   DropdownMenuLabel,
//   DropdownMenuSeparator,
//   DropdownMenuTrigger,
// } from '@/components/ui/dropdown-menu'
import { formatMoney } from '@/lib/utils'
// import { router } from '@inertiajs/react'
// import { MoreHorizontal } from 'lucide-react'
import { DataTableColumnHeader } from './data-table-column-header'

function formatResult(data) {
  let res = data
  switch (data) {
    case 'W':
      res = <span className="text-green-600">WIN</span>
      break
    case 'L':
      res = <span className="text-neutral-500">LOSE</span>
      break
    default:
      res = data
      break
  }
  return res
}

function formatSide(data) {
  let res = data
  switch (data) {
    case 'W':
      res = <span className="text-blue-500">WALA</span>
      break
    case 'M':
      res = <span className="text-red-500">MERON</span>
      break
    default:
      res = data
      break
  }
  return res
}

export const columns = [
  {
    id: 'select',
    header: ({ table }) => (
      <Checkbox
        checked={
          table.getIsAllPageRowsSelected()
          || (table.getIsSomePageRowsSelected() && 'indeterminate')
        }
        onCheckedChange={value => table.toggleAllPageRowsSelected(!!value)}
        aria-label="Select all"
        className="hidden"
      />
    ),
    cell: ({ row }) => (
      <Checkbox
        checked={row.getIsSelected()}
        onCheckedChange={value => row.toggleSelected(!!value)}
        aria-label="Select row"
        className="hidden"
      />
    ),
    enableSorting: false,
    enableHiding: true,
  },
  {
    accessorKey: 'id',
    header: ({ column }) => {
      return <DataTableColumnHeader className="hidden" column={column} title="#" />
    },
    cell: ({ row }) => <div className="hidden">{row.getValue('id')}</div>,
  },

  {
    accessorKey: 'fight_no',
    header: ({ column }) => {
      return <DataTableColumnHeader className="text-center" column={column} title="#" />
    },
  },
  {
    accessorKey: 'side',
    header: ({ column }) => {
      return <DataTableColumnHeader className="text-center" column={column} title="Side" />
    },
    cell: ({ row }) => {
      const side = formatSide(row.getValue('side'))
      return <div className="text-center font-medium">{side}</div>
    },
  },
  {
    accessorKey: 'status',
    header: ({ column }) => {
      return <DataTableColumnHeader className="text-center" column={column} title="Result" />
    },
    cell: ({ row }) => {
      const result = formatResult(row.getValue('status'))
      return <div className="text-center font-medium">{result}</div>
    },

  },
  {
    accessorKey: 'amount',
    header: ({ column }) => {
      return <DataTableColumnHeader className="text-center" column={column} title="Bet" />
    },
    cell: ({ row }) => {
      const amount = Number.parseFloat(row.getValue('amount'))
      return <div className="text-right font-medium">{formatMoney(amount)}</div>
    },
  },
  {
    accessorKey: 'percent',
    header: ({ column }) => {
      return <DataTableColumnHeader className="text-right" column={column} title="Percent" />
    },
    cell: ({ row }) => {
      const percent = Number.parseFloat(row.getValue('percent'))
      return <div className="text-right font-medium">{`${percent.toFixed(2)}%`}</div>
    },
  },
  {
    accessorKey: 'win_amount',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Win" />
    },
    cell: ({ row }) => {
      const winColor = row.getValue('status') === 'W' ? 'text-yellow-300' : 'text-white'
      return (
        <div className="text-right font-medium">
          <span className={winColor}>{formatMoney(row.getValue('win_amount'))}</span>
        </div>
      )
    },
  },
  {
    accessorKey: 'current_points',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Points" />
    },
    cell: ({ row }) => {
      return <div className="text-right font-medium">{formatMoney(row.getValue('current_points'))}</div>
    },
  },
  {
    accessorKey: 'created_at',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Date" />
    },
  },
  // {
  //   id: 'actions',
  //   cell: ({ row }) => {
  //     const event = row.original

  //     return (
  //       <DropdownMenu>
  //         <DropdownMenuTrigger asChild>
  //           <Button variant="ghost" className="h-8 w-8 p-0">
  //             <span className="sr-only">Open menu</span>
  //             <MoreHorizontal className="h-4 w-4" />
  //           </Button>
  //         </DropdownMenuTrigger>
  //         <DropdownMenuContent align="end">
  //           <DropdownMenuLabel>Actions</DropdownMenuLabel>
  //           <DropdownMenuItem
  //             onClick={() => navigator.clipboard.writeText(event.id)}
  //           >
  //             Copy event ID
  //           </DropdownMenuItem>
  //           <DropdownMenuSeparator />
  //           <DropdownMenuItem onClick={() => router.post(`/event/activate/${event.id}`)} disabled={event.status === 'ACTIVE'}>Activate</DropdownMenuItem>
  //           <DropdownMenuItem>Edit</DropdownMenuItem>
  //           <DropdownMenuItem>Delete</DropdownMenuItem>
  //         </DropdownMenuContent>
  //       </DropdownMenu>
  //     )
  //   },
  // },
]
